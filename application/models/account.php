<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends CI_Model {


	function __construct(){
		parent::__construct();
	}
	
	function get_pending_to_activate($days){
		$sql = "SELECT * FROM user where status = 'O' and DATE_ADD(creation, INTERVAL $days DAY) < CURDATE()";
		$result = $this->db->query($sql)->result();
		if(count($result))
			return $result;
			
		return false; 
	}
	
	function delete($id){
		return $this->db->delete('user', array('id' => $id)); 
	}

	function auth($username, $password){
				
		if(!$username || !$password)
			return false;
		
		$password = md5($password);
	
		$user = $this->db->get_where('sys_user', array('username' => $username, 'password' => $password))->result();
		if(count($user))
			return $user[0];
			
		return false; 
	}		
}