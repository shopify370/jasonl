<?php
class Admin extends Admin_Controller {
	var $data;
	function __construct() {
		parent::__construct();	
        $this->load->model('order_data');
		  $this->load->library("pagination");
	}

	public function index(){
		$config = array();
        $config["base_url"] = base_url() . "dashboard/admin/index";
        $config["total_rows"] = $this->order_data->get_total_prontolist();
        $config["per_page"] = 5;
        $config["uri_segment"] = 4;
        $config['use_page_numbers'] = TRUE;
        $config['suffix'] = '?'.http_build_query($_GET, '', "&");

        $this->pagination->initialize($config);
		$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$offset = $page==0? 0: ($page-1)*$config["per_page"];
		$data['orders'] = $this->order_data->getAllProntoList($config["per_page"], $offset);
		$data["links"] = $this->pagination->create_links();
        Template::render('admin/prontolisting',$data);
	}
}
?>