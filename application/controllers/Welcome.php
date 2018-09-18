<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {



	public function index()
	{
		$data_last_session = $this->session->has_userdata('__ci_last_regenerate');
		$data_user_login = $this->session->has_userdata('user_login');

		if(	$data_last_session && $data_user_login )
		{
			redirect('dashboard', 'location');
		}
		// $pages[] = page_make('welcome_message');
		$pages[] = page_make('users/entrance');
		load_view($pages,'entrance',false);
	}
}
