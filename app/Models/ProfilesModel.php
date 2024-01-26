<?php namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\DataBase\BaseBuilder;

class ProfilesModel extends Model
{
	protected $table = 'profiles';
	protected $primaryKey = 'profile_id';

	protected $allowedFields = [
		'profile_id',
		'profile_img_ava',
		'profile_city',
		'profile_current_act',
		'profile_privacy_buble',
		'profile_favourite_locs',
		'profile_blast_record',
		'profile_verified',
		'profile_miles',
		'profile_club',
		'profile_rideout'
	];
 
	public function getByAppUserId($app_user_id)
	{
		$profile = $this->whereIn('profile_id', function(BaseBuilder $builder) use ($app_user_id) {
			return $builder->select('profile_id')->from('app_users_rels')->where('app_user_id', $app_user_id);
		})->first();

		return $profile;
	}
}