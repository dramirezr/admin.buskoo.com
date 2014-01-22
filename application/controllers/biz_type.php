<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Biz_type extends CI_Controller {

	var $title = 'Biz Type';
	var $setup_params = array();
		
	function __construct(){
		parent::__construct();
		
		if(!$user = $this->session->userdata('user')){
			$this->lang->switch_uri($user->lang);		
			redirect($user->lang.'/login'); 
		}

		$this->load->library('grocery_CRUD');
	}
	
	function index(){	
		//$this->grocery_crud->set_theme('datatables');
		
		$this->grocery_crud->set_table('biz_type');
		
		//$this->grocery_crud->unset_add();
		//$this->grocery_crud->unset_delete();
		//$this->grocery_crud->unset_texteditor('notes');
		
		/* Campos de la lista */
		//$this->grocery_crud->set_theme('datatables');
		$this->grocery_crud->columns('id','name','hits');
		$this->grocery_crud->add_fields('id_parent', 'name', 'tag','icon');
    	$this->grocery_crud->edit_fields('id_parent', 'name', 'tag','icon');
    	$this->grocery_crud->display_as('id_parent', 'parent');
		$this->grocery_crud->set_relation('id_parent', 'biz_type', 'name');
		$this->grocery_crud->set_field_upload('icon','assets/images/icon');
					
		$this->setup_params['output'] = $this->grocery_crud->render();
  		$this->setup_params['title'] = lang('setup.rooms.title');
  		
		$this->render();
	}
	
	
	private function render(){
		
		$this->template->write('title', $this->title);
		
		$this->template->add_js('https://www.google.com/jsapi', 'import', FALSE, FALSE);

		$this->template->write_view('content', 'templates/crud', $this->setup_params, TRUE);
		
		$this->template->render();		
	}
	
	
	
}