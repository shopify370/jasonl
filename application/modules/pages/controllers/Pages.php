<?php
class Pages extends Admin_Controller {
	function __construct() {
		parent::__construct();	
        $this->load->model('orders_data');
	}

	public function index(){
		$data['orders'] = $this->orders_data->getAllOrders();
        Template::render('orderlist',$data);
	}
	
	public function createuser(){
		Template::render('adduser');
	}

	public function createuser_save(){
		$data='';
		$this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
		if ($this->form_validation->run() == TRUE) {
			
           $data = array(
			'first_name' => $this->input->post('first_name'),
			'last_name' => $this->input->post('last_name')
		);
		$id=$this->ion_auth->register($this->input->post('first_name'),$this->input->post('password'),$this->input->post('email_address'), $data);
		if($id){
			$this->orders_data->insertGroup($id);
			$data['message']="New user created successfully";
		}
           Template::render('adduser', $data);
        }
        else{
			Template::render('adduser');
        }
	}

	public function change_password(){
		$password=$this->input->post('password');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required');
		if ($this->form_validation->run() == TRUE) {  session_destroy();
			$user=$this->ion_auth->user()->row();
			$email=$user->email;
			$id=$this->ion_auth->reset_password($email, $password);
			redirect('admin/login');
        	Template::render('changepassword');
        }
        else{
        	Template::render('changepassword');
        }
	}

	public function orderdetail(){
		$delivery_detail=  ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data['orderdetail'] = $this->orders_data->getOrderDetail($delivery_detail);
		Template::render('orderdetail',$data);
	}

	public function pronto(){
		$data['pronto'] = $this->orders_data->getAllPronto();
        Template::render('prontolist',$data);
	}

	public function prontodetail(){
		$pronto_detail=  ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data['prontodetail'] = $this->orders_data->getProntoDetail($pronto_detail);
		$data['deliverydetail'] = $this->orders_data->deliverydetail($pronto_detail);
		$data['customerdetail'] = $this->orders_data->getCustomerDetail($pronto_detail);
		Template::render('prontodetail',$data);
	}
	
	public function orderList(){
		$config = array();
        $config["base_url"] = base_url() . "pages/orderList";
        $config["total_rows"] = $this->orders_data->get_total_prontolist();
        $config["per_page"] = 20;
        $config["uri_segment"] = 3;
        $config['use_page_numbers'] = TRUE;
        //$config['suffix'] = '?'.http_build_query($_GET, '', "&");

        $this->pagination->initialize($config);
		$page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$offset = $page==0? 0: ($page-1)*$config["per_page"];
		$data['orders'] = $this->orders_data->getAllProntoList($config["per_page"], $offset);
		$data["links"] = $this->pagination->create_links();
        Template::render('prontolisting',$data);
	}
	
}
?>