<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Business extends CI_Model {


	function __construct(){
		parent::__construct();
	}
	
	function get_subtypes(){
		$sql = "SELECT * FROM biz_type where id_parent IS NOT NULL";
		$result = $this->db->query($sql)->result();
		if(count($result))
			return $result;
			
		return false; 
	}
	
	function get_all(){
		return $this->db->get_where('post', array('post_type_id' => 1))->result();
	}

	function get_products($post_id, $active = 1, $inactivated_by_user = NULL){
		
		if($inactivated_by_user !== NULL)
			$inactivated_by_user = " AND inactivated_by_user = $inactivated_by_user";
			
		$sql = "SELECT " .
					"b.*," .
					"p.id as product_id," .
					"p.name," .
					"p.description," .
					"p.helper_file," .
					"p.unit " .
				"FROM bz_products b, product p WHERE b.post_id = $post_id AND b.product_id = p.id AND b.active = $active $inactivated_by_user ORDER BY p.name";
				
		return $this->db->query($sql)->result();
	}

	function get_tags($post_id){
			
		$tags_product_id = ci_config('tags_product_id');
	
		//Load the product specs
		$prodcts = $this->get_products($post_id);
		
		$tags = array();
		foreach($prodcts  as $p){
			if(in_array($p->product_id, $tags_product_id) && $p->active == 1){
				$i_data = unserialize($p->implementation_data);
				if(isset($i_data['tags']) && $i_data['tags'])
					$tags[] = preg_replace('/(?:\s\s+|\n|\t)/', ' ', trim(strtolower($i_data['tags'])));
			}
		}
		
		if(!count($tags))
			return '';
			
		return (count($tags) == 1 ) ? $tags[0] : implode(' ', $tags);
	}

	/*Sync the biz to solr*/
	function syncronize($id){
		//Get the post
		$post = $this->db->get_where('post', array('id' => $id))->result();
		if(!count($post))
			return false;
		
		$post = $post[0];
		
		//Get the metas
		$metas_obj = $this->db->get_where('postmeta', array('post_id' => $id))->result();
		if(!count($metas_obj))
			return false;
		
		foreach($metas_obj as $m){
			$metas[$m->meta_key] = $m->meta_value;
		}
		
		//Set the text fields
		$phones = isset($metas['phones']) ? ' '.$metas['phones'] : '';
		$address = isset($metas['address']) ? ' '.$metas['address'] : '';
		$CEO = isset($metas['CEO_name']) ? ' '.$metas['CEO_name'] : '';
		$email = isset($metas['CEO_email']) ? ' '.$metas['CEO_email'] : '';

		//Get the post type name
		$post_type = $this->db->get_where('post_type', array('id' => $post->post_type_id))->result();

		//get biz type
		$sql = "SELECT b.* FROM post_biz_types p, biz_type b WHERE p.post_id=$id AND p.biz_type_id = b.id";
		$biz_type = $this->db->query($sql)->result();
		$biz_type = $biz_type[0]; 
		
		$tags = trim(trim(strtolower($biz_type->name)).' '.trim(strtolower($biz_type->tag)).' '.trim(strtolower($post->tags)));
		
		//Get the bought business tags
		$custom_tags = $this->get_tags($id);
		$tags .= ' '.$custom_tags;
		
		//Solr data
		$data = array(
			'id' => $id,
			'name' => $post->name,
			'tags' => $tags,
			'content' => $post->content,
			'post_type_es' => $post_type[0]->name_es,
			'post_type_en' => $post_type[0]->name_en,
			'location' => "{$metas['lat']},{$metas['lng']}",
			'phones' => $phones,
			'address' => $address,
			//'ceo' => $CEO,
			//'email' => $email,
			'score_avg' => $post->score_avg
		);
		
		solr_syncronize($data);
		return true;	
	}
		
	function update_search_engine($id, $export = false){
		//Get the post
		$post = $this->db->get_where('post', array('id' => $id))->result();
		if(!count($post))
			return false;
		
		$post = $post[0];
		
		//Get the metas
		$metas_obj = $this->db->get_where('postmeta', array('post_id' => $id))->result();
		if(!count($metas_obj))
			return false;
		
		foreach($metas_obj as $m){
			$metas[$m->meta_key] = $m->meta_value;
		}
		
		//Set the text fields
		$phones = isset($metas['phones']) ? ' '.$metas['phones'] : '';
		$address = isset($metas['address']) ? ' '.$metas['address'] : '';
		$CEO = isset($metas['CEO_name']) ? ' '.$metas['CEO_name'] : '';
		$email = isset($metas['CEO_email']) ? ' '.$metas['CEO_email'] : '';

		//Get the post type name
		$post_type = $this->db->get_where('post_type', array('id' => $post->post_type_id))->result();
		
		//get biz type
		$sql = "SELECT b.* FROM post_biz_types p, biz_type b WHERE p.post_id=$id AND p.biz_type_id = b.id";
		$biz_type = $this->db->query($sql)->result();
		$biz_type = $biz_type[0]; 
		
		$tags = trim(trim(strtolower($biz_type->name)).' '.trim(strtolower($biz_type->tag)).' '.trim(strtolower($post->tags)));
					
		//Solr data
		$solr = array(
			'id' => $id,
			'name' => trim(strtolower($post->name)),
			'tags' => $tags,
			'content' => trim(strtolower($post->content)),
			'post_type_es' => trim(strtolower($post_type[0]->name_es)),
			'post_type_en' => trim(strtolower($post_type[0]->name_en)),
			'location' => "{$metas['lat']},{$metas['lng']}",
			'phones' => trim($phones),
			'address' => trim(strtolower($address)),
			//'ceo' => trim(strtolower($CEO)),
			//'email' => trim(strtolower($email)),
			'score_avg' => $post->score_avg
		);
		
		if($export)
			return $solr;
		
		//Solr actualization
		$options = array (
    		'hostname' => '127.0.0.1',
    		'port' => '8080',
    		'path' => 'solr/core1'
		);
 
		$client = new SolrClient($options);
		$doc = new SolrInputDocument();
		foreach($solr as $i => $d)
			$doc->addField($i, trim(strtolower($d)));
		
		$updateResponse = $client->addDocument($doc, FALSE);
		$client->commit();
		print_r($updateResponse->getResponse());
		//var_dump($client);
		
		
	}		
	
	function activate($id){
		return $this->db->update('post', array("state" => 'A'), array('id' => $id));
	}
	
	function inactivate($id){
		return $this->db->update('post', array("state" => 'I'), array('id' => $id));
	}
	
	function get_products_by_invoice($invoice_id){
		$sql = "SELECT b.* FROM invoice_items i, bz_products b WHERE i.invoice_id=$invoice_id AND i.bz_products_id = b.id";
		$result = $this->db->query($sql)->result();
		if(count($result))
			return $result;
			
		return false; 
	}
	
	function activate_product($id){
		return $this->db->update('bz_products', array("active" => '1'), array('id' => $id));
	}
	
	function inactivate_product($id){
		return $this->db->update('bz_products', array("active" => '0'), array('id' => $id));
	}
}
