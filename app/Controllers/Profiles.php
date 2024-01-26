<?php namespace App\Controllers;

use App\Models\AppUserModel;
use App\Models\AppUserRelsModel;
use App\Models\ProfilesModel;
use App\Models\Profile_relsModel;
use App\Models\Prof_act_relsModel;
use App\Models\ActivitiesModel;
use App\Models\LocationsModel;
use App\Models\ClubsModel;
use \App\Libraries\Oauth;
use \OAuth2\Request;
use App\Models\MediaModel;
use App\Models\MediaCommentModel;
use App\Models\UserMediaModel;
use App\Models\MediaVoteModel;
use App\Models\ClubInvitationsModel;
use App\Models\NotificationModel;

class Profiles extends BaseController
{
    public function index()
    {
		$modelAppUser = new AppUserModel;
		$modelAppUserRels = new AppUserRelsModel;
		$modelProfiles = new ProfilesModel;
		$modelProfile_rels = new Profile_relsModel;
		$modelProf_act_rels = new Prof_act_relsModel;
		$modelActivities = new ActivitiesModel;

		$data['page'] = 'profiles';
		$data['isAdmin'] = session()->get('role') === 'ADMIN' || session()->get('role') === 'SUPERADMIN' ? true : false;
		if (session()->get('role') === 'SUPERADMIN') $data['isSAdmin'] =  true;

		$AppUsers = $modelAppUser->findAll();
		$AppUserRels = $modelAppUserRels->findAll();
		$Profiles = $modelProfiles->findAll();
		$Activities = $modelActivities->findAll();

		foreach ($AppUsers as $appUser_key => $appUser) {
			$data['profiles'][] = $AppUsers[$appUser_key];
			foreach ($AppUserRels as $appUserRel_key => $appUserRel) {
				if ($appUser['app_user_id'] === $appUserRel['app_user_id']) {
					foreach ($Profiles as $profile_key => $profile) {
						if ($appUserRel['profile_id'] ===  $profile['profile_id']) {
							$data['profiles'][$appUser_key] = array_merge( $data['profiles'][$appUser_key], $Profiles[$profile_key] );

							$friends = $modelProfile_rels->where( ['profile_id' => $profile['profile_id']] )->findAll();
							foreach ($friends as $friend_key => $friend) {
								foreach ($AppUsers as $app_user_friend) {
									if ( $friend['app_user_id'] == $app_user_friend['app_user_id'] ) {
										$friends[$friend_key]['app_user_name'] = $app_user_friend['app_user_name'];
									}
								}
							}
							$data['profiles'][$appUser_key]['friends'] = $friends;

							$act_rels = $modelProf_act_rels->where( ['profile_id' => $profile['profile_id']] )->findAll();
							$data['profiles'][$appUser_key]['activities'] = [];
							foreach ($act_rels as $act_rel_key => $act_rel) {
								foreach ($Activities as $activity) {
									if ( $act_rel['activity_id'] == $activity['activity_id'] ) {
										$activity_data_arr = [
											'activity_name' => $activity['activity_name'],
											'activity_img' => $activity['activity_img'],
										];
										$data['profiles'][$appUser_key]['activities'][] = $activity_data_arr;
									}
								}
							}
						}
					}
				}
			}
		}

		echo view('templates/header', $data);
		echo view('pages/profiles');
		echo view('templates/footer');
    }

	public function verify()
	{
		if ($this->request->isAJAX()) {
			$modelProfiles = new ProfilesModel;
			$data = $this->request->getPost();
			$modelProfiles->save($data);
		}
	}

	public function current_activity()
	{
		$oauth = new Oauth();
		$request = new Request();
		$modelAppUserRels = new AppUserRelsModel;
		$modelProfiles = new ProfilesModel;
		$modelActivities = new ActivitiesModel;

		$app_user = $oauth->server->getAccessTokenData($request);
		$appUserRel = $modelAppUserRels->where(['app_user_id'=>$app_user['user_id']])->find();
		if (!$appUserRel && empty($appUserRel)) return $this->response->setStatusCode(400)->setJSON(["error"=>"wrong or empty id."]);

		$profile_current_act = $this->request->getVar('profile_current_act');
		if (!$profile_current_act) return $this->response->setStatusCode(400)->setJSON(["error"=>"profile_current_act not provided."]);
		// $activities = $modelActivities->findAll();
		// if (!array_search($profile_current_act, array_column($activities, 'activity_id'), true))
		// 	return $this->response->setStatusCode(400)->setJSON(["error"=>"Such activity not found.", 'profile_current_act'=>$profile_current_act, 'activities'=>$activities]);

		// return $this->response->setStatusCode(202)->setJSON(['success'=>$profile_current_act]);
		if ( $modelProfiles->update($appUserRel[0]['profile_id'], ['profile_current_act'=>$profile_current_act]) ) {
			return $this->response->setStatusCode(202)->setJSON(['success'=>'Profile current activity updated.']);
		} else {
			return $this->response->setStatusCode(400)->setJSON(["error"=>"DataBase failed to update."]);
		}
	}

