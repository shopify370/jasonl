<?php



if (!defined('BASEPATH'))

    exit('No direct script access allowed');



class common extends CI_Model {



    function __construct() {

        parent::__construct();

    }



    function code($code) {

        $codes = array(

            '0000' => 'Error occured',

            '0001' => 'Success',

            '0002' => 'Staff id or password is incorrect',

            '0003' => 'Invalid issue type id',

            '0004' => 'Invalid base id',

            '0005' => 'Image upload error',

            '0006' => 'You are not logged in from this device',

            '0007' => 'User is inactive.',

            '0008' => 'Invalid Access Token',

            '0009' => 'You have selected more than 3 images',

            '0010' => 'A new password has been sent to your nominated email address.',

            '0011' => 'No Posts',

            '0012' => 'Invalid User',

            '0013' => 'Image not uploaded',

            '0014' => 'Email doesn\'t exists',

            '0015' => 'Invalid city id',

            '0016' => 'No file selected',

            '0017' => 'Invalid report id',

            '0018' => 'Invalid file type',
            
            '0019' => 'Already added your response',

            '0020' => 'No questions available',

            

                

        );

        return $codes[$code];

    }



    function insert_data($table_name, $data) {

        if (is_array($data) && !empty($data)) {

            $insrtdb = $this->load->database('default', TRUE);

            $result = $insrtdb->insert($table_name, $data);

            if ($result) {

                return $insrtdb->insert_id();

            }

        }

        return FALSE;

    }



    function insert_batch($table_name, $data) {

        if (is_array($data) && !empty($data)) {

            $insrtdb = $this->load->database('default', TRUE);

            $result = $insrtdb->insert_batch($table_name, $data);

        }

        return $result ? TRUE : FALSE;

    }



}

