<?php namespace App\Models;

use CodeIgniter\Model;

class LocationsModel extends Model
{
  protected $table = 'locations';
  protected $primaryKey = 'loc_id';

  protected $allowedFields = ['loc_id', 'loc_title', 'loc_cors_start', 'loc_cors_finish', 'loc_cors_all', 'loc_city', 'loc_rating', 'loc_records', 'approved'];
 

}