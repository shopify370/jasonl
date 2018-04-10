<?php
class Contact extends Base_Controller {
	function __construct() {
		parent::__construct();	
        $this->load->model('contact_data');
	}

	public function index(){
		echo 'test';
	}

	public function contactdetail(){
		/* connect to gmail */
		$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
		$username = 'jasonldev5@gmail.com';
		$password = 'Dark_shadow';

		/* try to connect */
		$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());

		/* grab emails */
		//$emails = imap_search($inbox,'UNSEEN');
		$emails = imap_search($inbox,'UNSEEN FROM "JasonL Office Furniture (Shopify)"');

		/* if emails are returned, cycle through each... */
		if($emails) {
			/* put the newest emails on top */
			rsort($emails);/* for every email... */
			foreach($emails as $email_number) {
				/* get information specific to this email */
				$overview = imap_fetch_overview($inbox,$email_number,0);
				$message = imap_fetchstructure($inbox,$email_number,0 );
				
				$subject_line=$overview[0]->subject;
				$from_line=$overview[0]->from;
				
				$multi_array=array();
				$form_type='';
				if(strpos($subject_line, 'New customer message on') !== false && strpos($subject_line, '[SPAM]') === false) {
					$mail_body=imap_fetchbody ($inbox,$email_number,1);

					$i=0;
					if(strpos($mail_body, "Query Type:")){
						$multi_array[$i]['label']="query_type";
						$multi_array[$i]['index_no']=strpos($mail_body, "Query Type:");
						$multi_array[$i]['length']=11;
						$form_type='quote'; $i++;
					}
					else{
						$form_type='contact';
					}
					
					if(strpos($mail_body, "Name:")){
						$multi_array[$i]['label']="name";
						$multi_array[$i]['index_no']=strpos($mail_body, "Name:");
						$multi_array[$i]['length']=5;
						$i++;
					}
					
					if(strpos($mail_body, "Email:")){
						$multi_array[$i]['label']="email_address";
						$multi_array[$i]['index_no']=strpos($mail_body, "Email:");
						$multi_array[$i]['length']=6;
						$i++;
					}
					
					if(strpos($mail_body, "State:")){
						$multi_array[$i]['label']="state";
						$multi_array[$i]['index_no']=strpos($mail_body, "State:");
						$multi_array[$i]['length']=6;
						$i++;
					}
					
					if(strpos($mail_body, "Phone Number:")){
						$multi_array[$i]['label']="phone_number";
						$multi_array[$i]['index_no']=strpos($mail_body, "Phone Number:");
						$multi_array[$i]['length']=13;
						$i++;
					}
					
					if(strpos($mail_body, "Body:")){
						$multi_array[$i]['label']="message";
						$multi_array[$i]['index_no']=strpos($mail_body, "Body:");
						$multi_array[$i]['length']=5;
						$i++;
					}
					
					$database=array();
					
					foreach($multi_array as $single_value){
						$string_label = $single_value['label'];
						$index_value  = $single_value['index_no'];
						$label_length= $single_value['length'];
						
						$valuez=10000;
						foreach($multi_array as $just_index){
							$index_number=$just_index['index_no'];
							if($index_number>$index_value){
								if($valuez>$index_number){
									$valuez=$index_number;
								}
							}
						}
						$length=$valuez-$index_value-$label_length;
						$starting_point=$index_value+$label_length;
						$form_value=substr($mail_body,$starting_point, $length);
						$database[$string_label]=$form_value;

					}

					if(isset($database)){
						if(isset($form_type)){
							if($form_type=='quote'){
								$insert_id=$this->contact_data->savegetquote($database);

								$this->getaquote_webhook($insert_id,$database);
							}
							else if($form_type=='contact'){
								$insert_id=$this->contact_data->savecontactus($database);

								$this->contact_webhook($insert_id,$database);
							}
						}
					}

				}
				else{
					echo 'not from shopify subject';
				}
			}
		}
		/* close the connection */
		imap_close($inbox);
	}

	public function add_quote_data(){
		$data1 = file_get_contents('php://input');
		log_message('error','Add to Quote');
		log_message('error',$data1);
		
		$all_data=json_decode($data1, true);
		$data=array();
		if(ucfirst($all_data['name'])){ $data['name']=ucfirst($all_data['name']); }
		$subj = "Enquiry from ".$name;
		if($all_data['email']){ $data['email']=$all_data['email']; }
		if($all_data['phone']){ $data['phone']=$all_data['phone']; }
		if($all_data['enquiry']){ $data['enquiry']=$all_data['enquiry']; }
		if($all_data['products']){ $data['products']=json_encode($all_data['products']); }
		if($all_data['unique_key']){ $data['unique_key']=$all_data['unique_key']; } 
		$insert_id=$this->contact_data->insertcontactus($data);
		$this->addaquote_webhook($insert_id,$data);
	}
	
	public function contact_product_data(){
		$data1 = file_get_contents('php://input');
		log_message('error','Contact Product');
		log_message('error',$data1);
		$all_data=json_decode($data1, true);
		$data=array();
		if(ucfirst($all_data['name'])){ $data['name']=ucfirst($all_data['name']); }
		$subj = "Enquiry from ".$name;
		if($all_data['email']){ $data['email']=$all_data['email']; }
		if($all_data['phone']){ $data['phone']=$all_data['phone']; }
		if($all_data['product_name']){ $data['product_name']=$all_data['product_name']; }
		if($all_data['product_image']){ $data['product_image']=$all_data['product_image']; }
		if($all_data['selected_value']){ $data['selected_value']=$all_data['selected_value']; }
		if($all_data['quantity']){ $data['quantity']=$all_data['quantity']; }
		if($all_data['variant_id']){ $data['variant_id']=$all_data['variant_id']; }
		if($all_data['subtotal']){ $data['subtotal']=$all_data['subtotal']; }
		if($all_data['product_url']){ $data['product_url']=$all_data['product_url']; }
		
		$insert_id=$this->contact_data->contactproduct_data($data);
		$this->addaproductdata_webhook($insert_id,$data);
	}


	public function getaquote_webhook($insert_id, $database) {
		/* function call to send data from a curl */
		date_default_timezone_set('Australia/Sydney');
		$database['timestamp'] = date('Y/m/d h:i:s a', time());
		$data_string=json_encode($database);
		$contact_webhooks=$this->contact_data->getwebhookdata('get_quote_webhook');
		if($contact_webhooks){
			foreach($contact_webhooks as $webhooks){
				$url=$webhooks->webhook_url;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
				curl_setopt($ch , CURLOPT_FAILONERROR , true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$server_output = curl_exec ($ch);
				$resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				$curl_errno= curl_errno($ch);

				if($server_output === false){
					$dataz['weburl']=$url;
					$dataz['data_id']=$insert_id;
					$dataz['type']=1;
					$dataz['curl_error']=curl_error($ch);
					$this->contact_data->saveerrors($dataz);
				}
				else{
					 // echo 'Curl error: ' . curl_error($ch).' nth';
				}
				curl_close ($ch);
			}
		}
	}

	public function contact_webhook($insert_id, $database) {
		/* function call to send data from a curl */
		date_default_timezone_set('Australia/Sydney');
		$database['timestamp'] = date('Y/m/d h:i:s a', time());
		$data_string=json_encode($database);
		$contact_webhooks=$this->contact_data->getwebhookdata('contact_webhook');
		if($contact_webhooks){
			foreach($contact_webhooks as $webhooks){
				$url=$webhooks->webhook_url;
				//echo $url;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
				curl_setopt($ch , CURLOPT_FAILONERROR , true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$server_output = curl_exec ($ch);
				$resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

				$curl_errno= curl_errno($ch);
		
				if($server_output === false){
					$dataz['weburl']=$url;
					$dataz['data_id']=$insert_id;
					$dataz['type']=3;
					$dataz['curl_error']=curl_error($ch);
					$this->contact_data->saveerrors($dataz);
				}
				else{
					 //echo 'Curl error: ' . curl_error($ch).' nth';
				}
				curl_close ($ch);
			}
		}
	}
	
	public function addaquote_webhook($insert_id, $database) {
		/* function call to send data from a curl */
		date_default_timezone_set('Australia/Sydney');
		$database['timestamp'] = date('Y/m/d h:i:s a', time());
		$data_string=json_encode($database);
		$contact_webhooks=$this->contact_data->getwebhookdata('add_quote_webhook');
		if($contact_webhooks){
			foreach($contact_webhooks as $webhooks){
				$url=$webhooks->webhook_url;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
				curl_setopt($ch , CURLOPT_FAILONERROR , true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$server_output = curl_exec ($ch);
				$resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

				$curl_errno= curl_errno($ch);
		
				if($server_output === false){
					$dataz['weburl']=$url;
					$dataz['data_id']=$insert_id;
					$dataz['type']=2;
					$dataz['curl_error']=curl_error($ch);
					$this->contact_data->saveerrors($dataz);
				}
				else{
					 //echo 'Curl error: ' . curl_error($ch).' nth';
				}
				curl_close ($ch);
			}
		}
	}
	
	public function addaproductdata_webhook($insert_id, $database) {
		/* function call to send data from a curl */
		date_default_timezone_set('Australia/Sydney');
		$database['timestamp'] = date('Y/m/d h:i:s a', time());
		$data_string=json_encode($database);
		$contactproduct_webhooks=$this->contact_data->getwebhookdata('productcontact_webhook');
		if($contactproduct_webhooks){
			foreach($contactproduct_webhooks as $webhooks){
				$url=$webhooks->webhook_url;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
				curl_setopt($ch , CURLOPT_FAILONERROR , true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$server_output = curl_exec ($ch);
				$resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

				$curl_errno= curl_errno($ch);
				if($server_output === false){
					$dataz['weburl']=$url;
					$dataz['data_id']=$insert_id;
					$dataz['type']=3;
					$dataz['curl_error']=curl_error($ch);
					$this->contact_data->saveerrors($dataz);
				}
				else{
					 //echo 'Curl error: ' . curl_error($ch).' nth';
				}
				curl_close ($ch);
			}
		}
	}
} ?>