<?php
class Cre_data extends CI_Model {
	public function __construct(){
			$this->load->database();
	}

	public function getAllCredentials(){
		$this->db->select("*");
		$this->db->from("credential");
		$this->db->where("id", 1);
		$query = $this->db->get();
		return $query->row_array();
	}

	public function insertOrUpdate($data){
		$this->db->select("*");
		$this->db->from("credential");
		$this->db->where("id", 1);
		$query = $this->db->get();
		$returned_result=$query->row_array();
		
		if($returned_result){
			$this->db->where("id",1);
			$this->db->update('credential', $data);
		}
		else{
			 $this->db->insert('credential', $data);
		}
	}
}