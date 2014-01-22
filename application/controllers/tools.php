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


	function save_html_client(){
		//echo $_SERVER['DOCUMENT_ROOT'];
		//echo  $_SERVER["HTTP_HOST"] ;
		
		$this->load->model('business');
		//Load all the bz posts in invoice
		$posts = $this->business->billing_client();

		$archivo = ci_config('path_global_filesystem')."/client/tpl/templete.tpl";
		$abrir = fopen($archivo,'r+');
		$html = fread($abrir,filesize($archivo));
		$archivoxml = ci_config('path_global_filesystem')."/client/tpl/site_map.tpl";
		$abrirxml = fopen($archivoxml,'r+');
		$xml = fread($abrirxml,filesize($archivoxml));
		
		$urlsitemap = 	' 	<url>' .
  					  	'		<loc>[URL]</loc>' .
  					  	'		<lastmod>[LASTMOD]</lastmod>' . 
  						'		<changefreq>[CHANGE]</changefreq>' .
						'   	<priority>[PRIORITY]</priority>' .
						'	</url>' ;


		$index = 	'<nav><a href="[URL]"><h1>[TITLE]</h1></a> <br> <h2>[DESCRIPTION]</h2> </nav>'.
            		' <aside>' .
               		'		<div id="logotipo"><img src="[LOGO]"  width="80" height="80"></div>' .
            		'	</aside>' .
            		'	<section id="centro">' .
                	'		<article>[ADRRESS]</article>' .
                	'		<article>[PHONES]</article>' .
                	'		<article>[EMAIL]</article>' .
            		'	</section>';
        $html_sitemap 	= '';
        $html_index 	= '';
        $html_urlsite 	= '';
		foreach($posts as $p){
			//$docs[] = $this->business->billing_client($p->id, true);
			
			$html_post = $html;
			
			$postmeta = $this->business->getPostmeta($p->post_id);
			$html_post = str_replace("[TITLE]",$p->name,$html_post); 
			$extrainfo = '';
			$phones = '';
			$CEO_email = '';
			$address = '';
			foreach($postmeta as $pm){
				if( $pm->meta_key=='extrainfo') $extrainfo = $pm->meta_value;
				if( $pm->meta_key=='phones')  $phones = $pm->meta_value;
				if( $pm->meta_key=='CEO_email')  $CEO_email = $pm->meta_value;
				if( $pm->meta_key=='address')  $address = $pm->meta_value;
			}			
			$html_post = str_replace("[DESCRIPTION]",$extrainfo,$html_post);	
			$html_post = str_replace("[PHONES]",$phones,$html_post);
			$html_post = str_replace("[EMAIL]",$CEO_email,$html_post);	
			$html_post = str_replace("[ADRRESS]",$address,$html_post);

			$logo_url = '';
			$logo_id = '';
			if($logo = $this->business->getMedia(array('post_id' => $p->post_id, 'type' => 'logo', 'state' => 1))){
				$logo = $logo[0];
				$logo_url = ci_config('media_server_show_url').'/'.$logo->hash;
				$logo_id  = $logo->id;			
			}

			$html_post = str_replace("[LOGO]",$logo_url,$html_post);
			//$urlsite='http://'.ci_config('server_path_icon').'/'."es?pid=".$p->post_id;
			$urlpost='http://'.ci_config('server_path_icon').'/'."es?uid=".$p->user_id;
			$html_post = str_replace("[URL]",$urlpost,$html_post);
			
			$name = strtolower($this->sanear_string($p->name));
			$name = str_replace(" ","_",$name);
			$name = $name.'.html';
			$fd = fopen(ci_config('path_global_filesystem').'/client/'.$name, 'w');
			fwrite($fd, $html_post);
			fclose($fd);
			echo "Generando "."www.buskoo.com/client/".$name." <br>";
		

			//index file
			$html_indextemp = $index;
			$html_indextemp = str_replace("[TITLE]",$p->name,$html_indextemp); 
			$html_indextemp = str_replace("[LOGO]",$logo_url,$html_indextemp);
			$html_indextemp = str_replace("[DESCRIPTION]",$extrainfo,$html_indextemp);	
			$html_indextemp = str_replace("[PHONES]",$phones,$html_indextemp);
			$html_indextemp = str_replace("[EMAIL]",$CEO_email,$html_indextemp);	
			$html_indextemp = str_replace("[ADRRESS]",$address,$html_indextemp);
			$html_indextemp = str_replace("[URL]",$name,$html_indextemp);
			$html_index    .= '<hr>'.$html_indextemp;
			//mxl file
			$date = date("Y-m-d");
			$urlsitemaptemp = $urlsitemap ;
			$urlsitemaptemp = str_replace("[URL]",$urlpost,$urlsitemaptemp); 
			$urlsitemaptemp = str_replace("[LASTMOD]",$date,$urlsitemaptemp);
			$urlsitemaptemp = str_replace("[CHANGE]",'weekly',$urlsitemaptemp);	
			$urlsitemaptemp = str_replace("[PRIORITY]",'0.9',$urlsitemaptemp);
			$html_urlsite  .= $urlsitemaptemp;
			
		
		}

		$archivoindex = ci_config('path_global_filesystem')."/client/tpl/indice.tpl";
		$abririndex = fopen($archivoindex,'r+');
		$indexarchivo = fread($abririndex,filesize($archivoindex));
		$indexarchivo = str_replace("[CLIENT]",$html_index,$indexarchivo);	

		$fd = fopen(ci_config('path_global_filesystem').'/client/index.html', 'w');
		fwrite($fd, $indexarchivo);
		fclose($fd);
		echo "Generando www.buskoo.com/client/index.html <br>";

		$xml = str_replace("[SITEMAP]",$html_urlsite,$xml); 
		$fd = fopen(ci_config('path_global_filesystem').'/sitemap.xml', 'w');
		fwrite($fd, $xml);
		fclose($fd);
		echo "Generando www.buskoo.com/sitemap.xml <br>";

		
	}


function sanear_string($string)
{
 
    $string = trim($string);
    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );
    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );
    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );
    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );
    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );
    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );
    //Esta parte se encarga de eliminar cualquier caracter extraño
    $string = str_replace(
        array("\\", "¨", "º", "-", "~",
             "#", "@", "|", "!", "\"",
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "`", "]",
             "+", "}", "{", "¨", "´",
             ">", "<", ";", ",", ":",
             ".", ""),
        '',
        $string
    );
    return $string;
}


}
?>