	public function favourite_add()
	{
		$oauth = new Oauth();
		$request = new Request();
		$modelAppUserRels = new AppUserRelsModel;
		$modelProfiles = new ProfilesModel;

		$app_user = $oauth->server->getAccessTokenData($request);
		$appUserRel = $modelAppUserRels->where(['app_user_id'=>$app_user['user_id']])->find();
		if (!$appUserRel && empty($appUserRel)) return $this->response->setStatusCode(400)->setJSON(["error"=>"wrong or empty id."]);
		
		$favourite_locs = json_decode($this->request->getVar('favourite_locs'));
		if (!$favourite_locs) return $this->response->setStatusCode(400)->setJSON(["error"=>"favourite_locs not provided."]);

		$modelProfiles->update($appUserRel[0]['profile_id'], ['profile_favourite_locs'=>json_encode($favourite_locs)]);
		return $this->response->setStatusCode(202)->setJSON(['success'=>'Profile favourite locations updated.']);
	}

	public function update_miles()
	{
		$oauth = new Oauth();
		$request = new Request();
		$modelAppUserRels = new AppUserRelsModel;
		$modelProfiles = new ProfilesModel;

		$app_user = $oauth->server->getAccessTokenData($request);
		$appUserRel = $modelAppUserRels->where(['app_user_id'=>$app_user['user_id']])->find();
		if (!$appUserRel && empty($appUserRel)) return $this->response->setStatusCode(400)->setJSON(["error"=>"wrong or empty id."]);

		$profile_miles = intval($this->request->getVar('profile_miles'));
		if (!$profile_miles) return $this->response->setStatusCode(400)->setJSON(["error"=>"profile_miles not provided."]);

		$profile = $modelProfiles->find($appUserRel[0]['profile_id']);

		if ($profile['profile_miles'] === NULL) $profile['profile_miles'] = 0;

		if ( $modelProfiles->update($appUserRel[0]['profile_id'], ['profile_miles' => ($profile['profile_miles'] + $profile_miles)]) ) {
			return $this->response->setStatusCode(202)->setJSON(['success'=>'Profile profile_miles updated.', 'profile_miles' => ($profile['profile_miles'] + $profile_miles)]);
		} else {
			return $this->response->setStatusCode(400)->setJSON(["error"=>"DataBase failed to update."]);
		}
	}

	public function profile_rideout()
	{
		$oauth = new Oauth();
		$request = new Request();
		$modelAppUserRels = new AppUserRelsModel;
		$modelProfiles = new ProfilesModel;

		$app_user = $oauth->server->getAccessTokenData($request);
		$appUserRel = $modelAppUserRels->where(['app_user_id'=>$app_user['user_id']])->find();
		if (!$appUserRel && empty($appUserRel)) return $this->response->setStatusCode(400)->setJSON(["error"=>"wrong or empty id."]);

		$profile_rideout = intval($this->request->getVar('profile_rideout'));
		if (!$profile_rideout) return $this->response->setStatusCode(400)->setJSON(["error"=>"profile_rideout not provided."]);

		if ( $modelProfiles->update($appUserRel[0]['profile_id'], ['profile_rideout' => $profile_rideout]) ) {
			return $this->response->setStatusCode(202)->setJSON(['success'=>'Profile profile_rideout updated.', 'profile_rideout' => $profile_rideout]);
		} else {
			return $this->response->setStatusCode(400)->setJSON(["error"=>"DataBase failed to update."]);
		}
	}

	public function profile_club()
	{
		$oauth = new Oauth();
		$request = new Request();
		$modelAppUserRels = new AppUserRelsModel;
		$modelProfiles = new ProfilesModel;
		$modelClubs = new ClubsModel;

		$app_user = $oauth->server->getAccessTokenData($request);
		$appUserRel = $modelAppUserRels->where(['app_user_id'=>$app_user['user_id']])->find();
		if (!$appUserRel && empty($appUserRel)) return $this->response->setStatusCode(400)->setJSON(["error"=>"wrong or empty id."]);

		$club_id = intval($this->request->getVar('club_id'));
		if (!$club_id) return $this->response->setStatusCode(400)->setJSON(["error"=>"club_id not provided."]);
		$club = $modelClubs->find($club_id);
		if (!$club) return $this->response->setStatusCode(400)->setJSON(["error"=>"No such club_id found."]);

		if ( $modelProfiles->update($appUserRel[0]['profile_id'], ['profile_club' => $club_id]) ) {
			return $this->response->setStatusCode(202)->setJSON(['success'=>'Profile profile_club updated.', 'profile_club' => $club_id]);
		} else {
			return $this->response->setStatusCode(400)->setJSON(["error"=>"DataBase failed to update."]);
		}
	}

