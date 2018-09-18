<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_model extends CI_Model
{
    const TABLE = 'data';
    public $data;
    
    public $expect;
    public $actual;

    public function __construct()
    {
        parent::__construct();
        
        $this->load->database();

        // functions from database helper
        $this->data = getDatabaseObject(self::TABLE);
        
        $this->expect = $this->data ;
        $this->expect['type'] = 'expect';
        
        $this->actual = $this->data ;
        $this->actual['type'] = 'actual';

        $this->relation = $this->data ;
        $this->relation['type'] = 'relation';

    }

    public function index()
    {
        // echo "<pre>";
        // print_r($this->data);
        // print_r($this->db_data);
        // echo "</pre>";
    }

    public function add_relation($param_id,$proj_id)
    {
        $prev_set = $this->db->get_where(self::TABLE,array(
                                'param_id' => $param_id,
                                'proj_id' => $proj_id,
                                'type' => $this->relation['type'],
                            ))->result_array();
        if($prev_set)
        {
            return -1;
        }
        
        $this->db->select_max('data_id');
        $result = $this->db->get(self::TABLE)->row();  
        $this->relation['data_id'] = $result->data_id+1;
        $this->relation['param_id'] = $param_id;
        $this->relation['proj_id'] = $proj_id;
        $this->relation['time'] = time();

        $insert = $this->db->insert(self::TABLE, $this->relation);

        if($insert) return $this->relation['data_id'];
        else return NULL;
    }

    public function get($data_id)
    {
        return $this->db->select('D.data_id, D.type, D.time, D.value,
                                    PR.*, PA.*')
                            ->from('data as D')
                            ->join('parameters as PA' , 'PA.param_id = D.param_id')
                            ->join('projects as PR' , 'PR.proj_id = D.proj_id')
                            ->where('D.data_id' , $data_id)
                            ->get()
                            ->row_array();
    }

    public function  get_relation($proj_id)
    {
        return $this->db->select('D.data_id, D.type, D.time, D.value,
                                    PR.*, PA.*')
                            ->from('data as D')
                            ->join('parameters as PA' , 'PA.param_id = D.param_id')
                            ->join('projects as PR' , 'PR.proj_id = D.proj_id')
                            ->where('D.proj_id' , $proj_id)
                            ->where('D.type' , 'relation')
                            ->get()
                            ->result_array();
    }

    // public function add_data($param_id,$proj_id)
    // {
    //     $data = $this->data;

    //     $this->db->select_max('data_id');
    //     $result = $this->db->get(self::TABLE)->row();  

    //     $data['caption'] = $caption;
    //     $data['unit'] = $unit;

    //     $insert = $this->db->insert(self::TABLE, $data);

    //     if($insert) return $data['data_id'];
    //     else return NULL;
    // }

    // public function get_data_by_id($data_id)
    // {
    //     $query = $this->db->select('*')
    //                 ->get_where(self::TABLE, array('data_id' => $data_id));
    //     return $query->row_array();
    // }
    
    // public function get_datas()
    // {
    //     $query = $this->db->select('*')->from(self::TABLE)->get();
    //     return $query->result_array();
    // }

}