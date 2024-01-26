<?php namespace App\Models;

use CodeIgniter\Model;

class Prof_act_relsModel extends Model
{
  protected $table = 'prof_act_rels';
  protected $primaryKey = 'prof_act_id';

  protected $allowedFields = ['prof_act_id', 'profile_id', 'activity_id'];

}