	public function profile_update()
	{
		$oauth = new Oauth();
		$request = new Request();
		$modelAppUser = new AppUserModel;
		$modelAppUserRels = new AppUserRelsModel;
		$modelProfiles = new ProfilesModel;
		$modelProf_act_rels = new Prof_act_relsModel;
		$modelLocations = new LocationsModel;
		$modelActivities = new ActivitiesModel;
		$success_data = [];

		$app_user_token_data = $oauth->server->getAccessTokenData($request);
		$app_user = $modelAppUser->find($app_user_token_data['user_id']);
		$username = $this->request->getVar('username');
		if ($app_user === NULL) {
			if ($app_user = $modelAppUser->where('email', $username)->select(['app_user_id','email','app_user_name','scope','deleted_at'])->withDeleted()->find()) {
				return $this->response->setStatusCode(400)->setJSON(["error"=>"app_user is deleted",'app_user'=>$app_user]);
			} else return $this->response->setStatusCode(400)->setJSON(["error"=>"app_user returns null",'app_user'=>$app_user]);
		}
		if (!$app_user && empty($app_user)) return $this->response->setStatusCode(400)->setJSON(["error"=>"app_user not found or empty.",'app_user'=>$app_user]);

		$appUserRel = $modelAppUserRels->where(['app_user_id'=>$app_user['app_user_id']])->find();
		if (!$appUserRel && empty($appUserRel)) return $this->response->setStatusCode(400)->setJSON(["error"=>"wrong or empty id."]);
		$profile_id = $appUserRel[0]['profile_id'];

		$update_data = $this->request->getPost();

		if( $app_user['app_user_name'] != $update_data['app_user_name'] && $modelAppUser->where(['app_user_name' => $update_data['app_user_name']])->first() ) {
			return $this->response->setStatusCode(202)->setJSON(["error"=>"You have entered a duplicate Username!"]);
		}

		// return $this->response->setStatusCode(400)->setJSON(["error"=>$update_data]);
		if (isset($update_data['app_user_name'])) {
			$app_user_name_update = $modelAppUser->update($app_user['app_user_id'],['app_user_name'=>$update_data['app_user_name']]);
			if (!$app_user_name_update) return $this->response->setStatusCode(400)->setJSON(["error"=>"app_user_name update failed. Other info not updated"]);
			else $success_data['app_user_name'] = $update_data['app_user_name'];
		}

		if( isset($update_data['bio']) ) {
			$bio_update = $modelAppUser->update($app_user['app_user_id'], ['bio' => $update_data['bio']]);
			$success_data['bio'] = $update_data['bio'];
		}

		if ( isset($update_data['profile_city']) || isset($update_data['profile_privacy_buble']) || isset($update_data['profile_current_act']) ) {
			if(isset($update_data['profile_city'])) $data['profile_city'] = $update_data['profile_city'];
			if(isset($update_data['profile_privacy_buble'])) $data['profile_privacy_buble'] = $update_data['profile_privacy_buble'];
			if(isset($update_data['profile_current_act'])) $data['profile_current_act'] = $update_data['profile_current_act'];
			$profile_update = $modelProfiles->update($profile_id, $data);
			if (!$profile_update) return $this->response->setStatusCode(400)->setJSON(["error"=>"profile update failed. Profile activities also not updated"]);
			else foreach ($data as $key => $value) $success_data[$key] = $value;
		}

		if (isset($update_data['activity_ids'])) {
			$update_Prof_act_rels = [];
			$activity_ids = json_decode($update_data['activity_ids']);

			$modelProf_act_rels->where(['profile_id' => $profile_id])->delete();

			foreach( $activity_ids as $key => $activity_id ) {
				$modelProf_act_rels->insert(['profile_id' => $profile_id, 'activity_id' => $activity_id]);
			}

			$update_Prof_act_rels = $modelProf_act_rels->where(['profile_id' => $profile_id])->findAll();

			foreach ($update_Prof_act_rels as $value) $success_data['activity_ids'][] = $value;
		}

		if (isset($update_data['favourite_locs'])) {
			$favourite_locs = json_decode( $update_data['favourite_locs'] );

			$locs = $modelLocations->where('approved', 1)->findAll();
			foreach ($favourite_locs as $favourite_loc) {
				if (!in_array($favourite_loc, array_column($locs, 'loc_id'))) 
					return $this->response->setStatusCode(400)->setJSON(["error"=>"Such location not found.", 'favourite_loc'=>$favourite_loc, 'locs'=>$locs]);
			}

			if ( !$modelProfiles->update($appUserRel[0]['profile_id'], ['profile_favourite_locs'=>json_encode($favourite_locs)]) )
				return $this->response->setStatusCode(400)->setJSON(["error"=>"DataBase failed to update."]);
			else $success_data['profile_favourite_locs'] = $favourite_locs;
		}

		if (isset($update_data['update_ava'])) {
			// Check File
			if (!empty($_FILES['profile_img_ava']['name'])) {
				$img = $this->request->getFile('profile_img_ava');
				if ($img->isValid() && ! $img->hasMoved()) {
					$img->move('./uploads/profiles',$img->getClientName());
				}
			} else {
				$img = null;
			}

			if ( !$modelProfiles->update($appUserRel[0]['profile_id'], ['profile_img_ava'=>$img->getClientName()]) )
				return $this->response->setStatusCode(400)->setJSON(["error"=>"DataBase failed to update."]);
			else $success_data['profile_img_ava'] = $img->getClientName();
		}

		if (isset($update_data['blast_record'])) {
			$candidate_blast_record = $update_data['blast_record'];
			$profile = $modelProfiles->find($appUserRel[0]['profile_id']);
			if ($profile['profile_blast_record'] < $candidate_blast_record) {
				if ( !$modelProfiles->update($appUserRel[0]['profile_id'], ['profile_blast_record'=>$candidate_blast_record]) )
					return $this->response->setStatusCode(400)->setJSON(["error"=>"DataBase failed to update."]);
				else $success_data['profile_blast_record'] = $candidate_blast_record;
			} else $success_data['profile_blast_record'] = $profile['profile_blast_record'];
		}

		return $this->response->setStatusCode(202)->setJSON(['success' => 'Profile updated.', 'success_data' => $success_data]);
	}

