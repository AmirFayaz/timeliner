<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends CI_Model
{
    const TABLE = 'users';
    public $user;
    public $db_data;


    public function __construct()
    {
        parent::__construct();
        
        $this->load->database();

        // functions from database helper
        $this->user = getDatabaseObject(self::TABLE);
        $this->db_data = getDatabaseObject(self::TABLE ,TRUE);

    }

    public function index()
    {
        echo "<pre>";
        print_r($this->user);
        print_r($this->db_data);
        echo "</pre>";
    }

    public function checkNewMobile($mobile)
    {
        $query = $this->db->get_where(self::TABLE, array('mobile' => $mobile));
        if($query->row_array() !== NULL)
        {
            return false;
        }
        return true;
    }

    public function checkUserLogin($mobile,$password)
    {
        $query = $this->db->select('user_id,name,mobile')
                ->from(self::TABLE)
                ->where( array('mobile' => $mobile , 'password'  => $password))
                ->get();
        if($query->row_array() === NULL)
        {
            return FALSE;
        }
        return $query->row_array();
    }

    public function add_user($_user)
    {
        return $this->db->insert(self::TABLE, $_user);
    }

    public function get_user_by_mobile($mobile)
    {
        $query = $this->db->get_where(self::TABLE, array('mobile' => $mobile));
        return $query->row_array();
    }

    public function get_user_by_id($user_id)
    {
        $query = $this->db->select('user_id,name,mobile')
                    ->get_where(self::TABLE, array('user_id' => $user_id));
        return $query->row_array();
    }

/*
    public function get_user_by_id($user_id = FALSE)
    {
        if ($user_id === FALSE)
        {
            $query = $this->db->get('users');
            return $query->result_array();
        }

        $query = $this->db->get_where('users', array('user_id' => $user_id));
        return $query->row_array();
    }

    public function get_fullname($user_id)
    {
        $query = ($this->db->get_where('users', array('user_id' => $user_id)))->row_array();
        return $query['firstname'].$query['lastname'];
    }


    public function check_user_signin($signinInput)
    {
//        $user = NULL;
        $mobile =   $signinInput['signinInputMobile'];
        $pass   = $signinInput['signinInputPassword'];
        
        $user = $this->get_users($mobile);
        
        if($user['password']===$pass)
        {
            return $mobile;
        }
        return 0;
    }
    */
//    public function add_user($fname=NULL,$lname=NULL,$mobile,$pass)
}