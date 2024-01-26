<?php namespace App\Controllers;

use App\Models\UserModel;

class Register extends BaseController
{
	public function index()
	{
		$data = [];
		$data['page'] = 'register';
		$data['isAdmin'] = session()->get('role') === 'ADMIN' || session()->get('role') === 'SUPERADMIN' ? true : false;
		if (session()->get('role') === 'SUPERADMIN') $data['isSAdmin'] =  true;
		helper(['form']);
		$data['roles'] = array('MANAGER', 'ADMIN');
        $model = new UserModel();
        $data['users'] = $model->where(['role !='=>'SUPERADMIN'])
                                ->paginate(10, 'group1');
        $data['pager'] = $model->pager;

		if ($this->request->getMethod() == 'post') {
			//let's do the validation here
			$rules = [
				'nice_name' => 'required|min_length[3]|max_length[20]',
				'role' => 'required|in_list[MANAGER,ADMIN]',
				'email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email]',
				'password' => 'required|min_length[8]|max_length[255]',
				// 'password_confirm' => 'matches[pass]',
			];

			if (! $this->validate($rules)) {
				$data['validation'] = $this->validator;
			}else{
				$model = new UserModel();

				$newData = [
					'nice_name' => $this->request->getVar('nice_name'),
					'role' => $this->request->getVar('role'),
					'email' => $this->request->getVar('email'),
					'pass' => $this->request->getVar('password'),
				];
				$model->save($newData);
				$session = session();
                $session->setFlashdata('success', 'Successful Registration');
                
				return redirect()->to('register');
			}
		}

		echo view('templates/header', $data);
		echo view('pages/register', $data);
		echo view('templates/footer');
    }

    public function update_user()
    {
		if ($this->request->getMethod() == 'post') {
			//let's do the validation here
			$rules = [
				'nice_name' => 'required|min_length[3]|max_length[20]',
				'role' => 'required|in_list[MANAGER,ADMIN]',
				'email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email,user_id,{user_id}]',
				// 'password_confirm' => 'matches[pass]',
			];

			if (! $this->validate($rules)) {
				$data['validation'] = $this->validator;
                echo $this->request->getVar('user_id');
                echo $this->validator->listErrors();
			}else{
				$model = new UserModel();

                $id = $this->request->getVar('user_id');
				$newData = [
					'nice_name' => $this->request->getVar('nice_name'),
					'role' => $this->request->getVar('role'),
					'email' => $this->request->getVar('email'),
				];
				$model->update($id, $newData);
                
				return redirect()->to('register');
			}
		}
    }
	
    public function reset_user_pass()
    {
		if ($this->request->getMethod() == 'post') {
			//let's do the validation here
			$rules = [
				'password' => 'required|min_length[8]|max_length[255]',
			];

			if (! $this->validate($rules)) {
				$data['validation'] = $this->validator;
                echo $this->request->getVar('user_id');
                echo $this->validator->listErrors();
			}else{
				$model = new UserModel();

                $id = $this->request->getVar('user_id');
				$newData = [
					'pass' => $this->request->getVar('password'),
				];
				$model->update($id, $newData);
                
				return redirect()->to('register');
			}
		}
    }
    
    public function delete_user()
    {
        $data = [];

		if ($this->request->getMethod() == 'post') {
			//let's do the validation here
			$rules = [
				'user_id' => 'required|integer|matches[user_id]',
			];

			if (! $this->validate($rules)) {
                $data['validation'] = $this->validator;
                echo $this->request->getVar('user_id');
                echo $this->validator->listErrors();
			}else{
                $model = new UserModel();
                
				$model->delete($this->request->getVar('user_id'));
				$session = session();
                $session->setFlashdata('success', 'Successful Deletion');
                
				return redirect()->to('register');
			}
		}
    }

	//--------------------------------------------------------------------

}