	public function get_feeds() {
		$oauth = new Oauth();
		$request = new Request();
		$modelAppUser = new AppUserModel;
		$modelAppUserRels = new AppUserRelsModel;
		$modelProfiles = new ProfilesModel;
		$modelMedia = new MediaModel;
		$modelMediaComment = new MediaCommentModel;
		$modelMediaVote = new MediaVoteModel;
		$modelUserMedia = new UserMediaModel;

		$postData = $this->request->getPost();

		$app_user_id = $modelAppUserRels->where(['profile_id' => $postData['profile_id']])->first()['app_user_id'];

		$userMedias = $modelUserMedia->where([ 'app_user_id' => $app_user_id ])->orderBy('created_at', 'desc')->findAll();

		foreach( $userMedias as $key => $userMedia ) {
			$userMedias[$key]['info'] = $modelMedia->find( $userMedia['media_id'] );
			$userMedias[$key]['comment'] = $modelMediaComment->where([ 'user_media_id' => $userMedia['id'], 'parent_id' => '0' ])->findAll();
			$userMedias[$key]['like_cnt'] = count($modelMediaVote->where(['user_media_id' => $userMedia['id']])->findAll());
		}
		
		return $this->response->setStatusCode(202)->setJSON(['mediaList' => $userMedias]);
	}

	public function add_feed() {
		$oauth = new Oauth();
		$request = new Request();
		$modelAppUser = new AppUserModel;
		$modelAppUserRels = new AppUserRelsModel;
		$modelProfiles = new ProfilesModel;
		$modelMedia = new MediaModel;
		$modelMediaComment = new MediaCommentModel;
		$modelUserMedia = new UserMediaModel;
		$modelMediaVote = new MediaVoteModel;

		$postData = $this->request->getPost();

		// Check File
		if (!empty($_FILES['media']['name'])) {	
			$media = $this->request->getFile('media');
			if ($media->isValid() && ! $media->hasMoved()) {
				$media->move('./uploads/feeds',$media->getClientName());
			}
		} else {
			$media = null;
		}
		if( $media != null ) {
			// Add to database
			$duration = isset($postData['duration']) ? $postData['duration'] : 0;
			$type = $duration > 0 ? 'Video': 'Image';

			$newMedia_id = $modelMedia->insert([ 'url' => $media->getClientName(), 'type' => $type, 'duration' => $duration ]);

			$app_user_id = $modelAppUserRels->where(['profile_id' => $postData['profile_id']])->first()['app_user_id'];

			$modelUserMedia->insert(['app_user_id' => $app_user_id, 'media_id' => $newMedia_id]);
			$userMedias = $modelUserMedia->where([ 'app_user_id' => $app_user_id ])->orderBy('created_at', 'desc')->findAll();

			foreach( $userMedias as $key => $userMedia ) {
				$userMedias[$key]['info'] = $modelMedia->find( $userMedia['media_id'] );
				$userMedias[$key]['comment'] = $modelMediaComment->where([ 'user_media_id' => $userMedia['id'], 'parent_id' => '0' ])->findAll();
				$userMedias[$key]['like_cnt'] = count($modelMediaVote->where(['user_media_id' => $userMedia['id']])->findAll());
			}
			return $this->response->setStatusCode(202)->setJSON(['mediaList' => $userMedias]);
		}

		return $this->response->setStatusCode(400)->setJSON(["error"=>"Something went wrong."]);
	}

