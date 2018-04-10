<?php
class Activepage extends Admin_Controller {
	function __construct() {
		parent::__construct();	
        $this->load->model('campaign_data');
	}
	public function activeData(){
		$config = array();
        $config["base_url"] = base_url() . "campaign/activepage/activeData";
        $config["total_rows"] = $this->campaign_data->get_total_active('all');
        $config["per_page"] = 20;
        $config["uri_segment"] = 4;
        $config['use_page_numbers'] = TRUE;

        $this->pagination->initialize($config);
		$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$offset = $page==0? 0: ($page-1)*$config["per_page"];

		$data['errordata'] = $this->campaign_data->getAllData($config["per_page"], $offset, 'all');
		$data["links"] = $this->pagination->create_links();
		Template::render('activelist',$data);
	}

	public function activeError(){
		$config = array();
        $config["base_url"] = base_url() . "campaign/activepage/activeError";
        $config["total_rows"] = $this->campaign_data->get_total_active('error');
        $config["per_page"] = 20;
        $config["uri_segment"] = 4;
        $config['use_page_numbers'] = TRUE;

        $this->pagination->initialize($config);
		$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$offset = $page==0? 0: ($page-1)*$config["per_page"];

		$data['errordata'] = $this->campaign_data->getAllData($config["per_page"], $offset, 'error');
		$data["links"] = $this->pagination->create_links();
		Template::render('activelist',$data);
	}
	
	public function prontotoActive(){
		$config = array();
        $config["base_url"] = base_url() . "campaign/Activepage/prontotoActive";
        $config["total_rows"] = $this->campaign_data->get_total_pronto('all');
        $config["per_page"] = 20;
        $config["uri_segment"] = 4;
        $config['use_page_numbers'] = TRUE;

        $this->pagination->initialize($config);
		$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$offset = $page==0? 0: ($page-1)*$config["per_page"];

		$data['pronto_data'] = $this->campaign_data->getAllProntoData($config["per_page"], $offset, 'all');
		$data["links"] = $this->pagination->create_links();
		Template::render('prontolist',$data);
	}

	public function prontotoActiveErr(){
		$config = array();
        $config["base_url"] = base_url() . "campaign/Activepage/prontotoActiveErr";
        $config["total_rows"] = $this->campaign_data->get_total_pronto('error');
        $config["per_page"] = 20;
        $config["uri_segment"] = 4;
        $config['use_page_numbers'] = TRUE;

        $this->pagination->initialize($config);
		$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$offset = $page==0? 0: ($page-1)*$config["per_page"];

		$data['pronto_data'] = $this->campaign_data->getAllProntoData($config["per_page"], $offset, 'error');
		$data["links"] = $this->pagination->create_links();
		Template::render('prontolist',$data);
	}
	
	public function prontoacwebhook(){
		if ($this->input->post()) {
			if(isset($_POST['prontoacweb'])){
				$data_array=array();
				foreach($_POST['prontoacweb'] as $mypost){
					if($mypost!=''){
						$data_single['webhook_url']=$mypost;
						$data_array[]=$data_single;
					}
				}
				$this->campaign_data->insertwebhookdata('pronto_to_ac_webhook',$data_array);
			}
			else{
				$this->campaign_data->dropthetable('pronto_to_ac_webhook');
			}
		}

		$data['webook_data']=$this->campaign_data->getwebhookdata('pronto_to_ac_webhook');
		$data['pagetitle']='Pronto to AC Webhook';
		Template::render('prontowebhook', $data);
	}
	
	public function credentials(){
		if(isset($_POST['submit'])){
			$data=array();
			if($_POST['email']){ $email=$_POST['email']; } else{ $email=''; }
			$data['value']=$email;
			$this->campaign_data->webemaillist($data);
		}
		
		$data['emails'] = $this->campaign_data->getemaildata();
		Template::render('admindetail',$data);
	}
} ?>