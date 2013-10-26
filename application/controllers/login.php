<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	var $title = 'Login';
	var $error = NULL;
	var $enterprise = NULL;
	
	function __construct(){
		parent::__construct();
		
		if($user = $this->session->userdata('user')){
			$this->lang->switch_uri($user->lang);		
			redirect($user->lang.'/dashboard'); 
		}
		// load language file
		$this->lang->load('login');
	}
	
	public function index(){
		$this->load_view();
	}
	
	function do_login(){
		
		$username = $this->input->post('username', TRUE); 
		$password = $this->input->post('password', TRUE);
		 
		$this->load->model('account');
		
		if(!$user = $this->account->auth($username, $password)){
			$this->error = lang('login.error.noauth');
			$this->index();
			return false;
		}
		
		//Create the session
		$user->password = NULL;
		$this->session->set_userdata('user', $user);
		
		//load the features
		$config = ($user->config) ? unserialize($user->config) : array();
		$this->session->set_userdata('user_config', $config);
		
		$this->lang->switch_uri($user->lang);		
		redirect($user->lang.'/dashboard'); 
	}
	
	private function load_view(){
		
		$this->load->view('login', array(
					'title' => $this->title,
					'error' => $this->error,
					'enterprise' => $this->enterprise
					));
	}
	
	
} 
 
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */