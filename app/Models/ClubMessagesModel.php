<?php namespace App\Models;

use CodeIgniter\Model;

class ClubMessagesModel extends Model
{
	protected $table = 'club_messages';
	protected $primaryKey = 'id';

	protected $allowedFields = ['id', 'club_id', 'app_user_id', 'content', 'sent_at'];
}