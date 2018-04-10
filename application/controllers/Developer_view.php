<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * @property Webservice_model $webservice_model
 */
class Developer_view extends MX_Controller {

    public function __construct() {
        parent::__construct();

        if (ENVIRONMENT == 'production' and ( $this->input->get('user_access') != 'ebpearls.ta@gmail.com' or $this->input->get('user_pass') != 'ebpearls123456')) {
                exit('Access denied');
        }
    }

    public function index() {
        $links = array(
            array('link' => base_url('webservice/login'), 'title' => 'Login'),
            array('link' => base_url('webservice/forgot_password'), 'title' => 'Forgot Password'),
            array('link' => base_url('webservice/post_report'), 'title' => 'Post Report'),
            array('link' => base_url('webservice/add_report_images'), 'title' => 'Add Report Images'),
            array('link' => base_url('webservice/report_listing'), 'title' => 'Report Listing'),
            array('link' => base_url('webservice/report_detail'), 'title' => 'Report Detail'),
            array('link' => base_url('webservice/base_listing'), 'title' => 'Base Listing'),
            array('link' => base_url('webservice/airlines_listing'), 'title' => 'Airlines Listing'),
            array('link' => base_url('webservice/issue_types_listing'), 'title' => 'Issue Type Listing'),
            array('link' => base_url('webservice/city_listing'), 'title' => 'City Listing'),
            array('link' => base_url('webservice/employer_listing'), 'title' => 'Employer Listing'),
            array('link' => base_url('webservice/about'), 'title' => 'About'),
            array('link' => base_url('webservice/terms_conditions'), 'title' => 'Terms and Conditions'),
            array('link' => base_url('webservice/information'), 'title' => 'Information'),
            array('link' => base_url('webservice/handy_contacts'), 'title' => 'Handy Contacts'),
            array('link' => base_url('webservice/contact'), 'title' => 'Contacts'),
            array('link' => base_url('webservice/search_contact_form'), 'title' => 'Search Contacts'),
            array('link' => base_url('webservice/survey_question'), 'title' => 'Survey Question'),
            array('link' => base_url('webservice/survey_poll'), 'title' => 'Survey Poll'),
            array('link' => base_url('webservice/send_email'), 'title' => 'Send Email'),
            array('link' => base_url('webservice/logout'), 'title' => 'Logout'),
            array('link' => base_url('webservice/update_device_location'), 'title' => 'update Device Location'),
            array('link' => base_url('webservice/register_device'), 'title' => 'Register Device')
                
        );
        $this->load->view('developer_view', array('links' => $links));
    }

}
