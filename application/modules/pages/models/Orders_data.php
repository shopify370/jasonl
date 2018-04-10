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

	public function getAllPronto(){
		$this->db->select("*");
		$this->db->from("prontostatus");
		$this->db->order_by("id", "desc");
		$query = $this->db->get();
		return $query->result();
	}

	public function getProntoDetail($prontoid){
		$this->db->select("*");
		$this->db->from("prontostatus");
		$this->db->where("id", $prontoid);
		$query = $this->db->get();
		return $query->result();
	}
	
	public function getAllProntoList($limit, $start){
		$this->db->select("*");
		$this->db->from("prontoresponselist");
		$this->db->order_by("id", "desc");
		$this->db->limit($limit, $start);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_total_prontolist(){
	    $this->db->select("*");
		$this->db->from("prontoresponselist");
		$query = $this->db->get();
		$rowcount = $query->num_rows();
		return $rowcount;
	}

	public function deliverydetail($prontoid){
		$this->db->select("*");
		$this->db->from("shipping_request");
		$this->db->where("insert_id", $prontoid);
		$query = $this->db->get();
		return $query->result();
	}
	
	public function getCustomerDetail($prontoid){
		$this->db->select("*");
		$this->db->from("customer_request");
		$this->db->where("insert_id", $prontoid);
		$query = $this->db->get();
		return $query->result();
	}
	
	public function insertGroup($id){
		$insertdata=array('user_id'=> $id, 'group_id'=>1);
		$this->db->insert('users_groups', $insertdata);
	}
}
?>