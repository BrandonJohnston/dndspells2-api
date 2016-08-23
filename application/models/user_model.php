<?php

class User_model extends CI_Model {

    public function __construct()
    {

    }


    ////////////////////////////////////////////////////////////////////////////////
    //
    // Attempt to add a new user
    //
    ////////////////////////////////////////////////////////////////////////////////
    public function user_add($user_name, $user_email, $user_password)
    {
        $data = array (
            'name'          => $user_name,
            'email'         => $user_email,
            'password'      => md5($user_password),
            'signup_date'   => time()
        );
        return $this->db->insert('users', $data);
    }


    ////////////////////////////////////////////////////////////////////////////////
    //
    // Attempt to login a user
    //
    ////////////////////////////////////////////////////////////////////////////////
    public function user_login($email, $password)
    {
        $this->db->select('id, email, name');
        $this->db->from('users');
        $this->db->where('email', $email);
        $this->db->where('password', md5($password));
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() === 1)
        {
            return $query->result();
        }
        else
        {
            return FALSE;
        }
    }

}
