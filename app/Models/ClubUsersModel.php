<?php namespace App\Models;

use CodeIgniter\Model;

class ClubUsersModel extends Model
{
	protected $table = 'club_users';
	protected $primaryKey = 'id';

	protected $allowedFields = ['id', 'app_user_id', 'club_id'];
}