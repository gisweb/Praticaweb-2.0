<?//Creazione della query di ricerca		
		$numeroprot=$_POST["protocollo"];
		$richiedente=$_POST["richiedente"];
		$foglio=$_POST["foglio"];
		$mappale=$_POST["mappale"];
		
// Ricerca sulla tabella avvioproc (Protocollo)
		if ($numeroprot){
			$sql.="(protocollo='$numeroprot') and ";	
			$criterio.="protocollo=$numeroprot + ";
		}
		if ($richiedente){
			$sql.="(richiedente  ilike '%$richiedente%') and ";
			$criterio.="richiedente=$richiedente + ";
		}

		if ($foglio){
			$sql.="(foglio='$foglio') and ";
			$criterio.="foglio=$foglio + ";
		}
		if ($mappale){
			$sql.="(mappale='$mappale') and ";
			$criterio.="mappale=$mappale + ";
		}
		
		if($sql){
			$sql=substr($sql,0,strlen($sql)-4);
			if ($foglio or $mappale) $sqlRicerca="select distinct richiesta.pratica,data from cdu.richiesta,cdu.mappali where $sql and richiesta.pratica=mappali.pratica order by data desc";
			else
				$sqlRicerca="select distinct data,pratica from cdu.richiesta where $sql order by data desc";
		}
		else $sqlRicerca="select distinct * from cdu.richiesta order by data desc";
		//echo "$sqlRicerca<br>";
		
?>
