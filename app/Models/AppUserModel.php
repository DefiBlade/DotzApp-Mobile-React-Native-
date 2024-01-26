<?php namespace App\Models;

use CodeIgniter\Model;

class AppUserModel extends Model{
  protected $table = 'app_users';
  protected $primaryKey = 'app_user_id';
    
  protected $useSoftDeletes = true;

  protected $allowedFields = ['email','password','app_user_name', 'bio', 'scope', 'authorisation', 'Date_of_Birth', 'Veh_size', 'Veh_year', 'Veh_photo', 'cors_current'];
  protected $beforeInsert = ['beforeInsert'];
  protected $beforeUpdate = ['beforeUpdate'];

  protected $createdField  = 'd_reged';
  protected $updatedField  = 'd_updated';

  protected function beforeInsert(array $data){
    $data = $this->passwordHash($data);
    $data['data']['d_reged'] = date('Y-m-d H:i:s');
    return $data;
  }

  protected function beforeUpdate(array $data){
    $data = $this->passwordHash($data);
    $data['data']['d_updated'] = date('Y-m-d H:i:s');
    return $data;
  }

  protected function passwordHash(array $data){
    if(isset($data['data']['password']) && $data['data']['password'] !== '' && isset($data['data']['scope']) && $data['data']['scope'] === 'app_pass') {
      $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
    }

    return $data;
  }
}