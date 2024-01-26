<?php namespace App\Models;

use CodeIgniter\Model;

class MediaModel extends Model
{
    protected $table = 'medias';
    protected $primaryKey = 'id';

    protected $allowedFields = ['url', 'type', 'duration', 'created_at', 'updated_at'];
}
