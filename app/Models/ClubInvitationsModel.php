<?php namespace App\Models;

use CodeIgniter\Model;

class ClubInvitationsModel extends Model
{
	protected $table = 'club_invitations';
	protected $primaryKey = 'id';

	protected $allowedFields = ['id', 'club_id', 'app_user_id'];
}