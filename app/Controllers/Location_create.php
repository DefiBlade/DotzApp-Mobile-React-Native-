<?php namespace App\Controllers;

class Location_create extends BaseController
{
    public function index()
    {
		$data['page'] = 'location_create';
		$data['isAdmin'] = session()->get('role') === 'ADMIN' || session()->get('role') === 'SUPERADMIN' ? true : false;
		if (session()->get('role') === 'SUPERADMIN') $data['isSAdmin'] =  true;

		echo view('templates/header', $data);
		echo view('pages/location_create');
		echo view('templates/footer');
    }
}