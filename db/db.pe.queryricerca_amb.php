<?//Creazione della query di ricerca		

		$numeroprat=$_POST["numero"];
		$numeroprot=$_POST["protocollo"];
		$titolo=$_POST["titolo"];
		$richiedente=$_POST["richiedente"];
		$progettista=$_POST["progettista"];
		$via=$_POST["via"];
		$civico=$_POST["civico"];
		$foglio=$_POST["foglio"];
		$mappale=$_POST["mappale"];
		$nct=$_POST["nct"];
		$nceu=$_POST["nceu"];
		$idpr=$_POST["id_pratica"];				//ricerca commissioni per pratica discussa
		$data_conv=$_POST["data_convocazione"];			//ricerca commissioni per data di convocazione
		$all=$_POST["all"];	
		$tipoprat_ce=$_POST["tipoprat_ce"];		
		//$datapr1=$_POST["data_presentazione1"];	
		//$datapr2=$_POST["data_presentazione2"];
		//ricerca avanzata
			$soggetto=$_POST["soggetto"];		
			$nominativo=$_POST["nominativo"];	
			$tipopratica=$_POST["tipo"];
			$tipodata=$_POST["data"];
			$data_dal=$_POST["data_dal"];
			$data_al=$_POST["data_al"];
			$oggetto=$_POST["oggetto"];
			$resp_proc=$_POST["resp_proc"];
			$abitabi=$_POST["abitabi"];		
	
			$tiporicerca=$_POST["tiporicerca"];
		
