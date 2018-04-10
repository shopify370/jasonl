<?php
class Campaign_data extends CI_Model {

	public function __construct(){
		$this->load->database();
	}

	public function savecampaign($database){
		$data=array();
		if(isset($database['email'])){ $data['email']=$database['email']; }
		if(isset($database['first_name'])){ $data['first_name']=$database['first_name']; }
		if(isset($database['last_name'])){ $data['last_name']=$database['last_name']; }
		if(isset($database['phone'])){ $data['phone']=$database['phone']; }
		if(isset($database['orgname'])){ $data['orgname']=$database['orgname']; }
		if(isset($database['not_in_pronto'])) { $data['not_in_pronto']=$database['not_in_pronto']; } else{ $data['not_in_pronto']=0; }
		if(isset($database['fields'])){
			if(isset($database['fields']['postcode'])) {
				$data['postcode']=$database['fields']['postcode']; 
			}
			if(isset($database['fields']['state'])) {
				$data['address']=$database['fields']['state']; 
			}
		}
		
        $this->db->insert('campaign',$data);
        $insert_id = $this->db->insert_id();
        return  $insert_id;
	}
	
	public function savecampaigncron($database, $insert_id){
		$data=array();
		if(isset($database['email'])){ $data['email']=$database['email']; }
		if(isset($database['first_name'])){ $data['first_name']=$database['first_name']; }
		if(isset($database['last_name'])){ $data['last_name']=$database['last_name']; }
		if(isset($database['phone'])){ $data['phone']=$database['phone']; }
		if(isset($database['orgname'])){ $data['orgname']=$database['orgname']; }
		if(isset($database['fields'])){
			if(isset($database['fields']['postcode'])) {
				$data['postcode']=$database['fields']['postcode']; 
			}
			if(isset($database['fields']['state'])) {
				$data['address']=$database['fields']['state']; 
			}
		}
		if($insert_id){ $data['insert_id']=$insert_id; } else { $data['insert_id']=0; }
		
        $this->db->insert('camp_to_pronto_cron',$data);
        $insert_id = $this->db->insert_id();
        return  $insert_id;
	}

	public function getAllCredential(){
		$this->db->select("*");
		$this->db->from("credential");
		$this->db->where("id", 1);
		$query = $this->db->get();
		return $query->row_array();
	}
	
	public function saveerror($errorlist){
		$this->db->insert('campaign_error',$errorlist);
	}
	
	public function checkifprontosynced($email){
		$this->db->select('*');
		$this->db->from("pronto_email");
		$this->db->where('email_add',$email);
		$query = $this->db->get();

		if(empty($query->result())) {
			return true;
		} else {
			return false;
		}
	}
	
	public function getAllData($limit, $start, $value){
		$this->db->select('*');
		$this->db->from('campaign');
		if($value=='all'){ 
			$this->db->join('campaign_error', 'campaign.id = campaign_error.insert_id', 'left'); 
		}
		else { 
			$this->db->join('campaign_error', 'campaign.id = campaign_error.insert_id', 'inner'); 
		}
		$this->db->order_by('campaign.id DESC');
		$this->db->limit($limit, $start);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_total_active($value){
		$this->db->select("*");
		$this->db->from("campaign");
		if($value=='all'){ 
			$this->db->join('campaign_error', 'campaign.id = campaign_error.insert_id', 'left'); 
		}
		else { 
			$this->db->join('campaign_error', 'campaign.id = campaign_error.insert_id', 'inner'); 
		}
		$this->db->order_by('campaign.id DESC');
		$query = $this->db->get();
		$rowcount = $query->num_rows();
		return $rowcount;
	}
	
    public function getUpperValue(){
		$this->db->select('*');
		$this->db->from("keyvalue_pair");
		$this->db->where('slug','uppervalue_pronto');
		$query = $this->db->get();
		$rowcount = $query->row();
		if($rowcount){
			return $rowcount->value;
		}
		else{
			$data['slug']='uppervalue_pronto';
			$data['value']=0;
			$this->db->insert('keyvalue_pair',$data);
			return 0;
		}
	}
	
	public function insert_ActivePronto($data){
		$this->db->insert('pronto_to_active',$data);
		$insert_id = $this->db->insert_id();
        return  $insert_id;
	}
	
	public function setUpperValue($uppervalue){
	    $data = array( 'value' => $uppervalue );
        $this->db->where('slug', 'uppervalue_pronto');
        $this->db->update('keyvalue_pair', $data);
	}
	
	public function get_total_pronto($value='all'){
		$this->db->select("*");
		$this->db->from("pronto_to_active");
		if($value=='error'){ 
			$this->db->where('status',0);
		}
		$query = $this->db->get();
		$rowcount = $query->num_rows();
		return $rowcount;
	}

	public function getAllProntoData($limit, $start, $value='all'){
		$this->db->select('*');
		$this->db->from('pronto_to_active');
		if($value=='error'){ 
			$this->db->where('status',0);
		}
		$this->db->order_by('id DESC');
		$this->db->limit($limit, $start);
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function saveaccountnumber($account_number){
		$data['account_number']=$account_number;
		$this->db->insert('pronto_accountnumber',$data);
	}
	
	public function checkaccountstatus($account_id){
		$this->db->select('*');
		$this->db->from('pronto_accountnumber');
		$this->db->where('account_number',$account_id);
		$query = $this->db->get();
		
		if(empty($query->result())){
			return true;
		} else {
			return false;
		}
	}
	
	/* check if email from pronto to ac already exist in our db - don't send if already exists.*/
	public function checkemailstatus($email){
		$this->db->select('*');
		$this->db->from('pronto_email');
		$this->db->where('email_add',$email);
		$query = $this->db->get();
		
		if(empty($query->result())){
			return true;
		} else {
			return false;
		}
	}
	
	public function insertwebhookdata($tablename, $dataz){
		$this->db->empty_table($tablename);
		$this->db->insert_batch($tablename, $dataz); 
	}

	public function dropthetable($tablename){
		$this->db->empty_table($tablename);
	}

	public function getwebhookdata($tablename){
		$this->db->select("*");
		$this->db->from($tablename);
		$this->db->order_by("id", "asc");
		$query = $this->db->get();
		return $query->result();
	}
	
	/* get emails for webhook error with ac integration*/
	public function webemaillist($data){
		$this->db->select("*");
		$this->db->from("settings");
		$this->db->where("slug", "webhook_error_ac");
		$query = $this->db->get();
		$returned_result=$query->row_array();
		
		if($returned_result){
			$this->db->where("slug","webhook_error_ac");
			$this->db->update('settings', $data);
		}
		else{
			$data['slug']='webhook_error_ac';
			$this->db->insert('settings', $data);
		}
	}

	public function getemaildata(){
		$this->db->select("*");
		$this->db->from("settings");
		$this->db->where("slug", "webhook_error_ac");
		$query = $this->db->get();
		return $query->row_array();
	}

	public function getallemail(){
		$this->db->select("*");
		$this->db->from("settings");
		$this->db->where("slug", "webhook_error_ac");
		$query = $this->db->get();
		return $query->row_array();
	}
	
	public function prontoCronData(){
		$this->db->select("*");
		$this->db->from("camp_to_pronto_cron");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function drop_after_cron($id){
		 $this->db->delete('camp_to_pronto_cron', array('id' => $id)); 
	}
	
} ?>