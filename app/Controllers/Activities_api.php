<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use \App\Libraries\Oauth;
use \OAuth2\Request;

class Activities_api extends ResourceController
{
	protected $modelName = 'App\Models\ActivitiesModel';
	protected $format = 'json';
	protected $oauth = 'json';
	protected $request = 'json';
	protected $request_oauth;

    public function __construct()
    {
		$this->oauth = new Oauth();
		$this->request_oauth = Request::createFromGlobals();
	}

	public function index()
    {
        // if ($this->is_token_valid())
        // {
            helper("json_checker");
            $request_params = $this->request_oauth->getAllQueryParameters();
            
            if (isset(($request_params['activities']))) {
                if ( isJson($request_params['activities']) ) {
                    $request_activities = json_decode($request_params['activities'], true);
                    if ( is_array($request_activities) ) {
                        
                        $activities = $this->model->findAll();
                        return $this->response->setJSON($activities);
                        
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
        // }
        // else return $this->response->setJSON(["error"=>"Tokena is not valid"], 400);
	}

    public function is_token_valid()
    {
		$this->request_oauth = Request::createFromGlobals();
        return $this->oauth->server->verifyResourceRequest($this->request_oauth->createFromGlobals());
    }

	//--------------------------------------------------------------------

}