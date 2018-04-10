<?php
class Order_data extends CI_Model {
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
}
?>