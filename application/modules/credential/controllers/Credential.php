<?php
class Credential extends Admin_Controller {
	var $data;
	function __construct() {
		parent::__construct();	
        $this->load->model('cre_data');
	}

	public function index(){
		$data['credential'] = $this->cre_data->getAllCredentials();
       Template::render('admindetail',$data);
	}
	
	public function adminDetail(){
		if($_POST['submit']){
			$data=array();
			if($_POST['username']){ $username=$_POST['username']; } else{ $username=''; }
			if($_POST['password']){ $password=$_POST['password']; } else{ $password=''; }
			if($_POST['loginurl']){ $loginurl=$_POST['loginurl']; } else{ $loginurl=''; }
			if($_POST['email']){ $email=$_POST['email']; } else{ $email=''; }
			$data=array('id'=>1, 'username'=>$username, 'password'=>$password, 'loginurl'=>$loginurl, 'email'=>$email);
			$this->cre_data->insertOrUpdate($data);
		}
		else{
			
		}
		
		$data['credential'] = $this->cre_data->getAllCredentials();
		Template::render('admindetail',$data);
	}
}
?>