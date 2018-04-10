<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require __DIR__.'/vendor/autoload.php';
use phpish\shopify;

class Manual extends Admin_Controller {

	public function __construct(){
        parent::__construct();
        $this->load->model('order_model');
        $this->load->helper(array('form', 'url'));

       // $this->load->library('form_validation');  
        $this->load->library('form_validation');
        $this->form_validation->CI =& $this;
    }

	public function orderform(){
        Template::render('form');
	}

	public function getorder(){
        $this->form_validation->set_rules('orderid', 'Order ID', 'trim|required|callback_verifyorder');

        if ($this->form_validation->run() == FALSE){
            Template::render('form');
        }
        else{
            Template::render('form');
        }

	}

	public function verifyorder($orderid){
		$shopify_data = shopify\client(SHOPIFY_SHOP, SHOPIFY_APP_API_KEY, SHOPIFY_APP_PASSWORD, true);

		try
		{
		    $orderDetail= $shopify_data('GET /admin/orders/'.$orderid.'.json', array(), array());

		    if($orderDetail){
		    	$order_number=$orderDetail["order_number"];
                $order_id=$orderDetail["name"];
    			$lineItems    = $orderDetail["line_items"];
				$final_lineArray=array();
				$actualSkuArray=array();
				$error_message='';
				$error_status=0;
				foreach($lineItems as $single_line){
					$return_array=array();
					$sku_value=$single_line['sku'];
					$quantity_value=$single_line['quantity'];
					$price_value=$single_line['price'];
					$product_id=$single_line['product_id'];
					$varient_id=$single_line['variant_id'];
					if(strpos($sku_value, '(') !== false){
						$loc_start = stripos($sku_value,"(");
						$begin = trim(substr($sku_value, 0, $loc_start));

						if(strpos($sku_value, ')') !== false){
							$loc_end = strripos($sku_value,")");
							$end = trim(substr($sku_value, $loc_start+1,($loc_end-$loc_start-1)));
						}
						else{
							$end = trim(substr($sku_value, $loc_start+1)); 
						}

						if($begin){
							$return_array=$this->getArraySeparatedPipe($begin,$return_array);
							if(empty($return_array)){
								//update database with error
								$dummy_array=array();
								$dummy_array['order_id']=$order_number;
								$dummy_array['status']=0;
								$dummy_array['product_id']=$product_id;
								$dummy_array['varient_id']=$varient_id;
								$dummy_array['sku']=$sku_value;
								$skuInsertId=$this->order_model->saveOrderDetail($dummy_array);
								$dummy_array['type']=1;
								$dummy_array['message']="SKU cannot be parsed";
								$dummy_array['manual']=1;
								$this->order_model->insertProntoResponse($dummy_array,$skuInsertId);
								$final_lineArray=array();
								$error_message='Sku of Product ID '.$product_id.' of Order ID '.$order_number.' cannot be parsed';
								$error_status=1;
								$this->form_validation->set_message('verifyorder', $error_message); 
								return FALSE; 
								break;
							}
						}
						if($end){
							$return_array=$this->getArraySeparatedPipe($end,$return_array);
							if(empty($return_array)){
								//update database with error
								$dummy_array=array();
								$dummy_array['order_id']=$order_number;
								$dummy_array['status']=0;
								$dummy_array['product_id']=$product_id;
								$dummy_array['varient_id']=$varient_id;
								$dummy_array['sku']=$sku_value;
								$skuInsertId=$this->order_model->saveOrderDetail($dummy_array);
								$dummy_array['type']=1;
								$dummy_array['message']="SKU cannot be parsed";
								$dummy_array['manual']=1;
								$this->order_model->insertProntoResponse($dummy_array,$skuInsertId);
								$final_lineArray=array();
								$error_message='Sku of Product ID '.$product_id.' of Order ID '.$order_number.' cannot be parsed';
								$error_status=1;
								$this->form_validation->set_message('verifyorder', $error_message); 
								return FALSE; 
								break;
							}
						}
					}
					else{
						$return_array=$this->getArraySeparatedPipe($sku_value,$return_array);
						if(empty($return_array)){
							//update database with error
							$dummy_array=array();
							$dummy_array['order_id']=$order_number;
							$dummy_array['status']=0;
							$dummy_array['product_id']=$product_id;
							$dummy_array['varient_id']=$varient_id;
							$dummy_array['sku']=$sku_value;
							$skuInsertId=$this->order_model->saveOrderDetail($dummy_array);
							$dummy_array['type']=1;
							$dummy_array['message']="SKU cannot be parsed";
							$dummy_array['manual']=1;
							$this->order_model->insertProntoResponse($dummy_array,$skuInsertId);
							$final_lineArray=array();
							$error_message='Sku of Product ID '.$product_id.' of Order ID '.$order_number.' cannot be parsed';
							$error_status=1;
							$this->form_validation->set_message('verifyorder', $error_message); 
							return FALSE; 
							break;
						}
					}
					
					 if($return_array && is_array($return_array)){
					 	$storeLineItems=array();
						foreach($return_array as $one_row){
							$storeLineItems=$single_line;
							$storeLineItems['sku']=$one_row['sku'];
							$storeLineItems['price']=$one_row['price']/$one_row['pricedivider'];
							$storeLineItems['quantity']=$one_row['quantity']*$quantity_value;
							$final_lineArray[]=$storeLineItems;
						}
					}
				}

				if(!empty($final_lineArray)){
					$orderDetail["line_items"]=$final_lineArray;
					$successOrderDetail=array();
					$successOrderDetail['order_id']=$order_number;
					$successOrderDetail['orderdetail']=json_encode($orderDetail);
					$successOrderDetail['status']=1;
					$this->order_model->saveOrderDetail($successOrderDetail);
					$pronto_message=$this->sendDataToPronto($successOrderDetail['orderdetail']);
					if($pronto_message['error_status']==1){
						$this->form_validation->set_message('verifyorder', $pronto_message['message']); 
						return FALSE; 
					}
					else{
						$this->form_validation->set_message('verifyorder', $pronto_message['message']); 
						return FALSE; 
					}
				}
				if($error_status==1){
					$this->form_validation->set_message('verifyorder', $error_message); 
					return FALSE; 
				}
		    }
		}
		catch (shopify\ApiException $e)
		{
			$this->form_validation->set_message('verifyorder', 'Sorry Order ID not Found'); 
					return FALSE;
		    # HTTP status code was >= 400 or response contained the key 'errors'
		    echo $e; echo '<br/>';
		    print_R($e->getRequest()); echo '<br/>';
		    print_R($e->getResponse()); echo '<br/>';
		}
		catch (shopify\CurlException $e)
		{
		    # cURL error
		    $this->form_validation->set_message('verifyorder', 'Sorry Order ID not Found'); 
			return FALSE;
		    echo $e; echo '<br/>';
		    print_R($e->getRequest()); echo '<br/>';
		    print_R($e->getResponse()); echo '<br/>';
		}




	die;

		if($orderid=='test'){ return TRUE; } else{ 	$this->form_validation->set_message('verifyorder', 'Not does not include that'); return FALSE; }
	}

