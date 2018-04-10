<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require __DIR__.'/vendor/autoload.php';
//require __DIR__.'/vendor/phpish/shopify/shopify.php';
use phpish\shopify;
require __DIR__.'/conf.php';
class Find extends Base_Controller {
	public function __construct()
        {
                parent::__construct();
              //   $this->load->model('order_model');
                // $this->load->helper('url_helper');
        }
	//This is the function that is invoked by webhook in shopify
	public function getorder(){
		$orderid = $this->input->post('orderid');
        if($orderid){
           
        
		$shopify = shopify\client(SHOPIFY_SHOP, SHOPIFY_APP_API_KEY, SHOPIFY_APP_PASSWORD, true);
	try
	{ 

		 $order = $shopify('GET /admin/orders/'.$orderid.'.json', array(), array());
		 echo "<pre>";
		 print_r($order);die('yoi');
		// (
		// 	'redirect'=>array(
		// 		"path"=>"/1600l-x-700w-wenge-14088/--%3E",
		// 		"target"=> "/collections/office-desks/products/standing-office-desk-electric-height-adjustable-stand-up-1600l-x-800w",
		// 	)
		// ));

		//print_r($product);
	}
	catch (shopify\ApiException $e)
	{
		# HTTP status code was >= 400 or response contained the key 'errors'
		echo $e;
		print_R($e->getRequest());
		print_R($e->getResponse());
	}
	catch (shopify\CurlException $e)
	{
		# cURL error
		echo $e;
		print_R($e->getRequest());
		print_R($e->getResponse());
	}


		//you can get order details here from shopify
		//
		//die('you ra ');

		//After getting the data from shopify save it using the model defined above
		//$this->order_model->saveorder();

		//After data insertion is sucessful trigger the curl to send data to AX integration


		//check the response from AX integration and save the flag for success or failure 
		//to may be another model or the same previous model
        }
		
	}

    
}
