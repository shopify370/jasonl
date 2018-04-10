<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Admin extends Base_Controller {

    function __construct() {
       
        parent::__construct();
    }

    /**
     * Login user on the site
     *
     * @return void
     */
    function index() {
//        echo 'test';die;
        //check for logged_in
        if ($this->ion_auth->logged_in()) {
            
            $this->ion_auth->is_admin() ? redirect('admin', 'referesh') : $this->ion_auth->logout();
            }

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        //validate form input
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == true) {
            //check to see if the user is logging in
            //check for "remember me"
//			$remember = (bool) $this->input->post('remember');
            $remember = TRUE;

            if ($this->ion_auth->login($this->input->post('username'), $this->input->post('password'), $remember)) {
                //if the login is successful check admin login
                if ($this->ion_auth->is_admin()) {
                    //redirect them back to the admin dashboard
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    redirect('admin', 'refresh');
                } elseif ($this->ion_auth->is_staff_admin_1()) {
//                    echo 'test';die;

                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    redirect('admin', 'refresh');
                } elseif ($this->ion_auth->is_staff_admin_2()) {
//                    echo 'test';die;

                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    redirect('admin', 'refresh');
                }else {
//                    echo 'here';die;
//                    $this->ion_auth->is_staff_admin();die;
//                    echo $this->db->last_query();die;
                    $this->ion_auth->logout();
                    $data['message'] = "Not a valid user";
                }
            } else {
                $data['message'] = $this->ion_auth->errors();
            }
        } else {
            //the user is not logging in so display the login page
            //set the flash data error message if there is one
            $data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            // Get username from session and form
            $data['username'] = $this->input->post('username') ? $this->input->post('username') : get_cookie('identity');
            $data['password'] = $this->input->post('password') ? '' : get_cookie('password');
        }
        // Load the view
        Template::set_layout('template/admin_login');
        Template::render('login', $data);
//        $this->load->view('login',$data);
    }
    
    
    function logout() {
        //log the user out
        $this->session->unset_userdata('group_id');
        
        $logout = $this->ion_auth->logout();
        $this->session->set_flashdata('message', $this->ion_auth->messages());

        //redirect them back to the page they came from
        redirect('admin/login');
    }

}

/* End of file auth.php */
/* Location: ./application/controllers/auth.php */