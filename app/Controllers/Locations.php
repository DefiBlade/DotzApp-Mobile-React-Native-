<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LocationsModel;

class Locations extends BaseController
{
    public function index()
    {
		$LocationsModel = new LocationsModel;
		$data['page'] = 'locations';
		$data['isAdmin'] = session()->get('role') === 'ADMIN' || session()->get('role') === 'SUPERADMIN' ? true : false;
		if (session()->get('role') === 'SUPERADMIN') $data['isSAdmin'] =  true;

		$data['locs'] = $LocationsModel->where([ 'approved' => 1 ])->findAll();

		echo view('templates/header', $data);
		echo view('pages/locations');
		echo view('templates/footer');
    }
}