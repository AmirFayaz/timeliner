<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
    
    private $user;
    private $db_data;

    public function __construct()
    {
        parent::__construct();
    
        $this->user = $this->users_model->user;
        $this->db_data = $this->users_model->db_data;
    }
    
    public function index()
    {
        echo "<pre>";
        print_r($this->user);
        print_r($this->db_data);
        echo "</pre>";
    }

    public function signup()
    {
        print_r($this->user);
        $a = [];
        $a['name'] = 'امیر';
        $a['mobile'] = '9158138299';
        $a['pass'] = 'amir';
        $this->users_model->add_user($a);
    }

    public function signout()
    {
        $this->session->unset_userData('user_login');
        $this->session->sess_destroy();
        redirect(base_url());
    }
    
}