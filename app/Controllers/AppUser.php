<?php namespace App\Controllers;

use \App\Libraries\Oauth;
use \OAuth2\Request;
use CodeIgniter\API\ResponseTrait;
use App\Models\AppUserModel;
use App\Models\AppUserRelsModel;
use App\Models\ProfilesModel;
use App\Models\Profile_relsModel;
use App\Models\Prof_act_relsModel;
use App\Models\ActivitiesModel;
use App\Models\ProfilesPostsModel;
use App\Models\ProfilesPostsComments;
use App\Models\UserRecordModel;
use App\Models\LocationsModel;

class AppUser extends BaseController
{
	use ResponseTrait;

	public function login($userType = null) {
		if ($userType == 548) {
			$model_appusers = new AppUserModel();

			$appuser = $model_appusers->where('email', $this->request->getVar('username'))->first();
			if (!$appuser || empty($appuser)) return $this->response->setStatusCode(400)->setJSON(["error"=>"appuser {appuser} not found or empty.", 'appuser'=>$appuser]);
			$appuser_id = $appuser['app_user_id'];

			$data = [
				'app_user_id' => $appuser_id,
				'password' => $this->request->getVar('access_token'),
				'scope' => 'app_token',
			];
			$model_appusers->save($data);
			
			// logging
			$info = [
				'body' => $this->request->getVar('username'),
				'appuser_id' => json_encode($appuser_id),
				'data' => json_encode($data)
			];
			log_message('critical', "app_user login 548\nbody: {body}\nappuser_id: {appuser_id}\nData: {data}", $info);
		}

		$oauth = new Oauth();
		$request = new Request();
		$respond = $oauth->server->handleTokenRequest($request->createFromGlobals());
		if (!$respond || empty($respond)) return $this->response->setStatusCode(400)->setJSON(["error"=>"Error while handleTokenRequest.",'respond'=>$respond]);
		$model_appusers = new AppUserModel();
		$modelProfiles = new ProfilesModel;
		$modelAppUserRels = new AppUserRelsModel;
		$modelProfile_rels = new Profile_relsModel;
		$modelProf_act_rels = new Prof_act_relsModel;
		$modelActivities = new ActivitiesModel;
		$modelProfilesPosts = new ProfilesPostsModel;
		$modelProfilesPostsComments = new ProfilesPostsComments;
		$modelUserRecord = new UserRecordModel;
		$modelLocations = new LocationsModel;
		$AppUsers = $model_appusers->findAll();
		$Activities = $modelActivities->findAll();
		$allComments = $modelProfilesPostsComments->findAll();
		$body = $respond->getResponseBody();
		$body = json_decode($respond->getResponseBody());

		$app_user = $model_appusers->where('email', $this->request->getVar('username'))->select(['app_user_id','email','app_user_name','bio','scope'])->first();
		if ($app_user === NULL) {
			if ($app_user = $model_appusers->where('email', $this->request->getVar('username'))->select(['app_user_id','email','app_user_name','bio','scope','deleted_at'])->withDeleted()->find()) {
				return $this->response->setStatusCode(400)->setJSON(["error"=>"app_user is deleted",'app_user'=>$app_user]);
			} else return $this->response->setStatusCode(400)->setJSON(["error"=>"app_user returns null",'app_user'=>$app_user]);
		}
		if (!$app_user && empty($app_user)) return $this->response->setStatusCode(400)->setJSON(["error"=>"app_user not found or empty.",'app_user'=>$app_user]);
		
		$appUserRel = $modelAppUserRels->where(['app_user_id'=>$app_user['app_user_id']])->find();
		if (!$appUserRel && empty($appUserRel)) return $this->response->setStatusCode(400)->setJSON(["error"=>"wrong or empty id.",'app_user_id'=>$app_user['app_user_id']]);
		
		$profile = $modelProfiles->find($appUserRel[0]['profile_id']);

		$friends = $modelProfile_rels->where( ['profile_id' => $profile['profile_id']] )->select(['app_user_id','profile_rel_status'])->findAll();
		foreach ($friends as $friend_key => $friend) {
			foreach ($AppUsers as $app_user_friend) {
				if ( $friend['app_user_id'] == $app_user_friend['app_user_id'] ) {
					$friends[$friend_key]['app_user_name'] = $app_user_friend['app_user_name'];
					$friends[$friend_key]['bio'] = $app_user_friend['bio'];
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

					$loc_records = $modelUserRecord->where([ 'app_user_id' => $app_user_friend['app_user_id'] ])->findAll();
				
					foreach( $loc_records as $key => $loc_record ) {
						$loc_name = 'No name';
						if( $modelLocations->find( $loc_record['location_id'] ) != null )
							$loc_name = $modelLocations->find( $loc_record['location_id'] )['loc_title'];
						$loc_records[$key]['loc_name'] = $loc_name;
					}

					$friends[$friend_key]['loc_records'] = $loc_records;
				}
			}
		}
		$profile['friends'] = $friends;

		$act_rels = $modelProf_act_rels->where( ['profile_id' => $profile['profile_id']] )->select(['activity_id'])->findAll();
		$profile['activities'] = [];
		foreach ($act_rels as $act_rel) {
			$profile['activities'][] = $act_rel['activity_id'];
		}

		$profile['posts'] = $modelProfilesPosts->getByProfileId($profile['profile_id']);
		foreach ($profile['posts'] as $key => $post) {
			foreach ($allComments as $comment) {
				if ($post['pp_id'] == $comment['pp_id']) {
					$profile['posts'][$key]['comments'][] = $comment;
				}
			}
		}

		$profile['loc_records'] = $modelUserRecord->where(['app_user_id' => $app_user['app_user_id']])->findAll();
		foreach( $profile['loc_records'] as $key => $loc_record ) {
			$loc_name = 'No name';
			if( $modelLocations->find( $loc_record['location_id'] ) != null )
				$loc_name = $modelLocations->find( $loc_record['location_id'] )['loc_title'];
			$profile['loc_records'][$key]['loc_name'] = $loc_name;
		}

		$body->app_user = $app_user;
		$body->profile = $profile;

		//logging
		$info = [
			'body' => json_encode($body),
			'request' => json_encode($request),
		];
		log_message('critical', "app_user login\nbody: {body}\nrequest: {request}", $info);

		return $this->respond($body, $respond->getStatusCode());
	}

