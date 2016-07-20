<?php
/*
GESTIONE DEI MENU
NOTA quando aggiungo un idmenu alla lista lo aggiungo con un carattere # in testa in modo da sapere che è stato aggiunto dopo

*/
class Menu{
	var $tipo; //Tipo di menù (pratica,commissione...)
	var $path;
	var $schema;

	function Menu($tipo,$path){
//setto comunque idpratica nel costruttore  
		$this->tipo=$tipo;
		$this->path=$path;
		switch($tipo){
			case "agibilita":
				$this->schema="agi";
				break;
			case "vigilanza":
				$this->schema="vigi";
				break;
			case "commissione":
				$this->schema="ce";
				break;
			case "cdu":
				$this->schema="cdu";
				break;
			case "":
				$this->schema="storage";
				break;
			default:
				$this->schema="pe";
				break;
				
		}
	}

	function get_list($idpratica){
		if (isset($_SESSION["MENU_".$this->tipo."_$idpratica"])){
			$menu_pratica=$_SESSION["MENU_".$this->tipo."_$idpratica"];
		}
		else{
			$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
			if(!$db->db_connect_id)  die( "Impossibile connettersi al dadabase");

			if ($this->tipo=="commissione"){// menu per la commissione
				$menu_settings=@file(MENU."commissione.mnu");
				foreach ($menu_settings as $riga){
					$menu_pratica[]=explode(",",$riga);
				}
				$mnu="commissione";
			}	
            elseif ($this->tipo=="commissione_paesaggio"){// menu per la commissione del paesaggio
				$menu_settings=@file(MENU."commissione_paesaggio.mnu"); 
				foreach ($menu_settings as $riga){
					$menu_pratica[]=explode(",",$riga);
				}
				$mnu="commissione_paesaggio";
			}						
			elseif ($this->tipo=="cdu"){// menu per la commissione
				$menu_settings=@file(MENU."cdu.mnu");
				foreach ($menu_settings as $riga){
					$menu_pratica[]=explode(",",$riga);
				}
				$mnu="cdu";
			}
			elseif ($this->tipo=="condono"){
				$menu_settings=@file(MENU."condono.mnu");
				foreach ($menu_settings as $riga){
					$menu_pratica[]=explode(",",$riga);
				}
				$mnu="condono";
			}
			elseif ($this->tipo=="storage"){
				$menu_settings=@file(MENU."storage.mnu");
				foreach ($menu_settings as $riga){
					$menu_pratica[]=explode(",",$riga);
				}
				$mnu="storage";
			}
			elseif ($this->tipo=="vigilanza"){// menu per la commissione
                            //menu per le pratiche
				$sql="select menu_list,menu_file from vigi.menu where pratica=$idpratica;";
				$result = $db->sql_query($sql);
				if (!$result){
					echo "<p><b>ERRORE:</b><br>Configurazione dei menù errata<br>$sql</p>";
					exit;
				}
				$row = $db->sql_fetchrow();
				$menu_list=str_replace('#','',$row["menu_list"]);
				$menu_list=explode(",",$menu_list);
				$menu_file=$row["menu_file"];
				$menu_settings=@file(MENU."$menu_file".".mnu");
				if (!$menu_settings){
					echo "<p><b>ERRORE:</b><br>File di configurazione dei menù mancante o errato</p>";
					exit;
				}
				$cont_separatore=0;
				foreach ($menu_settings as $riga){
					$menu=explode(",",$riga);
					$idmenu=$menu[0];
					if (count($menu)==1){//riga di separazione
						if ($cont_separatore==0 && strpos($menu,'-')==0){ //una sola volta
							$menu_pratica[]="separatore-sezioni";
							$cont_separatore++;
						}
					}
                    if (count($menu)==2){
                        $menu_pratica[]=$menu[1];
                    }
                    elseif (in_array($idmenu,$menu_list)){
						$menu_pratica[]=$menu;
						$cont_separatore=0;
					}
				
				}
				$mnu="vigilanza";
			}	
			elseif ($this->tipo=="ambiente"){// menu per l'ambiente
				$menu_settings=@file(MENU."ambiente.mnu");
				foreach ($menu_settings as $riga){
					$menu_pratica[]=explode(",",$riga);
				}
				$mnu="ambiente";
			}					
			elseif($this->tipo=="pratica"){//menu per le pratiche
				$sql="select menu_list,menu_file from ".$this->schema.".menu where pratica=$idpratica;";
				$result = $db->sql_query($sql);
				if (!$result){
					echo "<p><b>ERRORE:</b><br>Configurazione dei menù errata</p>";
					exit;
				}
				$row = $db->sql_fetchrow();
				$menu_list=str_replace('#','',$row["menu_list"]);
				$menu_list=explode(",",$menu_list);
				$menu_file=$row["menu_file"];
				$menu_settings=@file(MENU."$menu_file".".mnu");
				if (!$menu_settings){
					echo "<p><b>ERRORE:</b><br>File di configurazione dei menù mancante o errato</p>";
					exit;
				}
				$cont_separatore=0;
				foreach ($menu_settings as $riga){
					$menu=explode(",",$riga);
					$idmenu=$menu[0];
					if (count($menu)==1){//riga di separazione
						if ($cont_separatore==0 && strpos($menu,'-')==0){ //una sola volta
							$menu_pratica[]="separatore-sezioni";
							$cont_separatore++;
						}
					}
                    if (count($menu)==2){
                        $menu_pratica[]=$menu[1];
                    }
                    elseif (in_array($idmenu,$menu_list)){
						$menu_pratica[]=$menu;
						$cont_separatore=0;
					}
				
				}
				$mnu="pratica";
			}
			$_SESSION["MENU_".$this->tipo."_$idpratica"]=$menu_pratica;	
		}
		$width='150px';
		print("<DIV id='nav-buttons' style='font-size:0px;'>");
		foreach ($menu_pratica as $menu){
			if (is_array($menu))
				print("<A tabIndex=\"0\" rif=\"$menu[3]\" href=\"javascript:pagina('".$menu[2]."',$idpratica)\">".$menu[1]."</A>\n");
			elseif($menu=='separatore-sezioni')
				print("<IMG style=\"height:1px;width:$width;border:0px;padding:1px;margin:0px;\" src=\"images/gray_light.gif\">");
			else{
                print "<div><a tabIndex=\"0\" class=\"label-button\">".$menu."</a></div>";
            }
		}
		print ("
		</DIV>");
		
		if ($this->tipo=="pratica" or $this->tipo=="condono" or $this->tipo=="cdu" or $this->tipo="commissione" or $this->tipo=="ambiente") print ("<div style=\"width:160; border-width:1 0 1 0px; border-style:solid; border-color:#336699; padding:2 0 2 0px\">
			<a href=\"javascript:loadintoIframe('myframe','".$this->path.".iter.php?pratica=$idpratica&tipo=$this->tipo')\" style=\"width:160\" class=\"iter-button\">Iter della Pratica</a>
	 
              </div>
		");
	}
	
	//Aggiunge la lista di menù alla nuova pratica
	function list_menu($idpratica,$tipo){
		if(!$idpratica) return;
		$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
		if(!$db->db_connect_id)  die( "Impossibile connettersi al dadabase");
		$db->sql_query ("delete from ".$this->menu.".menu where pratica=$idpratica;insert into pe.menu select $idpratica,menu_file,menu_default from pe.e_tipopratica where e_tipopratica.id=$tipo");	
		unset($_SESSION["MENU_".$this->tipo."_$idpratica"]);
		//$db->sql_close();	
	}
	
	//Aggiunge un menu ad una pratica esistente
	//Aggiungo # per riconoscerlo come aggiunto
	function add_menu($idpratica,$idmenu){
	//CONTROLLARE ESISTENZA DEL MENU PRIMA DI AGGIUNGERLO!!!!!!!!!!!!!!!!
	//Attenzione usare per i menu opzionali codici che non creino ambiguita nella ricerca 
		if(!$idpratica) return;
		$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
		if(!$db->db_connect_id)  die( "Impossibile connettersi al database");
		$sql="update ".$this->schema.".menu set menu_list=menu_list || ',#$idmenu' where strpos(menu_list,'#$idmenu')=0 and pratica=$idpratica;";
		//$db->sql_query ("update pe.menu set menu_list=menu_list || ',#$idmenu' where strpos(menu_list,'#$idmenu')=0 and pratica=$idpratica;");
		//echo $sql;
		$db->sql_query($sql);
		$menu = $_SESSION["MENU_".$this->tipo."_$idpratica"];
		
		$_SESSION["MENU_".$this->tipo."_$idpratica"] = sprintf("%s,%s",$menu,$idmenu);
		//$db->sql_close();	
	}
	
	function remove_menu($idpratica,$idmenu){
		//rimuovo un menu solo se è stato aggiunto quindi è sempre nalla forma #id
		if(!$idpratica) return;
		$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
		if(!$db->db_connect_id)  die( "Impossibile connettersi al dadabase");
		$mymenu=',#'.$idmenu;
		$db->sql_query ("update ".$this->schema.".menu set menu_list=overlay(menu_list placing '' from strpos(menu_list,'$mymenu')-1 for length('$mymenu')) where pratica=$idpratica;");
		unset($_SESSION["MENU_".$this->tipo."_$idpratica"]);
		//$db->sql_close();	
	}	
	
	function change_menu($idpratica,$oldtipo,$newtipo){
		if(!$idpratica) return;
		$db = new sql_db(DB_HOST.":".DB_PORT,DB_USER,DB_PWD,DB_NAME, false);
		if(!$db->db_connect_id)  die( "Impossibile connettersi al dadabase");
		$sql="select menu_list from ".$this->schema.".menu where pratica=$idpratica;";
		$result = $db->sql_query ($sql);
		$oldmenu=$db->sql_fetchfield("menu_list");
		$pos=strpos($oldmenu,"#");
		if ($pos)		//ho aggiunto dei menu al menu originale
			$oldmenu=substr($oldmenu,$pos);
		else
			$oldmenu="";
		$db->sql_query ("update ".$this->schema.".menu set menu_list=e_tipopratica.menu_default || '$oldmenu' from pe.e_tipopratica where e_tipopratica.id=$newtipo and pratica=$idpratica;");

		unset($_SESSION["MENU_".$this->tipo."_$idpratica"]);
		//$db->sql_close();
	}
}
?>