// Ricerca sulla tabella avvioproc (Protocollo)
		if ($numeroprat){
			$sql="(numero='$numeroprat') and ";
			$criterio.="pratica=$numeroprat + ";
		}
		if ($tipoprat_ce){
			$tipoprat_ce=substr($tipoprat_ce,0,2); 
			$sql="(tipo ilike '$tipoprat_ce%') and";
			$criterio.="tipo ilike '$tipoprat_ce%' + ";
		}
		if ($numeroprot){
			$sql.="(protocollo='$numeroprot') and ";	
			$criterio.="protocollo=$numeroprot + ";
		}
		if ($tipopratica){	
			if (strpos($tipopratica,"-")) {		// MODIFICHE DEL 10-02-2006	Caso di ricerca di più tipi di pratica
				$tipi=explode("-",$tipopratica);
				foreach($tipi as $val) $tmp_sql.="tipo=$val or ";
				$sql.="(".substr($tmp_sql,0,-4).") and ";
			}
			else								// FINE MODIFICHE
				$sql.="(tipo=$tipopratica) and ";
			$criterio.="id tipo=$tipopratica + ";
		}

		if ($resp_proc){
			$sql.="(resp_proc=$resp_proc) and ";
			$criterio.="id resp.=$resp_proc + ";
		}
		if ($oggetto){
			$sql.="(oggetto  ilike '%$oggetto%') and ";
			$criterio.="oggetto=$oggetto + ";
		}
		/*if ($datapr1 and !$datapr2){
			$sql.="data_presentazione='$datapr1' and ";
			$criterio.="data_presentazione=$datapr1 + ";
		}
		elseif ($datapr1 and $datapr2){
			$sql.="data_presentazione>='$datapr1' and data_presentazione<'$datapr2' and ";
			$criterio.="data_presentazione>=$datapr1 + data_presentazione<$datapr2 + ";
		}*/
		if($sql){
			$sql=substr($sql,0,strlen($sql)-4);
			$sqlProtocollo="select pratica,data_presentazione from pe.avvioproc where $sql and tipo>=30000 order by data_presentazione DESC";
			unset ($sql);
		}
		
		//Ricerca per tipo di data
		if ($tipodata){ 
			$np=strrpos($tipodata,".");
			$tabella=substr($tipodata,0,$np);
			$campo=substr($tipodata,$np+1);echo"$campo";
			if($data_al){
				$sql1.="($tipodata <= '$data_al')";
				$criterio.="$campo &lt; $data_al + ";
			}
			if($data_dal){
				if($data_al)
					$sql="($tipodata >= '$data_dal')";
				else
					$sql="($tipodata = '$data_dal')";
				$criterio.="$campo &gt; $data_dal + ";
			}
			if ($sql1) $sql.=" and $sql1"; 
			if ($sql) 
				if($campo=="data_presentazione")
					$sqlData="select distinct pratica,data_presentazione from pe.$tabella where $sql and tipo>=30000 order by data_presentazione DESC";
				else $sqlData="select distinct avvioproc.pratica,data_presentazione from pe.$tabella left join pe.avvioproc on(avvioproc.pratica=$tabella.pratica)where $sql and tipo>=30000 order by data_presentazione DESC";

			unset ($sql);
		}
			
		//Ricerca per titolo
		if ($titolo){
			$sqlTitolo="select distinct titolo.pratica,data_presentazione from pe.titolo left join pe.avvioproc on(avvioproc.pratica=titolo.pratica) where titolo='$titolo' and tipo>=30000 order by data_presentazione DESC";
			$criterio.="titolo=$titolo + ";
		}
		
		//Ricerca per soggetto
		if ($richiedente){
			$sqlRichiedente= "select distinct soggetti.pratica,data_presentazione from pe.soggetti left join pe.avvioproc on(avvioproc.pratica=soggetti.pratica) where (coalesce(cognome,'') || ' '::text || coalesce(nome,'') || ' '::text || coalesce(ragsoc,'') ilike '%$richiedente%' and richiedente=1) and tipo>=30000 order by data_presentazione DESC";
			$criterio.="richiedente=$richiedente + ";
		}
		if ($progettista){
			$sqlProgettista="select distinct soggetti.pratica,data_presentazione from pe.soggetti left join pe.avvioproc on(avvioproc.pratica=soggetti.pratica) where (coalesce(cognome,'') || ' '::text || coalesce(nome,'') || ' '::text || coalesce(ragsoc,'') ilike '%$progettista%' and progettista=1) and tipo>=30000 order by data_presentazione DESC";
			$criterio.="progettista=$progettista + ";
		}
		if (($soggetto)&&($nominativo)){
			$sqlTecnico="select distinct soggetti.pratica,data_presentazione from pe.soggetti left join pe.avvioproc on(avvioproc.pratica=soggetti.pratica) where (coalesce(cognome,'') || ' '::text || coalesce(nome,'') || ' '::text || coalesce(ragsoc,'') ilike '%$nominativo%' and $soggetto=1) and tipo>=30000 order by data_presentazione DESC";
			$criterio.="$soggetto=$nominativo + ";
		}
				
		//Ricerca per indirizzo
		if ($via){
			$sqlIndirizzo="select distinct indirizzi.pratica,data_presentazione from pe.indirizzi left join pe.avvioproc on(avvioproc.pratica=indirizzi.pratica) where via ilike '%$via%'  and tipo>=30000 order by data_presentazione DESC";
			$criterio.="via=$via + ";
			if ($civico){
				$sqlIndirizzo.=" and civico='$civico'";
				$criterio.="civico=$civico + ";
			}
		}
		
		//Ricerca catasto terreni
		if ($nct){
			if ($foglio){
				$sqlNCT="select distinct cterreni.pratica,data_presentazione from pe.cterreni left join pe.avvioproc on(avvioproc.pratica=cterreni.pratica) where foglio='$foglio' and tipo>=30000 order by data_presentazione DESC";
				$criterio.="foglio(NCT)=$foglio + ";
				if ($mappale){
					$sqlNCT.=" and mappale='$mappale'";
					$criterio.="mappale=$mappale + ";
				}
			}
		}
		
		//Ricerca catasto urbano
		if ($nceu){
			if ($foglio){
				$sqlNCEU="select distinct curbano.pratica,data_presentazione from pe.curbano left join pe.avvioproc on(avvioproc.pratica=curbano.pratica) where foglio='$foglio' and tipo>=30000 order by data_presentazione DESC";
				$criterio.="foglio(NCEU)=$foglio + ";
				if ($mappale){
					$sqlNCEU.=" and mappale='$mappale'";
					$criterio.="mappale=$mappale + ";
				}
			}
		}
		
		//Ricerca certificato abitabilita
		if ($abitabi){
			$sqlAbitabi="select distinct abitabipratica,data_presentazione from pe.abitabi left join pe.avvioproc on(avvioproc.pratica=abitabi.pratica) where prot_doc='$abitabi' and tipo>=30000 order by data_presentazione DESC";
			$criterio.="abitabilità=$abitabi + ";
		}	
		
		// Ricerca  commissioni
		if ($idpr){
			$sqlCommiss_pr="select distinct commissione as pratica from ce.discusse where (numero='$idpr') ";
			//$sqlc="(pratica=$idpr) and ";
			$criterio.="pratica=$idpr + ";
		}
		if ($data_conv){
			$sqlCommiss_dc="select * from ce.commissione where data_convocazione='$data_conv' order by data_convocazione DESC";
			//$sqlc.="(data_convocazione='$data_conv') and ";
			$criterio.="data_convocazione=$data_conv + ";
		}

		
		$criterio=substr($criterio,0,strlen($criterio)-3);
		
		($tiporicerca)?($op=" INTERSECT "):($op=" UNION     ");
		
		if ($sqlProtocollo){
			$sql="($sqlProtocollo) $op";
			$order="order by data_presentazione DESC";
		}
		if ($sqlData){
			$sql.="($sqlData) $op";
			$order="order by data_presentazione DESC";
		}
		if ($sqlTitolo)
			$sql.="($sqlTitolo) $op";
		if ($sqlRichiedente)
			$sql.="($sqlRichiedente) $op";		
		if ($sqlProgettista)
			$sql.="($sqlProgettista) $op";
		if ($sqlTecnico)
			$sql.="($sqlTecnico) $op";
		if ($sqlIndirizzo)
			$sql.="($sqlIndirizzo) $op";	
		if ($sqlNCT)
			$sql.="($sqlNCT) $op";	
		if ($sqlNCEU)
			$sql.="($sqlNCEU) $op";		
		if ($sqlAbitabi)
			$sql.="($sqlAbitabi) $op";	
		if ($sqlCommiss_pr)
			$sql.="($sqlCommiss_pr) $op";
		if ($sqlCommiss_dc)
			$sql.="($sqlCommiss_dc) $op";
		if ($all=="on"){
			$sql="(select pratica from ce.commissione order by data_convocazione DESC) $op";
			$criterio="tutte le commissioni";
		}
		if ($sql)
			$sqlRicerca="(".substr($sql,0,strlen($sql)-10).")";
	
		//echo $op."<br>";
		//print($sqlRicerca);

?>
