<?php namespace App\Models;

use CodeIgniter\Model;

class ProfilesPostsRelsModel extends Model
{
  protected $table = 'profile_posts_rels';
  protected $primaryKey = 'pp_rel_id';

  protected $allowedFields = ['profile_id', 'pp_rel_id', 'pp_id'];

}