	public function delete_feed() {
		$oauth = new Oauth();
		$request = new Request();
		$modelAppUser = new AppUserModel;
		$modelAppUserRels = new AppUserRelsModel;
		$modelProfiles = new ProfilesModel;
		$modelMedia = new MediaModel;
		$modelMediaComment = new MediaCommentModel;
		$modelUserMedia = new UserMediaModel;
		$modelMediaVote = new MediaVoteModel;

		$postData = $this->request->getPost();

		$user_media_id = $postData['feed_id'];
		$app_user_id = $modelUserMedia->where(['id' => $user_media_id])->first()['app_user_id'];
		
		// delete action
		$modelUserMedia->where(['id' => $user_media_id])->delete();
		$modelMediaComment->where(['user_media_id' => $user_media_id])->delete();
		$modelMediaVote->where(['user_media_id' => $user_media_id])->delete();

		$userMedias = $modelUserMedia->where([ 'app_user_id' => $app_user_id ])->orderBy('created_at', 'desc')->findAll();

		foreach( $userMedias as $key => $userMedia ) {
			$userMedias[$key]['info'] = $modelMedia->find( $userMedia['media_id'] );
			$userMedias[$key]['comment'] = $modelMediaComment->where([ 'user_media_id' => $userMedia['id'], 'parent_id' => '0' ])->findAll();
			$userMedias[$key]['like_cnt'] = count($modelMediaVote->where(['user_media_id' => $userMedia['id']])->findAll());
		}

		return $this->response->setStatusCode(202)->setJSON(['mediaList' => $userMedias]);
	}

	public function like_feed() {
		$request = new Request();
		$modelUserMedia = new UserMediaModel;
		$modelMediaVote = new MediaVoteModel;

		$postData = $this->request->getPost();
		
		$media_id = $postData['id'];
		$app_user_id = $postData['app_user_id'];
		
		$temp = $modelMediaVote->where(['app_user_id' => $app_user_id, 'user_media_id' => $media_id])->first();

		if( !empty($temp) ) {	// already exists;
			return $this->response->setStatusCode(202)->setJSON(['message' => 'You have already voted this feed.']);
		}

		$modelMediaVote->insert(['app_user_id' => $app_user_id, 'user_media_id' => $media_id]);
		
		$updatedMedia = $modelUserMedia->find($media_id);
		$updatedMedia['like_cnt'] = count( $modelMediaVote->where(['user_media_id' => $media_id])->findAll() );
		return $this->response->setStatusCode(202)->setJSON([ 'updatedMedia' => $updatedMedia ]);
	}

	public function share_feed() {
		$request = new Request();
		$modelUserMedia = new UserMediaModel;
		$modelAppUserRels = new AppUserRelsModel;

		$postData = $this->request->getPost();
		$user_media_id = $postData['id'];
		$profile_id = $postData['profile_id'];
		$app_user_id = $modelAppUserRels->where(['profile_id' => $profile_id])->first()['app_user_id'];

		$user_media = $modelUserMedia->find( $user_media_id );

		$temp = $modelUserMedia->where(['app_user_id' => $app_user_id, 'media_id' => $user_media['media_id']])->first();

		if( !empty($temp) )
			return $this->response->setStatusCode(202)->setJSON(['message' => 'You have already shared this feed.']);

		$modelUserMedia->insert(['app_user_id' => $app_user_id, 'media_id' => $user_media['media_id']]);

		$share_cnt = $modelUserMedia->find($user_media_id)['share_cnt'];
		$share_cnt ++;

		$modelUserMedia->where('id', $user_media_id)
						->set(['share_cnt' => $share_cnt])
						->update();
		
		$updatedMedia = $modelUserMedia->find($user_media_id);
		return $this->response->setStatusCode(202)->setJSON([ 'updatedMedia' => $updatedMedia ]);
	}

