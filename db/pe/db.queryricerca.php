<?php
//Creazione della query di ricerca		

 //print_array($_POST);
		$numeroprat=$_POST["numero"];
		$numeroprot=$_POST["protocollo"];
		$titolo=$_POST["titolo"];
		$richiedente=strtolower($_POST["richiedente"]);
		$progettista=strtolower($_POST["progettista"]);
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
		$nominativo=strtolower($_POST["nominativo"]);	
		$tipopratica=$_POST["tipo"];
		$tipodata=$_POST["data"];
		$data_dal=$_POST["data_dal"];
		$data_al=$_POST["data_al"];
        $data_ce=$_POST["data_ce"];
		$oggetto=strtolower($_POST["oggetto"]);
		$resp_proc=$_POST["resp_proc"];
		$resp_it=$_POST["resp_it"];
		$resp_ia=$_POST["resp_ia"];
		$abitabi=$_POST["abitabi"];
		$tiporicerca=$_POST["tiporicerca"];
              $ditta=strtolower($_POST["ditta"]);

               if ($_POST["comm_paesaggio"]=="1") 	
              	$sql_paes=" where tipo_comm='78'";
               elseif ($_POST["comm"]=="1")        
              	$sql_paes=" where tipo_comm <>'78'";
		

// Ricerca sulla tabella avvioproc (Protocollo)
		if ($numeroprat){
			$sql="(numero ilike '$numeroprat') and ";
			$criterio.="pratica ILIKE $numeroprat + ";
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
		if ($resp_ia){
			$sql.="(resp_ia=$resp_ia) and ";
			$criterio.="id resp.amm.=$resp_ia + ";
		}
		if ($resp_it){
			$sql.="(resp_it=$resp_it) and ";
			$criterio.="id resp.tecn.=$resp_it + ";
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
			$sqlProtocollo="select pratica,coalesce(coalesce(data_presentazione,data_prot),'01/01/1980'::date) as data_presentazione from pe.avvioproc where $sql";
			unset ($sql);
		}
		
		//Ricerca per tipo di data
		if ($tipodata){
			$np=strrpos($tipodata,".");
			$tabella=substr($tipodata,0,$np);
			$campo=substr($tipodata,$np+1);
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
					$sqlData="select pratica,coalesce(coalesce(data_presentazione,data_prot),'01/01/1980'::date) as data_presentazione from pe.$tabella where $sql order by data_presentazione DESC";
				else 
					$sqlData="select avvioproc.pratica,coalesce(coalesce(data_presentazione,data_prot),'01/01/1980'::date) as data_presentazione from pe.$tabella left join pe.avvioproc on(avvioproc.pratica=$tabella.pratica)where $sql order by data_presentazione DESC";

			unset ($sql);
		}
			
		//Ricerca per titolo
		if ($titolo){
			$sqlTitolo="select titolo.pratica,coalesce(coalesce(data_presentazione,data_prot),'01/01/1980'::date) as data_presentazione from pe.titolo left join pe.avvioproc on(avvioproc.pratica=titolo.pratica) where titolo='$titolo'";
			$criterio.="titolo=$titolo + ";
		}
		//Ricerca per Commissione Edilizia
        if ($data_ce){
			$sqlCE= "select pareri.pratica,coalesce(coalesce(data_presentazione,data_prot),'01/01/1980'::date) as data_presentazione from pe.pareri left join pe.avvioproc on(avvioproc.pratica=pareri.pratica) where (data_ril='$data_ce'::date and ente IN (2,8,33))";
			$criterio.="C.E.=$data_ce + ";
		}
		//Ricerca per soggetto
		if ($richiedente){
			$sqlRichiedente= "select soggetti.pratica,coalesce(coalesce(data_presentazione,data_prot),'01/01/1980'::date) as data_presentazione from pe.soggetti left join pe.avvioproc on(avvioproc.pratica=soggetti.pratica) where (coalesce(lower(cognome),'') || ' '::text || coalesce(lower(nome),'') || ' '::text || coalesce(lower(ragsoc),'') ilike '%$richiedente%' and richiedente=1)";
			$criterio.="richiedente=$richiedente + ";
		}
		if ($progettista){
			$sqlProgettista="select soggetti.pratica,coalesce(coalesce(data_presentazione,data_prot),'01/01/1980'::date) as data_presentazione from pe.soggetti left join pe.avvioproc on(avvioproc.pratica=soggetti.pratica) where (coalesce(lower(cognome),'') || ' '::text || coalesce(lower(nome),'') || ' '::text || coalesce(lower(ragsoc),'') ilike '%$progettista%' and progettista=1)";
			$criterio.="progettista=$progettista + ";
		}
		if (($soggetto)&&($nominativo)){
			$sqlTecnico="select soggetti.pratica,coalesce(coalesce(data_presentazione,data_prot),'01/01/1980'::date) as data_presentazione from pe.soggetti left join pe.avvioproc on(avvioproc.pratica=soggetti.pratica) where (coalesce(lower(cognome),'') || ' '::text || coalesce(lower(nome),'') || ' '::text || coalesce(lower(ragsoc),'') ilike '%$nominativo%' and $soggetto=1)";
			$criterio.="$soggetto=$nominativo + ";
		}
              if ($ditta){
			$sqlDitta="select soggetti.pratica,coalesce(coalesce(data_presentazione,data_prot),'01/01/1980'::date) as data_presentazione from pe.soggetti left join pe.avvioproc on(avvioproc.pratica=soggetti.pratica) where coalesce(lower(ragsoc),'') ilike '%$ditta%'";
			$criterio.="rag_soc=$ditta + ";
		}
				
		//Ricerca per indirizzo
		if ($via){
			$sqlIndirizzo="select indirizzi.pratica,coalesce(coalesce(data_presentazione,data_prot),'01/01/1980'::date) as data_presentazione from pe.indirizzi left join pe.avvioproc on(avvioproc.pratica=indirizzi.pratica) where via ilike '%$via%'";
			$criterio.="via=$via + ";
			if ($civico){
				$sqlIndirizzo.=" and civico='$civico'";
				$criterio.="civico=$civico + ";
			}
		}
		
		//Ricerca catasto terreni
		if ($nct){
			if ($foglio){
				$sqlNCT="select cterreni.pratica,coalesce(coalesce(data_presentazione,data_prot),'01/01/1980'::date) as data_presentazione from pe.cterreni left join pe.avvioproc on(avvioproc.pratica=cterreni.pratica) where foglio='$foglio'";
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
				$sqlNCEU="select curbano.pratica,coalesce(coalesce(data_presentazione,data_prot),'01/01/1980'::date) as data_presentazione from pe.curbano left join pe.avvioproc on(avvioproc.pratica=curbano.pratica) where foglio ILIKE '$foglio'";
				$criterio.="foglio(NCEU)=$foglio + ";
				if ($mappale){
					$sqlNCEU.=" and mappale ILIKE '$mappale'";
					$criterio.="mappale=$mappale + ";
				}
			}
		}
		
		//Ricerca certificato abitabilita
		if ($abitabi){
			$sqlAbitabi="select abitabi.pratica,coalesce(coalesce(data_presentazione,data_prot),'01/01/1980'::date) as data_presentazione from pe.abitabi left join pe.avvioproc on(avvioproc.pratica=abitabi.pratica) where prot_doc='$abitabi'";
			$criterio.="abitabilità=$abitabi + ";
		}	
		
		// Ricerca  commissioni
		if ($idpr){
			$sqlCommiss_pr="select distinct commissione as pratica from ce.discusse $sql_paes and (numero='$idpr') ";
			//$sqlc="(pratica=$idpr) and ";
			$criterio.="pratica=$idpr + ";
              }    
		if ($data_conv){
			$sqlCommiss_dc="select * from ce.commissione $sql_paes and data_convocazione='$data_conv' order by data_convocazione DESC";
			//$sqlc.="(data_convocazione='$data_conv') and ";
			$criterio.="data_convocazione=$data_conv + ";
		}

		
		$criterio=substr($criterio,0,strlen($criterio)-3);
		
		($tiporicerca)?($op=" INTERSECT "):($op=" UNION     ");
		
		if ($sqlProtocollo){
			$sql="($sqlProtocollo) $op";
			$order="order by 2 DESC";
		}
		if ($sqlData){
			$sql.="($sqlData) $op";
			$order="order by 2 DESC";
		}
		if ($sqlTitolo){
			$sql.="($sqlTitolo) $op";
                    //$order="order by data_presentazione DESC";
             }
		if ($sqlRichiedente){
			$sql.="($sqlRichiedente) $op";
			$order="order by 2 DESC";
        }		
		if ($sqlProgettista){
			$sql.="($sqlProgettista) $op";
			$order="order by 2 DESC";
        }
		if ($sqlTecnico){
			$sql.="($sqlTecnico) $op";
			$order="order by 2 DESC";
        }
        if ($sqlDitta){
			$sql.="($sqlDitta) $op";
			$order="order by 2 DESC";
        }
        if ($sqlCE){
			$sql.="($sqlCE) $op";
			$order="order by 2 DESC";
        }
		if ($sqlIndirizzo){
			$sql.="($sqlIndirizzo) $op";
			$order="order by 2 DESC";
		}
		if ($sqlNCT){
			$sql.="($sqlNCT) $op";
			$order="order by 2 DESC";
        }	
		if ($sqlNCEU){
			$sql.="($sqlNCEU) $op";
			$order="order by 2 DESC";
        }	
		
		if ($sqlAbitabi){
			$sql.="($sqlAbitabi) $op";
			$order="order by 2 DESC";
        }	
		if ($sqlCommiss_pr)
			$sql.="($sqlCommiss_pr) $op";
		if ($sqlCommiss_dc)
			$sql.="($sqlCommiss_dc) $op";
		if ($all=="on"){
			$sql="(select pratica from ce.commissione $sql_paes order by data_convocazione DESC) $op";
			$criterio="tutte le commissioni";
		}
		if ($sql)
			$sqlRicerca="((".substr($sql,0,strlen($sql)-10).") )";
	
		//echo $op."<br>";
		//print("<p>$sqlRicerca</p>");

?>
