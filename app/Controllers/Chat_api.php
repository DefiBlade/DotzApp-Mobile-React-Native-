<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use \App\Libraries\Oauth;
use \OAuth2\Request;
use App\Models\AppUserModel;
use App\Models\AppUserRelsModel;
use App\Models\ProfilesModel;
use App\Models\Profile_relsModel;
use App\Models\Prof_act_relsModel;
use App\Models\ActivitiesModel;
use App\Models\ProfilesPostsModel;
use App\Models\ProfilesPostsComments;

class Chat_api extends ResourceController
{
	protected $modelName = 'App\Models\ChatHistoryModel';
	protected $format = 'json';

	public function getChatHistory($app_user = null)
	{
		$oauth = new Oauth();
		$request = Request::createFromGlobals();
		$modelAppUser = new AppUserModel;
		$modelProfiles = new ProfilesModel;
		$modelProfile_rels = new Profile_relsModel;
		$modelProf_act_rels = new Prof_act_relsModel;
		$modelProfilesPosts = new ProfilesPostsModel;
		$modelProfilesPostsComments = new ProfilesPostsComments;

		$AppUsers = $modelAppUser->findAll();
		$allComments = $modelProfilesPostsComments->findAll();

		$app_user = $oauth->server->getAccessTokenData($request);

        $app_user_history = $this->model->where('author_id', $app_user['user_id'])->orWhere('msg_reciever_id', $app_user['user_id'])->findAll();

		$reciever_ids = [];
		$recievers = [];
		foreach ($app_user_history as $history_key => $msg_data) {
			if( !in_array($msg_data['msg_reciever_id'], $reciever_ids, true) && $msg_data['msg_reciever_id'] != $app_user['user_id'] ) array_push($reciever_ids, $msg_data['msg_reciever_id']);
			
			if( !in_array($msg_data['author_id'], $reciever_ids, true) && $msg_data['author_id'] != $app_user['user_id'] ) array_push($reciever_ids, $msg_data['author_id']);
		}

		foreach ($reciever_ids as $rec_key => $reciever) {
			$reciever_app_user = $modelAppUser->select(['app_user_id', 'app_user_name'])->find($reciever);
			$recievers[$rec_key] = $reciever_app_user;
			if (!$reciever_app_user) {
				$recievers[$rec_key]['app_user_id'] = $reciever;
				$recievers[$rec_key]['app_user_status'] = 'Does not exist or deleted.';
			}
			$profile = $modelProfiles->getByAppUserId($reciever);
			
			if ($profile) {
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
	
				$recievers[$rec_key]['profile'] = $profile;
			}
		}

		return $this->respond([$app_user_history, $recievers]);
	}
}