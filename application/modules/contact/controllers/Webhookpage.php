<?php 
class Webhookpage extends Admin_Controller {
	function __construct() {
		parent::__construct();	
        $this->load->model('contact_data');
	}

	public function contactwebhook(){
		if ($this->input->post()) {
			if(isset($_POST['contactweb'])){
				$data_array=array();
				foreach($_POST['contactweb'] as $mypost){
					if($mypost!=''){
						$data_single['webhook_url']=$mypost;
						$data_array[]=$data_single;
					}
				}
				$this->contact_data->insertcontactdata('contact_webhook',$data_array);
			}
			else{
				$this->contact_data->dropthetable('contact_webhook');
			}
		}

		$data['webook_data']=$this->contact_data->getwebhookdata('contact_webhook');
		$data['pagetitle']='Contact Us';
		Template::render('contactwebhook', $data);
	}
	
	public function product_contactwebhook(){
		if ($this->input->post()) {
			if(isset($_POST['contactweb'])){
				$data_array=array();
				foreach($_POST['contactweb'] as $mypost){
					if($mypost!=''){
						$data_single['webhook_url']=$mypost;
						$data_array[]=$data_single;
					}
				}
				$this->contact_data->insertcontactdata('productcontact_webhook',$data_array);
			}
			else{
				$this->contact_data->dropthetable('productcontact_webhook');
			}
		}

		$data['webook_data']=$this->contact_data->getwebhookdata('productcontact_webhook');
		$data['pagetitle']='Contact Product';
		Template::render('contactwebhook', $data);
	}

	public function getquotewebhook(){
		if ($this->input->post()) {
			if(isset($_POST['contactweb'])){
				$data_array=array();
				foreach($_POST['contactweb'] as $mypost){
					if($mypost!=''){
						$data_single['webhook_url']=$mypost;
						$data_array[]=$data_single;
					}
				}
				$this->contact_data->insertcontactdata('get_quote_webhook',$data_array);
			}
			else{
				$this->contact_data->dropthetable('get_quote_webhook');
			}
		}

		$data['webook_data']=$this->contact_data->getwebhookdata('get_quote_webhook');
		$data['pagetitle']='Get a Quote';
		Template::render('contactwebhook', $data);
	}

	public function addquotewebhook(){
		if ($this->input->post()) {
			if(isset($_POST['contactweb'])){
				$data_array=array();
				foreach($_POST['contactweb'] as $mypost){
					$data_single['webhook_url']=$mypost;
						$data_array[]=$data_single;

				}
				$this->contact_data->insertcontactdata('add_quote_webhook',$data_array);
			}
			else{
				$this->contact_data->dropthetable('add_quote_webhook');
			}
		}

		$data['webook_data']=$this->contact_data->getwebhookdata('add_quote_webhook');
		$data['pagetitle']='Add a Quote';
		Template::render('contactwebhook', $data);
	}

	public function errorloging(){
		$data['error_data']=$this->contact_data->geterrordata('webhook_error');
		Template::render('errorlist', $data);
	}

} ?>