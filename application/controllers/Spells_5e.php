<?php

class Spells_5e extends CI_Controller {


    function __construct() {

        parent::__construct();

        // Load user_model
        $this->load->model('spells_model_5e');


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
    //  Get Spells
    //
    ////////////////////////////////////////////////////////////////////////////////
    public function getspells()
    {

        $spellSlug = $this->input->get('spellSlug', TRUE);

        if ($spellSlug === 'all')
        {
            $data['spell'] = $this->spells_model_5e->get_spells(FALSE, TRUE);
        }
        else
        {
            // TODO: It is possible to request a spell slug that doesn't exist, I should handle that error rather than PHP barfing up a notice
            $data['spell'] = $this->spells_model_5e->get_spells($spellSlug, TRUE);
        }

        $this->output->cache(2*60); // hours * minutes
        $this->load->view('api/spells/5e/spellList_view', $data);

    }


}
