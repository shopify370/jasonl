<?php
class Orders extends Admin_Controller {
	function __construct() {
		parent::__construct();	
        $this->load->model('orders_data');
	}

	public function index(){
		$data['orders'] = $this->orders_data->getAllOrders();
        Template::render('orderlist',$data);
	}

	public function orderdetail(){
		$delivery_detail=  ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
		$data['orderdetail'] = $this->orders_data->getOrderDetail($delivery_detail);
		Template::render('orderdetail',$data);
	}
}
?>