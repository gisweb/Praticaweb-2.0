<?php
include_once "./lib/tabella.class.php";

class Tabella_v extends Tabella{

var $errors;
var $error_flag=0;
var $rigagrigia="\t<tr>\n\t\t<td style='font-size:0px;'><img src=\"images/gray_light.gif\" style='width:100%;height:1px;'></td>\n\t</tr>\n";	
var $tabella_elenco;//tabella dove prendo le opzioni per il tipo elenco

function set_tabella_elenco($nome_tabella){
	$this->tabella_elenco=$nome_tabella;
}

function set_errors($err){
	$this->errors=$err;
	$this->error_flag=1;
}
/*MODIFICA LOCK STATI AGGIUNTO PARAMETRO frozen*/
function get_controllo($label,$tipo,$w,$campo,$html5Attr,$frozen=0){
//function get_controllo($label,$tipo,$w,$campo){
//restituisce il controllo in funzione di tipo letto dal configfile e lo riempie con i dati il valore w può contenere più informazioni
	$retval=null; 
	$class=null;
	$help=null;
	$onChange=null;
	$dati=$this->array_dati[$this->curr_record];
	$err=$this->errors[$campo];
	$dato=$dati[$campo];
	if(isset($err)){
		$class="textbox ui-state-error";
		$help="<image src=\"images/small_help.gif\" onclick=\"alert('$err')\">";
        $title="title=\"$err\"";
	}
	$class=($err)?($class):("textbox");
	/*MODIFICA LOCK STATI SE IL CAMPO E' FROZEN AGGIUNGO disabled*/
	if ($frozen) $disabilitato="disabled";
	else
		$disabilitato="";
	/*FINE MODIFICA*/
	switch ($tipo) {
		case "hidden":
			$dato=($dato)?($dato):($this->array_hidden[$campo]);
			$retval="<INPUT  type=\"hidden\" name=\"$campo\" id=\"$campo\"  value=\"$dato\">";
			break;
		case "idriga":
		case "idkey":
			$retval="<INPUT  type=\"hidden\" name=\"$campo\" id=\"$campo\" value=\"$dato\" $html5Attr>";
			break;
		
		case "string":
			$retval=stripslashes($dato);
			break;
		
		case "ora":	
			if ($dato) 	$dato=number_format($dato,2, ':', '');			
			$retval="<INPUT class=\"$class\" maxLength=\"$w\" size=\"$w\" name=\"$campo\" id=\"$campo\" value=\"$dato\" $title $html5Attr $disabilitato >$help";
			break;
		case "numero":
			if ($dato) 
				$dato=number_format($dato,4, ',', '.');			
			else
				$dato="0";
			$retval="<INPUT class=\"$class\" maxLength=\"$w\" size=\"$w\" name=\"$campo\" id=\"$campo\" value=\"$dato\" $title $html5Attr $disabilitato>$help";
			break;
       case "intero":
			if ($dato) 
				$dato=number_format($dato,0, ',', '');			
			else
				$dato="0";
			$retval="<INPUT class=\"$class\" maxLength=\"$w\" size=\"$w\"  name=\"$campo\" id=\"numero\" value=\"$dato\" $title $html5Attr $disabilitato>$help";
			break;
            
        case "valuta":
		case "volume":
		case "superficie":
			if ($dato)
				$dato=number_format($dato,2, ',','.');
			else
				$dato="0,00";
			$retval="<INPUT class=\"$class\" maxLength=\"$w\" size=\"$w\" name=\"$campo\" id=\"$campo\" value=\"$dato\" $title $html5Attr $disabilitato>$help";
			break;
        case "upload":
            $size=intval($w+($w/5));
			$testo=stripslashes($dato);

            $retval="<INPUT class=\"$class\" type=\"file\" maxLength=\"$w\" size=\"$size\" name=\"$campo\" id=\"$campo\" value=\"$testo\" $html5Attr $disabilitato>$help";
            break;
		case "ui-button":
			$size=explode("x",$w);
			$jsfunction=$size[1];
			$width=$size[0];
			$retval="<button style=\"width:".$width."px\" id=\"$campo\">$label</button>";
			break;
		case "pratica":
		case "text":			
		case "textkey":
			$size=intval($w+($w/5));
			$testo=stripslashes($dato);
			$retval="<INPUT class=\"$class\" maxLength=\"$w\" size=\"$size\" name=\"$campo\" id=\"$campo\" value=\"$testo\" $html5Attr $disabilitato>$help";
			break;
                case "combosuggest":
                        $prms=explode('#',$w);
			if (count($prms)>1)
				list($size,$selectFN)=$prms;
			else{
				list($size)=$prms;
				$selectFN='setDatiAutoSuggest';
			}
			if (!$selectFN) $selectFN='setDatiAutoSuggest';
			$prms=array_slice($prms,2);
			for($i=0;$i<count($prms);$i++) $prms[$i]="'$prms[$i]'";
			$params=implode(',',$prms);
			$size=intval($size+($size/5));
			$testo=stripslashes($dato);
                        $retval=<<<EOT
<select class="$class" name="$campo" id="$campo" $title $html5Attr $disabilitato></select>$help
EOT;
                    break;
		case "autosuggest":
			$prms=explode('#',$w);
			if (count($prms)>1)
				list($size,$selectFN)=$prms;
			else{
				list($size)=$prms;
				$selectFN='setDatiAutoSuggest';
			}
			if (!$selectFN) $selectFN='setDatiAutoSuggest';
			$prms=array_slice($prms,2);
			for($i=0;$i<count($prms);$i++) $prms[$i]="'$prms[$i]'";
			$params=implode(',',$prms);
			$size=intval($size+($size/5));
			$testo=stripslashes($dato);		
			$retval=<<<EOT
<INPUT class=\"$class\" maxLength="$w" size="$size" name="$campo" id="$campo" value="$testo" $title $html5Attr $disabilitato>$help			
<button id="toggle_$campo" class="select_all"></button>				
<script>

	var data_$campo=new Object();
	jQuery('#$campo').autocomplete({
		source: function( request, response ) {
			data_$campo.term = request.term;
			data_$campo.field = '$campo';
			var flds=[$params];
			if (jQuery.isArray(flds)){
			    jQuery.each(flds,function(i,k){
				var v=jQuery('[name=\''+k+'\']').val();
				if (v){
				    data_$campo [k]=v;
				}
			    });
			}
	    
			jQuery.ajax({
			    url:suggestUrl,
			    dataType:'json',
			    type:'POST',
			    data:data_$campo,
			    success:response
			});
			
		    },
		select:$selectFN,
		minLength:0
	});
	jQuery('#toggle_$campo').button({
		icons: {
			primary: "ui-icon-circle-triangle-s"
		},
		text:false
	}).click(function(){
		jQuery('#$campo').autocomplete('search');
		return false;
	});
</script>
EOT;
			break;
		case "data":
                        
			$size=$w;
			if($min && $max) $range="$min:$max";
			else $range="1900:2050";
                        
			$data=$this->date_format(stripslashes($dato));
                        //$html5Attr="data-defaultDate=\"$defaultDate\" data-yearRange=\"$range\"";
			$retval="<INPUT class=\"$class textbox-date\" maxLength=\"$size\" size=\"$size\" name=\"$campo\" id=\"$campo\" value=\"$data\" $title $html5Attr $disabilitato>$help";
			/*$retval.=<<<EOT
<script>
	$(document).ready(function (){
		$('#$campo').datepicker({
			'dateFormat':'dd-mm-yy',
			changeMonth: true,
			changeYear: true,
                        $defaultDate
			$range
		});
	});
</script>
EOT;*/
			break;	
			                                                     
		case "textarea":
			$size=explode("x",$w);
			$retval="<textarea cols=\"$size[0]\" rows=\"$size[1]\" name=\"$campo\" id=\"$campo\" $title $html5Attr $disabilitato>$dato</textarea>";
			break;
		case "richtext":
			$size=explode("x",$w);
			$retval="<textarea cols=\"$size[0]\" rows=\"$size[1]\" name=\"$campo\" id=\"$campo\" $disabilitato>$dato</textarea>";
			$retval.="<script>";
			$retval.="\$('#$campo').tinymce({
				script_url : '/js/tinymce/tiny_mce.js',
				plugins : 'lists,table',
				language : 'it',
				theme : 'advanced',
				skin : 'o2k7',
				mode : 'textareas',
				theme_advanced_buttons1 : 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,table,|,removeformat,undo,redo,',
				theme_advanced_buttons2 : '',
				theme_advanced_buttons3 : '',
				//theme_advanced_disable :'indent,outdent,link,unlink,image,cleanup,hr,help,code,anchor,separator,visualaid,charmap,sub,sup',
				theme_advanced_toolbar_location : 'top',
				theme_advanced_toolbar_align : 'left',
				theme_advanced_resizing : 'true'
			});";
			$retval.="</script>";
			break;
		case "select"://elenco preso da file testo
			//echo $size;
			$size=explode("x",$w);
			$opzioni=$this->elenco_select($size[1],$dati[$campo]);
			$retval="<select style=\"width:$size[0]px\" class=\"$class\" name=\"$campo\"  id=\"$campo\" onmousewheel=\"return false\" $disabilitato>$opzioni</select>$help";
			break;
		
