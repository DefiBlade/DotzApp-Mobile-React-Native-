<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    
    protected $useSoftDeletes = true;

    protected $allowedFields = ['email', 'nice_name', 'role', 'pass', 'd_updated'];
    protected $beforeInsert = ['beforeInsert'];
    protected $beforeUpdate = ['beforeUpdate'];

    protected $createdField  = 'd_reged';
    protected $updatedField  = 'd_updated';

    protected function beforeInsert(array $data)
    {
        $data = $this->passwordHash($data);
        $data['data']['d_reged'] = date('Y-m-d H:i:s');

        return $data;
    }

    protected function beforeUpdate(array $data)
    {
        $data = $this->passwordHash($data);
        $data['data']['d_updated'] = date('Y-m-d H:i:s');
        return $data;
    }

    protected function passwordHash(array $data)
    {
        if (isset($data['data']['pass']))
            $data['data']['pass'] = password_hash($data['data']['pass'], PASSWORD_DEFAULT);

        return $data;
    }
}
