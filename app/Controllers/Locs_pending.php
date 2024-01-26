<?php namespace App\Controllers;

use App\Models\LocationsModel;
use App\Models\ActivitiesLocsRelsModel;
use App\Models\ActivitiesModel;

class Locs_pending extends BaseController
{
    public function index()
    {
		$LocationsModel = new LocationsModel;
		$data['page'] = 'locs_pending';
		$data['isAdmin'] = session()->get('role') === 'ADMIN' || session()->get('role') === 'SUPERADMIN' ? true : false;
		if (session()->get('role') === 'SUPERADMIN') $data['isSAdmin'] =  true;

		$data['locs'] = $LocationsModel->where(['approved' => 0])->findAll();

		echo view('templates/header', $data);
		echo view('pages/locs_pending');
		echo view('templates/footer');
	}
	
	public function approve()
	{
		$data = [];

		if ($this->request->getMethod() == 'post') {
			//let's do the validation here
			$rules = [
				'loc_id' => 'integer|matches[loc_id]',
			];

			if (! $this->validate($rules)) {
                $data['validation'] = $this->validator;
                echo $this->request->getVar('loc_id');
                echo $this->validator->listErrors();
			} else {
				$LocationsModel = new LocationsModel;

				$LocationsModel->where(['loc_id' => $this->request->getVar('loc_id')])->set(['approved' => 1])->update();

				$session = session();
				$session->setFlashdata('success', 'Successful Deletion');
				
				return redirect()->to('/locs_pending');
			}
		}
	}
	
	public function delete()
	{
		$data = [];

		if ($this->request->getMethod() == 'post') {
			//let's do the validation here
			$rules = [
				'loc_id' => 'integer|matches[loc_id]',
			];

			if (! $this->validate($rules)) {
                $data['validation'] = $this->validator;
                echo $this->request->getVar('loc_id');
                echo $this->validator->listErrors();
			} else {
                $model = new LocationsModel;
				$modelAc_loc_rels = new ActivitiesLocsRelsModel;

				$modelAc_loc_rels->where(['loc_id' => $this->request->getVar('loc_id')])->delete();

				$model->delete($this->request->getVar('loc_id'));

				$session = session();
                $session->setFlashdata('success', 'Successful Deletion');
                
				return redirect()->to('/locs_pending');
			}
		}
	}
}