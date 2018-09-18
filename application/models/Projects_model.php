<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projects_model extends CI_Model
{
    const TABLE = 'projects';
    public $project;
    public $db_data;


    public function __construct()
    {
        parent::__construct();
        
        $this->load->database();

        // functions from database helper
        $this->project = getDatabaseObject(self::TABLE);
        $this->db_data = getDatabaseObject(self::TABLE ,TRUE);
    }

    public function index()
    {
        echo "<pre>";
        print_r($this->project);
        print_r($this->db_data);
        echo "</pre>";
    }

    public function add_project($title,$created_by)
    {
        $project = $this->project;

        $this->db->select_max('proj_id');
        $result = $this->db->get(self::TABLE)->row();  
        $project['proj_id'] = $result->proj_id+1;
        $project['title'] = $title;
        $project['created_by'] = $created_by;
        $project['created_at'] = now();
        $insert = $this->db->insert(self::TABLE, $project);

        if($insert) return $project['proj_id'];
        else return NULL;
    }

    public function get_project_by_id($proj_id)
    {
        $query = $this->db->select('*')
                    ->get_where(self::TABLE, array('proj_id' => $proj_id));
        return $query->row_array();
    }
    
    public function get_projects_of_user($user_id)
    {
        $query = $this->db->select('*')
                    ->get_where(self::TABLE, array('created_by' => $user_id));
        return $query->result_array();
    }

}