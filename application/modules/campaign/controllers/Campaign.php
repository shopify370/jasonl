<?php
require __DIR__.'/includes/ActiveCampaign.class.php';
class Campaign extends Base_Controller {
	function __construct() {
		parent::__construct();	
        $this->load->model('campaign_data');
	}

	public function getActiveData(){
		$data = file_get_contents('php://input');
		log_message('error','AC automation');
		log_message('error',$data);

		if($data){
			parse_str($data, $get_array);
			if($get_array){
				//$datetime=$get_array['date_time'];
				if(isset($get_array['contact'])){
					$contactDetail=$get_array['contact'];
					$postcode='';
					$address='';
					
					if(isset($contactDetail['fields'])) {
						if(isset($contactDetail['fields']['postcode'])) { 
							$postcode=$contactDetail['fields']['postcode']; 
						}
						if(isset($contactDetail['fields']['state'])) { 
							$address=$contactDetail['fields']['state']; 
						}
						if(isset($contactDetail['fields']['created_source'])){
							if(isset($contactDetail['fields'][''])=="Pronto"){
								return;
							}
						}
					}
					
					if(isset($contactDetail['email'])){ $email=$contactDetail['email']; }
					
					$boolean_email=$this->campaign_data->checkifprontosynced($email);
					if($boolean_email){
						$contactDetail['not_in_pronto']=1; 
						$insert_id=$this->campaign_data->savecampaign($contactDetail);
						$this->campaign_data->savecampaigncron($contactDetail, $insert_id);
					}
					else{
						$contactDetail['not_in_pronto']=0;
						$insert_id=$this->campaign_data->savecampaign($contactDetail);
					}
					
				}
			}
		}
	}
	