	public function comment_feed() {
		$request = new Request();
		$modelMediaComment = new MediaCommentModel;
		$modelAppUser = new AppUserModel;
		$modelAppUserRels = new AppUserRelsModel;
		$modelProfiles = new ProfilesModel;
		$modelProf_act_rels = new Prof_act_relsModel;

		$postData = $this->request->getPost();
		$user_media_id = $postData['id'];

		$commentList = $modelMediaComment->where(['user_media_id' => $user_media_id, 'parent_id' => '0'])->findAll();

		foreach( $commentList as $key => $comment ) {
			$app_user_id = $comment['app_user_id'];
			$profile_id = $modelAppUserRels->where(['app_user_id' => $app_user_id])->first()['profile_id'];
			$posterName = $modelAppUser->find($app_user_id)['app_user_name'];
			$posterAvatar = $modelProfiles->find($profile_id)['profile_img_ava'];

			$commentList[$key]['posterName'] = $posterName;
			$commentList[$key]['posterAvatar'] = $posterAvatar;

			$commentList[$key]['poster'] = [];
			$commentList[$key]['poster']['activities'] = $modelProf_act_rels->where(['profile_id' => $profile_id])->findAll();
			$commentList[$key]['poster']['app_user_id'] = $app_user_id;
			$commentList[$key]['poster']['app_user_name'] = $posterName;
			$commentList[$key]['poster']['bio'] = $modelAppUser->find($app_user_id)['bio'];
			$commentList[$key]['poster']['posts'] = [];
			$commentList[$key]['poster']['profile_rel_status'] = "friends";
			$commentList[$key]['poster']['profile'] = $modelProfiles->find($profile_id);

			$commentList[$key]['replyList'] = $modelMediaComment->where(['parent_id' => $commentList[$key]['id']])->findAll();
			foreach( $commentList[$key]['replyList'] as $idx => $reply ) {
				$app_user_id = $reply['app_user_id'];
				$profile_id = $modelAppUserRels->where(['app_user_id' => $app_user_id])->first()['profile_id'];
				$posterName = $modelAppUser->find($app_user_id)['app_user_name'];
				$posterAvatar = $modelProfiles->find($profile_id)['profile_img_ava'];

				$commentList[$key]['replyList'][$idx]['posterName'] = $posterName;
				$commentList[$key]['replyList'][$idx]['posterAvatar'] = $posterAvatar;

				$commentList[$key]['replyList'][$idx]['poster'] = [];
				$commentList[$key]['replyList'][$idx]['poster']['activities'] = $modelProf_act_rels->where(['profile_id' => $profile_id])->findAll();
				$commentList[$key]['replyList'][$idx]['poster']['app_user_id'] = $app_user_id;
				$commentList[$key]['replyList'][$idx]['poster']['app_user_name'] = $posterName;
				$commentList[$key]['replyList'][$idx]['poster']['bio'] = $modelAppUser->find($app_user_id)['bio'];
				$commentList[$key]['replyList'][$idx]['poster']['posts'] = [];
				$commentList[$key]['replyList'][$idx]['poster']['profile_rel_status'] = "friends";
				$commentList[$key]['replyList'][$idx]['poster']['profile'] = $modelProfiles->find($profile_id);
			}
		}

		return $this->response->setStatusCode(202)->setJSON(['commentList' => $commentList]);
	}

	public function add_comment_feed() {
		$request = new Request();
		$modelMediaComment = new MediaCommentModel;
		$modelAppUserRels = new AppUserRelsModel;

		$postData = $this->request->getPost();
		$user_media_id = $postData['id'];
		$profile_id = $postData['profile_id'];
		$comment = $postData['comment'];
		$parent_id = $postData['parent_id'];
		$app_user_id = $modelAppUserRels->where(['profile_id' => $profile_id])->first()['app_user_id'];

		$temp = $modelMediaComment->where(['app_user_id' => $app_user_id, 'user_media_id' => $user_media_id])->first();

		// if( !empty($temp) )
		// 	return $this->response->setStatusCode(202)->setJSON(['message' => 'Has already commented']);
		
		$modelMediaComment->insert(['app_user_id' => $app_user_id, 'user_media_id' => $user_media_id, 'comment' => $comment, 'parent_id' => $parent_id]);

		$commentList = $modelMediaComment->where(['user_media_id' => $user_media_id, 'parent_id' => '0'])->findAll();
		return $this->response->setStatusCode(202)->setJSON(['success' => true, 'commentList' => $commentList]);
	}

