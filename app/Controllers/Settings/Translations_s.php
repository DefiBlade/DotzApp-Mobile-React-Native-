<?php namespace App\Controllers\Settings;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Translations_s extends BaseController
{
	public function index()
	{
		if ( ! is_file(APPPATH.'/Views/pages/settings.php'))
		{
			// Whoops, we don't have a page for that!
			throw new \CodeIgniter\Exceptions\PageNotFoundException('settings');
		}
		$data['page'] = 'settings';
		$data['isAdmin'] = session()->get('role') === 'ADMIN' || session()->get('role') === 'SUPERADMIN' ? true : false;
		if (session()->get('role') === 'SUPERADMIN') $data['isSAdmin'] =  true;
		if ($data['isSAdmin']) {
			$data['Translations']['Login'] = include(APPPATH.'Language/en/Login.php');

			$data['Translations']['Activities'] = include(APPPATH.'Language/en/Activities_lang_en.php');
			$data['Translations']['Clubs'] = include(APPPATH.'Language/en/Clubs_lang_en.php');
			$data['Translations']['profiles'] = include(APPPATH.'Language/en/profiles_lang_en.php');
			$data['Translations']['locations'] = include(APPPATH.'Language/en/locations_lang_en.php');
			$data['Translations']['locs_pending'] = include(APPPATH.'Language/en/locs_pending_lang_en.php');
			$data['Translations']['location_create'] = include(APPPATH.'Language/en/location_create.php');
			$data['Translations']['Chat'] = include(APPPATH.'Language/en/Chat.php');
			$data['Translations']['dotz_settings'] = include(APPPATH.'Language/en/dotz_settings_lang_en.php');

			$data['Translations']['Register'] = include(APPPATH.'Language/en/Register.php');

			$data['Translations']['Sidebar'] = include(APPPATH.'Language/en/Sidebar.php');
			$data['Translations']['TopBar'] = include(APPPATH.'Language/en/TopBar.php');
		}
		
		$db = db_connect();
		if ($db->tableExists('posts')) {
			$builder = $db->table('posts');
			$posts = $builder->get();
		}
		$data['postsCount'] = isset($builder) ? $builder->countAllResults() : 0;
		$data['posts'] = isset($posts) ? $posts->getResultArray() : 'No posts table found.';

		echo view('templates/header', $data);
		echo view('pages/settings', $data);
		echo view('templates/footer', $data);
	}

	public function update_translations()
	{
		// var_dump($this->request->getVar('translations'));
		// die();
        $db = db_connect();
        if (!$db->tableExists('translations')) return;
        $tr = $db->table('translations');
		$translations = $this->request->getVar('translations');
		foreach ($translations as $tr_name => $tr_data) {
			$data = array(
				'tr_data' => json_encode($tr_data),
				'tr_lang' => 'english',
			);
			$tr->where('tr_name', $tr_name);
			$tr->update($data);
		}
            
		return redirect()->to(base_url('/settings'));
	}

	public function create_translations()
	{
		$input = $this->request->getPost();
        $db = db_connect();
        if (!$db->tableExists('translations')) return;
        $tr = $db->table('translations');
		$translations = $tr->getWhere(['tr_name' => $input['tr_name']])->getRow();
		$tr_data = json_decode($translations->tr_data,true);
		$tr_data[$input['new_key']] = $input['new_value'];
		
		$data = array(
			'tr_name' => $input['tr_name'],
			'tr_data' => json_encode($tr_data),
			'tr_lang' => 'english',
		);
		$tr->where('tr_name', $input['tr_name']);
		$tr->update($data);
            
		return redirect()->to(base_url('/settings'));
	}
}