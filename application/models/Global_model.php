<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Global_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->database();
    }

    public function delete($table,$id)
    {
        $table_id = $table.'_id';
        $this->db->where( $table_id, $id);
        $this->db->delete($table); 
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