		case "multiselectdb":
			$size=explode("x",$w);
			$opzioni=$this->elenco_selectdb($size[1],explode(',',$dati[$campo]));
			//$class=($err)?($class):("class=\"multi\"");
			$retval="<select class=\"$class multi\" multiple=\"true\" style=\"width:$size[0]px\"  name=\"".$campo."[]\"  id=\"$campo\" $html5Attr $disabilitato>$opzioni</select>$help";
			break;
		case "multiselectdbview":
			$size=explode("x",$w);
			$opzioni=$this->elenco_select_view($size[1],'id in ('. $dati[$campo].')');
			$retval="<ol>$opzioni</ol>";
			break;
		case "selectdb"://elenco preso da query su db
			
			$size=explode("x",$w);
			$opzioni=$this->elenco_selectdb($size[1],Array($dati[$campo]),isset($size[2])?($size[2]):(null));
			
			if (isset($size[3])) $onChange="onChange=\"".$size[3]."()\"";
			$retval="<select style=\"width:$size[0]px\" class=\"$class\"  name=\"$campo\"  id=\"$campo\" onmousewheel=\"return false\" $onChange $title $html5Attr $disabilitato>$opzioni</select>$help";
			break;
		case "selectRPC":
			$size=explode("x",$w);
			$opzioni=$this->elenco_selectdb($size[1],Array($dati[$campo]),$size[2]);
			list($schema,$tb)=explode(".",$size[1]);
			