	public function delete_comment_feed() {
		$request = new Request();
		$modelMediaComment = new MediaCommentModel;
		$modelAppUser = new AppUserModel;
		$modelAppUserRels = new AppUserRelsModel;
		$modelProfiles = new ProfilesModel;
		$modelProf_act_rels = new Prof_act_relsModel;

		$postData = $this->request->getPost();
		$comment_id = $postData['comment_id'];

		$user_media_id = $modelMediaComment->where(['id' => $comment_id])->first()['user_media_id'];

		$modelMediaComment->where(['id' => $comment_id])->delete();

		$commentList = $modelMediaComment->where(['user_media_id' => $user_media_id, 'parent_id' => '0'])->findAll();

		foreach( $commentList as $key => $comment ) {
			$app_user_id = $comment['app_user_id'];
			$profile_id = $modelAppUserRels->where(['app_user_id' => $app_user_id])->first()['profile_id'];
			$posterName = $modelAppUser->find($app_user_id)['app_user_name'];
			$posterAvatar = $modelProfiles->find($profile_id)['profile_img_ava'];

			$commentList[$key]['posterName'] = $posterName;
			$commentList[$key]['posterAvatar'] = $posterAvatar;

			$commentList[$key]['poster'] = [];
			$commentList[$key]['poster']['activities'] = $modelProf_act_rels->where(['profile_id' => $profile_id])->findAll();
			$commentList[$key]['poster']['app_user_id'] = $app_user_id;
			$commentList[$key]['poster']['app_user_name'] = $posterName;
			$commentList[$key]['poster']['bio'] = $modelAppUser->find($app_user_id)['bio'];
			$commentList[$key]['poster']['posts'] = [];
			$commentList[$key]['poster']['profile_rel_status'] = "friends";
			$commentList[$key]['poster']['profile'] = $modelProfiles->find($profile_id);

			$commentList[$key]['replyList'] = $modelMediaComment->where(['parent_id' => $commentList[$key]['id']])->findAll();
			foreach( $commentList[$key]['replyList'] as $idx => $reply ) {
				$app_user_id = $reply['app_user_id'];
				$profile_id = $modelAppUserRels->where(['app_user_id' => $app_user_id])->first()['profile_id'];
				$posterName = $modelAppUser->find($app_user_id)['app_user_name'];
				$posterAvatar = $modelProfiles->find($profile_id)['profile_img_ava'];

				$commentList[$key]['replyList'][$idx]['posterName'] = $posterName;
				$commentList[$key]['replyList'][$idx]['posterAvatar'] = $posterAvatar;

				$commentList[$key]['replyList'][$idx]['poster'] = [];
				$commentList[$key]['replyList'][$idx]['poster']['activities'] = $modelProf_act_rels->where(['profile_id' => $profile_id])->findAll();
				$commentList[$key]['replyList'][$idx]['poster']['app_user_id'] = $app_user_id;
				$commentList[$key]['replyList'][$idx]['poster']['app_user_name'] = $posterName;
				$commentList[$key]['replyList'][$idx]['poster']['bio'] = $modelAppUser->find($app_user_id)['bio'];
				$commentList[$key]['replyList'][$idx]['poster']['posts'] = [];
				$commentList[$key]['replyList'][$idx]['poster']['profile_rel_status'] = "friends";
				$commentList[$key]['replyList'][$idx]['poster']['profile'] = $modelProfiles->find($profile_id);
			}
		}
		return $this->response->setStatusCode(202)->setJSON(['commentList' => $commentList]);
	}

	public function reply_comment() {
		$request = new Request();
		$modelMediaComment = new MediaCommentModel;
		$modelAppUser = new AppUserModel;
		$modelAppUserRels = new AppUserRelsModel;
		$modelProfiles = new ProfilesModel;
		$modelNotification = new NotificationModel;
		$modelProf_act_rels = new Prof_act_relsModel;
		
		$postData = $this->request->getPost();
		$parent_id = $postData['parent_id'];
		$comment = $postData['content'];
		$app_user_id = $postData['app_user_id'];
		$user_media_id = $modelMediaComment->find($parent_id)['user_media_id'];

		$modelMediaComment->insert(['app_user_id' => $app_user_id, 'user_media_id' => $user_media_id, 'comment' => $comment, 'parent_id' => $parent_id]);

		$app_user_name = $modelAppUser->find($app_user_id)['app_user_name'];
		$notify_user_id = $modelMediaComment->find($parent_id)['app_user_id'];
		$modelNotification->insert(['app_user_id' => $notify_user_id, 'content' => $app_user_name.' has replied to your comment!', 'read' => '0']);

		$commentList = $modelMediaComment->where(['user_media_id' => $user_media_id, 'parent_id' => '0' ])->findAll();
		
		foreach( $commentList as $key => $comment ) {
			$app_user_id = $comment['app_user_id'];
			$profile_id = $modelAppUserRels->where(['app_user_id' => $app_user_id])->first()['profile_id'];
			$posterName = $modelAppUser->find($app_user_id)['app_user_name'];
			$posterAvatar = $modelProfiles->find($profile_id)['profile_img_ava'];

			$commentList[$key]['posterName'] = $posterName;
			$commentList[$key]['posterAvatar'] = $posterAvatar;

			$commentList[$key]['poster'] = [];
			$commentList[$key]['poster']['activities'] = $modelProf_act_rels->where(['profile_id' => $profile_id])->findAll();
			$commentList[$key]['poster']['app_user_id'] = $app_user_id;
			$commentList[$key]['poster']['app_user_name'] = $posterName;
			$commentList[$key]['poster']['bio'] = $modelAppUser->find($app_user_id)['bio'];
			$commentList[$key]['poster']['posts'] = [];
			$commentList[$key]['poster']['profile_rel_status'] = "friends";
			$commentList[$key]['poster']['profile'] = $modelProfiles->find($profile_id);

			$commentList[$key]['replyList'] = $modelMediaComment->where(['parent_id' => $commentList[$key]['id']])->findAll();
			foreach( $commentList[$key]['replyList'] as $idx => $reply ) {
				$app_user_id = $reply['app_user_id'];
				$profile_id = $modelAppUserRels->where(['app_user_id' => $app_user_id])->first()['profile_id'];
				$posterName = $modelAppUser->find($app_user_id)['app_user_name'];
				$posterAvatar = $modelProfiles->find($profile_id)['profile_img_ava'];

				$commentList[$key]['replyList'][$idx]['posterName'] = $posterName;
				$commentList[$key]['replyList'][$idx]['posterAvatar'] = $posterAvatar;
				
				$commentList[$key]['replyList'][$idx]['poster'] = [];
				$commentList[$key]['replyList'][$idx]['poster']['activities'] = $modelProf_act_rels->where(['profile_id' => $profile_id])->findAll();
				$commentList[$key]['replyList'][$idx]['poster']['app_user_id'] = $app_user_id;
				$commentList[$key]['replyList'][$idx]['poster']['app_user_name'] = $posterName;
				$commentList[$key]['replyList'][$idx]['poster']['bio'] = $modelAppUser->find($app_user_id)['bio'];
				$commentList[$key]['replyList'][$idx]['poster']['posts'] = [];
				$commentList[$key]['replyList'][$idx]['poster']['profile_rel_status'] = "friends";
				$commentList[$key]['replyList'][$idx]['poster']['profile'] = $modelProfiles->find($profile_id);
			}
		}
		return $this->response->setStatusCode(202)->setJSON(['commentList' => $commentList]);
	}

