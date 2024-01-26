<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use \App\Libraries\Oauth;
use \OAuth2\Request;

class Clubs_api extends ResourceController
{
	protected $modelName = 'App\Models\ClubsModel';
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
			$clubs = $this->model->select(['club_id','club_name','club_description','club_img'])->findAll();
			return $this->respond($clubs);
		}
		else return $this->response->setStatusCode(400)->setJSON(["error"=>"Token is not valid"]);
	}

    public function is_token_valid()
    {
		$this->request_oauth = Request::createFromGlobals();
        return $this->oauth->server->verifyResourceRequest($this->request_oauth->createFromGlobals());
    }
}