			if (isset($size[3])) $onChange="onChange=\"javascript:".$size[3]."(this,$this->idpratica,'$schema')\"";
			$retval="<select style=\"width:$size[0]px\" class=\"$class\"  name=\"$campo\"  id=\"$campo\" onmousewheel=\"return false\" $html5Attr $onChange $disabilitato>$opzioni</select>$help";
			break;	
			
		case "elenco"://elenco di opzioni da un campo di db valori separati da virgola
			$size=explode("x",$w);	
			if (isset($size[2])) $onChange="onChange=\"".$size[2]."()\"";			
			$opzioni=$this->elenco_selectfield($campo,$dati[$campo],$size[1]);
			$retval="<select style=\"width:$size[0]px\" class=\"$class\"  name=\"$campo\"  id=\"$campo\" onmousewheel=\"return false\" $onChange $html5Attr $disabilitato>$opzioni</select>";	
			break;
		
		case "chiave_esterna":
			$size=explode("x",$w);
			$testo=stripslashes($this->get_chiave_esterna($dato,$size[1],$size[2]));
			$retval="<INPUT $class maxLength=\"$w\" size=\"$size\"  class=\"textbox\" name=\"$campo\" id=\"$campo\" value=\"$testo\" disabled>$help";
			break;
		case "checkbox":
			(($dati[$campo]=="t") or ($dati[$campo]=="on") or (abs($dati[$campo])==1))?($selezionato="checked"):($selezionato="");
			$ch=strtoupper($campo);
			if($dati[$campo]==-1) $ch="<font color=\"FF0000\">EX $ch</font>";
			$retval="<b>$ch</b><input type=\"checkbox\"  name=\"$campo\"  id=\"$campo\" $selezionato $html5Attr $disabilitato>&nbsp;&nbsp;";
			break;
		
		case "radio":
			(($dati[$campo]=="t") or ($dati[$campo]=="on") or ($dati[$campo]==1))?($selezionato="checked"):($selezionato="");
			$retval="<input type=\"radio\" name=\"opzioni\"  id=\"$campo\" $selezionato $html5Attr $disabilitato>";
			break;
		
		case "button":
			$size=explode("x",$w);
			$jsfunction=$size[1];
			$width=$size[0];
			$retval="<input class=\"hexfield1\" style=\"width:".$width."px\" type=\"button\" value=\"$label\" onclick=\"$jsfunction('$campo')\" >";
			break;
			
		case "submit":
			$retval="<input  name=\"$campo\"  id=\"$campo\" class=\"hexfield1\" style=\"width:".$w."px\" type=\"submit\" value=\"$label\" onclick=\"return confirmSubmit()\" >";
			break;
			
		case "yesno":
			$yselected=$nselected='';
			(($dati[$campo]=="t") or ($dati[$campo]=="on") or ($dati[$campo]==1) or (!isset($dati[$campo])))?($yselected="selected"):($nselected="selected");
			$opzioni="<option value=1 $yselected>SI</option><option value=0 $nselected>NO</option>";
			$retval="<select style=\"width:$w\" class=\"textbox\"  name=\"$campo\"  id=\"$campo\" onmousewheel=\"return false\" $disabilitato>$opzioni</select>";		  
			break;
			
		case "noyes":
			$yselected=$nselected='';
			(($dati[$campo]=="t") or ($dati[$campo]=="on") or ($dati[$campo]==1))?($yselected="selected"):($nselected="selected");
			$opzioni="<option value=1 $yselected>SI</option><option value=0 $nselected>NO</option>";
			$retval="<select style=\"width:$w\" class=\"textbox\"  name=\"$campo\"  id=\"$campo\" onmousewheel=\"return false\" $disabilitato>$opzioni</select>";		  
			break;	
			
