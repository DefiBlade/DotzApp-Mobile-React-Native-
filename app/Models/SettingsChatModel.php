<?php namespace App\Models;

use CodeIgniter\Model;

class SettingsChatModel extends Model{
  protected $table = 'settings_chat';
  protected $primaryKey = 'cs_id';
  protected $allowedFields = ['cs_id', 'cs_set_name', 'cs_set_val'];
 

}