<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Config_model extends CI_Model {


	function __construct(){
		parent::__construct();
	}
	
	function get_value($key){

		if(!$key)
			return false;
		
		$sql = "SELECT * FROM config WHERE keyname = '$key'";
		$result = $this->db->query($sql)->result();
		if(count($result))
			return $result[0]->value;
			
		return false; 
	}
	
	function get_follow_us_links(){
		$sql = "SELECT * FROM config WHERE keyname LIKE 'follow_us_%'";
		$result = $this->db->query($sql)->result();
		if(count($result))
			return $result;
			
		return false;		
	}
}