		case "pword":
                        $size=intval($w+($w/5));
			$testo=stripslashes($dato);
			$retval="<INPUT $class type=\"password\" maxLength=\"$w\" size=\"$size\"  class=\"textbox\" name=\"$campo\" id=\"$campo\" value=\"$dato\" $disabilitato>$help";
			break;
                case "search_text_equal":
                    list($schema,$table,$campo)=explode('.',$campo);
                    $id=sprintf("%s-%s-%s",$schema,$table,$campo);
                    $size=intval($w+($w/5));
                    $retval=<<<EOT
<input type="hidden" value="equal" name="$campo" class="search text" id="op_$id" datatable="$schema.$table"/>                           
<INPUT $class type="text" size="$size" class="textbox search" name="$campo" id="1_$id" value="">
EOT;
			break;
                case "search_text_contains":
			list($schema,$table,$campo)=explode('.',$campo);
                        $id=sprintf("%s-%s-%s",$schema,$table,$campo);
			$size=intval($w+($w/5));
			$retval=<<<EOT
<input type="hidden" value="contains" name="$campo" class="textbox search text" id="op_$id" datatable="$schema.$table"/>                           
<INPUT $class type="text" size="$size" class="textbox search" name="$campo" id="1_$id" value="">
EOT;
			break;
		case "search_text":
			list($schema,$table,$campo)=explode('.',$campo);
                        $id=sprintf("%s-%s-%s",$schema,$table,$campo);
			$size=intval($w+($w/5));
			$retval=<<<EOT
<select style="width:200px" class="textbox search text"  name="$campo"  id="op_$id" datatable="$schema.$table">
	<option value="">Seleziona =====></option>
	<option value="equal">Uguale a</option>
	<option value="contains">Contiene</option>
	<option value="startswith">Inizia per</option>
	<option value="endswith">Finisce per</option>
</select>
<INPUT $class type="text" size="$size" class="textbox search" name="$campo" id="1_$id" value="">
EOT;
			break;
		case "search_date":
                    list($schema,$table,$campo)=explode('.',$campo);
                    $id=sprintf("%s-%s-%s",$schema,$table,$campo);
                    $size=intval($w+($w/5));
                    $retval=<<<EOT
<select style="width:200px" class="textbox search date"  name="$campo"  id="op_$id" datatable="$schema.$table">
	<option value="">Seleziona =====></option>
	<option value="equal">Uguale a</option>
	<option value="great">Dopo il</option>
	<option value="less">Prima del</option>
	<option value="between">Compreso tra</option>
</select>
<INPUT $class type="text" size="$size" class="textbox search" name="$campo" id="1_$id" value="">
<INPUT $class type="text" size="$size" class="textbox search" style="display:none;" name="$campo" id="2_$id" value="">
<script>
	$('#1_$id').datepicker({
		'dateFormat':'dd-mm-yy',
		changeMonth: true,
		changeYear: true,
		$range
	});
	$('#2_$id').datepicker({
		'dateFormat':'dd-mm-yy',
		changeMonth: true,
		changeYear: true,
		$range
	});
	$('#op_$id').bind('change',function(){
		if($(this).val()==''){
			$('#1_$id').val('');
			$('#2_$id').val('');
			$('#2_$id').hide();
		}
		else if($(this).val()=='between'){
			$('#2_$id').show();
		}
		else{
			$('#2_$id').hide();
		}
	});
</script>
EOT;
			break;
		case "search_number":
                    list($schema,$table,$campo)=explode('.',$campo);
                    $id=sprintf("%s-%s-%s",$schema,$table,$campo);
                    $size=intval($w+($w/5));
                    $retval=<<<EOT
<select style="width:200px" class="textbox search number"  name="$campo"  id="op_$id" datatable="$schema.$table">
	<option value="">Seleziona =====></option>
	<option value="equal">Uguale a</option>
	<option value="great">Maggiore di</option>
	<option value="less">Minore di</option>
	<option value="between">Compreso tra</option>
</select>
<INPUT $class type="text" size="$size" class="textbox search" name="$campo" id="1_$id" value="">
<INPUT $class type="text" size="$size" class="textbox search" style="display:none;" name="$campo" id="2_$id" value="">
<script>
	$('#op_$id').bind('change',function(){
		if($(this).val()==''){
			$('#1_$id').val('');
			$('#2_$id').val('');
			$('#2_$id').hide();
		}
		else if($(this).val()=='between'){
			$('#2_$id').show();
		}
		else{
			$('#2_$id').hide();
		}
	});
</script>
EOT;
			break;
		case "search_list":

                    list($schema,$table,$campo)=explode('.',$campo);
                    $id=sprintf("%s-%s-%s",$schema,$table,$campo);
                    $size=explode("x",$w);
                    $opzioni=$this->elenco_selectdb($size[1],Array($dati[$campo]),isset($size[2])?($size[2]):(null));
                    //$retval="<select style=\"width:$size[0]px\" $class  name=\"$campo\"  id=\"$campo\" onmousewheel=\"return false\" $onChange $disabilitato>$opzioni</select>$help";
                    $retval=<<<EOT
<!--<select style="width:200px" class="textbox search text"  name="$campo"  id="op_$id" datatable="$schema.$table">
	<option value="">Seleziona =====></option>
	<option value="equal">Uguale a</option>
</select>-->
                            
<input type="hidden" value="equal" name="$campo" class="textbox search text" id="op_$id" datatable="$schema.$table"/>                           
select style="width:$size[0]px" class="textbox search"  name="$campo"  id="1_$id">$opzioni</select>
<script>

</script>
EOT;
		break;
	}	
		
	return $retval;
}

