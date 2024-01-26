<?php namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\DataBase\BaseBuilder;

class ProfilesPostsModel extends Model
{
	protected $table = 'profile_posts';
	protected $primaryKey = 'pp_id';

	protected $allowedFields = ['pp_id', 'pp_content', 'pp_type', 'pp_likes'];
 
	public function getByProfileId($profile_id)
	{
		$posts = $this->whereIn('pp_id', function(BaseBuilder $builder) use ($profile_id) {
			return $builder->select('pp_id')->from('profile_posts_rels')->where('profile_id', $profile_id);
		})->findAll();

		return $posts;
	}
}