<?php
class Contact_data extends CI_Model {

	public function __construct(){
		$this->load->database();
	}

	public function savegetquote($database){
		$data=array();
		if(isset($database['query_type'])){ $data['query_type']=$database['query_type']; }
		if(isset($database['name'])){ $data['name']=$database['name']; }
		if(isset($database['email_address'])){ $data['email_address']=$database['email_address']; }
		if(isset($database['state'])){ $data['state']=$database['state']; }
		if(isset($database['phone_number'])){ $data['phone_number']=$database['phone_number']; }
		if(isset($database['message'])){ $data['message']=$database['message']; }
		$data['status']=0;

        $this->db->insert('get_a_quote',$data);
        $insert_id = $this->db->insert_id();
        return  $insert_id;
	}

	public function savecontactus($database){
		$data=array();
		if(isset($database['query_type'])){ $data['query_type']=$database['query_type']; }
		if(isset($database['name'])){ $data['name']=$database['name']; }
		if(isset($database['email_address'])){ $data['email_address']=$database['email_address']; }
		if(isset($database['state'])){ $data['state']=$database['state']; }
		if(isset($database['phone_number'])){ $data['phone_number']=$database['phone_number']; }
		if(isset($database['message'])){ $data['message']=$database['message']; }
		$data['status']=0;

        $this->db->insert('contact_us',$data);
        $insert_id = $this->db->insert_id();
        return  $insert_id;
	}

	public function getwebhookdata($tablename){
		$this->db->select("*");
		$this->db->from($tablename);
		$this->db->order_by("id", "asc");
		$query = $this->db->get();
		return $query->result();
	}

	public function insertcontactdata($tablename, $dataz){
		$this->db->empty_table($tablename);
		$this->db->insert_batch($tablename, $dataz); 
	}
	
	public function dropthetable($tablename){
		$this->db->empty_table($tablename);
	}

	public function insertcontactus($data){
		$this->db->insert('add_a_quote',$data);
		$insert_id = $this->db->insert_id();
        return  $insert_id;
	}
	
	public function contactproduct_data($data){
		$this->db->insert('contactproduct_data',$data);
		$insert_id = $this->db->insert_id();
        return  $insert_id;
	}

	public function Getwebhooklist($tablename){

	}

	public function saveerrors($data){
		$this->db->insert('webhook_error',$data);
	}

	public function geterrordata($tablename){
		$this->db->select("*");
		$this->db->from($tablename);
		$this->db->order_by("id", "desc");
		$query = $this->db->get();
		return $query->result();
	}
} ?>