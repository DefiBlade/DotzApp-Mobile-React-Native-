<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use \App\Libraries\Oauth;
use \OAuth2\Request;
use App\Models\AppUserModel;
use App\Models\ProfilesModel;

class Profile_friends_api extends ResourceController
{
	protected $modelName = 'App\Models\Profile_relsModel';
	protected $format = 'json';
	protected $request;
	protected $exit = false;
	protected $app_user = false;

    public function __construct()
    {
		$oauth = new Oauth();
		$request = Request::createFromGlobals();
        if ( ! $oauth->server->verifyResourceRequest( $request->createFromGlobals() ) ) {
            $this->exit = true;
        } else {
            $this->app_user = $oauth->server->getAccessTokenData($request);
        }
	}

	public function index()
    {
        helper("json_checker");
        $request_params = $this->request->getAllQueryParameters();
        if (isset(($request_params['activities']))) {
            if ( isJson($request_params['activities']) ) {
                $request_activities = json_decode($request_params['activities'], true);
                if ( is_array($request_activities) ) {
                    $activities = $this->model->findAll();
                    if ( $activities === $request_activities ) {
                        return $this->response->setJSON("Unchanged");
                    }
                    else {
                        return $this->response->setJSON($activities);
                    }
                } else {
                    return $this->response->setJSON("Error. Required parameter is not an array.", 400);
                }
            } else {
                return $this->response->setJSON("Error. Required parameter is not JSON.", 400);
            }
        } else {
            return $this->response->setJSON("Error. Required parameter not found.", 400);
        }
	}

    public function add()
    {
        if ($this->exit) return $this->response->setStatusCode(400)->setJSON(["error"=>"Token is not valid"]);
        
        helper(['form']);

        $rules = [
            'app_user_id' => 'required|string',
            'profile_rel_status' => 'string'
        ];

        if(!$this->validate($rules)){
            return $this->fail($this->validator->getErrors());
        }else{
            $modelAppUser = new AppUserModel;
            $modelProfilesModel = new ProfilesModel;
            if ( $modelAppUser->find($this->request->getVar('app_user_id')) ) {
                $app_user_id = intval( $this->request->getVar('app_user_id') );
                $friend_profile = $modelProfilesModel->getByAppUserId($app_user_id);
            } else {
                return $this->response->setStatusCode(400)->setJSON(["error"=>"app_user_id not found"]);
            }

            $profile_rel_status = $this->request->getVar('profile_rel_status');
            $data = [
                'profile_id' => $this->app_user['profile_id'],
                'app_user_id' => $app_user_id,
            ];
            $friend_data = [
                'profile_id' => $friend_profile['profile_id'],
                'app_user_id' => $this->app_user['user_id'],
            ];
            $prof_rel = $this->model->where($data)->findAll();
            if ( !empty($profile_rel_status) ) {
                $data['profile_rel_status'] = $profile_rel_status;
                $friend_data['profile_rel_status'] = $profile_rel_status;
            }

            if ( !$prof_rel ) {
                if ( $this->model->insert($data) ) {
                    $this->model->insert($friend_data);
                    return $this->respondCreated($data);
                } else return $this->response->setStatusCode(400)->setJSON(["error"=>"error while inserting data"]);
            } else {
                if ( $this->model->update($prof_rel[0]['profile_rel_id'], $data) ) {
                    $this->model->update($prof_rel[0]['profile_rel_id'], $friend_data);
                    return $this->respondUpdated($data);
                } else return $this->response->setStatusCode(400)->setJSON(["error"=>"error while updating data"]);
            }
        }
    }

	//--------------------------------------------------------------------
    //ALTER TABLE `profile_rels` AUTO_INCREMENT = 1;

}