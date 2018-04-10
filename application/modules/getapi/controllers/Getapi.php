<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Getapi extends Base_Controller {
	public function __construct()
        {
                parent::__construct();
                 $this->load->model('order_model');
                // $this->load->helper('url_helper');
        }
	//This is the function that is invoked by webhook in shopify
	public function saveorder(){
		//you can get order details here from shopify
		die('you ra ');

		//After getting the data from shopify save it using the model defined above
		$this->order_model->saveorder();

		//After data insertion is sucessful trigger the curl to send data to AX integration


		//check the response from AX integration and save the flag for success or failure 
		//to may be another model or the same previous model
		
	}

    
}
