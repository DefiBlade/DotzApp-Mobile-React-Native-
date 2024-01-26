<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\I18n\Time;
use \App\Libraries\Oauth;
use \OAuth2\Request;
use App\Models\ActivitiesModel;

class Locs_pending_api extends ResourceController
{
	protected $modelName = 'App\Models\LocationsPendingModel';
	protected $format = 'json';
	protected $request_oauth;

    public function __construct()
    {
		$this->oauth = new Oauth();
		$this->request_oauth = Request::createFromGlobals();
	}

    public function add()
    {
        if ($this->is_token_valid())
        {
			helper(['form']);

			$rules = [
				'loc_p_cors_start' => 'string',
				// 'loc_p_cors_start' => 'required|string|max_length[50]',
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
					'loc_p_title' => $this->request->getVar('loc_p_title'),
					'loc_p_cors_start' => $this->request->getVar('loc_p_cors_start'),
					'loc_p_cors_finish' => $this->request->getVar('loc_p_cors_finish'),
					'loc_p_cors_all' => $this->request->getVar('loc_p_cors_all'),
					'loc_p_activity_id' => $activity_id,
					'loc_p_city' => $this->request->getVar('loc_p_city'),
				];

				$loc_p_id = $this->model->insert($data);
				$data['loc_p_id'] = $loc_p_id;
				return $this->respondCreated($data);
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