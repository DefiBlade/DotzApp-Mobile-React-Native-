<?php namespace App\Models;

use CodeIgniter\Model;

class ChatHistoryModel extends Model{
  protected $table = 'chat_history';
  protected $primaryKey = 'msg_id';
  protected $allowedFields = ["msg_id", "author_id", "author_name", "author_type", "message", "msg_time_sent", "msg_reciever_id", "msg_status", "request_status"];
 

}