function get_dato($tipo,$w,$campo){
//restituisce il dato come stringa
	$dati=$this->array_dati[$this->curr_record];

	switch ($tipo) {
		
		case "idriga":
			$retval="";
			break;
		case "pratica":
		case "text":
		case "string":
			if(isset($dati[$campo]))
				$retval=$dati[$campo];
			else
				$retval='';//'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			break;
			
		case "data":
			$retval=$data=$this->date_format($dati[$campo]);
			break;
		case "ora":
			$retval=number_format($dati[$campo],2, ':', '');
			break;
		case "percentuale":
			$retval=number_format($dati[$campo],2, ',', '.')." %";
			break;
		case "numero":
			$retval=number_format($dati[$campo],4, ',', '.');
			break;
		case "intero":
			$retval=number_format($dati[$campo],0, '', '');
			break;
		case "valuta":
		//setto la valuta aggiungendo il metodo setvaluta alla classe tabella e poi la uso qui
    //echo("<br>Formatto valuta : ".$dati[$campo]."<br>");
			$retval=number_format($dati[$campo],2, ',', '.')." &euro;";;
			break;		
		case "superficie":	
			$retval=number_format($dati[$campo],2, ',', '.')." mq";
			break;
		case "volume":	
			$retval=number_format($dati[$campo],2, ',', '.')." mc";
			break;
		case "yesno": 
			if ($dati[$campo]==0) $retval="NO";
			if ($dati[$campo]==1) $retval="SI";
			break;			
			
		case "textarea":
			$retval=str_replace("\n","<br>",$dati[$campo]);
			break;
		case "chiave_esterna":		//Restituisce il campo descrittivo di un elenco 
			$retval=$this->get_chiave_esterna($campo,$w);
            break;
		case "stampa":
			//uso w per il nome del form
			/*if ($_SESSION["PERMESSI"]<3 || ($_SESSION["PERMESSI"]==3 && $_SESSION["PROPR_PRATICA_$pr"]=='SI')){
				$retval="";
			}
			else*/
			$retval=$this->editable?$this->elenco_stampe($w):'';
			break;		
		case "elenco":
			$retval=$this->get_dato_elenco($campo);
		case "button_view":
			$size=explode("x",$w);
			if ($dati[$campo]==$dati["id"]) $retval="<b>Aggiungi nuova segnalazione</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name=\"".$dati[$campo]."\"  id=\"$campo\"  src=\"icons/answer.gif\" type=\"image\"  onclick=\"$w('".$dati[$campo]."','".$dati["pratica"]."')\" >";
			break;
		case "selectdb":		//Restituisce il campo descrittivo di un elenco 
			$size=explode("x",$w);
			$retval=$this->get_selectdb_value($dati[$campo],"id",$size[1],"opzione");
			break;
		case "multiselectdb":
			$size=explode("x",$w);
			$retval=$this->get_multiselectdb_value($dati[$campo],"id",$size[1],"opzione");
			break;
        case "riferimento":
            $prms=explode('#',$w);
            $size=array_shift($prms);
            $form=array_shift($prms);
            for($i=0;$i<count($prms);$i++){
                $params[$prms[$i]]=$this->array_dati[$row][$prms[$i]];
            }
            $params['pratica']=$dati[$campo];
            if (isset($this->params))
                foreach($this->params as $k=>$v){
                    $params[$k]=$v;
                }
            $obj=json_encode($params);
            $retval=($dati[$campo])?("<a href='javascript:goToPratica(\"$form.php\",$obj)'><img title=\"Visualizza la pratica\" src=\"images/view.png\" border=\"0\"></a>"):('');
            break;
			
	}
	return $retval;
}

function get_campo($campo){
	return $this->array_dati[$this->curr_record][$campo];
}
function get_data($campo){
	$data=$this->array_dati[$this->curr_record][$campo];
	return $this->date_format($data);
}

//MODIFICA LOCK STATI AGGIUNTO PARAMETRO $frozen_cols ARRAY DI CAMPI CONGELATI
//function get_riga_edit($nriga){
function get_riga_edit($nriga,$frozen_cols=Array()){
	$ctr='';
//prendo una riga che può essere fatta da uno,  due o più colonne
// restituisce la riga in modalità edit con label controllo associato
	$riga=$this->tab_config[$nriga];
	$lbl="";
	for ($i=0;$i<count($riga);$i++){
		list($label,$campo,$w,$tipo,$html5Data)=explode(';',$riga[$i]);
        $html5Attr=Array();
        if ($html5Data){
            $d=explode('#',$html5Data);
            //$d=(is_array($d))?($d):(Array($d));
            for($k=0;$k<count($d);$k++){
                list($key,$v)=explode('=',$d[$k]);
                
                $html5Attr[]="$key=\"$v\"";
            }
            $html5Attr=implode(" ",$html5Attr);
        }
		$tipo=trim($tipo);
		if(($tipo!="button") and ($tipo!="submit"))
			($lbl)?(($label)?($lbl.=" -  ".$label):($lbl)):($lbl=$label);
		//MODIFICA LOCK STATI CONTROLLO SE QUESTO CAMPO E' TRA QUELLI CONGELATI NEL CASO GLI PASSO UN PARAMETRO ADDIZIONALE
		if ($frozen_cols && in_array($campo,$frozen_cols))
			$ctr.=$this->get_controllo($label,$tipo,$w,$campo,$html5Attr,1)."&nbsp;&nbsp;";
		else
			$ctr.=$this->get_controllo($label,$tipo,$w,$campo,$html5Attr)."&nbsp;&nbsp;";
	}

	return array($lbl,$ctr);
}