	public function sendActivetoProntoCron(){
		//send to pronto
		$get_all_cron=$this->campaign_data->prontoCronData();

		if(!empty($get_all_cron)){
			$adminCredential=$this->campaign_data->getAllCredential();
			if($adminCredential['loginurl']){ $loginurl=$adminCredential['loginurl']; } else{  $loginurl='https://jasonl-bi.prontohosted.com.au:8443/pronto/rest/U01_avenue/'; }
			if($adminCredential['username']){ $username=$adminCredential['username']; } else{  $username='borisg'; }
			if($adminCredential['password']){ $password=$adminCredential['password']; } else{  $password='Wp35214$'; }

		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $loginurl.'login');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$headers = array();
			$headers[] = "Accept: application/xml";
			$headers[] = "Cache-Control: no-cache";
			$headers[]=  "X-Pronto-Username: ".$username;
			$headers[]=  "X-Pronto-Password: ".$password;
			$headers[]=  "Content-Type: application/xml";

			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			$generate_token = curl_exec($ch);
			if($generate_token === false){
				$error=curl_error($ch);

				$data_error['status']=0;
				$data_error['insert_id']=$insert_id;
				$data_error['error_msg']="Something wrong with Token Generation";
				$data_error['error_xml']=(string)$error;
				$insertID=$this->campaign_data->saveerror($data_error);
				//$this->sendactivetoprontomail($data_error['error_msg'], $email );
			}
			else{
				$token_parsing = new SimpleXMLElement($generate_token);
				if(isset($token_parsing->AuthStatus->Id)&&$token_parsing->AuthStatus->Description=='LOGIN_OK'){
					if(isset($token_parsing->token)&&!empty($token_parsing->token)){
						$pronto_token=$token_parsing->token;

						foreach($get_all_cron as $contactDetail) {
							$emailz=$contactDetail["email"];
							$email=$contactDetail["email"];
							$insert_id=$contactDetail["insert_id"];
							$account_notexist=$this->campaign_data->checkifprontosynced($emailz);
							if($contactDetail["last_name"]){ $last_name=$contactDetail["last_name"]; } else{ $last_name='none'; }
							if($account_notexist){
								//Added Later
								$NewAccountDetail='<CrmInsertAccountRequest><Accounts><Account><AccountName>'.htmlspecialchars(str_replace('&','&amp;',$contactDetail["first_name"])).'</AccountName><AccountRegionCode>000</AccountRegionCode><AccountTypeCode>WEB</AccountTypeCode><RepCode>001</RepCode><AccountEmail>'.$contactDetail["email"].'</AccountEmail><AccountPhoneNumber>'.$contactDetail["phone"].'</AccountPhoneNumber><FirstName>'.htmlspecialchars(str_replace('&','&amp;',$contactDetail["first_name"])).'</FirstName><Surname>'.htmlspecialchars(str_replace('&','&amp;',$last_name)).'</Surname><Postcode>'.$contactDetail["postcode"].'</Postcode><Address1>'.htmlspecialchars(str_replace('&','&amp;',$contactDetail["address"])).'</Address1></Account></Accounts></CrmInsertAccountRequest>';

								$PostNewCustomer=curl_init();
								curl_setopt($PostNewCustomer, CURLOPT_URL, $loginurl.'api/CrmInsertAccount');
								curl_setopt($PostNewCustomer, CURLOPT_POST, true);
								curl_setopt($PostNewCustomer, CURLOPT_POSTFIELDS, $NewAccountDetail); 
								curl_setopt($PostNewCustomer, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($PostNewCustomer, CURLOPT_SSL_VERIFYPEER, false);
								$headers = array();
								$headers[] = "Accept: application/xml";
								$headers[] = "Cache-Control: no-cache";
								$headers[]=  "X-Pronto-Token: ".$pronto_token;
								$headers[]=  "Content-Type: application/xml";
								curl_setopt($PostNewCustomer, CURLOPT_HTTPHEADER, $headers);
								$GetNewCustomerResponseData = curl_exec($PostNewCustomer);
								
								if($GetNewCustomerResponseData === false){
									$error_response= curl_error($PostNewCustomer);

									$data_error['status']=0;
									$data_error['insert_id']=$insert_id;
									$data_error['error_msg']="Something Wrong with creating new Customer";
									$data_error['error_xml']=(string)$error_response;
									$insertID=$this->campaign_data->saveerror($data_error);
									$this->sendactivetoprontomail($data_error['error_msg'], $email );
									curl_close ($PostNewCustomer);
								}
								else{
									curl_close ($PostNewCustomer);
									if(substr($GetNewCustomerResponseData, 0, 5) =="<?xml"){
										$NewcustomerResponseXml = new SimpleXMLElement($GetNewCustomerResponseData);
										if(isset($NewcustomerResponseXml->Accounts->Account)){
											if($NewcustomerResponseXml->Accounts->Account[0]['AccountNumber']){
												$this->campaign_data->saveaccountnumber($NewcustomerResponseXml->Accounts->Account[0]['AccountNumber']);
											}
											else{
												$data_error['status']=0;
												$data_error['insert_id']=$insert_id;
												$data_error['error_msg']="New Account Creation Failed. Returned data does not contains Account Code";
												$insertID=$this->campaign_data->saveerror($data_error);
												$this->sendactivetoprontomail($data_error['error_msg'], $email );
											}
										}
										else{
											$data_error['status']=0;
											$data_error['insert_id']=$insert_id;
											$data_error['error_msg']="New Customer Creation Failed. Returned data does not contains Account Tag";
											$insertID=$this->campaign_data->saveerror($data_error);
											$this->sendactivetoprontomail($data_error['error_msg'], $email );
										}
									}
									else{
										$data_error['status']=0;
										$data_error['insert_id']=$insert_id;
										$data_error['error_msg']="New Customer Creation. Returned data does not contains Customer Tag";
										$insertID=$this->campaign_data->saveerror($data_error);
										$this->sendactivetoprontomail($data_error['error_msg'], $email );
									}
								}
								//Added Later
							}
							$this->campaign_data->drop_after_cron($contactDetail["id"]);
						}
					}
				}
			}
			// end send to pronto
		}
	}
	
