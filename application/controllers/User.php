<?php

class User extends CI_Controller {


    ////////////////////////////////////////////////////////////////////////////////
    //
    //  Signup logic
    //
    ////////////////////////////////////////////////////////////////////////////////
    public function signup()
    {

        // Load form helper library
        $this->load->helper(array('form', 'url'));

        // Load form validation library
        $this->load->library('form_validation');

        // Load user_model
        $this->load->model('user_model');


        // Load our form into $_POST so validation will work
        $_POST = json_decode(file_get_contents("php://input"), true);

        // Validate form fields
        $this->form_validation->set_rules(
            'name',
            'Name',
            'required|trim|strip_tags',
            array(
                'required'  => 100
            )
        );
        $this->form_validation->set_rules(
            'email',
            'Email',
            'required|valid_email|is_unique[users.email]|trim|strip_tags',
            array(
                'required'      => 100,
                'is_unique'     => 101,
                'valid_email'   => 102
            )
        );
        $this->form_validation->set_rules(
            'password',
            'Password',
            'required|trim|strip_tags',
            array(
                'required'  => 100
            )
        );

        if ($this->form_validation->run() === FALSE)
        {

            // Form Validation Failed, return error to user
            // TODO: figure out error codes
            $this->output->set_status_header('400');
            $data['response'] = array(
                'authorized' => FALSE,
                'error' => TRUE
            );

        }
        else
        {

            // Form Validation Success, Add the posted form data to the database as a new user
            $user_name = $this->input->post('name');
            $user_email = $this->input->post('email');
            $user_password = $this->input->post('password');

            $this->user_model->user_add($user_name, $user_email, $user_password);


            // query the database
            $result = $this->user_model->user_login($user_email, $user_password);

            if ($result)
            {

                // We found an email / password pair, create a session
                $sess_array = array();
                foreach($result as $row)
                {
                    $sess_array = array(
                        'name'      => $row->name,
                        'id'        => $row->id,
                        'email'     => $row->email,
                        'logged_in' => TRUE
                    );

                    $this->session->set_userdata($sess_array);
                }

                $data['response'] = array(
                    'authorized'    => TRUE,
                    'userId'        => $sess_array['id'],
                    'userName'      => $sess_array['name']
                );

            }
            else
            {

                // We did not find an email / password pair, return an error
                $data['response'] = array(
                    'authorized' => FALSE,
                    'error' => TRUE
                );

            }

        }

        $this->load->view('api/user/signup_view', $data);

    }

}

?>