function get_riga_view($nriga){
// restituisce la riga in modalità view
	$testo_riga='';
	$riga=$this->tab_config[$nriga];
	for ($i=0;$i<count($riga);$i++){
		list($label,$campo,$w,$tipo)=explode(';',$riga[$i]);
		if ($label)  $label="<b>$label:&nbsp;</b>";
		$dato=$this->get_dato(trim($tipo),$w,$campo);
		if ($label.$dato)  
			$testo_riga.=$label.$dato."&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	return $testo_riga;
}
 
function edita(){
//if($this->error_flag==1)
	//echo ("I campi evidenziati in rosso non sono validi");
	//crea la tabella di editing
        $info = pathinfo(TAB.$this->config_file);
        $idTab=sprintf("%s-%s",$info["filename"],($this->curr_record)?($this->curr_record):('0'));
	$nrighe=$this->num_col;
	$tabella="<table id=\"$idTab\" cellPadding=\"2\" border=\"0\" class=\"stiletabella\" width=\"100%\">\n";

	//MODIFICA PER LOCK STATI
	/*CONTROLLO CHE LO STATO DELLA TABELLA NON SIA FROZEN   ---   CONDIZIONI PERCHE' AVVENGA:  IL DB DEVE ESSERE SETTATO , DEVO AVERE UNA TABELLA*/
	/*if (isset($this->db)){
		$tmpdb=$this->db;
		if (!isset($this->idpratica)) $this->idpratica=$this->array_dati[$this->curr_record]["pratica"];
		$sql="SELECT frozen FROM ".$this->tabelladb." WHERE id=".$this->array_dati[$this->curr_record]["id"]." AND pratica=".$this->idpratica;
		print_debug($sql,"tabella");
		$tmpdb->sql_query($sql);
		$frozen=$tmpdb->sql_fetchfield("frozen");
		//CERCO I CAMPI DA CONGELARE
		if ($frozen){
			list($schema,$tab)=explode(".",$this->tabelladb);
			$sql="SELECT colonne FROM cn.e_lock_stati WHERE tabella='$tab' AND nomeschema='$schema' AND (fase=$frozen OR fase=0);";
			print_debug($sql,"tabella");
			$tmpdb->sql_query($sql);
			$frozen_cols=$tmpdb->sql_fetchfield("colonne");
			$frozen_cols=str_replace("{","",$frozen_cols);
			$frozen_cols=str_replace("}","",$frozen_cols);
			$frozen_cols=str_replace("'","",$frozen_cols);
			$frozen_cols=explode(",",$frozen_cols);
		}
		
	}*/
	//if (!isset($frozen_cols)) $frozen_cols=Array();
	/*FINE LOCK*/
	
	for ($i=0;$i<$nrighe;$i++){
		
		//$riga=$this->get_riga_edit($i);
		//MODIFICA LOCK STATI AGGIUNTO PARAMETRO $frozen_cols ARRAY DI CAMPI CONGELATI
		if (isset($frozen_cols))
			$riga=$this->get_riga_edit($i,$frozen_cols);
		else
			$riga=$this->get_riga_edit($i);
		$tabella.="\t<tr>\n";
		//colonna label
		//$tabella.="\t\t<td width=\"200\" bgColor=\"#728bb8\"><font color=\"#ffffff\"><b>$riga[0]</b></font></td>\n";
		$tabella.="\t\t<td width=\"200\" class=\"label\">$riga[0]</td>\n";
		//colonna controlli campi
		$tabella.="\t\t<td valign=\"middle\">$riga[1]</td>\n";
		$tabella.="\t</tr>";
	}
	$tabella.="</table>\n";
	//aggiungo i campi nascosti che possono servire
	//MODIFICA PER LOCK STATI HO SPOSTATO LA RIGA PIU' SU
	if (!isset($this->idpratica)) $this->idpratica=$this->array_dati[$this->curr_record]["pratica"];	
	$tabella.="
	<INPUT type=\"hidden\" name=\"id\" value=\"".$this->array_dati[$this->curr_record]["id"]."\">
	<INPUT type=\"hidden\" name=\"pratica\" value=\"$this->idpratica\">
	<INPUT type=\"hidden\" name=\"chk\" value=\"".$this->array_dati[$this->curr_record]["chk"]."\">
	<INPUT type=\"hidden\" name=\"config_file\" value=\"$this->config_file\">\n
	";
    
    $buttons=$this->set_buttons();

	print $tabella;
    print $buttons;
}

function tabella($curr=0){
//crea la tabella per l'elenco in consultazione

	$nrighe=$this->num_col;
	$span=2*$nrighe;
	$tabella="<table class=\"stiletabella\"  cellpadding=\"2\" cellspacing=\"1\" width=\"95%\">\n";
	if ($this->viewable){
		for ($i=0;$i<$nrighe;$i++){
			$riga=$this->get_riga_view($i);
			$tabella.="\t<tr>\n";
			if (!$i){
				$tabella.="\t\t<td width=\"95%\">$riga</td>\n";
				//$tabella.="<td  rowspan=\"".$span."\" align=\"center\" valign=\"middle\">".$this->doc."</td>\n";
			}else{
				$tabella.="\t\t<td>$riga</td>\n";
			}
			$tabella.="\t</tr>\n";	
			if ($i<$nrighe-1) $tabella.=$this->rigagrigia;				
		}
	}
	else
		$tabella.="\t<tr><td><b>Non si dispone dei diritti per visualizzare i dati</b></td></tr>\n";
	$tabella.="</table>\n";
    
    
/*AGGIUNTA 24/11/2011 BOTTONI*/    
    
	$buttons=$this->set_buttons();

	print $tabella;
    print $buttons;
}	

function elenco($form=SELF){
	for ($i=0;$i<$this->num_record;$i++){
		$this->curr_record=$i;
        $this->idtabella=$this->array_dati[$i]['id'];
        $this->array_hidden["id"]="";
		$this->get_titolo($form);
		$this->tabella();
	}
}



//########################## ELENCHI ########################

function elenco_select($tabella,$selezionato){
// dal file tab crea la lista di opzioni per il controllo SELECT
	$retval='';
	$elenco=file(TAB_ELENCO."$tabella.tab");
        if(count(explode(',',$elenco[$i]))==2){
            for ($i=0;$i<count($elenco);$i++){
                    list($value,$label)=explode(',',$elenco[$i]);
                    (trim($$value)==trim($selezionato))?($selected="selected"):($selected="");
                    $retval.="\n<option value=\"$value\" $selected>".trim($label)."</option>";
            }
        }
        else{
            for ($i=0;$i<count($elenco);$i++){
                    (trim($elenco[$i])==trim($selezionato))?($selected="selected"):($selected="");
                    $retval.="\n<option $selected>".trim($elenco[$i])."</option>";
            }
        }
	return $retval;
}

function elenco_selectdb($tabella,$selezionato,$filtro=''){
// dalla tabella crea la lista di opzioni per il controllo SELECT

	if (!isset($this->db)) $this->connettidb();
	$sql="select id,opzione from $tabella";
	if (trim($filtro)){
		if (!ereg("=",$filtro))
			$filtro="$filtro='".$this->array_dati[$this->curr_record][$filtro]."'";
		$sql.=" where $filtro";

	}
	
	utils::debug(DEBUG_DIR.'selectdb.debug',$sql);
	$result = $this->db->sql_query ($sql);
	if (!$result){
		return;
	}
	$retval="";
	$elenco = $this->db->sql_fetchrowset();
	$nrighe=$this->db->sql_numrows();
	if (!$nrighe) return "\n<option value=\"\">Seleziona =====></option>";
	for  ($i=0;$i<$nrighe;$i++){
		(in_array($elenco[$i]["id"],$selezionato))?($selected="selected"):($selected="");
		$retval.="\n<option value=\"".$elenco[$i]["id"]."\" $selected>".$elenco[$i]["opzione"]."</option>";
  	}
	return $retval;
}

function elenco_select_view($tabella,$filtro){
	if (!isset($this->db)) $this->connettidb();
	$sql="select id,opzione from $tabella";
	if (trim($filtro)){
		$sql.=" where $filtro";

	}
	print_debug($sql,NULL,"tabella");
	$result = $this->db->sql_query ($sql);
	if (!$result){
		return;
	}
	$retval="";
	$elenco = $this->db->sql_fetchrowset();
	$nrighe=$this->db->sql_numrows();
	
	for  ($i=0;$i<$nrighe;$i++){
		$retval.="\n<li>".$elenco[$i]["opzione"]."</li>";
  	}
	return $retval;
	
}

function elenco_selectfield($campo,$selezionato,$filtro){
// dalla tabella crea la lista di opzioni per il controllo SELECT
//Utilizzata x ora solo sulla tabella per il calcolo degli oneri
//Temporanea fino alla costruzione dell'interfaccia di gestione configurazione tabella oneri

	$tabella=$this->tabella_elenco;
	if (!isset($this->db)) $this->connettidb();
	$sql="select $campo from $tabella";
	if (trim($filtro)){
		$filtro="id=".$this->array_dati[$this->curr_record][$filtro];
		$sql.=" where $filtro";
	}
	if ($this->debug)	echo("sql=$sql");
	print_debug($sql,NULL,"tabella");
	$this->db->sql_query ($sql);	
	//$elenco = $this->db->sql_fetchrowset();
	$elenco=$this->db->sql_fetchfield($campo);
	if (!$elenco){
		return;
	}
	$ar_elenco=explode(";",$elenco);
	//echo "array=";print_r($ar_elenco);
	$nopt=count($ar_elenco)/2;
	$i=0;
	while  ($i<count($ar_elenco)){
		$desc=$ar_elenco[$i];
		$i++;
		$val=$ar_elenco[$i];
		$i++;
		($val==$selezionato)?($selected="selected"):($selected="");
		$retval.="\n<option value=\"".$val."\" $selected>".$desc."</option>";
  	}
	return $retval;
}

function get_dato_elenco($campo){
//casino temporaneo fino alla costruzione interfaccia gestione oneri
//dato il campo prendo l'id dal post (!!!!ORRRORRRE) e restituitsco il valore di descrizione

	$dati=$this->array_dati[$this->curr_record];
	$tabella=$this->tabella_elenco;
	if (!isset($this->db)) $this->connettidb();
	$sql="select $campo from e_oneri where id=".$_POST["tabella"];
	if ($this->debug)	echo("sql=$sql");
	print_debug($sql,NULL,"tabella");
	$this->db->sql_query ($sql);	
	//$elenco = $this->db->sql_fetchrowset();
	$elenco=$this->db->sql_fetchfield($campo);
	if (!$elenco){
		return;
	}
	$ar_elenco=explode(";",$elenco);
	//echo "array=";print_r($ar_elenco);
	for ($i=0;$i<count($ar_elenco);$i++){
		if ($ar_elenco[$i]==$dati[$campo])
			$retval=$ar_elenco[$i-1];
  	}
	return $retval;
}

function get_chiave_esterna($val,$tab,$campo){
	$sql="SELECT $campo FROM $tab WHERE id::varchar='$val';";
	//echo "<p>$sql</p>";
	$this->db->sql_query($sql);
	return $this->db->sql_fetchfield($campo);
	
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
		$sql="SELECT $campo FROM $tab WHERE $fld::varchar='$val';";
		//echo "<p>$sql</p>";
		if (!isset($this->db)) $this->connettidb();
		print_debug($sql,null,"fkey");
		if(!$this->db->sql_query($sql))
			print_debug("Errore Chiave Esterna\n".$sql,null,"error");
	
	}
	return $this->db->sql_fetchfield($campo);
}
function get_multiselectdb_value($val,$fld,$tab,$campo){
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
		$sql="SELECT $campo FROM $tab WHERE $fld::varchar = ANY(string_to_array('$val',','));";

		if (!isset($this->db)) $this->connettidb();
		if(!$this->db->sql_query($sql))
			print_debug("Errore Chiave Esterna\n".$sql,null,"error");
	
	}
	return implode(',',$this->db->sql_fetchlist($campo));
}

