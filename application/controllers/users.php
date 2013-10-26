<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {

	var $title = 'Posts';
	var $setup_params = array();
		
	function __construct(){
		parent::__construct();
		
		$this->load->library('grocery_CRUD');
	}
	
	function index(){	
		//$this->grocery_crud->set_theme('datatables');
		
		$this->grocery_crud->set_table('user');
		
		//$this->grocery_crud->unset_add();
		//$this->grocery_crud->unset_delete();
		//$this->grocery_crud->unset_texteditor('notes');
		
		/* Campos de la lista */
		$this->grocery_crud->columns('id','email','name','city','address','phone','cellphone', 'creation', 'status');
		
		/* Campos para editar */
		$this->grocery_crud->add_fields('email', 'passwd', 'name','creation', 'status');
		//$this->grocery_crud->field_type('creation','invisible');
		$this->grocery_crud->field_type('passwd','password');
    	
    	$this->grocery_crud->edit_fields('email', 'passwd','name','creation','status');
		$this->grocery_crud->callback_edit_field('passwd',array($this,'set_password_input_to_empty'));
		
		$this->grocery_crud->callback_before_update(array($this, 'before_update'));
		$this->grocery_crud->callback_before_insert(array($this, 'before_insert'));
		
			
		$this->setup_params['output'] = $this->grocery_crud->render();
  		$this->setup_params['title'] = lang('setup.rooms.title');
  		
		$this->render();
	}
	
	function before_insert($post_array){
		
		//Encrypt password only if is not empty. Else don't change the password to an empty field
	    if(!empty($post_array['passwd'])){
	        $post_array['passwd'] = md5($post_array['passwd']);
	    }else{
	        $post_array['passwd'] = md5(ci_config('default_new_user_pass'));
	    }
	    		
		//$post_array['creation'] = date('Y-d-m');
		$post_array['status'] = 'A';
		
		return $post_array;
	}
	
	function before_update($post_array, $primary_key){
	    
	    //Encrypt password only if is not empty. Else don't change the password to an empty field
	    if(!empty($post_array['passwd'])){
	        $post_array['passwd'] = md5($post_array['passwd']);
	    }else{
	        unset($post_array['passwd']);
	    }
	    
	    return $post_array;
	}
	
	function set_password_input_to_empty() {
    	return "<input type='password' name='passwd' value='' />";
	}

	private function render(){
		
		$this->template->write('title', $this->title);
		
		$this->template->add_js('https://www.google.com/jsapi', 'import', FALSE, FALSE);

		$this->template->write_view('content', 'templates/crud', $this->setup_params, TRUE);
		
		$this->template->render();		
	}
	
	
	
}