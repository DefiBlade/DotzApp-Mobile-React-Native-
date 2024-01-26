<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ActivitiesModel;

class Activities extends BaseController
{
    public function index()
    {
		$modelActivities = new ActivitiesModel;
		$data['page'] = 'activities';
		$data['isAdmin'] = session()->get('role') === 'ADMIN' || session()->get('role') === 'SUPERADMIN' ? true : false;
		if (session()->get('role') === 'SUPERADMIN') $data['isSAdmin'] =  true;

		$data['activities'] = $modelActivities->findAll();
        $data['msg'] = session()->getFlashdata('msg');

		echo view('templates/header', $data);
		echo view('pages/activities');
		echo view('templates/footer');
	}
	
	public function addActivity()
	{
		if (empty($_FILES['activity_img']['name'])) return 'No file uploaded';
		
        $img = $this->request->getFile('activity_img');
        if ($img->isValid() && ! $img->hasMoved()) {
            $img->move('./uploads/activities',$img->getClientName());
        }
		$modelActivities = new ActivitiesModel;
		$activity_name = $this->request->getVar('activity_name');

		$data = array(
			'activity_name' => $activity_name,
			'activity_description' => $this->request->getVar('activity_description'),
			'activity_img' => $img->getClientName(),
		);

		if ($modelActivities->save($data)) {
			$session = session();
			$session->setFlashdata('msg', "\"{$activity_name}\" added successfuly!");
			return redirect()->to(base_url('/activities'));
		} else {
			$session = session();
			$session->setFlashdata('msg', "Error adding \"{$activity_name}\"");
		}
	}

    public function updateActivity()
    {
		if ($this->request->getMethod() !== 'post') return;
		if (!empty($_FILES['activity_img']['name'])) {
			$img = $this->request->getFile('activity_img');
			if ($img->isValid() && ! $img->hasMoved()) {
				$img->move('./uploads/activities',$img->getClientName());
			}
		} else {
			$img = null;
		}
	
		$modelActivities = new ActivitiesModel();
		$activity_name = $this->request->getVar('activity_name');

		$id = $this->request->getVar('activity_id');
		$newData = [
			'activity_name' => $this->request->getVar('activity_name'),
			'activity_description' => $this->request->getVar('activity_description'),
		];
		if ($img) $newData['activity_img'] = $img->getClientName();

		if ($modelActivities->update($id, $newData)) {
			$session = session();
			$session->setFlashdata('msg', "\"{$activity_name}\" updated!");
			return redirect()->to(base_url('/activities'));
		} else {
			$session = session();
			$session->setFlashdata('msg', "Error updating \"{$activity_name}\"");
		}
    }

	public function delete_activity()
	{
		if ($this->request->getMethod() == 'post') {
			$rules = [
				'activity_id' => 'integer|matches[activity_id]',
			];
			$activity_id = $this->request->getVar('activity_id');
			$activity_name = $this->request->getVar('activity_name');

			if (! $this->validate($rules)) {
                $data['validation'] = $this->validator;
                echo $activity_id;
                echo $this->validator->listErrors();
			} else {
                $model = new ActivitiesModel();
                
				$model->delete($activity_id);
				$session = session();
                $session->setFlashdata('msg', "{$activity_name} was deleted");
                
				return redirect()->to('/activities');
			}
		}
	}
}