	public function sendActiveData(){
		//send to pronto
		$adminCredential=$this->campaign_data->getAllCredential();
		if($adminCredential['loginurl']){ $loginurl=$adminCredential['loginurl']; } else{  $loginurl='https://jasonl-bi.prontohosted.com.au:8443/pronto/rest/L01_avenue/'; }
		if($adminCredential['username']){ $username=$adminCredential['username']; } else{  $username='borisg'; }
		if($adminCredential['password']){ $password=$adminCredential['password']; } else{  $password='Wp35214$'; }

		/*All Data send to Pronto start*/
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $loginurl.'login');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$headers = array();
		$headers[] = "Accept: application/xml";
		$headers[] = "Cache-Control: no-cache";
		$headers[]=  "X-Pronto-Username: ".$username;
		$headers[]=  "X-Pronto-Password: ".$password;
		$headers[]=  "Content-Type: application/xml";

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$generate_token = curl_exec($ch);
		if($generate_token === false){
			$error=curl_error($ch);
			$data_error['status']=0;
			//$data_error['insert_id']=$insert_id;
			$data_error['error_msg']="Something wrong with Token Generation";
			$data_error['error_xml']=(string)$error;
			//$insertID=$this->campaign_data->saveactiveerror($data_error);
		}
		else{
			$token_parsing = new SimpleXMLElement($generate_token);
			if(isset($token_parsing->AuthStatus->Id)&&$token_parsing->AuthStatus->Description=='LOGIN_OK'){
				if(isset($token_parsing->token)&&!empty($token_parsing->token)){
					$pronto_token=$token_parsing->token;

					$upper_value=$this->campaign_data->getUpperValue();
					if($upper_value && is_numeric($upper_value)){
						$upperAccountId=$upper_value;
						$lower_limit=$upper_value+1;
					}
					else{
					    $upperAccountId=0;
						$lower_limit=1;
					}
					$upper_limit=$lower_limit+299;
		
					$GetAccountDetail='<CrmGetAccountsRequest xmlns="http://www.pronto.net/crm/1.0"><RecordLimit>400</RecordLimit><RequestFields><Accounts><Account><AccountNumber/><AccountName/><AccountEmail/><AccountType/><AccountPhoneCountryCode/><AccountPhoneAreaCode/><AccountPhoneNumber/><RepCode/><Contact/><AccountNameIndex/></Account></Accounts></RequestFields><Filters><AccountNumber><Range><From>'.$lower_limit.'</From><To>'.$upper_limit.'</To></Range></AccountNumber></Filters></CrmGetAccountsRequest>';
					
					/*Added later*/
					$check_account=curl_init();
					curl_setopt($check_account, CURLOPT_URL, $loginurl.'api/CrmGetAccounts');
					curl_setopt($check_account, CURLOPT_POST, true);
					curl_setopt($check_account, CURLOPT_POSTFIELDS, $GetAccountDetail); 
					curl_setopt($check_account, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($check_account, CURLOPT_SSL_VERIFYPEER, false);
					$headers = array();
					$headers[] = "Accept: application/xml";
					$headers[] = "Cache-Control: no-cache";
					$headers[] =  "X-Pronto-Token: ".$pronto_token;
					$headers[] =  "Content-Type: application/xml";

					curl_setopt($check_account, CURLOPT_HTTPHEADER, $headers);
					$GetAccountResponseData = curl_exec($check_account);
					if($GetAccountResponseData === false){
						$error=curl_error($check_account);
						$data_error['status']=0;
						$data_error['error_message']="Something wrong with CusGetAccounts";
						$data_error['error_xmlresponse']=(string)$error;
						curl_close ($check_account);
					}
					else{
						curl_close ($check_account);
						if(substr($GetAccountResponseData, 0, 5) =="<?xml"){
							$accountResponseXml = new SimpleXMLElement($GetAccountResponseData);
							if(isset($accountResponseXml->Accounts->Account)){
								$accountList=$accountResponseXml->Accounts->Account;
								
								foreach($accountList as $list){
									
									$data=array();
									$webhook=array();
									$data['accountid']=$accountId=(int)$list->AccountNumber;
									$data_returned=$this->campaign_data->checkaccountstatus($accountId);
									
									if($data_returned){
										$data['email']=$email=(string)$list->AccountEmail;
										
										$email_status=$this->campaign_data->checkemailstatus($email);
										if($email_status){
											$data['postcode']=$postcode=(string)$list->AccountPostcode;
											$data['phone']=$phone=(string)$list->AccountPhoneNumber;
											if(isset($list->Contacts)){
												$accountContact=$list->Contacts->Contact;
												foreach($accountContact as $singleContact){
													$data['first_name']=$firstname=(string)$singleContact->ContactFirstName;
													$middlename=(string)$singleContact->ContactMiddleName;
													$data['last_name']=$surname=(string)$singleContact->ContactSurname;
													$name=$firstname.' '.$middlename;
													if($singleContact->ContactType=='Primary'){
														break;
													}
												}
											}
									
											$data['error']=0;
											$data['xml_error']="error";
											$ac = new ActiveCampaign("https://jasonl.api-us1.com", "e3888b37cec06ede3ca8c7cd79e9a9aa5adddd4df7bc0896065068cc24e7d5b4ae4c6a3d");
											$list_id = 1;
										
											$contact = array(
												"email"              => $email,
												"first_name"         => $name,
												"last_name"          => $surname,
												"p[{$list_id}]"      => $list_id,
												"status[{$list_id}]" => 1,
												"field[3,0]"=>$postcode,
												"field[36,0]"=>"Pronto",
												"tags"=>'pronto',
											);

											$contact_add = $ac->api("contact/add", $contact);

											if (!(int)$contact_add->success) {
												// request failed
												$data['status']=0;
												//echo $contact_add->error;
												$data['error']=$errorz=$contact_add->error;
												if($email){
														log_message('error',$errorz);
													//$this->sendprontotoactivemail($errorz,$accountId);
												}
											}
											else{
												// successful request
												$data['status']=1;
												$contact_id = (int)$contact_add->subscriber_id;
												
												if($name){ $webhook['fname']=$name; } else{ $webhook['fname']=''; }
												if($surname){ $webhook['lname']=$surname; } else{ $webhook['lname']=''; }
												if($email){ $webhook['email']=$email; } else{ $webhook['email']=''; }
												if($postcode){ $webhook['postcode']=$postcode; } else{ $webhook['postcode']=''; }
												if($phone){ $webhook['phone']=$phone; } else{ $webhook['phone']=''; }
												if($accountId){ $webhook['accountId']=$accountId; } else{ $webhook['accountId']=''; }
												$this->sendtowebhook($insert_id,$webhook);
												
											}
											$this->campaign_data->insert_ActivePronto($data); 
										}
									}
									if($upperAccountId<$accountId){ $upperAccountId=$accountId; }
								}
								$this->campaign_data->setUpperValue($upperAccountId);
								
							}
						}
					}
				/*Added later*/
				}
			}
		}
		
	}
	
