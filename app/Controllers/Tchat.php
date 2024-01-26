<?php namespace App\Controllers;

class Tchat extends BaseController
{
	public function index()
	{
		$data['page'] = 'tchat';
		$data['isAdmin'] = session()->get('role') === 'ADMIN' || session()->get('role') === 'SUPERADMIN' ? true : false;
		if (session()->get('role') === 'SUPERADMIN') $data['isSAdmin'] =  true;
		$data['sidebar_data'] = $this->sidebar_data;

		echo view('templates/header', $data);
		echo view('pages/tchat');
		echo view('templates/footer');
	}

	//--------------------------------------------------------------------

}