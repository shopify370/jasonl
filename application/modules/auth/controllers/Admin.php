<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->lang->load('auth');
        $this->load->model('ion_auth_model');
       	
    }
}
