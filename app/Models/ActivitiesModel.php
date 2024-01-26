<?php namespace App\Models;

use CodeIgniter\Model;

class ActivitiesModel extends Model
{
  protected $table = 'activities';
  protected $primaryKey = 'activity_id';
    
  protected $useSoftDeletes = true;

  protected $allowedFields = ['activity_id', 'activity_name', 'activity_description', 'activity_img'];
 

}