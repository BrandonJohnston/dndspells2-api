<?php

class User extends CI_Controller {


    function __construct() {

        parent::__construct();

        // Load form helper library
        $this->load->helper('form');

        // Load form validation library
        $this->load->library('form_validation');

        // Load user_model
        $this->load->model('user_model');


        //
        // Cross site headers - allow access to API from a different domain
        //
        header("Access-Control-Allow-Credentials: true");
        header('Access-Control-Allow-Origin: http://127.0.0.1:8080');
        header("Access-Control-Allow-Headers: Origin, X-CSRF-Token, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

        if ( "OPTIONS" === $_SERVER['REQUEST_METHOD'] ) {
            die();
        }

    }


    ////////////////////////////////////////////////////////////////////////////////
    //
    //  Signup logic
    //
    ////////////////////////////////////////////////////////////////////////////////
    public function signup()
    {

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
                'required'      => 101,
                'is_unique'     => 102,
                'valid_email'   => 103
            )
        );

        $this->form_validation->set_rules(
            'password',
            'Password',
            'required|trim|strip_tags',
            array(
                'required'  => 104
            )
        );

        if ($this->form_validation->run() === FALSE)
        {

            $errorCodes = intval(strip_tags(validation_errors()));

            // Form Validation Failed, return error to user
            // TODO: figure out error codes
            //$this->output->set_status_header('400');
            $data['response'] = array(
                'loggedin' => FALSE,
                'error' => $errorCodes
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
                foreach($result as $row)
                {

                    $this->session->set_userdata('name', $row->name);
                    $this->session->set_userdata('id', $row->id);
                    $this->session->set_userdata('email', $row->email);
                    $this->session->set_userdata('loggedin', TRUE);

                    $data['response'] = array(
                        'name'      => $row->name,
                        'id'        => $row->id,
                        'email'     => $row->email,
                        'loggedin'  => TRUE
                    );

                }

            }
            else
            {

                // We did not find an email / password pair, return an error
                $data['response'] = array(
                    'loggedin' => FALSE,
                    'error' => 105
                );

            }

        }

        $this->load->view('api/user/signup_view', $data);

    }


    ////////////////////////////////////////////////////////////////////////////////
    //
    //  login - attempts to log in a user
    //
    ////////////////////////////////////////////////////////////////////////////////
    public function login()
    {

        // Load our form into $_POST so validation will work
        $_POST = json_decode(file_get_contents("php://input"), true);

        // Validate form fields
        $this->form_validation->set_rules(
            'email',
            'Email',
            'required|trim|strip_tags',
            array(
                'required'      => 101
            )
        );

        $this->form_validation->set_rules(
            'password',
            'Password',
            'required|trim|strip_tags',
            array(
                'required'  => 104
            )
        );


        if ($this->form_validation->run() === FALSE)
        {

            $errorCodes = intval(strip_tags(validation_errors()));

            // Form Validation Failed, return error to user
            $data['response'] = array(
                'loggedin' => FALSE,
                'error' => $errorCodes
            );

        }
        else
        {

            // Form Validation Success
            $user = json_decode(file_get_contents("php://input"), true);

            // Create the users session
            $user_email = $user['email'];
            $user_password = $user['password'];

            // query the database
            $result = $this->user_model->user_login($user_email, $user_password);

            if ($result)
            {

                // We found an email / password pair, create a session
                foreach($result as $row)
                {

                    $this->session->set_userdata('name', $row->name);
                    $this->session->set_userdata('id', $row->id);
                    $this->session->set_userdata('email', $row->email);
                    $this->session->set_userdata('loggedin', TRUE);

                    $data['response'] = array(
                        'name'      => $row->name,
                        'id'        => $row->id,
                        'email'     => $row->email,
                        'loggedin'  => TRUE
                    );

                }

            }
            else
            {

                // We did not find an email / password pair, return an error
                $data['response'] = array(
                    'loggedin' => FALSE,
                    'error' => 105
                );

            }

        }

        $this->load->view('api/user/login_view', $data);

    }


    ////////////////////////////////////////////////////////////////////////////////
    //
    //  currentUser - returns data about the current user session
    //
    ////////////////////////////////////////////////////////////////////////////////
    public function logout()
    {

        $this->session->sess_destroy();
        $newdata = array(
            'id'        => '',
            'email'     => '',
            'loggedin' => FALSE,
        );
        $this->session->unset_userdata($newdata);
        $data['logout'] = array(
            'loggedin' => FALSE
        );
        $this->load->view('api/user/logout_view', $data);

    }


    ////////////////////////////////////////////////////////////////////////////////
    //
    //  currentUser - returns data about the current user session
    //
    ////////////////////////////////////////////////////////////////////////////////
    public function currentUser()
    {



        $user = $this->session->all_userdata();

        if ($user['loggedin']) {
            $data['response'] = array(
                'name'      => $this->session->userdata('name'),
                'id'        => $this->session->userdata('id'),
                'email'     => $this->session->userdata('email'),
                'loggedin' => TRUE
            );
        }
        else
        {
            $data['response'] = array(
                'loggedin' => FALSE,
                'error' => 'You are not logged in.'
            );
        }
        $this->load->view('api/user/currentuser_view', $data);

    }

}
