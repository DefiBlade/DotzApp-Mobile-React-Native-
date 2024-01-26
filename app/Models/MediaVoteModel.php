<?php namespace App\Models;

use CodeIgniter\Model;

class MediaVoteModel extends Model
{
    protected $table = 'media_votes';
    protected $primaryKey = 'id';

    protected $allowedFields = ['app_user_id', 'user_media_id'];
}
