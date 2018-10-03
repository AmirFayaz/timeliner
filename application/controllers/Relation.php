<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Relation extends CI_Controller {
	
    public function __construct()
    {
        parent::__construct();
    }

    public function index($data_id)
	{
        // $this->output->enable_profiler(TRUE);

		$rel = $this->data_model->get($data_id);
		// need login
		$user_login = get_loggedin_user();
		if($user_login !== NULL && $rel!== NULL && $rel['created_by'] == $user_login['user_id'])
		{
			$data['rel'] = $rel;	

			$pages[] = page_make('relation/index',$data);
			load_view($pages);
		}
		else
		{
			redirect('');
		}
	}
}
