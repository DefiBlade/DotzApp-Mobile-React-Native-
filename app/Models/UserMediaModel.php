<?php namespace App\Models;

use CodeIgniter\Model;

class UserMediaModel extends Model
{
    protected $table = 'user_medias';
    protected $primaryKey = 'id';

    protected $allowedFields = ['app_user_id', 'media_id', 'like_cnt', 'share_cnt'];
}
