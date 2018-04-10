<?php
class Orders_data extends CI_Model {
	public function __construct(){
			$this->load->database();
	}

	public function getOrderData(){
		$this->db->select("*");
		$this->db->from("skuorder");
		$this->db->order_by("id", "desc");
		$query = $this->db->get();
		return $query->result();
	}

	public function getAllOrders(){
		$this->db->select("*");
		$this->db->from("orderlist");
		$this->db->order_by("id", "desc");
		$query = $this->db->get();
		return $query->result();
	}
	
	public function getOrderDetail($orderid){
		$this->db->select("*");
		$this->db->from("orderlist");
		$this->db->where("id", $orderid);
		$query = $this->db->get();
		return $query->result();
		
	}
}