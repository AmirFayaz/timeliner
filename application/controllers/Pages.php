<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends CI_Controller {



	public function dashboard()
	{

        $this->output->enable_profiler(TRUE);

		// need login
		$user_login = get_loggedin_user();
		// pr($user_login);die();
		if(isset($user_login['user_id']))
		{
			$projects = $this->projects_model->get_projects_of_user($user_login['user_id']);
			$data['projects'] = $projects;
			$parameters = $this->parameters_model->get_params();
			$data['parameters'] = $parameters;
			$pages[] = page_make('pages/dashboard' , $data);
			load_view($pages,'dashboard');
		}
		else
		{
			redirect('welcome');
		}
	}

	public function home()
	{
		$pages[] = page_make('welcome_message');
		load_view($pages);
	}

}
