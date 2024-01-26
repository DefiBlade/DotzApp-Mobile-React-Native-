<?php namespace App\Models;

use CodeIgniter\Model;

class OauthTokenModel extends Model{
  protected $table = 'oauth_access_tokens';
  protected $allowedFields = ['access_token','client_id','user_id','expires','scope'];
}