<?php
class Testdata extends Base_Controller {
	var $data;
	function __construct() {
		parent::__construct();	
        $this->load->model('cre_data');
	}

	public function index(){
		$data1 = file_get_contents('php://input'); 
		error_log('Zapier settings'); 
		error_log($data1);
	}
	
}
?>