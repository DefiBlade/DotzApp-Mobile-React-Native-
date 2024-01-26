<?php namespace App\Models;

use CodeIgniter\Model;

class ConnectionsModel extends Model{
  protected $table = 'connections';
	protected $primaryKey = 'c_id';
  protected $allowedFields = ['c_user_id', 'c_resource_id', 'c_name', 'c_user_type', 'c_user_profile', 'my_cur_loc'];
 

}