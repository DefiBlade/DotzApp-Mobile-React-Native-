<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\I18n\Time;
use \App\Libraries\Oauth;
use \OAuth2\Request;
use App\Models\AppUserModel;
use App\Models\ActivitiesLocsRelsModel;
use App\Models\ActivitiesModel;
use App\Models\ProfilesModel;
use App\Models\ProfilesPostsModel;
use App\Models\ProfilesPostsComments;
use App\Models\Profile_relsModel;
use App\Models\Prof_act_relsModel;
use App\Models\UserRecordModel;
use App\Models\LocationsModel;

class Locs_api extends ResourceController
{
	protected $modelName = 'App\Models\LocationsModel';
	protected $format = 'json';
	protected $request_oauth;

    public function __construct()
    {
		$this->oauth = new Oauth();
		$this->request_oauth = Request::createFromGlobals();
	}

	public function index()
	{
        if ($this->is_token_valid())
        {
			$modelAppUser = new AppUserModel;
			$modelAc_loc_rels = new ActivitiesLocsRelsModel;
			$modelActivities = new ActivitiesModel;
			$modelProfiles = new ProfilesModel;
            $modelProfile_rels = new Profile_relsModel;
            $modelProf_act_rels = new Prof_act_relsModel;
            $modelProfilesPosts = new ProfilesPostsModel;
            $modelProfilesPostsComments = new ProfilesPostsComments;

			$locs = $this->model->where('approved', 1)->findAll();
			$ac_loc_rels = $modelAc_loc_rels->findAll();
			$activities = $modelActivities->findAll();
			$AppUsers = $modelAppUser->findAll();
            $allComments = $modelProfilesPostsComments->findAll();

			foreach ($locs as $loc_key => $loc) {
				foreach ($ac_loc_rels as $ac_loc_rel) {
					if ($loc['loc_id'] == $ac_loc_rel['loc_id']) {
						foreach ($activities as $activity) {
							if ($activity['activity_id'] == $ac_loc_rel['activity_id']) {
								$locs[$loc_key]['activity'] = $activity;
							}
						}
					}
				}
				$records = json_decode($loc['loc_records']);

				if (!empty($records)) {
					foreach ($records as $record) {
						if (property_exists($record,'app_user_id')) {
							$profile = $modelProfiles->getByAppUserId($record->app_user_id);
							if( !empty($profile) ) {
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
							} else {
								$profile = [];
								$profile['friends'] = [];
								$profile['activities'] = [];
								$profile['posts'] = [];
							}

							$record->profile = $profile;
						}
					}
					$locs[$loc_key]['loc_records'] = $records;
				}
			}

			return $this->respond($locs);
		}
		else {
			return $this->response->setStatusCode(400)->setJSON(["error"=>"Token is not valid"]);
		}
	}

	public function loc_record()
	{
        if ($this->is_token_valid())
        {
			$modelAppUser = new AppUserModel;
			$modelProfiles = new ProfilesModel;

			$app_user = $this->oauth->server->getAccessTokenData($this->request_oauth);
			$loc_id = $this->request->getVar('loc_id');
			$new_record = $this->request->getVar('loc_record');
			if ($loc_id || $new_record) {
				$loc = $this->model->find($loc_id);
				$app_user = $modelAppUser->find($app_user['user_id']);
				$profile = $modelProfiles->getByAppUserId($app_user['app_user_id']);

				if ( $loc['loc_records'] !== NULL ) {
					sscanf($new_record, "%d:%d:%d", $hours, $minutes, $seconds);

					$new_record_seconds = isset($hours) ? $hours * 3600 + $minutes * 60 + $seconds : $minutes * 60 + $seconds;
					$records = json_decode($loc['loc_records']);
					if ( empty( $records ) ) $updateHappened = false;

					foreach ($records as $key => $record) {
						sscanf($record->time, "%d:%d:%d", $hours, $minutes, $seconds);
						$record_seconds = isset($hours) ? $hours * 3600 + $minutes * 60 + $seconds : $minutes * 60 + $seconds;
						if ($record_seconds > $new_record_seconds) {
							if (count($records) == 3) {
								$records[$key] = ['time' => $new_record, 'app_user_id' => $app_user['app_user_id'], 'app_user_name' => $app_user['app_user_name'], 'profile_img_ava' => $profile['profile_img_ava']];
							} else {
								array_splice( $records, $key, 0, [['time' => $new_record, 'app_user_id' => $app_user['app_user_id'], 'app_user_name' => $app_user['app_user_name'], 'profile_img_ava' => $profile['profile_img_ava']]] );
							}
							$updateHappened = $this->model->update($loc_id, ['loc_records'=>json_encode( $records )]);
							if (!$updateHappened) return $this->response->setStatusCode(400)->setJSON(["error"=>"DataBase failed to update."]);
							break;
						} else {
							$updateHappened = false;
						}
					}

					if (count($records) < 3 && !$updateHappened) {
						$records[] = ['time' => $new_record, 'app_user_id' => $app_user['app_user_id'], 'app_user_name' => $app_user['app_user_name'], 'profile_img_ava' => $profile['profile_img_ava']];
						$updateHappened = $this->model->update($loc_id, ['loc_records'=>json_encode( $records )]);
						if (!$updateHappened) return $this->response->setStatusCode(400)->setJSON(["error"=>"DataBase failed to update."]);
					}

				} else {
					$loc_records = [['time' => $new_record, 'app_user_id' => $app_user['app_user_id'], 'app_user_name' => $app_user['app_user_name'], 'profile_img_ava' => $profile['profile_img_ava']]];
					$updateHappened = $this->model->update($loc_id, [ 'loc_records'=>json_encode( $loc_records ) ]);
				}
				
				// TODO: add User Record
				$modelUserRecord = new UserRecordModel;
				$modelLocations = new LocationsModel;

				$data = [];
				$data['app_user_id'] = $this->request->getVar('app_user_id');
				$data['user_routes'] = $this->request->getVar('userRoutes');
				$data['location_id'] = $this->request->getVar('loc_id');
				$data['distance'] = $this->request->getVar('distance');
				$data['pace'] = $this->request->getVar('pace');
				$data['time'] = $this->request->getVar('loc_record');
				$data['created_at'] = $this->request->getVar('created_at');
				$modelUserRecord->insert($data);

				$loc_records = $modelUserRecord->where(['app_user_id' => $this->request->getVar('app_user_id')])->findAll();
				
				foreach( $loc_records as $key => $loc_record ) {
					$loc_name = 'No name';
					if( $modelLocations->find( $loc_record['location_id'] ) != null )
						$loc_name = $modelLocations->find( $loc_record['location_id'] )['loc_title'];
					$loc_records[$key]['loc_name'] = $loc_name;
				}
				return $this->response->setStatusCode(202)->setJSON(['loc_records' => ($loc_records)]);
			} else {
				return $this->response->setStatusCode(400)->setJSON(["error"=>"No loc_id or new_record provided."]);
			}
		}
		else return $this->response->setStatusCode(400)->setJSON(["error"=>"Token is not valid"]);
	}