	public function sendactivetoprontomail($error_data, $email){
	    $config = Array(    
	      'protocol' => 'smtp',
	      'smtp_host' => 'ssl://smtp.googlemail.com',
	      'smtp_port' => 465,
	      'smtp_user' => 'jasonldev5@gmail.com',
	      'smtp_pass' => 'Dark_shadow',
	      'smtp_timeout' => '4',
	      'mailtype' => 'html',
	      'charset' => 'iso-8859-1'
	    );
		
 		$emailCredential=$this->campaign_data->getallemail();
		if($emailCredential['value']){ $emails=$emailCredential['value']; } else{ $emails='dark23shadow@gmail.com'; }
		
		$data = array('error_message'=> $error_data);
		$data['email']=$email;

		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		$this->email->from('jasonldev5@gmail.com', 'Jasonl');
	
		$this->email->to($emails); // replace it with receiver mail id
		$this->email->subject('Active Campaign to Pronto Error.'); // replace it with relevant subject
		$body = $this->load->view('emails/actoprontofailure.php',$data,TRUE);
		$this->email->message($body); 
		$this->email->send();
	}
	
	public function sendprontotoactivemail($error_data, $accountid){
	    $config = Array(    
	      'protocol' => 'smtp',
	      'smtp_host' => 'ssl://smtp.googlemail.com',
	      'smtp_port' => 465,
	      'smtp_user' => 'jasonldev5@gmail.com',
	      'smtp_pass' => 'Dark_shadow',
	      'smtp_timeout' => '4',
	      'mailtype' => 'html',
	      'charset' => 'iso-8859-1'
	    );
		
 		$emailCredential=$this->campaign_data->getallemail();
		if($emailCredential['value']){ $emails=$emailCredential['value']; } else{ $emails='dark23shadow@gmail.com'; }
		
		$data = array('error_message'=> $error_data);
		$data['accountid']=$accountid;

		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		$this->email->from('jasonldev5@gmail.com', 'Jasonl');
	
		$this->email->to($emails); // replace it with receiver mail id
		$this->email->subject('Pronto to Active Campaign Error'); // replace it with relevant subject
		$body = $this->load->view('emails/prontotoacfailure.php',$data,TRUE);
		$this->email->message($body); 
		$this->email->send();
	}
	
	public function sendtowebhook($insert_id,$webhook_data){
		date_default_timezone_set('Australia/Sydney');
		$webhook_data['timestamp'] = date('Y/m/d h:i:s a', time());
		$data_string=json_encode($webhook_data);
		$pronto_webhooks=$this->campaign_data->getwebhookdata('pronto_to_ac_webhook');
		if($pronto_webhooks){
			foreach($pronto_webhooks as $webhooks){
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
				}
				else{
					 //echo 'Curl error: ' . curl_error($ch).' nth';
				}
				curl_close ($ch);
			}
		}
	}
}
?>