	public function get_shopify_data(){
		$shopify_data = shopify\client(SHOPIFY_SHOP, SHOPIFY_APP_API_KEY, SHOPIFY_APP_PASSWORD, true);
		$orderDetail= $shopify_data('GET /admin/orders/23138828300.json', array(), array());

		echo '<pre>';
		 print_r($orderDetail);
		 echo '</pre>';
	}

		public function checkIfAlreadyExist($array, $key,$val){
		foreach ($array as $key1=>$item){
			if (is_array($item) && $this->checkIfAlreadyExist($item, $key, $val)!='-1') return $key1;
			if (isset($item[$key]) && $item[$key] == $val) return $key1;
		}
		return -1;
	}
	
	//This function will parse the sku value
	public function getArraySeparatedPipe($ourstring, $return_array){
		$pipe_position   = stripos($ourstring,'|');
		$arrayHolder=array();
		if($pipe_position){
			$sku_firstpart   = trim(substr($ourstring, 0, $pipe_position));
			$sku_secondpart  = trim(substr($ourstring, $pipe_position+1));
			$sku_firstarray  = explode(',',$sku_firstpart);
			$sku_secondarray = explode(',',$sku_secondpart);
			foreach($sku_firstarray as $key=>$value){
				$var_dummy=array();
				if (array_key_exists($key,$sku_secondarray)){
					if(empty($return_array)){
						$var_dummy['sku']=$value;
						$var_dummy['price']=$sku_secondarray[$key];
						$var_dummy['quantity']=1;
						$var_dummy['pricedivider']=1;
						$var_dummy['flag']=1;
						$return_array[]=$var_dummy;
					}
					else{
						//check if sku already exist else do something else.
						$kky=$this->checkIfAlreadyExist($return_array,'sku',$value);
						if($kky!='-1'){
							$return_array[$kky]['quantity']+=1;
							if($sku_secondarray[$key]==''||$sku_secondarray[$key]==0){
								$return_array[$kky]['pricedivider']+=1;
							}
							else{
								
								if($return_array[$kky]['price']==0){
									$return_array[$kky]['pricedivider']+=1;
								}
								$return_array[$kky]['price']=$sku_secondarray[$key];
							}
						}
						else{
							$var_dummy['sku']=$value;
							$var_dummy['price']=$sku_secondarray[$key];
							$var_dummy['quantity']=1;
							$var_dummy['pricedivider']=1;
							$var_dummy['flag']=1;
							$return_array[]=$var_dummy;
						}
					}
				}
				else{
					$return_array=array();
					break;
				}
			}
		}
		else{
			$return_array=$arrayHolder;
		}
		return $return_array;
	}




