<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Billing extends CI_Controller {

	var $title = 'Billing';
	var $setup_params = array();
		
	function __construct(){
		parent::__construct();
		
		$this->load->library('grocery_CRUD');
	}
	
	function index(){	
		//$this->grocery_crud->set_theme('datatables');
		
		$this->grocery_crud->set_table('invoice');
		
		$this->grocery_crud->unset_add();
		$this->grocery_crud->unset_delete();
		$this->grocery_crud->unset_texteditor('notes');
		
		$this->grocery_crud->set_relation('user_id','user','name');
		$this->grocery_crud->set_relation('post_id','post','name');
		
		$this->grocery_crud->columns('id', 'user_id', 'post_id', 'notes' ,'payment_method', 'date', 'balance', 'iva' ,'total', 'seller_id', 'state');
		
		$this->grocery_crud->display_as('id','Numero');
		$this->grocery_crud->display_as('user_id','Usuario');
		$this->grocery_crud->display_as('post_id','Local');
		$this->grocery_crud->display_as('notes','Notas');
		$this->grocery_crud->display_as('payment_method','Método pago');
		$this->grocery_crud->display_as('date','Fecha venta');
		$this->grocery_crud->display_as('balance','Por pagar');
		$this->grocery_crud->display_as('seller_id','Vendedor');
		$this->grocery_crud->display_as('state','Estado');
		
		$this->grocery_crud->fields('balance','state','notes','billing_name','billing_identification','billing_address');
		
		$this->grocery_crud->callback_after_update(array($this, 'after_update'));
			
		$this->setup_params['output'] = $this->grocery_crud->render();
  		$this->setup_params['title'] = lang('setup.rooms.title');
  		
		$this->render();
	}
	
	function after_update($post_array,$primary_key){
		
		$this->load->model('business'); 
		$this->load->model('invoice'); 
		
		//Load the invoice
		$invoice = $this->invoice->get_by_id($primary_key);
		$invoice = $invoice[0];
		
		/*$fp = fopen('/tmp/debug', 'w');
		fwrite($fp, var_export($invoice, true));
		fclose($fp);*/
		
		if($post_array['state'] == 'paid' && $invoice->activate_biz == '1'){
			$this->business->activate($invoice->post_id);
		}elseif($post_array['state'] != 'paid' && $invoice->activate_biz == '1'){
			$this->business->inactivate($invoice->post_id);
		}
		
		$products = $this->business->get_products_by_invoice($primary_key);
		
		foreach($products as $p){
			if($post_array['state'] == 'paid')
				$this->business->activate_product($p->id);
			else
				$this->business->inactivate_product($p->id);
		}
		
		//Close the balance
		if($post_array['state'] == 'paid'){
			$this->invoice->update($primary_key, array('balance' => 0));
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