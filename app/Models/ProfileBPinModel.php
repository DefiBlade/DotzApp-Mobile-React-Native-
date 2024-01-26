<?php namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\DataBase\BaseBuilder;

class ProfileBPinModel extends Model
{
	protected $table = 'profile_bpin_event';
	protected $primaryKey = 'bPin_ev_id';

	protected $allowedFields = ['bPin_ev_id', 'bPin_ev_author', 'bPin_msg', 'bPin_cors', 'bPin_ev_expires_at','bPin_ev_joiners'];
}