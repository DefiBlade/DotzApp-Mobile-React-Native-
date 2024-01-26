<?php namespace App\Models;

use CodeIgniter\Model;

class AppUserRelsModel extends Model
{
  protected $table = 'app_users_rels';
  protected $primaryKey = 'app_users_rel_id';
  
  protected $allowedFields = ['app_users_rel_id', 'app_user_id', 'profile_id'];
 

}