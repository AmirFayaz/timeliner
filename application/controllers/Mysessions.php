<?php

class Mysessions extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo '<pre>';
        print_r(session_id());
        echo '<br/>';
        echo '<br/>';
        print_r($this->get());
        echo '</pre>';

    }

    public function get($property = NULL)
    {
        if($property === NULL)
        {
            return $this->session->get_userdata();
        }
        return $this->session->userdata($property);
    }

    public function set($property,$value)
    {
        return $this->session->set_userdata($property,$value);
    }

    public function unset($property)
    {
        return $this->session->unset_userdata($property);
    }

    public function has($property)
    {
        return $this->session->has_userdata($property);
    }


}