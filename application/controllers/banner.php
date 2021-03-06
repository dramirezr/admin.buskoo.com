<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Banner extends CI_Controller {

	var $title = 'Biz Type';
	var $setup_params = array();
		
	function __construct(){
		parent::__construct();
		
		$this->load->library('grocery_CRUD');
	}
	
	function index(){	
		//$this->grocery_crud->set_theme('datatables');
		
		$this->grocery_crud->set_table('banner');
		
		//$this->grocery_crud->unset_add();
		//$this->grocery_crud->unset_delete();
		//$this->grocery_crud->unset_texteditor('notes');
		
		/* Campos de la lista */


		$this->grocery_crud->set_theme('datatables');
		$this->grocery_crud->columns('id','description','state');
		$this->grocery_crud->add_fields('description', 'url','content', 'image','state');
    	$this->grocery_crud->edit_fields('description', 'url', 'content', 'image','state');
    	$this->grocery_crud->required_fields('description', 'content', 'image','state');
		$this->grocery_crud->set_field_upload('image','assets/images/banner');
					
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