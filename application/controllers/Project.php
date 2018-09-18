<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends CI_Controller {
	
    public function __construct()
    {
        parent::__construct();
    }

    public function edit($proj_id)
	{
        // $this->output->enable_profiler(TRUE);
		$proj = $this->projects_model->get_project_by_id($proj_id);
		
		// need login
		// echo $proj_id;
		$user_login = get_loggedin_user();
		if($user_login !== NULL && $proj!== NULL && $proj['created_by'] == $user_login['user_id'])
		{
			$parameters = $this->parameters_model->get_params();
			$data['parameters'] = $parameters;
			
			$relation = $this->data_model->get_relation($proj_id);
			$data['relation'] = $relation;	

			$data['proj']=$proj;

			$pages[] = page_make('project/edit',$data);
			load_view($pages);
		}
		else
		{
			redirect('');
		}
	}
}
