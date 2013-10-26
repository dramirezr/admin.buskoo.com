<?php
class Tools extends CI_Controller {

	public function verify_account_activation(){
		
		$this->load->model('account');
		$days = $this->config_model->get_value('account_activation_days');
		
		$accounts = $this->account->get_pending_to_activate($days);
		if(!$accounts){
			echo 'No accounts to delete'.PHP_EOL;
			return null;	
		}
			
		foreach($accounts as $a){
			if($this->account->delete($a->id))
				echo 'Deleted account ID: '.$a->id.PHP_EOL;
			else
				echo 'Error deleting account ID: '.$a->id.PHP_EOL;
		}
	}
	
	public function load_business_types($file_path){
		
		if(!$this->input->is_cli_request())
			exit ('Not available for this run mode.');
		
		$file_path = str_replace('-', '/', $file_path);

		$data = file($file_path);
		
		$formatted = array();
		foreach($data as $row){
			$fields = explode(',', $row);
			$tipo = ucfirst(strtolower($fields[0]));
			$formatted[$tipo][] = array(trim($fields[1]), trim($fields[2]));
		}
		
		$tipos_index = array_keys($formatted);
		
		$count = 1;
		foreach($tipos_index as $t){
			$tipos[$t] = $count;
			echo $sql[] = "INSERT INTO biz_type (id, name) VALUES ($count, '$t');";
			echo PHP_EOL;
			$count++;
		}
		
		foreach($formatted as $type => $descendants){
			foreach($descendants as $data){
				$parent = $tipos[$type];
				echo $sql[] = "INSERT INTO biz_type (id, id_parent, name, tag) VALUES ($count, $parent, '{$data[0]}' , '{$data[1]}');";
				echo PHP_EOL;
				$count++;				
			}
		}
	}
	
	function load_business($file_path){
		if(!$this->input->is_cli_request())
			exit ('Not available for this run mode.');
		
		$this->load->model('business');
		
		$file_path = str_replace('-', '/', $file_path);
		$dirname = dirname($file_path);
		
		$data = file($file_path);
		
		$tmp = $this->business->get_subtypes();
		foreach($tmp as $t){
			$types[strtolower($t->name)] = $t->id; 
		}
		
		$fd = fopen($dirname.'/sitios.sql', 'w');
		
		fwrite($fd, "START TRANSACTION;\n");		
		
		$count = 1;
		foreach($data as $index => $line){
			$row = explode("\t", $line);
			$type = strtolower($row[5]);
			$type_id = (isset($types[$type])) ? $types[$type] : NULL;
			if($type_id){
				$name = trim(str_replace("'", "''", $row[6]));
				$today = date('Y-m-d');
				//$content = $row[11] ? "'{$row[11]}'" : 'NULL';
				$lat = str_replace(',', '.', $row[3]);
				$lng = str_replace(',', '.', $row[4]);
				
				$phones = "{$row[8]} {$row[9]}";
				
				fwrite($fd, "INSERT INTO post (id, post_type_id, name, creation, last_update) VALUES ($count, 1, '$name', '$today', '$today');\n");		

				fwrite($fd, "INSERT INTO post_biz_types (post_id, biz_type_id) VALUE ($count, $type_id);\n");		

				fwrite($fd, "INSERT INTO postmeta (post_id, meta_key, meta_value) VALUE ($count, 'lat', '$lat');\n");		
				fwrite($fd, "INSERT INTO postmeta (post_id, meta_key, meta_value) VALUE ($count, 'lng', '$lng');\n");		
					
				if(strlen (str_replace(' ','', $phones)))
					fwrite($fd, "INSERT INTO postmeta (post_id, meta_key, meta_value) VALUE ($count, 'phones', '$phones');\n");	
	
				if($row[7])
					fwrite($fd, "INSERT INTO postmeta (post_id, meta_key, meta_value) VALUE ($count, 'address', '{$row[7]}');\n");
					
				$count++;				
			}else{
				echo $name = str_replace("'", "''", $row[6]).PHP_EOL;
			}
		}
		
		fwrite($fd, "COMMIT;\n");		
		
		fclose($fd);				
	}
	
	function export_biz_json(){
		$this->load->model('business');
		
		//Load all the bz posts
		$posts = $this->business->get_all();
		foreach($posts as $p){
			$docs[] = $this->business->update_search_engine($p->id, true);
		}
		
		$fd = fopen('/home/ecuamaps/tmp/sitios.json', 'w');
		fwrite($fd, json_encode($docs));
		fclose($fd);
		return true;		
	}	
	
	function update_solr(){
		$this->load->model('business');
		
		//Load all the bz posts
		$posts = $this->business->get_all();
		foreach($posts as $p){
			$this->business->update_search_engine($p->id);
		}
		return true;
	}
	
	function test_export_json($id){
		$this->load->model('business');
		
		$result = $this->business->update_search_engine($id, true);
		
		print_r($result);
	}
}
?>