	public function delete_profile() {
		$request = new Request();
		$modelProfiles = new ProfilesModel;
		$modelAppUser = new AppUserModel;
		$modelAppUserRels = new AppUserRelsModel;

		$postData = $this->request->getPost();

		$app_user_id = $postData['app_user_id'];
		$profile_id = $modelAppUserRels->where(['app_user_id' => $app_user_id])->first()['profile_id'];
		$app_users_rel_id = $modelAppUserRels->where(['app_user_id' => $app_user_id])->first()['app_users_rel_id'];

		if( isset($app_users_rel_id) )
			$modelAppUserRels->delete($app_users_rel_id);

		if( isset($app_user_id) )
			$modelAppUser->delete($app_user_id);

		if( isset($profile_id) )
			$modelProfiles->delete($profile_id);

		return redirect()->to('profiles');
	}

	public function get_notification() {
		$oauth = new Oauth();
		$request = new Request();
		$modelClubs = new ClubsModel;
		$modelClubInvitations = new ClubInvitationsModel;
		$modelNotification = new NotificationModel;

		$postData = $this->request->getPost();
		$app_user_id = $postData['app_user_id'];
		$notifications = $modelClubInvitations->where(['app_user_id' => $app_user_id])->findAll();
		foreach( $notifications as $key => $notification ) {
			$notifications[$key]['club_name'] = $modelClubs->where(['club_id' => $notification['club_id']])->first()['club_name'];
			$notifications[$key]['type'] = 'club_invitation';
		}

		$temp = $modelNotification->where(['app_user_id' => $app_user_id, 'read' => '0'])->orderBy('created_at', 'desc')->findAll();
		foreach( $temp as $key => $tmp ) {
			$tmp['type'] = 'notify';
			array_push($notifications, $tmp);
		}

		return $this->response->setStatusCode(202)->setJSON(['notifications' => $notifications]);
	}

	public function close_notification() {
		$oauth = new Oauth();
		$request = new Request();
		$modelClubs = new ClubsModel;
		$modelClubInvitations = new ClubInvitationsModel;
		$modelNotification = new NotificationModel;

		$postData = $this->request->getPost();
		$notification_id = $postData['notification_id'];

		$modelNotification->where(['id' => $notification_id])
							->set(['read' => '1'])
							->update();

		$app_user_id = $modelNotification->find($notification_id)['app_user_id'];

		$notifications = $modelClubInvitations->where(['app_user_id' => $app_user_id])->findAll();
		foreach( $notifications as $key => $notification ) {
			$notifications[$key]['club_name'] = $modelClubs->where(['club_id' => $notification['club_id']])->first()['club_name'];
			$notifications[$key]['type'] = 'club_invitation';
		}

		$temp = $modelNotification->where(['app_user_id' => $app_user_id, 'read' => '0'])->orderBy('created_at', 'desc')->findAll();
		foreach( $temp as $key => $tmp ) {
			$tmp['type'] = 'notify';
			array_push($notifications, $tmp);
		}

		return $this->response->setStatusCode(202)->setJSON(['notifications' => $notifications, 'message' => 'You have seen the notification.']);
	}
}