<?php namespace App\Libraries;

//use \OAuth2\Storage\Pdo;
use \OAuth2\Storage\Memory;
use \OAuth2\GrantType\ClientCredentials;
// use \OAuth2\GrantType\AuthorizationCode;
use \App\Libraries\CustomOauthStorage;


class Oauth{
  var $server;

  function __construct(){
    $this->init();
  }

  public function init(){
    $dsn = getenv('database.default.DSN');
    $username = getenv('database.default.username');
    $password = getenv('database.default.password');

    // create test clients in memory
    // $clients = array('TestClient' => array('client_secret' => 'TestSecret'),'TestClientT' => array('client_secret' => 'TestSecret'));

    // create a storage object
    // $cc_storage = new Memory(array('client_credentials' => $clients));
    $storage = new CustomOauthStorage(['dsn' => $dsn, 'app_user_name' => $username, 'password' => $password]);

    // create the grant type
    // $cc_grantType = new ClientCredentials($cc_storage, array(
      // 'allow_credentials_in_request_body' => false
    // ));

    //Server config
    $config = array(
      'access_lifetime' => 31536000
    );
    
    $this->server = new \OAuth2\Server($storage, $config);
    $this->server->addGrantType(new \OAuth2\GrantType\UserCredentials($storage));
    // $this->server->addGrantType($cc_grantType);
  }
}