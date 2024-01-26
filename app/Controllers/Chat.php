<?php namespace App\Controllers;

use App\Models\AppUserModel;
use App\Models\ChatHistoryModel;
use App\Models\SettingsChatModel;

class Chat extends BaseController
{
    private $db;

    public function __construct()
    {
		if (!session()->get('user_id')) return redirect()->to(base_url());
		$this->db = db_connect();
		if (!$this->db->tableExists('chat_history')) $this->installChatHistoryDb();
		if (!$this->db->tableExists('connections')) $this->installChatConnectionsDb();
	}
	
	public function index()
	{
		$appUserModel = new AppUserModel;
		$ChatHistoryModel = new ChatHistoryModel;
		$SettingsChatModel = new SettingsChatModel;
		$data['page'] = 'chat';
		$data['isAdmin'] = session()->get('role') === 'ADMIN' || session()->get('role') === 'SUPERADMIN' ? true : false;
		if (session()->get('role') === 'SUPERADMIN') $data['isSAdmin'] =  true;
		// $data['sidebar_data'] = $this->sidebar_data;

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
		echo view('pages/chat');
		echo view('templates/footer');
	}

	public function getChatHistory()
	{
		if ($this->request->isAJAX()) {
			$ChatHistoryModel = new ChatHistoryModel;
			$app_user_id = $this->request->getVar('app_user_id');

			$app_user_history = $ChatHistoryModel->where('author_id', $app_user_id)->orWhere('msg_reciever_id', $app_user_id)->findAll();
			$msg_ids_arr = [];
			foreach ($app_user_history as $key => $msg) {
				$msg_ids_arr[] = $msg['msg_id'];
			}
			$data = [
				'msg_status' => 'read'
			];
			$ChatHistoryModel->update($msg_ids_arr, $data);
			return json_encode($app_user_history);
		}
	}

	public function installChatHistoryDb()
	{
		$forge = \Config\Database::forge();
		$fields = [
			'msg_id'          => [
					'type'           => 'INT',
					'auto_increment' => true
			],
			'author_id'          => [
					'type'           => 'INT',
			],
			'author_name'       => [
					'type'           => 'VARCHAR',
					'constraint'     => 256,
			],
			'author_type'      => [
					'type'           =>'VARCHAR',
					'constraint'     => 256,
			],
			'message'      => [
					'type'           =>'TEXT',
					'constraint'     => 256,
			],
			'msg_status'      => [
					'type'           =>"ENUM('unread', 'read')",
					'default' => 'unread',
			],
			'msg_time_sent datetime default current_timestamp',
			'msg_timestamp_sent timestamp default current_timestamp',
			'msg_reciever_id'      => [
					'type'           =>"INT",
			],
		];
		$forge->addField($fields);
		$forge->addPrimaryKey('msg_id');
		$forge->createTable('chat_history', TRUE);
	}

	public function installChatConnectionsDb()
	{
		$forge = \Config\Database::forge();
		$fields = [
			'c_id'          => [
					'type'           => 'INT',
					'auto_increment' => true
			],
			'c_resource_id'          => [
					'type'           => 'INT',
			],
			'c_user_id'          => [
					'type'           => 'INT',
			],
			'c_name'       => [
					'type'           => 'VARCHAR',
					'constraint'     => 50,
			],
			'c_user_type'      => [
					'type'           =>'VARCHAR',
					'constraint'     => 30,
			],
		];
		$forge->addField($fields);
		$forge->addPrimaryKey('c_id');
		$forge->createTable('connections', TRUE);
	}

	//--------------------------------------------------------------------

}