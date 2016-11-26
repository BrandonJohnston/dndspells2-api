<?php

class Spells_model_5e extends CI_Model {

    public function __construct()
    {

    }


    ////////////////////////////////////////////////////////////////////////////////
    //
    // Get spells data and format
    //
    ////////////////////////////////////////////////////////////////////////////////
    public function get_spells($slug = FALSE, $api = FALSE)
    {

        // Is this a call from the API controller function?
        if($api === TRUE)
        {
            // Did the request ask for a specific spell?
            if($slug === FALSE)
            {
                // There is no slug, so get the entire spell list
                $query = $this->db->get('spells_5e');
                $data = $query->result_array();

                // Convert class string to an array & bool values to true/false
                foreach ($data as $key => $value)
                {
                    $spellClassesArr = explode(',', $value['classes']);
                    $data[$key]['classes'] = $spellClassesArr;

                    $data[$key]['concentration'] = $value['concentration'] == 0 ? false : true;
                    $data[$key]['ritual'] = $value['ritual'] == 0 ? false : true;
                    $data[$key]['material'] = $value['material'] == 0 ? false : true;
                    $data[$key]['somatic'] = $value['somatic'] == 0 ? false : true;
                    $data[$key]['verbal'] = $value['verbal'] == 0 ? false : true;
                }

                return $data;
            }
            else
            {
                // There is a slug, so get only the requested spell
                $query = $this->db->get_where('spells_5e', array('slug' => $slug));
                $data = $query->row_array();

                // Convert class string to an array
                $spellClassesArr = explode(',', $data['classes']);
                $data['classes'] = $spellClassesArr;

                return $data;
            }
        }
        else
        {
            // This is not an API CALL
            if($slug === FALSE)
            {
                $query = $this->db->get('dnd_spell_list_5e');
                return $query->result_array();
            }

            $query = $this->db->get_where('dnd_spell_list_5e', array('slug' => $slug));
            return $query->row_array();
        }

    }

}
