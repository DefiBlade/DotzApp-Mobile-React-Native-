<?php namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\DataBase\BaseBuilder;

class ProfilesPostsComments extends Model
{
	protected $table = 'profile_posts_comments';
	protected $primaryKey = 'ppc_id';

	protected $allowedFields = ['pp_id', 'ppc_content', 'ppc_id'];
}