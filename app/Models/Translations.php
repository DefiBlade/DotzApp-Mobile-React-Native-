<?php namespace App\Models;

use CodeIgniter\Model;

class Translations extends Model
{
    public function get_translations($tr_name)
    {
        $db = db_connect();
        if (!$db->tableExists('translations')) return;
        $tr = $db->table('translations');
        $translations = $tr->getWhere(['tr_name' => $tr_name])->getRow();

        if ($translations === NULL) {
            $trans_init = ["form_panel"=>"Add Visual", "main_panel"=>"Visuals"];
            $data = [
                'tr_name' => $tr_name,
                'tr_data' => json_encode($trans_init),
                'tr_lang' => 'english'
            ];
            $tr->insert($data);
            return $trans_init;
        } else {
            return json_decode($translations->tr_data,true);
        }
    }
}