<?php namespace App\Controllers\Settings;

use App\Controllers\BaseController;
use App\Models\SettingsChatModel;

class Chat_s extends BaseController
{
    private $ChatModel;
    private $db;

    public function __construct()
    {
		if (!session()->get('user_id')) return redirect()->to(base_url());
		$this->db = db_connect();
        $this->ChatModel = new SettingsChatModel;
		if (!$this->db->tableExists('settings_chat')) $this->installChatSettingsDb();
    }

	public function index()
	{
        
		$data['page'] = 'settings_chat';
		$data['isAdmin'] = session()->get('role') === 'ADMIN' || session()->get('role') === 'SUPERADMIN' ? true : false;
		if (session()->get('role') === 'SUPERADMIN') $data['isSAdmin'] =  true;
        $data['sidebar_data'] = $this->sidebar_data;
        
		$data['chat_sets'] = $this->ChatModel->findAll();
		// $data['chat_sets'] = $this->ChatModel->select('cs_server_link, cs_server_port')->findAll();

		echo view('templates/header', $data);
		echo view('pages/chat_s');
		echo view('templates/footer');
	}

	public function update_chat_sets()
	{
		$chat_sets = $this->request->getVar('chat_sets');
		// var_dump($chat_sets);
		// die();
		foreach ($chat_sets as $chat_set) {
			foreach ($chat_set as $key => $value) {
				$data = array(
					'cs_id' => $chat_set['cs_id'],
					'cs_set_name' => $key,
					'cs_set_val' => $value,
				);
			}
			$this->ChatModel->save($data);
		}
            
		return redirect()->to(base_url('chat-sets'));
	}

	public function installChatSettingsDb()
	{
		$forge = \Config\Database::forge();
		$fields = [
			'cs_id'          => [
					'type'           => 'INT',
					'constraint'     => 11,
					'unsigned'       => true,
					'auto_increment' => true
			],
			'cs_set_name'       => [
					'type'           => 'VARCHAR',
					'constraint'     => 256,
					'unique'         => true,
			],
			'cs_set_val'      => [
					'type'           =>'VARCHAR',
					'constraint'     => 256,
			],
		];
		$forge->addField($fields);
		$forge->addPrimaryKey('cs_id');
		if ($forge->createTable('settings_chat', TRUE)) {
			$data_sets = ['chat_server_domain' => 'localhost', 'chat_server_port' => '8083'];
			foreach ($data_sets as $key => $value) {
				$data = array(
					'cs_set_name' => $key,
					'cs_set_val' => $value,
				);
				$this->ChatModel->save($data);
			}
		}
	}
}