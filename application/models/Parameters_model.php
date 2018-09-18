<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Parameters_model extends CI_Model
{
    const TABLE = 'parameters';
    public $param;
    public $db_data;


    public function __construct()
    {
        parent::__construct();
        
        $this->load->database();

        // functions from database helper
        $this->param = getDatabaseObject(self::TABLE);
        $this->db_data = getDatabaseObject(self::TABLE ,TRUE);
    }

    public function index()
    {
        echo "<pre>";
        print_r($this->param);
        print_r($this->db_data);
        echo "</pre>";
    }

    public function add_param($caption,$unit)
    {
        $param = $this->param;

        $this->db->select_max('param_id');
        $result = $this->db->get(self::TABLE)->row();  

        $param['caption'] = $caption;
        $param['unit'] = $unit;

        $insert = $this->db->insert(self::TABLE, $param);

        if($insert) return $param['param_id'];
        else return NULL;
    }

    public function get_param_by_id($param_id)
    {
        $query = $this->db->select('*')
                    ->get_where(self::TABLE, array('param_id' => $param_id));
        return $query->row_array();
    }
    
    public function get_params()
    {
        $query = $this->db->select('*')->from(self::TABLE)->get();
        return $query->result_array();
    }

}