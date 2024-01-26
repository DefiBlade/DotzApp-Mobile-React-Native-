<?php namespace App\Models;

use CodeIgniter\Model;

class MediaCommentModel extends Model
{
    protected $table = 'media_comments';
    protected $primaryKey = 'id';

    protected $allowedFields = ['app_user_id', 'user_media_id', 'comment', 'parent_id'];
}
