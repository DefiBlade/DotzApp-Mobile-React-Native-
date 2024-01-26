<?php namespace App\Models;

use CodeIgniter\Model;

class LocationsPendingModel extends Model
{
    protected $table = 'locations_pending';
    protected $primaryKey = 'loc_p_id';
    
    protected $useSoftDeletes = true;
    
    protected $allowedFields = ['loc_p_id', 'loc_p_title', 'loc_p_cors_start', 'loc_p_cors_finish', 'loc_p_cors_all', 'loc_p_activity_id', 'loc_p_city'];
 

}