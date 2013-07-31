
<?
function rmdirr($dirname) {
    if (!file_exists($dirname)) {
        return false;
    }
    if (is_file($dirname)) {
        return unlink($dirname);
    }
    $dir = dir($dirname);
    while (false !== $entry = $dir->read()) {
        if ($entry == '.' || $entry == '..') {
            continue;
        }
        rmdirr("$dirname/$entry");
    }
    $dir->close();
    return rmdir($dirname);
}

function unpackZip($file,$dir) {	
	if ($zip = zip_open(getcwd().$file)) {
     if ($zip) {
       while ($zip_entry = zip_read($zip)) {
         if (zip_entry_open($zip,$zip_entry,"r")) {
           $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
           $dir_name = dirname(zip_entry_name($zip_entry));
		   
           if (($dir_name == ".") || (!is_dir($dir_name))) {
				$dir_op = $dir;
				foreach ( explode("/",$dir_name) as $k) {
                 $dir_op = $dir_op . $k;
                 if (is_file($dir_op)) unlink($dir_op);
                 if (!is_dir($dir_op)) mkdir($dir_op);
                 $dir_op = $dir_op . "/" ;
                 }
               }
           $fp=fopen($dir.zip_entry_name($zip_entry),"w");
           fwrite($fp,$buf);
           zip_entry_close($zip_entry);
       } else
           return false;
       }
       zip_close($zip);
     }
  } else
     return false;
  return true;
}
function get_paragr($node) {
	while ($node) {
		if ($node->node_type()==XML_ELEMENT_NODE && $node->tagname()=="p") return $node;
		$node=$node->parent_node();
	}
	return $node;

}
//Ottengo tabelle e campi per le query
function get_table_fields($r) {
	$user_fields=$r->get_elements_by_tagname("user-field-get");
	$out=array();
	for($i=0;$i<count($user_fields);$i++) {
		list($attr)=$user_fields[$i]->attributes();
		$str=$attr->value();
		list($table,$fields)=explode(".",$str);
		if (array_key_exists($table,$out)) {
			if (!in_array($fields,$out[$table])) array_push($out[$table],$fields);
		}
		else {
			$tmp=array($fields);
			$out[$table]=$tmp;
		}
	}
	return $out;
}
//Visita di un albero scegliendo sempre il primo figlio da visitare
function visita_XML($node,$lev) {
	$i=0;
	while ($node)  {
		$nome=$node->node_name();
		echo "<br> Livello $lev Â° Elemento $i Â° Nodo : $nome  <br>";
		print_r($node);
		if ($node->has_attributes()) {
			echo "<br>-------- ATTRIBUTI ---------<br>";
			$attr=$node->attributes();
			foreach ($attr as $v) {
				print_r($v);
				echo "<br>";
			}
		}
		echo "<br> Nodi Figli : ".count($node->child_nodes())."<br>";
		$i++;
		if ($node->has_child_nodes()) {
			$lev++;
			visita_XML($node->first_child(),$lev);
			$lev--;
		}
		$node= $node->next_sibling();	
	}
}
function crea_query($table,$fields,$cond) {
		return "SELECT DISTINCT ".implode(",",$fields)." FROM $table WHERE $cond";
}

	include "./db/config.db.php";
	include "./lib/mod_zipfile.class.php";
	
	// Inizializzazioni --- condizione della query SQL
	$cond="pratica=$idpratica";
	if (strlen($nomefile)<5) $nomefile= substr($modello,0,strlen($modello)-4)."$idpratica.sxw"; 
	$dir="gestione_stampe/tmp";
	if (!is_dir($dir)) mkdir($dir);
	$dir_modelli="/gestione_stampe/modelli/";
	$dir_save=getcwd()."/gestione_stampe/".$_SESSION["USERNAME"];
	if (!is_dir($dir_save)) mkdir($dir_save);
	// unzippo il modello del documento
	if (unpackZip($dir_modelli.$modello,$dir."/")) //echo "Dovrei aver scompattato!!!!<br>";

//Acquisisco il file xml
	if(!$dom = domxml_open_file(getcwd()."/$dir/content.xml")) {
	  echo "error";
	  exit;
	}
	$db = new sql_db($dbhost, $dbuname, $dbpass, $dbname, false);
	if(!$db->db_connect_id)  die( "Impossibile connettersi al database $dbtype");
	$root=$dom->document_element();
	$start=$root->get_elements_by_tagname("body");
	$lista_field=$root->get_elements_by_tagname("user-field-get");
	$out=get_table_fields($root);
	$risultato=array();
	//Ciclo sulle Tabelle
	foreach($out as $key=>$value) {
		$sql_query[$key]=crea_query($key,$value,$cond);
		$db->sql_query($sql_query[$key]);
		$ris[$key] = $db->sql_fetchrowset();
		//Ciclo sui campi delle tabelle
		foreach($value as $val) {
			$s=$key.".".$val;
			$risultato[$s]=array();
			//Ciclo sui valori dei campi
			//echo "<br>";print_r($ris[$key]);
			foreach($ris[$key] as $k=>$v)	array_push($risultato[$s],$v[$val]);
		}
	}
	//Modifico il file 
	foreach($lista_field as $val){
		$p=get_paragr($val);
		list($attr)=$p->attributes();
		$p_value=$attr->value();
		list($attr)=$val->attributes();
		$str=$attr->value();
		$dati=$risultato[$str];
		if (count($dati)==1) {
			$pippo=implode(" ",$dati);
			$new_root_node=$dom->create_element("text:p");
			$new_root_node->set_attribute("text:style-name",$p_value);
			$new_node=$dom->create_text_node(utf8_encode(html_entity_decode($pippo)));
			$new_root_node=$val->replace_node($new_node);
		}
		else {
			$last=$p->next_sibling();
			$prev=$p->previous_sibling();
			$father=$p->parent_node();

			for($i=0;$i<count($dati);$i++) {
					$tmp_root[$i]=$dom->create_element("text:p");
					$tmp_root[$i]->set_attribute("text:style-name",$p_value);
					$tmp=$dom->create_text_node(utf8_encode(html_entity_decode($dati[$i])));
					$tmp_root[$i]->append_child($tmp);
					if (!$last)  $father->append_child($tmp_root[$i]);
					else
						$father->insert_before($tmp_root[$i],$last);
			}
			$p->unlink_node();
			$val->unlink_node();
		}
	}
	// Scrivo il file 
	$dom->dump_file(getcwd()."/$dir/content.xml", false, true);
	//visita_XML($root,0);
	//Comprimo il file 
	$zipfile = new zipfile();     
	$zipfile->add_sub_dir("./".$dir,"./".$dir);
	$zipfile->list_file("./".$dir,"./".$dir);
	$fd = fopen ($dir_save."/".$nomefile, "wb"); 
	$out = fwrite ($fd,$zipfile->file()); 
	fclose ($fd);
	rmdirr($dir);
?>