<?php 

namespace App\Libraries;
use CodeIgniter\I18n\Time;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Models\ConnectionsModel;
use App\Models\UserModel;
use App\Models\AppUserModel;
use App\Models\OauthTokenModel;
use App\Models\ChatHistoryModel;

use App\Models\AppUserRelsModel;
use App\Models\ProfilesModel;
use App\Models\ProfilesPostsModel;
use App\Models\ProfilesPostsComments;
use App\Models\ProfilesPostsRelsModel;
use App\Models\Profile_relsModel;
use App\Models\Prof_act_relsModel;
use App\Models\ProfileBPinModel;

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $userModel;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $db = db_connect();
        $db->reconnect();
        $uriQuery = $conn->httpRequest->getUri()->getQuery(); //access_token=12312313
        $uriQueryArr = explode('=',$uriQuery); //$uriQueryArr[1]
        
        if (empty($uriQueryArr[1])) {
            // logging
            $log_data = [
                'uriQuery' => $uriQuery,
                'no_token' => empty($uriQueryArr[1]),
                'uriQueryArr' => $uriQueryArr[1],
            ];
            log_message('alert', 'No token provided! | uriQueryArr: "{uriQueryArr}" | uriQuery: "{uriQuery}"', $log_data);
            trigger_error(json_encode(["msg"=>"No token provided!","eCode"=>4000]), E_USER_WARNING);
        }

        $userModel = new UserModel();
        $OauthTokenModel = new OauthTokenModel();
        $conModel = new ConnectionsModel();

        if (strlen($uriQueryArr[1]) < 10) {
            $user = $userModel->find($uriQueryArr[1]);
            
            if (empty($user)) {
                // logging
                $log_data = [
                    'user' => empty($user) ? 'User {$uriQueryArr[1]} not found!' : $user,
                    'uriQueryArr' => $uriQueryArr[1],
                ];
                log_message('alert', 'uriQueryArr: "{uriQueryArr}" | user: "{user}"', $log_data);
                trigger_error(json_encode(["msg"=>"User {$uriQueryArr[1]} not found!","eCode"=>4001]), E_USER_WARNING);
            }

            $user['user_type'] = 'admin';
        } else {
            $token = $OauthTokenModel->where('access_token', $uriQueryArr[1])->first();
            
            if (empty($token)) {
                // logging
                $log_data = [
                    'token' => empty($token),
                    'uriQueryArr' => $uriQueryArr[1],
                ];
                log_message('alert', 'uriQueryArr: "{uriQueryArr}" | token: "{token}"', $log_data);
                trigger_error(json_encode(["msg"=>"{$uriQueryArr[1]} is not a valid token!","eCode"=>4002]), E_USER_WARNING);
            }
            $modelAppUser = new AppUserModel;
            $modelProfiles = new ProfilesModel;
            $modelProfile_rels = new Profile_relsModel;
            $modelProf_act_rels = new Prof_act_relsModel;
            $modelProfilesPosts = new ProfilesPostsModel;
            $modelProfilesPostsComments = new ProfilesPostsComments;
            $modelProfileBPin = new ProfileBPinModel;
            $AppUsers = $modelAppUser->findAll();
            $allComments = $modelProfilesPostsComments->findAll();

            $app_user = $modelAppUser->find($token['user_id']);
            $profile = $modelProfiles->getByAppUserId($token['user_id']);
            // $friends = $modelProfile_rels->select(['app_user_id','profile_rel_status'])->getWhere( 'profile_id', $profile['profile_id'] );
            $friends = $modelProfile_rels->where( ['profile_id' => $profile['profile_id'], 'profile_rel_status' => 'friends'] )->select(['app_user_id'])->findAll();
            foreach ($friends as $friend_key => $friend) {
                foreach ($AppUsers as $app_user_friend) {
                    if ( $friend['app_user_id'] == $app_user_friend['app_user_id'] ) {
                        $friends[$friend_key]['app_user_name'] = $app_user_friend['app_user_name'];
                        $friends[$friend_key]['profile'] = $modelProfiles->getByAppUserId($app_user_friend['app_user_id']);
                        $act_rels = $modelProf_act_rels->where( ['profile_id' => $profile['profile_id']] )->select(['activity_id'])->findAll();
                        $friends[$friend_key]['activities'] = [];
                        foreach ($act_rels as $act_rel) $friends[$friend_key]['activities'][] = $act_rel['activity_id'];
                        $friend_profile['posts'] = $friends[$friend_key]['posts'] = $modelProfilesPosts->getByProfileId($friends[$friend_key]['profile']['profile_id']);
                        foreach ($friend_profile['posts'] as $key => $friend_profile_post) {
                            foreach ($allComments as $comment) {
                                if ($friend_profile_post['pp_id'] == $comment['pp_id']) {
                                    $friends[$friend_key]['posts'][$key]['comments'][] = $comment;
                                }
                            }
                        }
                    }
                }
            }
            $profile['friends'] = $friends;

            $act_rels = $modelProf_act_rels->where( ['profile_id' => $profile['profile_id']] )->select(['activity_id'])->findAll();
            $profile['activities'] = [];
            foreach ($act_rels as $act_rel) $profile['activities'][] = $act_rel['activity_id'];

            $profile['posts'] = $modelProfilesPosts->getByProfileId($profile['profile_id']);
            foreach ($profile['posts'] as $key => $post) {
                foreach ($allComments as $comment) {
                    if ($post['pp_id'] == $comment['pp_id']) {
                        $profile['posts'][$key]['comments'][] = $comment;
                    }
                }
            }

            // Blast event
            $bPin_ev = $modelProfileBPin->where('bPin_ev_author', $app_user['app_user_id'])->first();
            if ($bPin_ev) {
                $nowStamp = Time::now();
                if ($nowStamp > $bPin_ev['bPin_ev_expires_at']) {
                    // If event has expired:
                    // check joiners counter and if beats - update profile event record
                    if ($bPin_ev['bPin_ev_joiners'] !== NULL) {
                        $bPin_ev['bPin_ev_joiners'] = json_decode($bPin_ev['bPin_ev_joiners']);
                        $candidate_blast_record = count($bPin_ev['bPin_ev_joiners']);
                        if ($profile['profile_blast_record'] < $candidate_blast_record) {
                            $modelProfiles->update($profile['profile_id'], ['profile_blast_record'=>$candidate_blast_record]);
                        }
                    }

                    // delete event
                    $modelProfileBPin->delete($bPin_ev['bPin_ev_id']);

                    $profile['bPin_ev'] = NULL;
                } else {
                    if ($bPin_ev['bPin_ev_joiners'] !== NULL) $bPin_ev['bPin_ev_joiners'] = json_decode($bPin_ev['bPin_ev_joiners']);
                    $bPin_ev['bPin_ev_expires_at'] = Time::parse($bPin_ev['bPin_ev_expires_at'])->timestamp; // Converting active event expiration datetime to timestamp
                }
            }
            $profile['bPin_ev'] = $bPin_ev;

            $user = $app_user;
            $user['user_id'] = $app_user['app_user_id'];
            $user['nice_name'] = $app_user['app_user_name'];
            $user['user_type'] = 'app_user';
            $user['profile'] = $profile;

            // logging users enter chat - to be removed
            $log_data = [
                'uriQuery' => $uriQuery,
                'uriQueryArr' => $uriQueryArr[1],
                'app_user' => json_encode($app_user)
            ];
            log_message('warning', 'source: Chat onOpen,app_user: "{app_user}" ,token: "{uriQueryArr}"', $log_data);
        }

        $conn->user = $user;
        $this->clients->attach($conn);
        
        // Store the new connection to send messages to later
        $conModel->where('c_user_id', $user['user_id'])->delete();
        $conData = [
                'c_user_id' => $user['user_id'],
                'c_resource_id' => $conn->resourceId,
                'c_name' => $user['nice_name'],
                'c_user_type' => $user['user_type'],
        ];
        if (isset($user['profile'])) $conData['c_user_profile'] = json_encode($user['profile'], JSON_UNESCAPED_SLASHES);

        $conModel->save($conData);

        $users = $conModel->findAll();
        foreach ($users as $key => $user) $users[$key]['c_user_profile'] = json_decode($user['c_user_profile']);
        $users = ['users' => $users];

        // Send all users info to all users
        foreach ($this->clients as $client) $client->send(json_encode($users, JSON_UNESCAPED_SLASHES));
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $db = db_connect();
        $db->reconnect();
                
        $msgObj = json_decode($msg);
        $msgTimeSent = $msgObj->msg_timestamp_sent;
        $msg_type = '';
        $softError = false;
        if ( isset($msgObj->msg) ) {
            $msg_type = 'msg';
            $ChatHistoryModel = new ChatHistoryModel();
            $data = [
                'author_id' => $from->user['user_id'],
                'author_name' => $from->user['nice_name'],
                'author_type' => $from->user['user_type'],
                'message' => $msgObj->msg,
                'msg_time_sent' => $msgTimeSent,
                'msg_timestamp_sent' => $msgTimeSent,
                'msg_reciever_id' => $msgObj->msg_reciever_id
            ];
            if ( isset($msgObj->friendship_request) ) $data['request_status'] = $msgObj->friendship_request;

            $ChatHistoryModel->save($data);
        } else if ( isset($msgObj->blast_msg) ) {
            $msg_type = 'blast_msg';
            $data['blast_author_user_id'] = $from->user['user_id'];
            $data['blast_msg'] = $msgObj->blast_msg;
            $data['blast_activities'] = $msgObj->blast_activities;
            $data['msg_timestamp_sent'] = $msgTimeSent;
        } else if ( isset($msgObj->bPin_start) ) {
            $msg_type = 'bPin_start';
            $modelProfileBPin = new ProfileBPinModel();
            $bPin_expires_at = Time::createFromTimestamp($msgObj->bPin_expires_at); // Converting timestamp to datetime obj

            $data = [
                'bPin_ev_author' => $from->user['user_id'],
                'bPin_msg' => $msgObj->bPin_msg,
                'bPin_cors' => $msgObj->bPin_cors,
                'bPin_ev_expires_at' => $bPin_expires_at,
            ];

            $bPin_ev = $modelProfileBPin->where('bPin_ev_author', $data['bPin_ev_author'])->first(); // Try to get active event
            if ($bPin_ev) {
                $nowStamp = Time::now();
                if ($nowStamp > $bPin_ev['bPin_ev_expires_at']) {
                    // If event has expired:
                    // check joiners counter and if beats - update profile event record
                    if ($bPin_ev['bPin_ev_joiners'] !== NULL) {
                        $modelProfiles = new ProfilesModel();
                        $candidate_blast_record = count($bPin_ev['bPin_ev_joiners']);
                        $profile = $modelProfiles->getByAppUserId($from->user['user_id']);
                        if ($profile['profile_blast_record'] < $candidate_blast_record) {
                            $modelProfiles->update($profile['profile_id'], ['profile_blast_record'=>$candidate_blast_record]);
                        }
                    }

                    // delete event
                    $modelProfileBPin->delete($bPin_ev['bPin_ev_id']);

                    // Setup new event
                    $data['bPin_ev_id'] = $modelProfileBPin->insert($data);
                    $data['bPin_ev_expires_at'] = $msgObj->bPin_expires_at; // Converting back to timestamp
                } else {
                    // else return softerror:
                    $data['bPin_ev_expires_at'] = $msgObj->bPin_expires_at; // Converting back to timestamp
                    $bPin_ev['bPin_ev_expires_at'] = Time::parse($bPin_ev['bPin_ev_expires_at'])->timestamp; // Converting active event expiration datetime to timestamp
                    $softError = ["error"=>"bPin_ev_author already has an active event.","data"=>$data,"active_event"=>$bPin_ev,"time"=>$nowStamp->timestamp];
                }
            } else {
                // Setup new event as none active found
                $data['bPin_ev_id'] = $modelProfileBPin->insert($data);
            }
            
            $data['msg_timestamp_sent'] = $msgTimeSent;
        } else if ( isset($msgObj->bPin_join) ) {
            $msg_type = 'bPin_join';
            $modelProfileBPin = new ProfileBPinModel();
            
            $data['bPin_ev_id'] = $msgObj->bPin_ev_id;
            $data['bPin_ev_joiner'] = $from->user['user_id'];

            $bPin_ev = $modelProfileBPin->find($data['bPin_ev_id']); // Get active event
            if ($bPin_ev['bPin_ev_joiners'] == NULL) {
                $bPin_ev['bPin_ev_joiners'] = json_encode([ $data['bPin_ev_joiner'] ]);
            } else {
                $joiners_arr = json_decode($bPin_ev['bPin_ev_joiners']);
                array_push( $joiners_arr, $data['bPin_ev_joiner'] );
                $bPin_ev['bPin_ev_joiners'] = json_encode($joiners_arr);
            }
            $modelProfileBPin->save($bPin_ev);
            $data['bPin_ev_author'] = $bPin_ev['bPin_ev_author'];

            $data['msg_timestamp_sent'] = $msgTimeSent;
        } else if ( isset($msgObj->my_cur_loc) ) {
            $msg_type = 'my_cur_loc';
            $data['c_sender_user_id'] = $from->user['user_id'];
            $data['my_cur_loc'] = $msgObj->my_cur_loc;
            $data['msg_timestamp_sent'] = $msgTimeSent;

            $conModel = new ConnectionsModel();
            $con_sender = $conModel->where('c_user_id', $data['c_sender_user_id'])->first();
            // // logging users enter chat - to be removed
            // $log_data = [
            //     'con_sender' => $con_sender['c_id']
            // ];
            // log_message('alert', 'source: Chat onOpen, con_sender: "{con_sender}"', $log_data);
            $conModel->update($con_sender['c_id'], ['my_cur_loc'=>$data['my_cur_loc']]);
        } else if ( isset($msgObj->pp_content) ) {
            $modelProfiles = new ProfilesModel;
            $modelProfilesPosts = new ProfilesPostsModel;
            $modelProfilesPostsRels = new ProfilesPostsRelsModel;
            $msg_type = 'pp_content';
            $data['c_sender_user_id'] = $from->user['user_id'];
            $data['pp_content'] = $msgObj->pp_content;
            if ( isset($msgObj->type) ) $data['pp_type'] = $msgObj->type;
            $data['msg_timestamp_sent'] = $msgTimeSent;

            if ($msgObj->post_action === 'addPost') {
                $profile = $modelProfiles->getByAppUserId($from->user['user_id']);

                $pp_id = $modelProfilesPosts->insert($data);
                if ($pp_id) {
                    $data['pp_id'] = $pp_id;
                    $modelProfilesPostsRels->insert(['profile_id'=>$profile['profile_id'],"pp_id"=>$pp_id]);
                } else {
                    $softError = ["error"=>"Error while inserting post.","data"=>$data];
                }
            } elseif ( $msgObj->post_action === 'updatePost' && isset($msgObj->type) ) {
                if ($msgObj->type !== 'record') {
                    $modelProfilesPosts->update($msgObj->pp_id, $data);
                    $data['pp_id'] = $msgObj->pp_id;
                } else {
                    $softError = ["error"=>"Records cannot be updated","data"=>$data];
                }
            } elseif ( $msgObj->post_action === 'incLike' && isset($msgObj->type) ) {
                if ($msgObj->type !== 'record') {
                    $pp_comment = $modelProfilesPosts->find($msgObj->pp_id);
                    $update = $modelProfilesPosts->update($msgObj->pp_id, ['pp_likes'=>++$pp_comment['pp_likes']]);
                    $data['pp_comment'] = $pp_comment;
                    $data['pp_commentpp_likes'] = $pp_comment['pp_likes'];
                    $data['update'] = $update;
                } else {
                    $softError = ["error"=>"Records cannot be updated","data"=>$data];
                }
            }
            $data['post_action'] = $msgObj->post_action;
        } else if ( isset($msgObj->ppc_content) ) {
            $modelProfilesPosts = new ProfilesPostsModel;
            $modelProfilesPostsComments = new ProfilesPostsComments;
            $msg_type = 'ppc_content';
            $data['c_sender_user_id'] = $from->user['user_id'];
            $data['ppc_content'] = $msgObj->ppc_content;
            $data['msg_timestamp_sent'] = $msgTimeSent;

            $profilePosts = $modelProfilesPosts->findAll();
            if ( in_array($msgObj->pp_id, array_column($profilePosts, 'pp_id')) ) {
                if ($msgObj->comment_action === 'addComment') {
                    $data['pp_id'] = $msgObj->pp_id;
                    $ppc_id = $modelProfilesPostsComments->insert($data);
                    if ($ppc_id) {
                        $data['ppc_id'] = $ppc_id;
                    } else {
                        $softError = ["error"=>"Error while inserting comment.","data"=>$data];
                    }
                } elseif ( $msgObj->comment_action === 'updateComment' ) {
                    $data['ppc_id'] = $msgObj->ppc_id;
                    $modelProfilesPostsComments->update($data['ppc_id'], $data);
                }
            } else {
                $softError = ["error"=>"Profile post with id {$msgObj->pp_id} not found","data"=>$data];
            }
        } else if ( isset($msgObj->blob) ) {
            $msg_type = 'blob';
            helper(['filesystem', 'array']);
            $directory_map = directory_map('./uploads/drivers/chat_files/');
            if (!isset($directory_map['app_user_'.$msgObj->msg_reciever_id.'\\'])) {
                mkdir('./uploads/drivers/chat_files/app_user_'.$msgObj->msg_reciever_id, 0777, true);
            }
            $path = 'uploads/drivers/chat_files/app_user_'.$msgObj->msg_reciever_id.'/'.$msgObj->name;
            $map = directory_map('./uploads/drivers/chat_files/app_user_'.$msgObj->msg_reciever_id.'/');
            $file_name = $msgObj->name;
            $file_name_arr = explode('.', $file_name);
            $file_name_part = $file_name_arr[0] . '_';
            if (is_file($path)) {
                $matches = array_filter($map,
                    function($item) use ($file_name_part) {
                        return (levenshtein($item,$file_name_part,1,1,0) == 0);
                    }
                );
                foreach ($matches as $key => $match) {
                    $match = explode('.',$match)[0];
                    $match_arr = explode('_', $match);
                    $matchess[] = $match_arr[1];
                }
                asort($matchess);
                $file_name = $file_name_part . (end($matchess)+1) . '.' . $file_name_arr[1];
            }
            $path = 'uploads/drivers/chat_files/app_user_'.$msgObj->msg_reciever_id.'/'.$file_name;
            write_file('./'. $path, utf8_decode($msgObj->blob));
            
            $ChatHistoryModel = new ChatHistoryModel();
    
            $data = [
                'author_id' => $from->user['user_id'],
                'author_name' => $from->user['nice_name'],
                'author_type' => $from->user['user_type'],
                'message' =>  $path,
                'msg_time_sent' => $msgTimeSent,
                'msg_timestamp_sent' => $msgTimeSent,
                'msg_reciever_id' => $msgObj->msg_reciever_id
            ];
            if ( $msgObj->msg_type ) $data['msg_type'] = $msgObj->msg_type;
            $ChatHistoryModel->save($data);
        }

        foreach ($this->clients as $client) {
            // The sender is not the receiver, send to each client connected
            if ($from !== $client && !$softError) {
                $data['user'] = $client->user;
                $data['msg_timestamp_sent'] = $msgTimeSent;

                if ($from->user['user_type'] === 'app_user') {
                    switch ($msg_type) {
                        case 'msg':
                            if ($msgObj->msg_reciever_id === $client->user['user_id']) $client->send(json_encode($data));
                            break;
                        case 'my_cur_loc':
                        case 'pp_content':
                        case 'ppc_content':
                        case 'blast_msg':
                        case 'bPin_start':
                            unset($data['user']);
                            $client->send(json_encode($data));
                            break;
                        case 'bPin_join':
                            unset($data['user']);
                            if ($data['bPin_ev_author'] === $client->user['user_id']) {
                                $client->send(json_encode($data));
                            }
                            break;
                        default:
                            if ($msgObj->msg_reciever_id === $client->user['user_id']) $client->send(json_encode($data));
                            break;
                    }
                } else {
                    if ($msgObj->msg_reciever_id === $client->user['user_id']) {
                        $client->send(json_encode($data));
                    }
                }
            } elseif ($from == $client) {
                if ($softError) {
                    $client->send(json_encode($softError));
                } else {
                    switch ($msg_type) {
                        case 'pp_content':
                        case 'ppc_content':
                            unset($data['user']);
                            $client->send(json_encode($data));
                            break;
                        case 'bPin_start':
                            unset($data['user']);
                            $data['bPin_event'] = 'started';
                            $client->send(json_encode($data));
                        default:
                            break;
                    }
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $db = db_connect();
        $db->reconnect();
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        $conModel = new ConnectionsModel();
        $conModel->where('c_resource_id', $conn->resourceId)->delete();
        $users = $conModel->findAll();
        foreach ($users as $key => $user) $users[$key]['c_user_profile'] = json_decode($user['c_user_profile']);
        $users = ['users' => $users];
        foreach ($this->clients as $client) {
            $client->send(json_encode($users));
        }

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        // logging
        $log_data = [
            'error' => $e->getTraceAsString(),
            'conn' => json_encode($conn),
            'eMessage' => $e->getMessage(),
            'ExceptionCode' => $e->getCode(),
            'getConnections' => json_encode(\CodeIgniter\Database\Config::getConnections()),
            'userModel' => json_encode($this->userModel),
        ];
        log_message('critical', "\n1. eMessage: \"{eMessage}\"\n2 ExceptionCode: {ExceptionCode}\n3. conn: {conn}\n4. Trace:\n{error}\ngetConnections: {getConnections}\nuserModel: {userModel}", $log_data);

        $eMessage = json_decode($log_data['eMessage']);
        if ($eMessage !== null) {
            $error = array( $eMessage->msg, 'code' => $eMessage->eCode );
            $conn->send( json_encode( [ 'error' => $error ] ) );
            $conn->close($eMessage->eCode);
        } else {
            $conn->close();
        }
    }
}