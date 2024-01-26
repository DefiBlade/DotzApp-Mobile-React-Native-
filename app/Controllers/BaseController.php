<?php
namespace App\Controllers;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */

use CodeIgniter\Controller;

class BaseController extends Controller
{

	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	protected $helpers = [];

	/**
	 * Constructor.
	 */
	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);
		$session = \Config\Services::session();
		$language = \Config\Services::language();
		$language->setLocale($session->lang);

		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		// E.g.:
		// $this->session = \Config\Services::session();
		
		$db = db_connect();
		// if ($db->tableExists('posts')) $posts_builder = $db->table('posts');
		// if ($db->tableExists('quotes')) $quotes_builder = $db->table('quotes');
		// if ($db->tableExists('visuals')) $visuals_builder = $db->table('visuals');
		// if ($db->tableExists('visuals_cats')) $visuals_cats_builder = $db->table('visuals_cats');

		$this->sidebar_data['posts'] = isset($posts_builder) ? $posts_builder->countAllResults() : 0;
		$this->sidebar_data['quotes'] = isset($quotes_builder) ? $quotes_builder->countAllResults() : 0;
		$this->sidebar_data['visuals'] = isset($visuals_builder) ? $visuals_builder->countAllResults() : 0;
		$this->sidebar_data['visuals_cats'] = isset($visuals_cats_builder) ? $visuals_cats_builder->countAllResults() : 0;
	}

}