// >>>>>>>>>>>>>>>>>>>>>>> FUNZIONI DI RICERCA NUOVO NOMINATIVO (da vedere)<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

function set_elenco_trovati($sql,$schema="pe"){

       $sql="SELECT DISTINCT ON (coalesce(soggetti.codfis,''),coalesce(soggetti.ragsoc,'') ) id,coalesce(soggetti.codfis,'') as codfis , coalesce(soggetti.ragsoc,'') as ragsoc,coalesce(datanato::varchar,'') as datanato,coalesce(comunato,'') as comunato,((((COALESCE(soggetti.cognome, ''::character varying)::text || COALESCE(' '::text || soggetti.nome::text, ''::text)) || COALESCE(' '::text || soggetti.titolo::text, ''::text)) || COALESCE(' '::text || soggetti.ragsoc::text, ''::text)) || COALESCE(' '::text || soggetti.indirizzo::text, ''::text)) || COALESCE((' ('::text || soggetti.prov::text) || ')'::text, ''::text) AS soggetto
	FROM $schema.soggetti where $sql ORDER BY coalesce(soggetti.codfis,''),coalesce(ragsoc,''),id DESC ;";
	//echo($sql);
	if (!isset($this->db)) $this->connettidb();
	$result = $this->db->sql_query($sql);
	return $this->db->sql_numrows();
	
}

