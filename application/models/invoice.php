<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invoice extends CI_Model {


	function __construct(){
		parent::__construct();
	}

	function get_by_id($id){
		return $this->db->get_where('invoice', array('id' => $id))->result();
	}
	
	function update($id, $data){
		return $this->db->update('invoice', $data, array('id' => $id));
	}
}