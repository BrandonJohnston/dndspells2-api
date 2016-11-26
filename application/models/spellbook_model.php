<?php

class Spellbook_model extends CI_Model {

    public function __construct()
    {

    }


    ////////////////////////////////////////////////////////////////////////////////
    //
    // Insert new spellbook into database
    //
    ////////////////////////////////////////////////////////////////////////////////
    public function spellbook_add($user_id, $char_name, $char_class, $private, $spells)
    {

        $spellsString = implode(',', $spells);

        $data = array (
            'user_id' => $user_id,
            'char_name' => $char_name,
            'char_class' => $char_class,
            'private' => $private,
            'spells' => $spellsString
        );

        $this->db->insert('spellbooks', $data);
        return $this->db->insert_id();

    }

}
