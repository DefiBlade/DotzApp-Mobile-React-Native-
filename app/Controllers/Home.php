<?php namespace App\Controllers;

use App\Models\UserModel;

class Home extends BaseController
{
	public function index($message = '')
	{
		$data = [];
		helper(['form']);


		if ($this->request->getMethod() == 'post') {
			//let's do the validation here
			$rules = [
				'email' => 'required|min_length[6]|max_length[50]|valid_email',
				'password' => 'required|min_length[8]|max_length[255]|validateUser[email,password]',
			];

			$errors = [
				'password' => [
					'validateUser' => 'Email or Password don\'t match'
				]
			];

			// if (! $this->validate($rules, $errors)) {
			// 	$data['validation'] = $this->validator;
			// }else{
				$model = new UserModel();

				$user = $model->where('email', $this->request->getVar('email'))
											->first();

				if( !$user ) {
					$message = `User doesn't exist`;
					
					$data['message'] = $message;
					$data['page'] = 'login';
			
					echo view('templates/header', $data);
					echo view('pages/login');
					echo view('templates/footer');
					return;
				}

				$this->setUserSession($user);
				//$session->setFlashdata('success', 'Successful Registration');
				return redirect()->to('locs_pending');

			// }
		}
		$data['message'] = $message;
		$data['page'] = 'login';

		echo view('templates/header', $data);
		echo view('pages/login');
		echo view('templates/footer');
	}

	private function setUserSession($user){
		$data = [
			'user_id' => $user['user_id'],
			'nice_name' => $user['nice_name'],
			'role' => $user['role'],
			'email' => $user['email'],
			'isLoggedIn' => true,
		];

		session()->set($data);
		return true;
	}

	public function logout(){
		session()->destroy();
		return redirect()->to('/');
	}

	//--------------------------------------------------------------------

}
