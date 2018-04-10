<?php
	class Order_model extends CI_Model {
        public function __construct(){
            $this->load->database();
        }

        public function saveorder($order){
			$orderid=$order['order_id'];
			$sku=$order['sku'];
			$price=$order['price'];
			$quantity=$order['quantity'];
			$data = array('order_id'=>$orderid, 'sku'=>$sku, 'price'=>$price, 'quantity'=>$quantity );
			$this->db->insert('skuorder',$data);
		}
		
		public function saveerror($error){
			$orderid=$error['order_id'];
			$product_id=$error['message'];
			$varient=$error['order_id'];
			$message=$error['message'];
			$data = array('order_id'=>$orderid, 'product_id'=>$product_id, 'variant_id'=>$varient, 'message'=>$message );
			$this->db->insert('skuerror',$data);
		}
		
		 public function saveOrderDetail($order){
			$orderid=$order['order_id'];
			$status=$order['status'];
			if(isset($order['orderdetail'])){ $orderdetail=$order['orderdetail'];	} else{	$orderdetail=''; }
			if(isset($order['product_id'])){  $product_id=$order['product_id']; }	else{	$product_id='';	}
			if(isset($order['varient_id'])){ $varient_id=$order['varient_id'];	} else{	$varient_id='';	}
			if(isset($order['sku'])){ $sku=$order['sku'];	} else{	$sku=''; }
			if(isset($order['prontointegrate'])){ $prontointegrate=$order['prontointegrate'];	} else{	$prontointegrate=0; }

			$data = array('order_id'=>$orderid, 'order_detail'=>$orderdetail, 'status'=>$status, 'product_id'=>$product_id, 'varient_id'=>$varient_id, 'sku'=>$sku, 'prontointegrate'=>$prontointegrate);
			$this->db->insert('orderlist',$data);
			$insert_id = $this->db->insert_id();
			return $insert_id;
		}
		
		public function prontostatus($pronto){
			$orderid=$pronto['order_id'];
			$status=$pronto['status'];
			if(isset($pronto['error_message'])){ $error_message=$pronto['error_message']; } else{ $error_message=''; }
			if(isset($pronto['error_xmlresponse'])){ $xmlresponse=$pronto['error_xmlresponse']; } else{ $xmlresponse=''; }
			if(isset($pronto['error_xmlrequest'])){ $xmlrequest=$pronto['error_xmlrequest']; } else{ $xmlrequest=''; }
			if(isset($pronto['customerId'])){ $customerId=$pronto['customerId']; } else{ $customerId=''; }
			if(isset($pronto['pronto_orderId'])){ $prontoOrderId=$pronto['pronto_orderId']; } else{ $prontoOrderId=''; }
			$data = array('order_id'=>$orderid, 'error_message'=>$error_message, 'status'=>$status, 'error_xmlresponse'=>$xmlresponse, 'error_xmlrequest'=>$xmlrequest, 'customer_id'=>$customerId, 'pronto_OrderId'=>$prontoOrderId );
			$this->db->insert('prontostatus',$data);
			$insert_id = $this->db->insert_id();
			return $insert_id;
		}
		
		public function getAllCredential(){
			$this->db->select("*");
			$this->db->from("credential");
			$this->db->where("id", 1);
			$query = $this->db->get();
			return $query->row_array();
		}
		
		public function insertProntoResponse($orderDetail, $insertId){
			$orderid=$orderDetail['order_id'];
			$status=$orderDetail['status'];
			$type=$orderDetail['type'];
			$insertid=$insertId;
			if(isset($orderDetail['message'])) { $message=$orderDetail['message']; } else { $message=''; }
			if(isset($orderDetail['manual'])) { $manual=$orderDetail['manual']; } else { $manual=0; }
			$data = array('order_id'=>$orderid, 'message'=>$message, 'status'=>$status, 'type'=>$type, 'insertId'=>$insertid, 'manual'=>$manual );
			$this->db->insert('prontoresponselist',$data);
		}
		
		public function insertshippingResponse($shippingDetail){
			if($shippingDetail['insert_id']){ $data['insert_id']=$shippingDetail['insert_id']; } else{ $data['insert_id']=''; }
			if($shippingDetail['delivery_request']){ $data['delivery_request']=$shippingDetail['delivery_request']; } else{ $data['delivery_request']=''; }
			if($shippingDetail['delivery_response']){ $data['delivery_response']=$shippingDetail['delivery_response']; } else{ $data['delivery_response']=''; }
			if($shippingDetail['message']){ $data['message']=$shippingDetail['message']; } else{ $data['message']=''; }
			$this->db->insert('shipping_request',$data);
		}
		
		public function insertCustomerResponse($customerData){
			if($customerData['insert_id']){ $data['insert_id']=$customerData['insert_id']; } else{ $data['insert_id']=''; }
			if($customerData['customer_request']){ $data['customer_request']=$customerData['customer_request']; } else{ $data['customer_request']=''; }
			if($customerData['customer_response']){ $data['customer_response']=$customerData['customer_response']; } else{ $data['customer_response']=''; }
			$this->db->insert('customer_request',$data);
		}
		
		public function checkforOrderId($orderId){
			$this->db->select("*");
			$this->db->from("prontostatus");
			$this->db->where("order_id", $orderId);
			$query = $this->db->get();
			return $query->row_array();
		}
		
		public function get_order_for_pronto(){
			$array_condition = array('status' => 1, 'prontointegrate' => 0);
			$this->db->select("*");
			$this->db->from("orderlist");
			$this->db->where($array_condition);
			$query = $this->db->get();
			return $query->result_array();
		}

		public function updateprontostatus($insertid){
			if($insertid){
				$this->db->set('prontointegrate', 1); 
				$this->db->where('id', $insertid); 
				$this->db->update('orderlist');
			}
		}
		
		public function saveemailonly($email){
			$this->db->select('*');
			$this->db->from("pronto_email");
			$this->db->where('email_add',$email);
			$query = $this->db->get();

			if(empty($query->result())) {
				$data['email_add']=$email;
				$this->db->insert('pronto_email',$data);
			}
		}
		
		public function check_order_number($order_number){
			if($order_number){
				$this->db->select('*');
				$this->db->from("orderlist");
				$this->db->where('order_id',$order_number);
				$query = $this->db->get();
				if(empty($query->result())) {
					return true;
				} else {
					return false;
				}
			}
			else{
				return true;
			}
		}
		
	}
?>