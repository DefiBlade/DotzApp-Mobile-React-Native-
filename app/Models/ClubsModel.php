<?php namespace App\Models;

use CodeIgniter\Model;

class ClubsModel extends Model
{
	protected $table = 'clubs';
	protected $primaryKey = 'club_id';

	protected $useSoftDeletes = true;

	protected $allowedFields = ['club_id', 'club_name', 'club_description', 'club_img', 'owner_user_id'];
}