<?php namespace App\Controllers;

use App\Models\AppUserModel;
use App\Models\ChatHistoryModel;
use App\Models\SettingsChatModel;

class Schat extends BaseController
{
	public function index()
	{
		$appUserModel = new AppUserModel;
		$ChatHistoryModel = new ChatHistoryModel;
		$SettingsChatModel = new SettingsChatModel;
		$data['page'] = 'schat';
		$data['isAdmin'] = session()->get('role') === 'ADMIN' || session()->get('role') === 'SUPERADMIN' ? true : false;
		if (session()->get('role') === 'SUPERADMIN') $data['isSAdmin'] =  true;
		$data['sidebar_data'] = $this->sidebar_data;

		foreach ($SettingsChatModel->select('cs_set_name, cs_set_val')->findAll() as $subArr) {
			$data['chat_settings'][$subArr['cs_set_name']] = $subArr['cs_set_val'];
		}

		$data['app_users'] = $appUserModel->findAll();
		foreach ($data['app_users'] as $key => $app_user) {
			$app_user_history = $ChatHistoryModel->where('author_id', $app_user['app_user_id'])->orWhere('msg_reciever_id', $app_user['app_user_id'])->findAll();
			if (count($app_user_history) > 0) {
				$data['app_users'][$key]['app_user_latest_msg'] = $app_user_history[count($app_user_history)-1];
				$countUnread = 0;
				$msg_ids_arr = [];
				foreach ($app_user_history as $msg_key => $msg) {
					if ($msg['msg_status'] === 'unread') $countUnread++;
					$msg_ids_arr[] = $msg['msg_id'];
				}
				$data['app_users'][$key]['app_user_unread_msg'] = $countUnread;
				$data['app_users'][$key]['app_user_test'] = $msg_ids_arr;
			}
		}

		echo view('templates/header', $data);
		echo view('pages/schat');
		echo view('templates/footer');
	}

	//--------------------------------------------------------------------

}