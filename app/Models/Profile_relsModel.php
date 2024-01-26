<?php namespace App\Models;

use CodeIgniter\Model;

class Profile_relsModel extends Model
{
  protected $table = 'profile_rels';
  protected $primaryKey = 'profile_rel_id';

  protected $allowedFields = ['profile_id', 'app_user_id', 'profile_rel_status'];

}