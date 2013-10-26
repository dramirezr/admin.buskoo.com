<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pqr extends CI_Controller {

	var $title = 'Config';
	var $setup_params = array();
		
	function __construct(){
		parent::__construct();
		
		$this->load->library('grocery_CRUD');
	}
	
	function index(){	
		//$this->grocery_crud->set_theme('datatables');
		
		$this->grocery_crud->where('ref_name','pqr');  
		$this->grocery_crud->set_table('tasks');
		
		$this->grocery_crud->columns('ref_id', 'open_date', 'content', 'state' ,'close_date');
		
		$this->grocery_crud->display_as('ref_id','Email');
		$this->grocery_crud->display_as('open_date','Apertura');
		$this->grocery_crud->display_as('content','Mensaje');
		$this->grocery_crud->display_as('state','Estado');
		$this->grocery_crud->display_as('close_date','Cierre');
		
		$this->grocery_crud->unset_add();
		$this->grocery_crud->unset_delete();
		
		/* Campos para editar */
		//$this->grocery_crud->edit_fields('state');
		$this->grocery_crud->fields('content', 'state');
		$this->grocery_crud->callback_edit_field('content',array($this,'edit_content_field'));
			
		$this->setup_params['output'] = $this->grocery_crud->render();
  		$this->setup_params['title'] = lang('setup.rooms.title');
  		
		$this->render();
	}
	
	function edit_content_field($value, $primary_key){
		return '<div id="field-content" class="readonly_label">'.$value.'</div>';
	}
	
	function after_update($post_array,$primary_key){
		
		$this->load->model('business');
		
		if($post_array['state'] == 'A'){
			$this->business->syncronize($primary_key);
		}else{
			delete_solr_document($primary_key);
		}
				
		return true;
	}
	
	private function render(){
		
		$this->template->write('title', $this->title);
		
		$this->template->add_js('https://www.google.com/jsapi', 'import', FALSE, FALSE);

		$this->template->write_view('content', 'templates/crud', $this->setup_params, TRUE);
		
		$this->template->render();		
	}
	
	
	
}