	public function register($userType = null) {
		helper('form');
		$data = [];
		$email = $this->request->getVar('email');
		$password = $this->request->getVar('password');
		$scope = $userType == 548 ? $this->request->getVar('scope') : 'app_pass';
		$scope_allowed = $userType == 548 ? 'app_token' : 'app_pass';
		$password_rules = 'required|min_length[8]';

		$rules = [
			'email' => "required|valid_email|is_unique[app_users.email,email.{$email}]",
			'password' => $password_rules,
			'name' => 'required|min_length[3]|max_length[20]',
		];

		if ( !$this->validate($rules) ) {
			return $this->fail($this->validator->getErrors());
		} elseif ( $this->validate($rules) && !$this->validator->check($scope,'in_list['.$scope_allowed.']') ) {
			return $this->fail('Skope '.$scope.' is not allowed');
		} else {
			$model = new AppUserModel();
			$modelAppUserRels = new AppUserRelsModel;
			$modelProfiles = new ProfilesModel;
			$modelActivities = new ActivitiesModel;
			$modelProf_act_rel = new Prof_act_relsModel;

			if( $model->where(['app_user_name' => $this->request->getVar('name')])->first() ) {
				return $this->response->setStatusCode(202)->setJSON(["error"=>"You have entered a duplicate Username!"]);
			}

			// Check Activities
			if ($this->request->getVar('activities')) {
				$activities = json_decode($this->request->getVar('activities'));
				foreach ($activities as $key => $activity) {
					if ( !$modelActivities->find($activity) ) return $this->response->setStatusCode(400)->setJSON(["error"=>"activity_id {$activity} not found."]);
				}
			} else {
				return $this->response->setStatusCode(400)->setJSON(["error"=>"No activities provided."]);
			}

			// Check File
			if (!empty($_FILES['profile_img_ava']['name'])) {
				$img = $this->request->getFile('profile_img_ava');
				if ($img->isValid() && ! $img->hasMoved()) {
					$img->move('./uploads/profiles',$img->getClientName());
				}
			} else {
				$img = null;
			}
			$img_name = $img ? $img->getClientName() : null;

			// AppUser
			$data = [
                'email' => $email,
                'password' => $password,
				'app_user_name' => $this->request->getVar('name'),
				'bio' => $this->request->getVar('bio'),
				'scope' => $scope
			];

			$app_user_id = $model->insert($data);
			$data['id'] = $app_user_id;
			unset($data['password']);

			// Profile
			$dataProfile = [
                'profile_img_ava' => $img_name,
                'profile_city' => $this->request->getVar('profile_city'),
                'profile_privacy_buble' => $this->request->getVar('profile_privacy_buble'),
			];
			$data['profile_img_ava'] = $dataProfile['profile_img_ava'];
			$data['profile_city'] = $dataProfile['profile_city'];
			$data['profile_privacy_buble'] = $dataProfile['profile_privacy_buble'];

			$profile_id = $modelProfiles->insert($dataProfile);

			$modelAppUserRels->insert(['app_user_id' => $app_user_id, 'profile_id' => $profile_id]);

			// Activities
			foreach ($activities as $activity) {
				$dataActivities = [
					'activity_id' => $activity,
					'profile_id' => $profile_id
				];
				$modelProf_act_rel->insert($dataActivities);
			}
			$data['profile_id'] = $profile_id;
			$data['activities'] = $activities;

			return $this->respondCreated($data);
		}

	}

	public function getProfileInfoById()
	{

	}
}
