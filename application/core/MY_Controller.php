<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Base_Controller extends MX_Controller {
    protected $current_user = NULL;
    protected $current_user_group = NULL;
     protected $data = array();
      public $autoload = array(
        'helper' => array('url', 'form'),
        'libraries' => array('auth/ion_auth'),
    );
   
    
    public function __construct() {
        parent::__construct();
         if ($this->ion_auth->logged_in()) {
            $this->current_user = $this->ion_auth->user()->row();
            $this->current_user_group  = $this->ion_auth->get_users_groups($this->current_user->id)->row();;
            
            // $this->current_user->id = (int) $this->current_user->user_id;
//			$this->current_user->user_img = gravatar_link($this->current_user->email, 22, $this->current_user->email, "{$this->current_user->email} Profile");
            $this->load->library('session');
            $userdata = array('user_group' => $this->current_user_group);
            $this->session->set_userdata($userdata);
        }
        Template::set('current_user', $this->current_user);
        Template::set('current_user_group', $this->current_user_group);
   }
    
}
class Admin_Controller extends Base_Controller{
     public function __construct() {
        parent::__construct();
//        $this->data = array('3','4');
if ($this->ion_auth->is_admin() === FALSE) {
        if($this->ion_auth->is_staff_admin_1() == FALSE){
                if($this->ion_auth->is_staff_admin_2() == FALSE){
                     redirect('admin/login');   
                }
        }
            
        }
        Template::set_base_title('Admin Panel');
        Template::set_layout('template/layout');

        $this->load->helper(array('html'));

        // Pagination config
        $this->pager = array();
        $this->pager['full_tag_open'] = '<div class="table_pagination left">';
        $this->pager['full_tag_close'] = '</div>';
        $this->pager['next_link'] = '&raquo;';
        $this->pager['prev_link'] = '&laquo;';

        $this->pager['first_link'] = '&laquo;&laquo;';
        $this->pager['prev_link'] = '&laquo;';
        $this->pager['cur_tag_open'] = '<a href="javascript:void(0)" class="active">';
        $this->pager['cur_tag_close'] = '</a>'; 

    }
}

//class Staff_Admin_1_Controller extends Base_Controller{
//     public function __construct() {
//        parent::__construct();
//if ($this->ion_auth->is_staff_admin_1() === FALSE) {
//            redirect('admin/login');
//        }
//        Template::set_base_title('Staff Admin Panel');
//        Template::set_layout('template/staff_admin_1');
//
//        $this->load->helper(array('html'));
//
//        // Pagination config
//        $this->pager = array();
//        $this->pager['full_tag_open'] = '<div class="table_pagination left">';
//        $this->pager['full_tag_close'] = '</div>';
//        $this->pager['next_link'] = '&raquo;';
//        $this->pager['prev_link'] = '&laquo;';
//
//        $this->pager['first_link'] = '&laquo;&laquo;';
//        $this->pager['prev_link'] = '&laquo;';
//        $this->pager['cur_tag_open'] = '<a href="javascript:void(0)" class="active">';
//        $this->pager['cur_tag_close'] = '</a>'; 
//
//    }
//}
//
//class Staff_Admin_2_Controller extends Base_Controller{
//     public function __construct() {
//        parent::__construct();
//if ($this->ion_auth->is_staff_admin_2() === FALSE) {
//            redirect('admin/login');
//        }
//        Template::set_base_title('Staff Admin Panel');
//        Template::set_layout('template/staff_admin_2');
//
//        $this->load->helper(array('html'));
//
//        // Pagination config
//        $this->pager = array();
//        $this->pager['full_tag_open'] = '<div class="table_pagination left">';
//        $this->pager['full_tag_close'] = '</div>';
//        $this->pager['next_link'] = '&raquo;';
//        $this->pager['prev_link'] = '&laquo;';
//
//        $this->pager['first_link'] = '&laquo;&laquo;';
//        $this->pager['prev_link'] = '&laquo;';
//        $this->pager['cur_tag_open'] = '<a href="javascript:void(0)" class="active">';
//        $this->pager['cur_tag_close'] = '</a>'; 
//
//    }
//}

class Front_Controller extends Base_Controller{
    
    public function __construct() {
        parent::__construct();

    }
}