	//Send Data To Pronto
	public function sendDataToPronto($dataDetail){
		$adminCredential=$this->order_model->getAllCredential();
		if($adminCredential['loginurl']){ $loginurl=$adminCredential['loginurl']; } else{  $loginurl='https://jasonl-bi.prontohosted.com.au:8443/pronto/rest/U01_avenue/'; }
		if($adminCredential['username']){ $username=$adminCredential['username']; } else{  $username='borisg'; }
		if($adminCredential['password']){ $password=$adminCredential['password']; } else{  $password='Wp35214$'; }


		$manual_error=array();
		
		$jsonal_array=json_decode($dataDetail,true);
		$OrderId=$jsonal_array['order_number'];

		/*All Data send to Pronto start*/
		$Customer__Code='';
		$ProntoCustomerCreated=false;
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
			$data_error['order_id']=$OrderId;
			$data_error['status']=0;
			$data_error['error_message']="Something wrong with Token Generation";
			$data_error['error_xmlresponse']=(string)$error;
			$insertID=$this->order_model->prontostatus($data_error);
			$data_error['type']=2;
			$data_error['message']=$data_error['error_message'];
			$data_error['manual']=1;
			$this->order_model->insertProntoResponse($data_error,$insertID);
				
			$manual_error['error_status']=1;
			$manual_error['message']=$data_error['error_message'];
			$manual_error['insertid']=$insertID;
			return $manual_error;
			//$this->sendProntoMail($OrderId, $data_error['error_message'], $insertID);
		}
		else{
			$token_parsing = new SimpleXMLElement($generate_token);
			if(isset($token_parsing->AuthStatus->Id)&&$token_parsing->AuthStatus->Description=='LOGIN_OK'){
				if(isset($token_parsing->token)&&!empty($token_parsing->token)){
					$pronto_token=$token_parsing->token;
					/* Check if Customer Exists. */
					if(array_key_exists('customer', $jsonal_array)){
						$customer_detail=$jsonal_array['customer'];
						$customer_id=$customer_detail['id'];
						$pronto_customer=strtoupper(base_convert($customer_id, 10, 35));
						$GetCustomerRequestData='<CusGetCustomersRequest xmlns="http://www.pronto.net/cus/1.0.0"><Parameters><CustomerCode>'.$pronto_customer.'</CustomerCode></Parameters><Filters><CustomerName></CustomerName></Filters><RequestFields><Customers><Customer><CustomerCode /><CustomerName /><RepCode /><TerritoryCode /><WarehouseCode /></Customer></Customers></RequestFields></CusGetCustomersRequest>';
						$check_customer=curl_init();
						curl_setopt($check_customer, CURLOPT_URL, $loginurl.'api/CusGetCustomers');
						curl_setopt($check_customer, CURLOPT_POST, true);
						curl_setopt($check_customer, CURLOPT_POSTFIELDS, $GetCustomerRequestData); 
						curl_setopt($check_customer, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($check_customer, CURLOPT_SSL_VERIFYPEER, false);
						$headers = array();
						$headers[] = "Accept: application/xml";
						$headers[] = "Cache-Control: no-cache";
						$headers[]=  "X-Pronto-Token: ".$pronto_token;
						$headers[]=  "Content-Type: application/xml";

						curl_setopt($check_customer, CURLOPT_HTTPHEADER, $headers);
						$GetCustomerResponseData = curl_exec($check_customer);
						if($GetCustomerResponseData === false){
							$error=curl_error($check_customer);
							$data_error['order_id']=$OrderId;
							$data_error['status']=0;
							$data_error['error_message']="Something wrong with CusGetCustomers";
							$data_error['error_xmlresponse']=(string)$error;
							$insertID=$this->order_model->prontostatus($data_error);
							$data_error['type']=2;
							$data_error['message']=$data_error['error_message'];
							$data_error['manual']=1;
							$this->order_model->insertProntoResponse($data_error,$insertID);
							
							$manual_error['error_status']=1;
							$manual_error['message']=$data_error['error_message'];
							$manual_error['insertid']=$insertID;
							return $manual_error;

							curl_close ($check_customer);
						}
						else{
							curl_close ($check_customer);
							if(substr($GetCustomerResponseData, 0, 5) =="<?xml"){
								$customerResponseXml = new SimpleXMLElement($GetCustomerResponseData);
								if(isset($customerResponseXml->Customers->Customer)){
									if(count($customerResponseXml->Customers->Customer)==1){
										if(!empty($customerResponseXml->Customers->Customer->CustomerCode)){
											$Customer__Code=$customerResponseXml->Customers->Customer->CustomerCode;
										}
										else{
											//Customer Exist but Customer Code not returned.
											$errorresponse=$GetCustomerResponseData;
											$errorrequest=$GetCustomerRequestData;
											$data_error['order_id']=$OrderId;
											$data_error['status']=0;
											$data_error['error_message']="Customer Code Not Found";
											$data_error['error_xmlresponse']=(string)$errorresponse;
											$data_error['error_xmlrequest']=(string)$errorrequest;
											$insertID=$this->order_model->prontostatus($data_error);
											$data_error['type']=2;
											$data_error['message']=$data_error['error_message'];
											$data_error['manual']=1;
											$this->order_model->insertProntoResponse($data_error,$insertID);
											//$this->sendProntoMail($OrderId, $data_error['error_message'], $insertID);
											$manual_error['error_status']=1;
											$manual_error['message']=$data_error['error_message'];
											$manual_error['insertid']=$insertID;
											return $manual_error;
										}
									}
									else{
										$errorresponse=$GetCustomerResponseData;
										$errorrequest=$GetCustomerRequestData;
										$data_error['order_id']=$OrderId;
										$data_error['status']=0;
										$data_error['error_message']="Multiple Customer Returned";
										$data_error['error_xmlresponse']=(string)$errorresponse;
										$data_error['error_xmlrequest']=(string)$errorrequest;
										$insertID=$this->order_model->prontostatus($data_error);
										$data_error['type']=2;
										$data_error['message']=$data_error['error_message'];
										$data_error['manual']=1;
										$this->order_model->insertProntoResponse($data_error,$insertID);
										//$this->sendProntoMail($OrderId, $data_error['error_message'], $insertID);
										$manual_error['error_status']=1;
										$manual_error['message']=$data_error['error_message'];
										$manual_error['insertid']=$insertID;
										return $manual_error;

									}
								}
								else{
									//not customer code create customers
									$NewCustomerDetail='<CusPostNewCustomerRequest xmlns="http://www.pronto.net/cus/1.0.0"><Customers><Customer CustomerCode="'.$pronto_customer.'"><ContactName>'.htmlspecialchars(str_replace('&','&amp;',$customer_detail["default_address"]["name"])).'</ContactName><CustomerName>'.htmlspecialchars(str_replace('&','&amp;',$customer_detail["default_address"]["name"])).'</CustomerName><AddressName>'.htmlspecialchars(str_replace('&','&amp;',$customer_detail["default_address"]["name"])).'</AddressName><Address1>'.htmlspecialchars(str_replace('&','&amp;',$customer_detail["default_address"]["company"])).'</Address1><Address2>'.htmlspecialchars($customer_detail["default_address"]["address1"]).'</Address2><Address3>'.htmlspecialchars(str_replace('&','&amp;',$customer_detail["default_address"]["city"])).'</Address3><Address5>'.htmlspecialchars(str_replace('&','&amp;',$customer_detail["default_address"]["province_code"])).'</Address5><Email>'.$customer_detail["email"].'</Email><AddressCountryCode>'.$customer_detail["default_address"]["country_code"].'</AddressCountryCode><PhoneNo>'.$customer_detail["default_address"]["phone"].'</PhoneNo><AddressPostcode>'.$customer_detail["default_address"]["zip"].'</AddressPostcode><PriceCodeCode>R</PriceCodeCode><RepCode>001</RepCode><TerritoryCode>000</TerritoryCode><WarehouseCode>BOT</WarehouseCode><TaxCalcLevelCode>L</TaxCalcLevelCode><TermsDiscCode>C</TermsDiscCode><TypeCode>W</TypeCode><FreightCode>TNT</FreightCode><CreditLimitCode>1</CreditLimitCode></Customer></Customers></CusPostNewCustomerRequest>';
									$PostNewCustomer=curl_init();
									curl_setopt($PostNewCustomer, CURLOPT_URL, $loginurl.'api/CusPostNewCustomer');
									curl_setopt($PostNewCustomer, CURLOPT_POST, true);
									curl_setopt($PostNewCustomer, CURLOPT_POSTFIELDS, $NewCustomerDetail); 
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
										$error_request=$NewCustomerDetail;
										$data_error['order_id']=$OrderId;
										$data_error['status']=0;
										$data_error['error_message']="Something Wrong with creating new Customer";
										$data_error['error_xmlresponse']=(string)$error_response;
										$data_error['error_xmlrequest']=(string)$NewCustomerDetail;
										$insertID=$this->order_model->prontostatus($data_error);
										$data_error['type']=2;
										$data_error['message']=$data_error['error_message'];
										$data_error['manual']=1;
										$this->order_model->insertProntoResponse($data_error,$insertID);
										//$this->sendProntoMail($OrderId, $data_error['error_message'], $insertID);
										$manual_error['error_status']=1;
										$manual_error['message']=$data_error['error_message'];
										$manual_error['insertid']=$insertID;
										return $manual_error;
										curl_close ($PostNewCustomer);

									}
									else{
										curl_close ($PostNewCustomer);
										if(substr($GetNewCustomerResponseData, 0, 5) =="<?xml"){
											$NewcustomerResponseXml = new SimpleXMLElement($GetNewCustomerResponseData);
											if(isset($NewcustomerResponseXml->Customers->Customer)){
												if(!empty($NewcustomerResponseXml->Customers->Customer->CustomerCode)){
													$Customer__Code=$NewcustomerResponseXml->Customers->Customer->CustomerCode;
													$ProntoCustomerCreated=true;
													$ProntoCustomerRequestXml=(string)$NewCustomerDetail;
													$ProntoCustomerResponseXml=(string)$GetNewCustomerResponseData;
												}
												else{
													$error_response=$GetNewCustomerResponseData;
													$error_request=$NewCustomerDetail;
													$data_error['order_id']=$OrderId;
													$data_error['status']=0;
													$data_error['error_message']="New Customer Creation. Returned data does not contains CustomerCode";
													$data_error['error_xmlresponse']=(string)$error_response;
													$data_error['error_xmlrequest']=(string)$error_request;
													$insertID=$this->order_model->prontostatus($data_error);
													$data_error['type']=2;
													$data_error['message']=$data_error['error_message'];
													$data_error['manual']=1;
													$this->order_model->insertProntoResponse($data_error,$insertID);
													//$this->sendProntoMail($OrderId, $data_error['error_message'], $insertID);
													$manual_error['error_status']=1;
													$manual_error['message']=$data_error['error_message'];
													$manual_error['insertid']=$insertID;
													return $manual_error;
																				
												}
											}
											else{
												$error_response=$GetNewCustomerResponseData;
												$error_request=$NewCustomerDetail;
												$data_error['order_id']=$OrderId;
												$data_error['status']=0;
												$data_error['error_message']="New Customer Creation. Returned data does not contains Customer Tag";
												$data_error['error_xmlresponse']=(string)$error_response;
												$data_error['error_xmlrequest']=(string)$error_request;
												$insertID=$this->order_model->prontostatus($data_error);
												$data_error['type']=2;
												$data_error['message']=$data_error['error_message'];
												$data_error['manual']=1;
												$this->order_model->insertProntoResponse($data_error,$insertID);
												//$this->sendProntoMail($OrderId, $data_error['error_message'], $insertID);
												$manual_error['error_status']=1;
												$manual_error['message']=$data_error['error_message'];
												$manual_error['insertid']=$insertID;
												return $manual_error;
												
											}
										}
										else{
											//it is not xml at alls.
											$error_response=$GetNewCustomerResponseData;
											$error_request=$NewCustomerDetail;
											$data_error['order_id']=$OrderId;
											$data_error['status']=0;
											$data_error['error_message']="New Customer Creation returned non xml data";
											$data_error['error_xmlresponse']=(string)$error_response;
											$data_error['error_xmlrequest']=(string)$error_request;
											$insertID=$this->order_model->prontostatus($data_error);
											$data_error['type']=2;
											$data_error['message']=$data_error['error_message'];
											$data_error['manual']=1;
											$this->order_model->insertProntoResponse($data_error,$insertID);
											//$this->sendProntoMail($OrderId, $data_error['error_message'], $insertID);
											$manual_error['error_status']=1;
											$manual_error['message']=$data_error['error_message'];
											$manual_error['insertid']=$insertID;
											return $manual_error;
										}
									}
								}
							}
							else{
								//it is not xml at alls.
								$error=$GetCustomerResponseData;
								$data_error['order_id']=$OrderId;
								$data_error['status']=0;
								$data_error['error_message']="Get Customer returned non xml data";
								$data_error['error_xmlresponse']=(string)$error;
								$insertID=$this->order_model->prontostatus($data_error);
								$data_error['type']=2;
								$data_error['message']=$data_error['error_message'];
								$data_error['manual']=1;
								$this->order_model->insertProntoResponse($data_error,$insertID);
								//$this->sendProntoMail($OrderId, $data_error['error_message'], $insertID);
								$manual_error['error_status']=1;
								$manual_error['message']=$data_error['error_message'];
								$manual_error['insertid']=$insertID;
								return $manual_error;
							}
							
							if($Customer__Code){
								$orderDetail=$jsonal_array['line_items'];
								$shippingDetail=$jsonal_array['shipping_lines'];
								$discountDetail=$jsonal_array['discount_codes'];
								$total_price=$jsonal_array['total_price'];
								$order_number=$jsonal_array['order_number'];
								$order_date=$jsonal_array['created_at'];
								$deliverySlot=0;
											
								if($orderDetail){
									$salesLine='';
									foreach($orderDetail as $lineItem){ if($lineItem["sku"]=='NONE'){ continue; }
										$salesLine.='<SalesOrderLine><ItemCode>'.$lineItem["sku"].'</ItemCode><OrderedQty>'.$lineItem["quantity"].'</OrderedQty><BackorderQty>'.$lineItem["quantity"].'</BackorderQty><ItemPrice>'.$lineItem["price"].'</ItemPrice><ShippedQty>0</ShippedQty></SalesOrderLine>';
									}
									
									
									if($shippingDetail){
										foreach($shippingDetail as $shipLine){
											$shipping_title=$shipLine['title'];
											$shipping_price=$shipLine['price'];
											

											if (strpos($shipping_title, 'PICK-UP') !== false) {
											   $salesLine.='<SalesOrderLine><ItemCode>S070</ItemCode><OrderedQty>1</OrderedQty><ItemPrice>0</ItemPrice></SalesOrderLine>';
											   $deliverySlot=5;
											}
											else{
												if(strpos($shipping_title, 'Standard') !== false){
													$deliverySlot=5;
													if(strpos($shipping_title, 'Assembly') !== false){
														$lastdollarsign=strrpos($shipping_title, '$'); 
													
														$amount_afterdollar=substr($shipping_title,$lastdollarsign+1);
														$standard_price=$shipping_price-(int)$amount_afterdollar;
														if(strpos($shipping_title, '+') !== false){
															$str_array=explode("+",$shipping_title);
															
															$salesLine.='<SalesOrderLine><ItemCode>S068</ItemCode><OrderedQty>1</OrderedQty><ItemPrice>'.$standard_price.'</ItemPrice></SalesOrderLine><SalesOrderLine><ItemCode>ASSEM</ItemCode><OrderedQty>1</OrderedQty><ItemPrice>'.$amount_afterdollar.'</ItemPrice></SalesOrderLine>';
														}
														else{
															$salesLine.='<SalesOrderLine><ItemCode>S068</ItemCode><OrderedQty>1</OrderedQty><ItemPrice>'.$standard_price.'</ItemPrice></SalesOrderLine><SalesOrderLine><ItemCode>ASSEM</ItemCode><OrderedQty>1</OrderedQty><ItemPrice>'.$amount_afterdollar.'</ItemPrice></SalesOrderLine>';
															
														}

													}
													else{
														$salesLine.='<SalesOrderLine><ItemCode>S068</ItemCode><OrderedQty>1</OrderedQty><ItemPrice>'.$shipping_price.'</ItemPrice></SalesOrderLine>';
													}
												}
												elseif(strpos($shipping_title, 'Express') !== false){
													$deliverySlot=3;
													if(strpos($shipping_title, 'Assembly') !== false){
														$lastdollarsign=strrpos($shipping_title, '$'); 
														$amount_afterdollar=substr($shipping_title,$lastdollarsign+1);
														$assembly_price=$shipping_price-(int)$amount_afterdollar;
														if(strpos($shipping_title, '+') !== false){
															$str_array=explode("+",$shipping_title);
															
															$salesLine.='<SalesOrderLine><ItemCode>S069</ItemCode><OrderedQty>1</OrderedQty><ItemPrice>'.$assembly_price.'</ItemPrice></SalesOrderLine><SalesOrderLine><ItemCode>ASSEM</ItemCode><OrderedQty>1</OrderedQty><ItemPrice>'.$amount_afterdollar.'</ItemPrice></SalesOrderLine>';
														}
														else{
															$salesLine.='<SalesOrderLine><ItemCode>S069</ItemCode><OrderedQty>1</OrderedQty><ItemPrice>'.$assembly_price.'</ItemPrice></SalesOrderLine><SalesOrderLine><ItemCode>ASSEM</ItemCode><OrderedQty>1</OrderedQty><ItemPrice>'.$amount_afterdollar.'</ItemPrice></SalesOrderLine>';
														}
													}
													else{
														$salesLine.='<SalesOrderLine><ItemCode>S069</ItemCode><OrderedQty>1</OrderedQty><ItemPrice>'.$shipping_price.'</ItemPrice></SalesOrderLine>';
													}
												}
											}
										}
									}
									
									if($discountDetail){
										foreach($discountDetail as $discount){
											$promocode=$discount['code'];
											$price=$discount['amount'];
											$negate_price= -1*$price;
											$salesLine.='<SalesOrderLine><ItemCode>S074</ItemCode><OrderedQty>1</OrderedQty><ItemPrice>'.$negate_price.'</ItemPrice></SalesOrderLine>';
										}
									}
									
									if($salesLine){
										$newOrderSaleRequestXml='<SalesOrderInsertSalesOrdersRequest><SalesOrders><SalesOrder SOOrderNo="'.$order_number.'"><InvoiceNo>'.$order_number.'</InvoiceNo><CustomerReference>'.$order_number.'</CustomerReference><PartShipmentAllowedCode>N</PartShipmentAllowedCode>';
										
										if($deliverySlot){
											$delivery_date=$this->getDeliveryDate($deliverySlot,$order_date);
											if($delivery_date){
												$newOrderSaleRequestXml.='<DeliveryDate>'.$delivery_date.'</DeliveryDate>';
											}
										}
										
										$newOrderSaleRequestXml.='<CustomerCode>'.$Customer__Code.'</CustomerCode><SalesOrderLines>'.$salesLine.'</SalesOrderLines><SalesOrderPaymentDetails><SalesOrderPayment><Amount>'.$total_price.'</Amount><StoreID>WEB</StoreID><TerminalNo>999</TerminalNo><TypeCode>W</TypeCode></SalesOrderPayment></SalesOrderPaymentDetails></SalesOrder></SalesOrders></SalesOrderInsertSalesOrdersRequest>';
										

										$PostNewSalesOrder=curl_init();
										curl_setopt($PostNewSalesOrder, CURLOPT_URL, $loginurl.'api/SalesOrderInsertSalesOrders');
										curl_setopt($PostNewSalesOrder, CURLOPT_POST, true);
										curl_setopt($PostNewSalesOrder, CURLOPT_POSTFIELDS, $newOrderSaleRequestXml); 
										curl_setopt($PostNewSalesOrder, CURLOPT_RETURNTRANSFER, true);
										curl_setopt($PostNewSalesOrder, CURLOPT_SSL_VERIFYPEER, false);
										$headers = array();
										$headers[] = "Accept: application/xml";
										$headers[] = "Cache-Control: no-cache";
										$headers[]=  "X-Pronto-Token: ".$pronto_token;
										$headers[]=  "Content-Type: application/xml";
										curl_setopt($PostNewSalesOrder, CURLOPT_HTTPHEADER, $headers);
										$GetNewSalesResponseData = curl_exec($PostNewSalesOrder);
										if($GetNewSalesResponseData === false){

											$error_response=curl_error($PostNewSalesOrder);
											$error_request=$newOrderSaleRequestXml;
											$data_error['order_id']=$OrderId;
											$data_error['status']=0;
											$data_error['customerId']=$Customer__Code;
											$data_error['error_message']="Something went wrong with creating new Sales Order.";
											$data_error['error_xmlresponse']=(string)$error_response;
											$data_error['error_xmlrequest']=(string)$error_request;
											$insertID=$this->order_model->prontostatus($data_error);
											$data_error['type']=2;
											$data_error['message']=$data_error['error_message'];
											$data_error['manual']=1;
											$this->order_model->insertProntoResponse($data_error,$insertID);
											//$this->sendProntoMail($OrderId, $data_error['error_message'], $insertID);
											$manual_error['error_status']=1;
											$manual_error['message']=$data_error['error_message'];
											$manual_error['insertid']=$insertID;
											return $manual_error;
											curl_close ($PostNewSalesOrder);
										}
										else{
											curl_close ($PostNewSalesOrder);
											if(substr($GetNewSalesResponseData, 0, 5) =="<?xml"){
												$NewSalesResponseXml = new SimpleXMLElement($GetNewSalesResponseData);
												if(isset($NewSalesResponseXml->SalesOrders->SalesOrder)){
													if(!empty($NewSalesResponseXml->SalesOrders->SalesOrder->SOOrderNo)){

														$SalesOrderNo=$NewSalesResponseXml->SalesOrders->SalesOrder->SOOrderNo;

														if(isset($NewSalesResponseXml->APIResponseStatus->APIErrors)){
															//Sales order might not have integrated completely.
															$error_response=$GetNewSalesResponseData;
															$error_request=$newOrderSaleRequestXml;
															$data_error['order_id']=$OrderId;
															$data_error['status']=1;
															$data_error['customerId']=$Customer__Code;
															$data_error['pronto_orderId']=$SalesOrderNo;
															$data_error['error_message']="Order are inserted but something went wrong.";
															$data_error['error_xmlresponse']=(string)$error_response;
															$data_error['error_xmlrequest']=(string)$error_request;
															$insertID=$this->order_model->prontostatus($data_error);
															$data_error['type']=2;
															$data_error['message']=$data_error['error_message'];
															$data_error['manual']=1;
															$this->order_model->insertProntoResponse($data_error,$insertID);
															//$this->sendProntoMail($OrderId, $data_error['error_message'], $insertID);
															$manual_error['error_status']=1;
															$manual_error['message']=$data_error['error_message'];
															$manual_error['insertid']=$insertID;
															//return $manual_error;
														}
														else{
															//sales order inserted completely.
															$error_response=$GetNewSalesResponseData;
															$error_request=$newOrderSaleRequestXml;
															$data_error['order_id']=$OrderId;
															$data_error['status']=1;
															$data_error['customerId']=$Customer__Code;
															$data_error['pronto_orderId']=$SalesOrderNo;
															$data_error['error_message']="Order is inserted Successfully.";
															$data_error['error_xmlresponse']=(string)$error_response;
															$data_error['error_xmlrequest']=(string)$error_request;
															$insertID=$this->order_model->prontostatus($data_error);
															$data_error['type']=2;
															$data_error['message']=$data_error['error_message'];
															$data_error['manual']=1;
															$this->order_model->insertProntoResponse($data_error,$insertID);
															//$this->sendProntoMail($OrderId, $data_error['error_message'], $insertID);
															$manual_error['error_status']=0;
															$manual_error['message']=$data_error['error_message'];
															$manual_error['insertid']=$insertID;
															//return $manual_error;
														}
														
														if($ProntoCustomerCreated){
															$data_cus['customer_request']=$ProntoCustomerRequestXml;
															$data_cus['customer_response']=$ProntoCustomerResponseXml;
															$data_cus['insert_id']=$insertID;
															$this->order_model->insertCustomerResponse($data_cus);
														}

														$shippingAddress=$jsonal_array['shipping_address'];
														
														$sendDeliveryAddressXml='<SalesOrderPostDeliveryAddressRequest xmlns="http://www.pronto.net/so/1.0.0"><SalesOrderDeliveryDetails><SalesOrderDelivery SOOrderNo="'.$SalesOrderNo.'" SOBOSuffix="" TypeCode="DA"><AddressName>'.$shippingAddress["name"].'</AddressName><Address1>'.htmlspecialchars(str_replace('&','&amp;',$shippingAddress["company"])).'</Address1><Address2>'.htmlspecialchars(str_replace('&','&amp;',$shippingAddress["address1"])).'</Address2><Address3>'.htmlspecialchars(str_replace('&','&amp;',$shippingAddress["city"])).'</Address3><Address4>'.htmlspecialchars(str_replace('&','&amp;',$shippingAddress["province_code"])).'</Address4><AddressPostcode>'.$shippingAddress["zip"].'</AddressPostcode><Phone>'.$shippingAddress["phone"].'</Phone></SalesOrderDelivery></SalesOrderDeliveryDetails></SalesOrderPostDeliveryAddressRequest>'; 

														$PostDeliveryAddress=curl_init();
														curl_setopt($PostDeliveryAddress, CURLOPT_URL, $loginurl.'api/SalesOrderPostDeliveryAddress');
														curl_setopt($PostDeliveryAddress, CURLOPT_POST, true);
														curl_setopt($PostDeliveryAddress, CURLOPT_POSTFIELDS, $sendDeliveryAddressXml); 
														curl_setopt($PostDeliveryAddress, CURLOPT_RETURNTRANSFER, true);
														curl_setopt($PostDeliveryAddress, CURLOPT_SSL_VERIFYPEER, false);
														$headers = array();
														$headers[] = "Accept: application/xml";
														$headers[] = "Cache-Control: no-cache";
														$headers[]=  "X-Pronto-Token: ".$pronto_token;
														$headers[]=  "Content-Type: application/xml";
														curl_setopt($PostDeliveryAddress, CURLOPT_HTTPHEADER, $headers);
														$GetNewDeliveryAddressData = curl_exec($PostDeliveryAddress);
														if($GetNewDeliveryAddressData === false){
															echo 'Curl error: ' . curl_error($PostDeliveryAddress);
															curl_close ($PostDeliveryAddress);
														}
														else{
															curl_close ($PostDeliveryAddress);
															$NewDeliveryResponseXml = new SimpleXMLElement($GetNewDeliveryAddressData);
															$data_shipping['insert_id']=$insertID;
															$data_shipping['delivery_request']=(string)$sendDeliveryAddressXml;
															$data_shipping['delivery_response']=(string)$GetNewDeliveryAddressData;
															$data_shipping['message']='Successfylly Saved.';
															$this->order_model->insertshippingResponse($data_shipping);
															
														}
														
														return $manual_error;
													}
													else{
														//new sales order number found not created properly
														$error_response=$GetNewSalesResponseData;
														$error_request=$newOrderSaleRequestXml;
														$data_error['order_id']=$OrderId;
														$data_error['status']=0;
														$data_error['error_message']="New order Creation did not returned order number.";
														$data_error['error_xmlresponse']=(string)$error_response;
														$data_error['error_xmlrequest']=(string)$error_request;
														$insertID=$this->order_model->prontostatus($data_error);
														$data_error['type']=2;
														$data_error['message']=$data_error['error_message'];
														$data_error['manual']=1;
														$this->order_model->insertProntoResponse($data_error,$insertID);
														//$this->sendProntoMail($OrderId, $data_error['error_message'], $insertID);
														$manual_error['error_status']=1;
														$manual_error['message']=$data_error['error_message'];
														$manual_error['insertid']=$insertID;
														return $manual_error;
													}
												}
												else{
													//Order Posting Failed creation failed.
													$error_response=$GetNewSalesResponseData;
													$error_request=$newOrderSaleRequestXml;
													$data_error['order_id']=$OrderId;
													$data_error['status']=0;
													$data_error['error_message']="New order Creation Not successful.";
													$data_error['error_xmlresponse']=(string)$error_response;
													$data_error['error_xmlrequest']=(string)$error_request;
													$insertID=$this->order_model->prontostatus($data_error);
													$data_error['type']=2;
													$data_error['message']=$data_error['error_message'];
													$data_error['manual']=1;
													$this->order_model->insertProntoResponse($data_error,$insertID);
													//$this->sendProntoMail($OrderId, $data_error['error_message'], $insertID);
													$manual_error['error_status']=1;
													$manual_error['message']=$data_error['error_message'];
													$manual_error['insertid']=$insertID;
													return $manual_error;
												}
											}
											else{
												$error_response=$GetNewSalesResponseData;
												$error_request=$newOrderSaleRequestXml;
												$data_error['order_id']=$OrderId;
												$data_error['status']=0;
												$data_error['error_message']="Response from new order creation is not xml.";
												$data_error['error_xmlresponse']=(string)$error_response;
												$data_error['error_xmlrequest']=(string)$error_request;
												$insertID=$this->order_model->prontostatus($data_error);
												$data_error['type']=2;
												$data_error['message']=$data_error['error_message'];
												$data_error['manual']=1;
												$this->order_model->insertProntoResponse($data_error,$insertID);
												//$this->sendProntoMail($OrderId, $data_error['error_message'], $insertID);
												$manual_error['error_status']=1;
												$manual_error['message']=$data_error['error_message'];
												$manual_error['insertid']=$insertID;
												return $manual_error;
											}
										}
									}
								}
								else{
									//order detail not found.
									$data_error['order_id']=$OrderId;
									$data_error['status']=0;
									$data_error['error_message']="Orer Items not found";
									$insertID=$this->order_model->prontostatus($data_error);
									$data_error['type']=2;
									$data_error['message']=$data_error['error_message'];
									$data_error['manual']=1;
									$this->order_model->insertProntoResponse($data_error,$insertID);
									//$this->sendProntoMail($OrderId, $data_error['error_message'], $insertID);
									$manual_error['error_status']=1;
									$manual_error['message']=$data_error['error_message'];
									$manual_error['insertid']=$insertID;
									return $manual_error;
								}
							}
						}
					}
					else{
						//customer not found on orders.
						$data_error['order_id']=$OrderId;
						$data_error['status']=0;
						$data_error['error_message']="Customers Not Found.";
						$insertID=$this->order_model->prontostatus($data_error);
						$data_error['type']=2;
						$data_error['message']=$data_error['error_message'];
						$data_error['manual']=1;
						$this->order_model->insertProntoResponse($data_error,$insertID);
						//$this->sendProntoMail($OrderId, $data_error['error_message'], $insertID);
						$manual_error['error_status']=1;
						$manual_error['message']=$data_error['error_message'];
						$manual_error['insertid']=$insertID;
						return $manual_error;
					}
					/* Check if Customer Exists. */
				} 
				else{
				//Token node does not exist or Token node is empty
				$error=(string)$generate_token;
				$data_error['order_id']=$OrderId;
				$data_error['status']=0;
				$data_error['error_message']="Something went wrong with token. Token Not Found";
				$data_error['error_xmlresponse']=(string)$error;
				$insertID=$this->order_model->prontostatus($data_error);
				$data_error['type']=2;
				$data_error['message']=$data_error['error_message'];
				$data_error['manual']=1;
				$this->order_model->insertProntoResponse($data_error,$insertID);
				//$this->sendProntoMail($OrderId, $data_error['error_message'], $insertID);
				$manual_error['error_status']=1;
				$manual_error['message']=$data_error['error_message'];
				$manual_error['insertid']=$insertID;
				return $manual_error;
				}
			}
			else{
				$error=(string)$generate_token;
				$data_error['order_id']=$OrderId;
				$data_error['status']=0;
				$data_error['error_message']="Something went wrong with token";
				$data_error['error_xmlresponse']=(string)$error;
				$insertID=$this->order_model->prontostatus($data_error);
				$data_error['type']=2;
				$data_error['message']=$data_error['error_message'];
				$data_error['manual']=1;
				$this->order_model->insertProntoResponse($data_error,$insertID);
				//$this->sendProntoMail($OrderId, $data_error['error_message'], $insertID);
				$manual_error['error_status']=1;
				$manual_error['message']=$data_error['error_message'];
				$manual_error['insertid']=$insertID;
				return $manual_error;
			}
			
		}
		/*All Date send to Pronto end*/
	}

	//send mail
		public function sendProntoMail($order_number, $message, $insertID){
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
		$adminCredential=$this->order_model->getAllCredential();
		if($adminCredential['email']){ $emails=$adminCredential['email']; } else{  $emails='dark23shadow@gmail.com'; }
		
		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		$this->email->from('jasonldev5@gmail.com', 'Jasonl Pronto');
		$this->email->reply_to('jasonldev5@gmail.com', 'Jasonl');
		
		$data = array('order_number'=> $order_number, 'message'=>$message, 'insertId'=>$insertID);
		$this->email->to($emails); // replace it with receiver mail id
		$this->email->subject('Pronto Integration'); // replace it with relevant subject
		$body = $this->load->view('emails/prontofalilure.php',$data,TRUE);
		$this->email->message($body); 
		$this->email->send();
	}
	
	public function getDeliveryDate($shiftDay, $original_date){
		$delivery_date=date('Y-m-d', strtotime($original_date."+".$shiftDay." days"));
		return $delivery_date; 
	}
}
?>