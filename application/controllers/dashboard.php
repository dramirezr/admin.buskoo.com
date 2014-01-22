<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	var $title = 'Dashboard';
	var $dasboard_params = array();
		
	function __construct(){
		parent::__construct();
		if(!$user = $this->session->userdata('user')){
			$this->lang->switch_uri($user->lang);		
			redirect($user->lang.'/login'); 
		}

	}
	
	function index(){		
		$this->render();
	}
	
	private function render(){
		
		$this->template->write('title', $this->title);
		
		$this->template->add_js('https://www.google.com/jsapi', 'import', FALSE, FALSE);

		$this->template->write_view('content', 'templates/dashboard', $this->dasboard_params, TRUE);
		
		$this->template->render();		
	}
	
}