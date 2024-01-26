<?php namespace App\Models;

use CodeIgniter\Model;

class UserRecordModel extends Model
{
    protected $table = 'user_records';
    protected $primaryKey = 'id';

    protected $allowedFields = ['app_user_id', 'user_routes', 'location_id', 'distance', 'pace', 'time', 'created_at'];
}
