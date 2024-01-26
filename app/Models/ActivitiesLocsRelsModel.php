<?php namespace App\Models;

use CodeIgniter\Model;

class ActivitiesLocsRelsModel extends Model
{
  protected $table = 'activities_locs_rels';
  protected $primaryKey = 'ac_loc_rel_id';

  protected $allowedFields = ['activity_id', 'loc_id'];
}