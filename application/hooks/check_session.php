<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function check_session(){
	$CI =& get_instance();
	
	if(!$CI->session->userdata('user') && $CI->uri->segment(2) != 'login')
		redirect('login'); 
}