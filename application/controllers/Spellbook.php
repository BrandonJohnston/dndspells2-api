<?php

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

class Spellbook extends CI_Controller {


    function __construct() {

        parent::__construct();

        // Load form helper library
        $this->load->helper('form');

        // Load form validation library
        $this->load->library('form_validation');

        // Load user_model
        $this->load->model('spellbook_model');

    }


    ////////////////////////////////////////////////////////////////////////////////
    //
    //  Create new spellbook
    //
    ////////////////////////////////////////////////////////////////////////////////
    public function create()
    {

        // Load our form into $_POST so validation will work
        $_POST = json_decode(file_get_contents("php://input"), true);

        // Validate form fields
        $this->form_validation->set_rules('char_name', 'char_name', 'required|trim|strip_tags');
        $this->form_validation->set_rules('char_class', 'char_class', 'required|trim|strip_tags');


        if ($this->form_validation->run() === FALSE)
        {
            // Form validation failed, pass errors back
            $data['response'] = array(
                'validate' => FALSE,
                'nameError' => strip_tags(form_error('char_name')),
                'classError' => strip_tags(form_error('char_class'))
            );
        }
        else
        {

            $user_id = $this->input->post('id');
            $char_name = $this->input->post('char_name');
            $char_class = $this->input->post('char_class');
            $private = $this->input->post('private');
            $spells = $this->input->post('spells');

            // Send data to model for addition to database
            $spellbookId = $this->spellbook_model->spellbook_add($user_id, $char_name, $char_class, $private, $spells);

            $data['response'] = array(
                'validate' => TRUE,
                'spellbookID' => $spellbookId
            );

        }


        // Load the response into view
        $this->load->view('api/spellbook/create_view', $data);

    }

}