function elenco_trovati($pratica,$schema="pe"){
	$nomi=$this->db->sql_fetchrowset();	
	print "
	<TABLE cellPadding=1  cellspacing=2 border=0 class=\"stiletabella\" width=\"600\">
	<tr>
		<td colspan=2 height=20 width=\"90%\" bgColor=\"#728bb8\"><font face=\"Verdana\" color=\"#ffffff\" size=\"2\"><b>I seguenti ".$this->db->sql_numrows() ." nominativi corrispondono ai criteri di ricerca</b></font></td>
	</tr>";
	foreach ($nomi as $ardati){
	print "
	<tr height=10>
		<td width=40><a href=$schema.scheda_soggetto.php?mode=new&pratica=$pratica&id=$ardati[id]><img src=\"images/left.gif\" border=0></a></td>
		<td width=100%>$ardati[soggetto] nato a $ardati[comunato] il $ardati[datanato]</td>
	</tr>
	<tr>
		<td colspan=2><img src=\"images/gray_light.gif\" height=\"1\" width=\"100%\"></td>
	</tr>";
	}
	print "</table>";
}
/*function elenco_rif($fields,$tab,$filter){
	$campi=implode(",",$fields);
	$sql="SELECT $campi FROM $tab WHERE $filter";
	if (!isset($this->db)) $this->connettidb();
	$result = $this->db->sql_query($sql);
}*/
}//end class


?>	