	public function loc_rate()
	{
        if ($this->is_token_valid())
        {
			$loc_id = $this->request->getVar('loc_id');
			$new_rate = $this->request->getVar('loc_rating');
			if ($loc_id || $new_rate) {
				$loc = $this->model->find($loc_id);
				if ($loc['loc_rating'] !== NULL) {
					$arr = [$loc['loc_rating'], $new_rate];
					$avr = floor(( array_sum($arr) / count($arr) ) * 2) / 2; //count average & than round to nearest half
					$updateHappened = $this->model->update($loc_id, ['loc_rating'=>$avr]);
				} else {
					$updateHappened = $this->model->update($loc_id, ['loc_rating'=>$new_rate]);
				}
				return $this->response->setStatusCode(202)->setJSON(['success'=>$updateHappened && isset($avr) ? $avr : $new_rate]);
			} else {
				return $this->response->setStatusCode(400)->setJSON(["error"=>"No loc_id or loc_rating provided."]);
			}
		}
		else return $this->response->setStatusCode(400)->setJSON(["error"=>"Token is not valid"]);
	}

	public function add() {
		if ($this->is_token_valid())
        {
			$modelAc_loc_rels = new ActivitiesLocsRelsModel;

			helper(['form']);

			$rules = [
				'loc_p_cors_start' => 'string',
				'loc_p_cors_finish' => 'string',
				'loc_p_cors_all' => 'string'
			];

			if(!$this->validate($rules)){
				return $this->fail($this->validator->getErrors());
			}else{
				$oauth = new Oauth();
				$request = Request::createFromGlobals();
				$app_user = $oauth->server->getAccessTokenData($request);
				$modelActivities = new ActivitiesModel;
				if ( $modelActivities->find($this->request->getVar('activity_id')) ) {
					$activity_id = intval( $this->request->getVar('activity_id') );
				} else {
					return $this->response->setJSON("Error. activity_id not found.", 400);
				}
				$data = [
					'loc_title' => $this->request->getVar('loc_p_title'),
					'loc_cors_start' => $this->request->getVar('loc_p_cors_start'),
					'loc_cors_finish' => $this->request->getVar('loc_p_cors_finish'),
					'loc_cors_all' => $this->request->getVar('loc_p_cors_all'),
					'loc_activity_id' => $activity_id,
					'loc_city' => $this->request->getVar('loc_p_city'),
				];

				$loc_p_id = $this->model->insert($data);
				$data['loc_p_id'] = $loc_p_id;

				$modelAc_loc_rels->insert([ 'activity_id' => $activity_id, 'loc_id' => $loc_p_id ]);

				// TODO: add User Record
				$type = $this->request->getVar('type');
				if( $type != 'gpsRoute' )
					return $this->response->setStatusCode(202)->setJSON(['success' => true]);
				else {
					$modelUserRecord = new UserRecordModel;
					$modelLocations = new LocationsModel;
	
					$data = [];
					$data['app_user_id'] = $this->request->getVar('app_user_id');
					$data['user_routes'] = $this->request->getVar('userRoutes');
					$data['location_id'] = $loc_p_id;
					$data['distance'] = $this->request->getVar('distance');
					$data['pace'] = $this->request->getVar('pace');
					$data['time'] = $this->request->getVar('loc_record');
					$data['created_at'] = $this->request->getVar('created_at');
					$modelUserRecord->insert($data);
	
					$loc_records = $modelUserRecord->where(['app_user_id' => $this->request->getVar('app_user_id')])->findAll();
	
					foreach( $loc_records as $key => $loc_record ) {
						$loc_name = 'No name';
						if( $modelLocations->find( $loc_record['location_id'] ) != null )
							$loc_name = $modelLocations->find( $loc_record['location_id'] )['loc_title'];
						$loc_records[$key]['loc_name'] = $loc_name;
					}
					return $this->response->setStatusCode(202)->setJSON(['loc_records' => ($loc_records)]);
				}
			}
		}
		else return $this->response->setJSON(["error"=>"Token is not valid"], 400);
	}

    public function is_token_valid()
    {
		$this->request_oauth = Request::createFromGlobals();
        return $this->oauth->server->verifyResourceRequest($this->request_oauth->createFromGlobals());
    }

	//--------------------------------------------------------------------

}