<?php
include_once "./lib/tabella.class.php";

class Tabella_h extends Tabella{

var $def_col;//definizione delle colonne
var $colore_colonne="#E7EFFF";//"#CCCCCC";
var $color_title="#728bb8";
var $color_head_font="#415578";
var $color_title_font="#FFFFFF";
var $info_target;//pagina di destinazione per il link info
var $img_punto; //nome del gif da usare come punto elenco della tabella

function set_target($target){
	$this->info_target=$target;
}
function set_punto($image){
	$this->img_punto=$image;
}

function set_color($intestazione,$font_intestazione,$titolo,$font_titolo){
// ************ da fare********************usare class?????	
    //$this->color_title=$titolo;	
	//$this->color_title_font=$font_titolo;	
	//aggiungere stile
	$this->colore_colonne=$intestazione;
	$this->color_head_font=$font_intestazione;
}

function get_cella($row,$col){
	$nome=$this->def_col[$col][1]; //nome del campo
	$valore=htmlspecialchars($this->array_dati[$row][$nome], ENT_QUOTES,"UTF-8");//valore del campo
	$w=$this->def_col[$col][2];//larghezza del campo
	$tipo=trim($this->def_col[$col][3]);//tipo del campo
        $classe=($this->array_dati[$row]["row_class"])?(' class="'.$this->array_dati[$row]["row_class"].'"'):("");
	//echo "<p>Riga $row $nome : $valore</p>";
	switch ($tipo){//tipo campo in configfile
		case "idriga":
			$retval="<td><input type=\"hidden\" name=\"idriga\" value=\"$valore\" ></td>\n";
			break;
		case "pratica":
		case "text":
			$valore=html_entity_decode($valore);
			$retval="<td$classe>$valore</td>\n";
			break;
			
	//	Modificato Marco
		case "ora":
			$valore=number_format($dati[$campo],2, ':', '');
			if ($valore!=0)	
				$retval="<td$classe>$valore</td>\n";
			else
				$retval="<td$classe>---</td>\n";
			break;
		case "numero":
			$valore=number_format($valore,4, ',', '.');
			if ($valore!=0)	
				$retval="<td$classe>$valore</td>\n";
			else
				$retval="<td$classe>---</td>\n";
			break;
			
		case "valuta":
			//echo("<br>Formatto valuta : $valore<br>");
			if ($valore){
				$valore=number_format($valore,2, ',', '.');
				$retval="<td$classe> $valore</td>\n";
			}
			else
				$retval="<td$classe>---</td>\n";
			break;
		case "superficie":
			if ($valore!=0)	
				$retval="<td$classe>$valore mq</td>\n";
			else
				$retval="<td$classe>---</td>\n";
			break;
		case "volume":
			if ($valore!=0)	
				$retval="<td$classe>$valore mc</td>\n";
			else
				$retval="<td$classe>---</td>\n";
			break;
	// Fine Modifica
		case "data":
			$data=$this->date_format(stripslashes($valore));
			$retval="<td$classe>$data</td>\n";
			break;	
			
		case "checkbox":
			//$id=$this->array_dati[$row][$nome];
			//(($valore=="t") or ($valore==1))?($selezionato="checked"):($selezionato="");
			if ($valore==-1)
				$retval="<td align=\"center\" valign=\"middle\" width=\"7\"><input type=\"checkbox\" name=\"$valore\" id=\"$valore\" value=\"$nome\" disabled checked></td>\n";
			else
				$retval="<td align=\"center\" valign=\"middle\" width=\"7\"><input type=\"checkbox\" name=\"$valore\" id=\"$valore\" value=\"$nome\" $selezionato></td>\n";
			break;
			
		case "checkbox_chk":
			$retval="<td align=\"center\" valign=\"middle\" width=\"7\"><input type=\"checkbox\" name=\"$valore\" id=\"$valore\" value=\"$nome\" checked=\"checked\"></td>\n";
			break;
		
		case "radio":
			$id=$this->array_dati[$row]["id"];
			//if ($nome=="id") $selezionato="checked";
			(($valore=="t") or ($valore==1))?($selezionato="checked"):($selezionato="");
			$retval="<td align=\"center\" valign=\"middle\" width=\"7\"><input width=\"7\" type=\"radio\" name=\"$id\" value=\"$nome\" $selezionato></td>\n";
			break;
		case "btn_edit":
                    $prms=$this->getParams($row,$w);
                    $obj=json_encode($prms['params']);
                    $destination=($this->array_dati[$row]["row_form"])?($this->array_dati[$row]["row_form"]):($prms['form']);
                    $retval="";
                    if ($this->editable){
                        $retval.="<td align=\"center\" valign=\"middle\"  class=\"printhide\" style=\"width:$prms[size]\">";
                        $retval.="<a href='javascript:linkToEdit(\"$destination.php\",$obj)'><img title=\"Modifica\" src=\"images/edit.png\" border=\"0\"></a>";
                        $retval.="</td>\n";
                    }
                    break;
                case "btn_pwview":
                    $prms=$this->getParams($row,$w);
                    $obj=json_encode($prms['params']);
                    $retval="";
                    if ($this->viewable){
                        $retval.="<td align=\"center\" valign=\"middle\"  class=\"printhide\" style=\"width:$prms[size]\">";
                        $retval.="<a href='javascript:loadInto(\"".$prms['form'].".php\",$obj)'><img title=\"Visualizza\" src=\"images/view.png\" border=\"0\"></a>";
                        $retval.="</td>\n";
                    }
                    break;
                case "btn_view":
                    $prms=$this->getParams($row,$w);
                    $obj=json_encode($prms['params']);
                    $retval="";
                    $destination=($this->array_dati[$row]["row_form"])?($this->array_dati[$row]["row_form"]):($prms['form']);
                    if ($this->viewable){
                        $retval.="<td align=\"center\" valign=\"middle\"  class=\"printhide\" style=\"width:$prms[size]\">";
                        $retval.="<a href='javascript:linkToView(\"$destination.php\",$obj)'><img title=\"Visualizza\" src=\"images/view.png\" border=\"0\"></a>";
                        $retval.="</td>\n";
                    }
                    break;
                case "btn_delete":
                    $prms=$this->getParams($row,$w);
                    $obj=json_encode($prms['params']);
                    $retval="";
                    if ($this->editable){
                        $retval.="<td align=\"center\" valign=\"middle\"  class=\"printhide\" style=\"width:$prms[size]\">";
                        $retval.="<a href='javascript:linkToDelete(\"".$prms['form'].".php\",$obj)'><img title=\"Elimina\" src=\"images/delete.png\" border=\"0\"></a>";
                        $retval.="</td>\n";
                    }
                    break;
        case "btn_word":
            $prms=$this->getParams($row,$w);
            $obj=json_encode($prms['params']);
            $retval="";
            if ($this->viewable){
                $retval.="<td align=\"center\" valign=\"middle\"  class=\"printhide\" style=\"width:$prms[size]\">";
                $retval.="<a href='javascript:linkToView(\"".$prms['form'].".php\",$obj)'><img title=\"Visualizza\" src=\"images/word.gif\" border=\"0\"></a>";
                $retval.="</td>\n";
            }
            break;
        case "btn_info":
            $prms=$this->getParams($row,$w);
            $obj=json_encode($prms['params']);
            $retval="<td align=\"center\" valign=\"middle\"  class=\"printhide\">";
            $retval.="<a href='javascript:linkToView(\"".$prms['form'].".php\",$obj)'><img title=\"Visualizza\" src=\"images/view.png\" border=\"0\"></a>";
            if ($this->editable) $retval.="<a href='javascript:linkToEdit(\"".$prms['form'].".php\",$obj)'><img title=\"Modifica\" src=\"images/edit.png\" border=\"0\"></a>";
            $retval.="</td>\n";
            break;
        case "btn_modelli":
            $prms=$this->getParams($row,$w);
            $obj=json_encode($prms['params']);
            $retval="<td align=\"center\" valign=\"middle\"  class=\"printhide\">";
            $retval.="<a target='modelli' href='/modelli/".$valore."'><span class='ui-icon ui-icon-document' title='Visualizza il modello di stampa'/></a>";
            
            $retval.="</td>\n";
            break;
        case "info":
            if(isset($this->tag) && $this->tag){
                    $args=$this->tag;
                    $jslink="link('$valore','$args')";
            }
            else
                    $jslink="link('$valore')";

            $retval="<td align=\"center\" valign=\"middle\"  class=\"printhide\"><a href=\"javascript:$jslink\"><img src=\"images/view.png\" border=\"0\"></a></td>\n";
            break;
			
		case "info0":
			$pratica=$this->idpratica;
			$target=$this->info_target;
			$args='';
			if($this->tag) 
				$args=(','.$this->tag);
			if($valore){
				$jslink="link0('$valore',$pratica,'$target'$args)";
				$retval="<td align=\"center\" valign=\"middle\" width=\"$w\" class=\"printhide\"><a 	href=\"javascript:$jslink\"><img src=\"images/info.gif\" border=\"0\"></a></td>\n";
				}else{
					$retval="<td></td>\n";
				}
				
				
			break;
		case "info1":
			$pratica=$this->idpratica;
			$target=$this->info_target;
			$args='';
			if($this->array_dati[$row][$this->tag]){
				$jslink="link1('$valore',$pratica,'$target')";
				$retval="<td align=\"center\" valign=\"middle\" width=\"$w\" class=\"printhide\"><a href=\"javascript:$jslink\"><img src=\"images/info.gif\" border=\"0\"></a></td>\n";
			}
			else{
					$retval="<td></td>\n";
			}
				
				
			break;
				
		/*case "info1":
			$l="10%";
			$jslink="link('$valore')";
			$retval="<a href=\"javascript:$jslink\"><img src=\"icons/foto.gif\" border=\"0\">$valore</a>\n";
			break;*/
			
		case "upload":
			$retval="<td align=\"center\" valign=\"middle\"  width=\"$w\"><a href=\"upload.php?id=$valore\"><img src=\"images/upload.gif\" border=\"0\"></a></td>\n";
			break;		
		
		case "zoom":
			$jslink=$this->zoomto(trim($this->tabelladb),$valore);
			$retval="<td align=\"center\" valign=\"middle\" width=\"$w\" class=\"printhide\"><a href=\"javascript:$jslink\"><img src=\"images/zoom.gif\" border=\"0\"></a></td>\n";
			break;

		case "zoom_gc":
			$jslink=$this->zoomto_gc(trim($this->tabelladb),$valore);
			$retval="<td align=\"center\" valign=\"middle\" width=\"$w\" class=\"printhide\"><a href=\"javascript:$jslink\"><img src=\"images/zoom.gif\" border=\"0\"></a></td>\n";
			break;

		case "yesno":
			($valore==0)?($yn="NO"):($yn="SI");
			$retval="<td align=\"left\" valign=\"middle\"  width=\"$w\">$yn</td>\n";
			break;		
		
		case "delete":
			$id=$this->array_dati[$row]["id"];
			if (isset($this->tag) && $this->tag){
				$pp=$this->tag;
				$jslink="elimina('$id','$pp')";
			}
			else{
				$jslink="elimina('$valore')";
			}
			$retval="<td  align=\"center\" valign=\"middle\"  width=\"$w\"  class=\"printhide\" ><a href=\"javascript:$jslink;\"><img src=\"images/delete16.gif\" border=\"0\"></a></td>\n";
			break;
			
		case "semaforo":
			($valore)?($img="frossa"):($img="fblu");
			$retval="<td  align=\"center\" valign=\"middle\"  width=\"$w\"  class=\"printhide\" ><img src=\"images/$img.gif\" border=\"0\"></td>\n";
			
			break;
			
		case "image":
			$img=(!$valore)?("frossa.gif"):($valore);
			$retval="<td  align=\"center\" valign=\"middle\"  width=\"$w\"  class=\"printhide\" ><img src=\"images/$img\" border=\"0\"></td>\n";
			
			break;
		//Genera un array di text indicizzati su id
		case "text_box":
			//$data=$this->date_format(stripslashes($valore));
                        $data=$valore;
			$nome.="[".$this->array_dati[$row]["id"]."]";
			$retval="<td$classe><input $class maxLength=\"$w\" size=\"$w\"  class=\"textbox\" name=\"$nome\" id=\"data\" value=\"$data\">$help"; 
			break;
		case "multiple_yesno":
			$nome.="[".$this->array_dati[$row]["id"]."]";
            $sel=($valore)?(Array('selected','')):(Array('','selected'));
			$opzioni="<option value=1 $sel[0]>SI</option><option value=0 $sel[1]>NO</option>";
			$retval="<td><select style=\"width:$w\" class=\"textbox\"  name=\"$nome\"  onmousewheel=\"return false\" $disabilitato>$opzioni</select></td>";
			break;	
		case "radio1":
			$id=$this->array_dati[$row]["id"];
			//if ($nome=="id") $selezionato="checked";
			(($valore=="t") or ($valore==1))?($selezionato="checked"):($selezionato="");
			$retval="<td align=\"center\" valign=\"middle\" width=\"7\"><input width=\"7\" type=\"radio\" name=\"$nome\" value=\"$id\" $selezionato></td>\n";
			break;	
			
		case "link"	:
			switch ($this->tag){
				case "modello" :
					$jslink="crea_rtf";
					break;
				case "rtf" :
					$jslink="apri";
					break;
				case "pdf" :
					$jslink="apri";
					break;
			}
			//list($file,$ext)=explode(".",$valore);
			$tmp=explode(".",$valore);
			$ext=array_pop($tmp);
			$file=implode(".",$tmp);
			$retval="<td><a href=\"javascript:$jslink('$valore')\" style=\"width:$w\">$file</a></td>\n";
			break;
			
		case "punto":
			$p_image=$this->img_punto;
			$retval="<td align=\"center\" valign=\"middle\" width=\"$w\"><img src=\"images/$p_image.gif\" border=\"0\"></td>\n";
			break;	
		//crea  un array di text area con associata un'immagine che permette di visualizzare le text area. Di default è nascosta
		case "nota":
			$nome.="[".$this->array_dati[$row]["id"]."]";
			$imm="imm_".$nome;
			$retval="<td$classe>&nbsp;&nbsp;<img border=\"0\" id=\"$imm\" height=\"12\" src=\"images/left.gif\" onclick=\"show_note('$nome','$imm')\">&nbsp;<span id=\"$nome\" style=\"display:none\"><textarea name=\"$nome\" cols=\"$w\" rows=\"2\">$valore</textarea>$help</span>"; 
			break;
		case "selectdb":		//Restituisce il campo descrittivo di un elenco 
			$size=explode("x",$w);
			$retval="<td$classe valign=\"middle\" width=\"$size[0]\">".$this->get_selectdb_value($valore,"id",$size[1],"opzione")."</td>";
			break;	
	}
	return $retval;
}	


function elenco (){
$ncols=$this->num_col;
$all="center";
	//Intestazione delle colonne
	$tabella="
		<table id=\"\" cellPadding=\"1\"  cellspacing=\"1\" border=\"0\" class=\"stiletabella dt\" width=\"90%\">
			<tr bgcolor=\"$this->colore_colonne\">\n";

	//riga intestazione colonne ecreazione di def_col
	for ($i=0;$i<$ncols;$i++){
		$rowDef=explode(";",$this->tab_config[$i][0]);//qui trovo la definizione della i-esima colonna 
                $this->def_col[]=$rowDef;
                
                if (!in_array($rowDef[3],Array("hidden"))){
                    $tabella.="\t\t\t\t<th align=\"$all\" width=\"".$this->def_col[$i][2]."\"><font face=\"Verdana\" color=\"$this->color_head_font\" size=\"1\"><b>".$this->def_col[$i][0]."</b></font></th>\n";		
                    $all="left";
                }
	}
	$tabella.="\t\t\t</tr>\n";
	
	for ($i=0;$i<$this->num_record;$i++){
		$tabella.="\t\t\t<tr>\n";//CICLO SULLE COLONNE
                for ($j=0; $j<$ncols; $j++){
                    if (!in_array($this->tab_config[$i][3],Array("hidden"))){
                        $tabella.="\t\t\t\t".$this->get_cella($i,$j);
                    }
                }
			
		 $tabella.="\t\t\t</tr>\n";
		 $tabella.="\t\t\t<tr>\n\t\t\t\t<td colspan=\"$ncols\"><img src=\"images/gray_light.gif\" height=\"1\" width=\"99%\"></td>\n\t\t\t</tr>\n";			
	}
		//fine righe di dettaglio
	$tabella.="\t\t</table>\n";

	$buttons=$this->set_buttons();

	print $tabella;
    print $buttons;
}
function get_selectdb_value($val,$fld,$tab,$campo){
	if ($val==-1)
		return "Non definito";
	elseif(!$val){
		
		switch($tab){
			default:
				$fkey="Non definito";
				break;
		}
		return $fkey;
	}
	else{
		$sql="SELECT $campo FROM $tab WHERE $fld='$val';";
		//echo "<p>$sql</p>";
		if (!isset($this->db)) $this->connettidb();
		print_debug($sql,null,"fkey");
		if(!$this->db->sql_query($sql))
			print_debug("Errore Chiave Esterna\n".$sql,null,"error");
	
	}
	return $this->db->sql_fetchfield($campo);
			
}
function elenco_h($t){
        $ncols=$this->num_record;
        $all="center";

	//Intestazione delle colonne
	$tabella="
		<table cellPadding=\"1\"  cellspacing=\"1\" border=\"0\" class=\"stiletabella\" width=\"90%\">
			<tr bgcolor=\"$this->colore_colonne\">\n";

	//riga intestazione colonne ecreazione di def_col
	for ($i=0;$i<$ncols;$i++){
		$this->def_col[]=explode(",",$this->tab_config[$i][0]);//qui trovo la definizione della i-esima colonna 
                 if (!in_array($this->tab_config[$i][3],Array("hidden"))){
                    $tabella.="\t\t\t\t<td width=\"".$this->def_col[$i][2]."\"><font face=\"Verdana\" color=\"$this->color_head_font\" size=\"1\"><b>".$this->def_col[$i][0]."</b></font></td>\n";		
                    $all="left";
                 }
	}
	$tabella.="\t\t\t</tr>\n";
	$tabella.="\t\t\t<tr><td valign=\"middle\" class=\"printhide\">\n<b>$t&nbsp;:&nbsp;</b>";//CICLO SULLE COLONNE
	for ($i=0;$i<$this->num_record;$i++){
                 if (!in_array($this->tab_config[$i][3],Array("hidden"))){
                    $tabella.="\t\t\t\t".$this->get_cella($i,0)."&nbsp;&nbsp;";
                 }
	}
	$tabella.="</td>\t\t\t</tr>\n";
	$tabella.="\t\t\t<tr>\n\t\t\t\t<td colspan=\"$ncols\"><img src=\"images/gray_light.gif\" height=\"1\" width=\"99%\"></td>\n\t\t\t</tr>\n";			
	//fine righe di dettaglio
	$tabella.="\t\t</table>\n";
	print $tabella;
}


function zoomto($tabella,$id){
//DA RIFARE PER RENDERLA PARAMETRICA TEMPORANEA PER FARLA ANDARE

	$msgerr="Oggetto non presente in cartografia";

	switch ($tabella){
		
		case "pe.cu_info":
		case "pe.ct_info":
		case "cdu.vista_mappali":
		case "pe.vista_mappali_asserviti":
			$buff=20;
			$mappale=explode('@',$id);

			$sql = "SELECT oid, xmin(box3d(".THE_GEOM.")), ymin(box3d(".THE_GEOM.")), xmax(box3d(".THE_GEOM.")), ymax(box3d(".THE_GEOM.")) FROM map.ct_particelle where foglio='". $mappale[1] ."' and mappale='" .$mappale[0] . "'"   ;
			
			if (!isset($this->db)) $this->connettidb();
			$result = $this->db->sql_query($sql);
			$map=$this->db->sql_fetchrow();
			if ($map){
				$func="openNew('config=".MAPPA_PRATICHE."&objlayer=".LAYER_MAPPALI."&objid=".$map["oid"]."&zoomextent=". ($map["xmin"]-$buff) . ";" . ($map["ymin"]-$buff) . ";" . ($map["xmax"]+$buff) . ";" . ($map ["ymax"]+$buff) ."')";
			}else{
				$func= "alert('$msgerr')";
			}
			return $func;
			break;
		
		
		case "pe.indirizzi":
			$buff=50;
			$sql = "SELECT via,civico from pe.indirizzi where id=$id;";
			if (!isset($this->db)) $this->connettidb();
			$result = $this->db->sql_query($sql);
			$indi=$this->db->sql_fetchrow();
			if ($indi){
				$via=$indi["via"];
				$civico=$indi["civico"];
				$sql="SELECT oid,x(posizione),y(posizione) from map.civici where via ilike '$via' and civico='$civico';";
				$result = $this->db->sql_query($sql);
				$map=$this->db->sql_fetchrow();
				if ($map){	
				$func="openNew('config=".MAPPA_PRATICHE."&objlayer=".LAYER_CIVICI."&objid=".$map["oid"]."&zoomextent=". ($map["x"]-$buff) . ";" . ($map["y"]-$buff) . ";" . ($map["x"]+$buff) . ";" . ($map ["y"]+$buff) ."')";
				}else{
					$func= "alert('$msgerr')";
				}
			}else{
				$func= "alert('$msgerr')";
			}
			return $func;
			break;
		}
}


function zoomto_gc($tabella,$id){

$msgerr="Oggetto non presente in cartografia";

	switch ($tabella){
		
		case "pe.cu_info":
		case "pe.ct_info":
		case "cdu.vista_mappali": 
			$buff=20;
			$mappale=explode('@',$id);
            		$sezione='';
			if ($mappale[2]) $sezione=" and sezione='". $mappale[2] ."'";
			$sql = "SELECT gid, xmin(box3d(".THE_GEOM."))-50 as xmin, ymin(box3d(".THE_GEOM."))-50 as ymin, xmax(box3d(".THE_GEOM."))+50 as xmax, ymax(box3d(".THE_GEOM."))+50 as ymax FROM nct.particelle where foglio='". $mappale[1] ."' and mappale='" .$mappale[0] . "'"  .$sezione ;
			if (!isset($this->db)) $this->connettidb();
			$result = $this->db->sql_query($sql); //echo"$sql";
			$map=$this->db->sql_fetchrow();
			
			$_SESSION["MAPSET_".MAPPA_PRATICHE]["RESULT"]= Array("LAYER"=>"OBJ_LAYER","LAYER_TYPE"=>"2","ID_FIELD" => "gid","ID_LIST" => Array("0"=>$map["gid"]),"EXTENT" => Array("0"=>$map["xmin"],"1"=>$map["ymin"],"2"=>$map["xmax"],"3"=>$map["ymax"]));
               
                   			
                    if ($map){ 
				$func=(GC_VERSION==2)?("ApriMappa('".MAPSETID."','".TEMPLATE."','qt=".QTID_PARTICELLE."&objid=$map[gid]')"):("openMap(".MAPSETID.",'gisclient','&extent=$map[xmin],$map[ymin],$map[xmax],$map[ymax]')");
			}else{
				$func= "alert('$msgerr')";
			}
                            
			return $func;
			break;
		
		case "pe.indirizzi":
			$buff=50;
			$sql = "SELECT via,civico from pe.indirizzi where id=$id;"; 
			if (!isset($this->db)) $this->connettidb();
			$result = $this->db->sql_query($sql);
			$indi=$this->db->sql_fetchrow();
			if ($indi){
				$via=$indi["via"];
				$civico=$indi["civico"];
				//
				$sql="SELECT gid from civici.pe_civici inner join civici.pe_vie on(id=strada) where nome ilike '$via' and label='$civico';";
				echo 
                                $result = $this->db->sql_query($sql);
				$map=$this->db->sql_fetchrow();
				if ($map){	
					$func="ApriMappa('".MAPSETID."','".TEMPLATE."','qt=".QTID_CIVICI."&objid=$map[gid]')";
				}else{
					$func= "alert('$msgerr')";
				}
			}else{
				$func= "alert('$msgerr')";
			}
			return $func;
			break;
		}
	}
    function defColDataTable(){
        for($i=0;$i<count($this->tab_config);$i++){
            $d=explode(";",$this->tab_config[$i][0]);
            switch($d[3]){
                case "data":
                    $type="uk_date";
                    break;
                case "numero":
                case "valuta":
                case "superficie":    
                case "volume":    
                    $type="number";
                    break;
                default:
                    $type="string";
                    break;
            }
            //print_array($this->tab_config[$i]);
            $w=explode("#",$d[2]);
            $colDef[]=Array(
                "sTitle"=>$d[0],
                "sWidth"=>$w[0],
                "mDataProp"=>$d[1],
                "sType"=>$type
            );
        }
        return $colDef;
    }
}//end class
