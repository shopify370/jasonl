<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Description of login
 *
 * @author rabin
 */
class Login extends Front_Controller {

    function __construct() {
        parent::__construct();

        $this->load->library('form_validation');
        $this->form_validation->CI = & $this;
        $this->template->set_layout('template/frontend_template');
    }

    public function index() {
 if ($this->ion_auth->logged_in()) {
            
            $this->ion_auth->is_staff_admin_1() ? redirect('staff_admin_1', 'referesh') : $this->ion_auth->logout();
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
                if ($this->ion_auth->is_staff_admin_1()) {
                    //redirect them back to the admin dashboard
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    redirect('staff_admin_1', 'refresh');
                } else {

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

    public function register() {
//       if ($this->member_auth->logged_in())
//          redirect('welcome');
        // echo'hello';die;
        // echo $this->input->post('first_name');
        $status = 0;
        $this->data = array();

        $this->form_validation->set_rules(array(
            array(
                'field' => 'first_name',
                'label' => 'First Name',
                'rules' => 'xss_clean|trim|required|max_length[200]'
            ),
            array(
                'field' => 'last_name',
                'label' => 'Last Name',
                'rules' => 'xss_clean|trim|required|max_length[200]'
            ),
            array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'valid_email|xss_clean|trim|required|max_length[200]|unique[members.member_email]'
            ),
            array(
                'field' => 'postcode',
                'label' => 'Postcode',
                'rules' => 'xss_clean|trim|required|max_length[10]'
            ),
            array(
                'field' => 'terms_conditions',
                'label' => 'terms_conditions',
                'rules' => 'xss_clean|trim|required'
            ),
            array(
                'field' => 'address',
                'label' => 'Address',
                'rules' => 'xss_clean|trim|max_length[500]'
            ),
            array(
                'field' => 'membership_type',
                'label' => 'Membership Type',
                'rules' => 'xss_clean|trim|max_length[12]|integer|required'
            ),
            array(
                'field' => 'confirm_password',
                'label' => 'Confirm Password',
                'rules' => 'trim|xss_clean|required|max_length[50]|matches[password]'
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|xss_clean|required|max_length[50]'
            ),
        ));

        if ($this->form_validation->run() == TRUE) {
            // echo 'hi';die;
            $email = $_POST['email'];
            $password = $_POST['password'];
            $activation_code = sha1(md5(microtime()));
            $additionalData = array(
                'member_first_name' => $this->input->post('first_name'),
                'member_last_name' => $this->input->post('last_name'),
                'member_contact_no' => $this->input->post('mobile'),
                'member_postcode' => $this->input->post('postcode'),
                'member_address' => $this->input->post('address'),
                'membership_type_id' => $this->input->post('membership_type'),
                'activation_code' => $activation_code
            );
            if ($userId = $this->member_auth->register($email, $password, $additionalData)) {
                // echo 'hi';die;
//                $this->data['msg'] = $this->member_auth->get_message();
                $this->session->set_flashdata('msg', $this->member_auth->get_messages());
                $status = 1;

//                redirect('user/login');
                if ($status == 1) {
                    $this->load->library('email');
                    $from = "info@rewards.com.au";

                    $message = "<html>
                            <body>
                                <p><b>Hi {$this->input->post('first_name')}, </b></p>
                                <p>To activate your rewards account "
                            . anchor('activate/' . $userId . '/' . $activation_code, 'click here') .
                            "</p>
                            </body>
                            </html>";
                    $this->email->to($_POST['email'])
                            ->from($from, "Rewards")
                            ->subject('welcome')
                            ->message($message);

                    $this->email->send();
                }
                echo json_encode(array('status' => $status));
            } else {
                $status = 0;
                $msg = $this->member_auth->get_errors();
                echo json_encode(array('status' => $status, 'msg' => $msg));
            }
        } else {
            $status = 0;
            $msg = validation_errors();
            echo json_encode(array('status' => $status, 'msg' => $msg));
        }
    }

    public function edit_profile() {
//       if ($this->member_auth->logged_in())
//          redirect('welcome');
        // echo'hello';die;
        // echo $this->input->post('first_name');
        $status = 0;
        $this->data = array();

        $this->form_validation->set_rules(array(
            array(
                'field' => 'first_name',
                'label' => 'First Name',
                'rules' => 'xss_clean|trim|required|max_length[200]'
            ),
            array(
                'field' => 'last_name',
                'label' => 'Last Name',
                'rules' => 'xss_clean|trim|required|max_length[200]'
            ),
            array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'valid_email|xss_clean|trim|required|max_length[200]'
            ),
            array(
                'field' => 'postcode',
                'label' => 'Postcode',
                'rules' => 'xss_clean|trim|required|max_length[10]'
            ),
            array(
                'field' => 'address',
                'label' => 'Address',
                'rules' => 'xss_clean|trim|max_length[500]'
            ),
            array(
                'field' => 'membership_type',
                'label' => 'Membership Type',
                'rules' => 'xss_clean|trim|max_length[12]|integer|required'
            ),
        ));

        if ($this->form_validation->run() == TRUE) {
            // echo 'hi';die;
            $id = $this->current_member->member_id;
            $email = $_POST['email'];
            $first_name = $this->input->post('first_name');
            $last_name = $this->input->post('last_name');
            $member_contact_no = $this->input->post('mobile');
            $member_postcode = $this->input->post('postcode');
            $member_address = $this->input->post('address');
            $membership_type = $this->input->post('membership_type');



            if ($userId = $this->member_auth->edit_profile($id, $first_name, $last_name, $email, $member_address, $member_contact_no, $member_postcode, $membership_type) == TRUE) {
                //echo 'hi';die;
//                $this->data['msg'] = $this->member_auth->get_message();
//                $this->session->set_flashdata('msg', $this->member_auth->get_messages());
                $status = 1;

//                redirect('user/login');

                echo json_encode(array('status' => $status));
            } else {
                //echo $this->db->last_query();die;

                $status = 0;
                $msg = $this->member_auth->get_errors();
                echo json_encode(array('status' => $status, 'msg' => $msg));
            }
        } else {
            $status = 0;
            $msg = validation_errors();
            // echo $msg;die;
            echo json_encode(array('status' => $status, 'msg' => $msg));
        }
    }

    function change_password() {
         $insrtdb = $this->load->database('webservice', TRUE);
        $status = 0;
        $this->data = array();
        $this->form_validation->set_rules(array(
            array(
                'field' => 'old_password',
                'label' => 'Old Password',
                'rules' => 'trim|xss_clean|required|max_length[50]'
            ),
            array(
                'field' => 'confirm_password',
                'label' => 'Confirm Password',
                'rules' => 'trim|xss_clean|required|max_length[50]|matches[password]'
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|xss_clean|required|max_length[50]'
            ),
        ));
        if ($this->form_validation->run() == TRUE) {
            $id = $this->current_member->member_id;
            $password = encryptPassword($this->input->post('password'));
            $old_password = encryptPassword($this->input->post('old_password'));
            $query = $insrtdb->query("CALL sp_changePassword('$id','$old_password','$password')");
            
             $insrtdb->reconnect();
            $result = $query->row();
            if ($result->status == 1) {
                $status = 1;
                echo json_encode(array('status' => $status));
            } elseif($result->status == 0) {
                $status = 0;
                $msg = "Your old password didn't match";
                echo json_encode(array('status' => $status, 'msg' => $msg));
            }
        } else {
            $status = 0;
            $msg = validation_errors();
//            echo $msg;
//            die;
            echo json_encode(array('status' => $status, 'msg' => $msg));
        }
    }

    public function logout() {
        if ($this->member_auth->logged_in()) {
            $this->member_auth->logout();
            $this->session->set_flashdata('msg', $this->member_auth->get_messages());
        }
        redirect('');
    }

    public function forgot_password() {

        if ($this->input->is_ajax_request() && $this->input->post()) {
            $insrtdb = $this->load->database('webservice', TRUE);
            // debug($_POST);
            if ($this->_valid_email()) {
                $this->load->library('email');
                $email = $this->input->post('user-email');
                $query = $insrtdb->query("CALL sp_checkmember('$email')");
                $insrtdb->reconnect();

                $result = $query->row();

                if ($result->status == 1) {
                    $token = $this->member_auth->hashCode(20);
                    $temp_pass = $this->member_auth->hashCode(8);
                    $encrypt_temp_pass = $this->member_auth->encryptPassword($temp_pass);
                    $url = site_url("member/reset_password/{$result->member_id}/{$token}");
                    // echo $url;
                    //$this->db->reconnect();
                    $insrtdb = $this->load->database('webservice', TRUE);
                    $insrtdb->query("CALL sp_assigntoken('$email','$token','$encrypt_temp_pass')");
                    $insrtdb->reconnect();


                    $to = $email;
                    $subject = "Password Reset";

                    $message = "<p>Reset Password.</p>";
//				$message .= "<p>Your new password is: ".$temp_pass."</p>";
                    $message .= "<p>Please <a href='{$url}' >click here</a> to change the password.</p>";
                    // $message .= "<label><a href=\"{$url}\" target=\"_blank\">{$url}</a></label>";

                    $from = "info@rewards.com.au";

                    $this->email->to($to)
                            ->from($from, 'Rewards Club App')
                            ->subject($subject)
                            ->message($message);
                    $this->email->send();
                    echo json_encode(array(
                        'status' => 'success'
                    ));
                    exit();

//                    if ($this->email->send()) {
//                        echo json_encode(array(
//                            'status' => 'success'
//                        ));
//                        exit();
//                    } else {
//                        echo json_encode(array(
//                            'status' => 'not_sent',
//                        ));
//                        exit();
//                    }
                } else {
                    echo json_encode(array(
                        'status' => 'not_found',
                    ));
                    exit();
                }
            } else {

                echo json_encode(array(
                    'status' => 'error',
                    'errors' => array(
                        'user-email' => form_error('user-email','<div class= "error show-error">','</div>')
                    ))
                );
                exit();
            }
        }
    }

    function reset_password($user_id, $token) {
        $insrtdb = $this->load->database('webservice', TRUE);
        $user = $insrtdb->where('change_password_token', $token)->where('member_id', $user_id)->get('members')->row(); //pass the code to profile

        if (is_object($user)) {
            $this->load->library(array('form_validation', 'session'));

            $this->form_validation->set_rules('new', 'New Password', 'required|min_length[4]|matches[new_confirm]');
            $this->form_validation->set_rules('new_confirm', "Confirm Password", 'required');

            if ($this->form_validation->run() == false) {
                //display the form
                //set the flash data error message if there is one
                $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

                $this->data['min_password_length'] = 6;
                $this->data['new_password'] = array(
                    'name' => 'new',
                    'id' => 'new',
                    'type' => 'password'
                );
                $this->data['new_password_confirm'] = array(
                    'name' => 'new_confirm',
                    'id' => 'new_confirm',
                    'type' => 'password'
                );
            } else {
                $identity = (int) $user_id;

                $new_pass = $this->input->post('new');
                
                $encrypt_newpass = encryptPassword($new_pass);

                $query = $insrtdb->query("CALL sp_resetPassword('{$identity}','{$encrypt_newpass}')");
                $insrtdb->reconnect();
                $result = $query->row();
                if ($result->status == 1) {

                    $this->data['content'] = "Your password has been changed.";
                } else {

                    $this->data['content'] = "<p>Sorry, your link couldn't be validated.</p>";
                    //$this->data['content'] .= "<p>You can now close this window.</p>";
                }
            }
        } else {

            $this->data['content'] = "<p>Sorry, your link couldn't be validated.</p>";
        }
        $this->template->add_title_segment('Information For Members');

        Template::render('frontend/forgot_password', $this->data);
//        $this->load->view('frontend/forgot_password', $this->data);
    }

    function _valid_email() {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        $this->form_validation->set_rules('user-email', 'Email', 'required|valid_email');
        return $this->form_validation->run($this);
    }

//    function member_check($email) {
//        $result = $this->member_m
//        if ($result) {
//            $this->CI->form_validation->set_message('email_check', "The %s field does not have our email.");
//            return FALSE;
//        } else {
//            return TRUE;
//        }
//    }

    function activate($member_id, $activation_code) {
        $id = (int) $member_id;
        $checkuserid = $this->db->query("select member_id from members where member_id = '$id' and activation_code = '$activation_code'");
        if ($checkuserid->num_rows() > 0) {
            $this->db->update('members', array('member_active' => '1'), array('member_id' => $id));
            $this->db->update('members', array('activation_code' => ''), array('member_id' => $id));
            $member_email = $this->db->select('member_email,member_first_name')->from('members')->where("member_id = '$id'")->get();
            $member_email = $member_email->row();
            $this->load->library('email');

            $to = $member_email->member_email;
            $subject = "Welcome to Rewards Club";

            $message = "<html>
                                <body>
                                    <p><b>Hi {$member_email->member_first_name}, </b></p>
                                    <p>
                                        Welcome!!!
                                    </p>
                                </body>
                                </html>";

            $from = "info@rewards.com.au";
            $this->email->to($member_email->member_email)
                    ->from($from, "Rewards")
                    ->subject($subject)
                    ->message($message);

            $this->email->send();

            echo $content = "<p>Your account is activated now.Thank you</p>";
        } else {

            echo $content = "<p>Invalid Link</p>";
        }
    }

}
