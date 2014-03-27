<?php
$query=Array();
$query["default"]=<<<EOT
SELECT DISTINCT A.pratica,A.numero,A.protocollo,A.data_prot,A.data_presentazione,A.oggetto,B.nome as tipo_pratica,C.descrizione as tipo_intervento,coalesce(D.nome,'non assegnata')  as responsabile,E.richiedente,F.progettista,G.elenco_ct,H.elenco_cu,I.ubicazione
FROM pe.avvioproc A LEFT JOIN 
pe.e_tipopratica B ON(A.tipo=B.id) LEFT JOIN
pe.e_intervento C ON (A.intervento=C.id) LEFT JOIN
admin.users D ON(A.resp_proc=D.userid) LEFT JOIN 
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as richiedente FROM pe.soggetti WHERE richiedente=1 AND voltura=0 GROUP BY pratica) E USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as progettista FROM pe.soggetti WHERE progettista=1 AND voltura=0 GROUP BY pratica) F USING(pratica) LEFT JOIN
(SELECT * FROM pe.grp_particelle_ct) G USING(pratica) LEFT JOIN
(SELECT * FROM pe.grp_particelle_cu) H USING(pratica) LEFT JOIN
(SELECT indirizzi.pratica, array_to_string(array_agg((COALESCE(indirizzi.via, ''::character varying)::text || COALESCE(' '::text || indirizzi.civico::text)) || COALESCE(' int.'::text || indirizzi.interno::text, ''::text)), ', '::text) AS ubicazione
   FROM pe.indirizzi
  GROUP BY indirizzi.pratica) I USING(pratica)
WHERE pratica IN (%s) 
%s %s LIMIT %s OFFSET %s                 
EOT;
/*QUERY per la ricerca e il raggruppamento dei civici*/
$query["civico"]=<<<EOT
SELECT DISTINCT A.pratica,A.numero,A.protocollo,A.data_prot,A.data_presentazione,A.oggetto,B.nome as tipo_pratica,C.descrizione as tipo_intervento,coalesce(D.nome,'non assegnata')  as responsabile,E.richiedente,F.progettista,G.elenco_ct,H.elenco_cu,I.via,I.civico,I.interno
FROM pe.avvioproc A LEFT JOIN 
pe.e_tipopratica B ON(A.tipo=B.id) LEFT JOIN
pe.e_intervento C ON (A.intervento=C.id) LEFT JOIN
admin.users D ON(A.resp_proc=D.userid) LEFT JOIN 
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as richiedente FROM pe.soggetti WHERE richiedente=1 AND voltura=0 GROUP BY pratica) E USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as progettista FROM pe.soggetti WHERE progettista=1 AND voltura=0 GROUP BY pratica) F USING(pratica) LEFT JOIN
( SELECT DISTINCT foo.pratica, btrim((COALESCE('Sezione: '::text || foo.sezione::text, ''::text) || COALESCE(' Foglio: '::text || foo.foglio::text, ''::text)) || COALESCE(' Mappali: '::text || foo.mappali, ''::text)) AS elenco_ct
   FROM ( SELECT DISTINCT a.pratica, b.nome AS sezione, COALESCE(a.foglio, ''::character varying) AS foglio, array_to_string(array_agg(COALESCE(a.mappale, ''::character varying)), ','::text) AS mappali
           FROM (SELECT DISTINCT pratica,sezione,foglio,mappale from pe.cterreni) a
      LEFT JOIN nct.sezioni b USING (sezione)
     GROUP BY a.pratica, b.nome, COALESCE(a.foglio, ''::character varying)) foo) G USING(pratica) LEFT JOIN
( SELECT DISTINCT foo.pratica, btrim((COALESCE('Sezione: '::text || foo.sezione::text, ''::text) || COALESCE(' Foglio: '::text || foo.foglio::text, ''::text)) || COALESCE(' Mappali: '::text || foo.mappali, ''::text)) AS elenco_cu
   FROM ( SELECT DISTINCT a.pratica, b.nome AS sezione, COALESCE(a.foglio, ''::character varying) AS foglio, array_to_string(array_agg(COALESCE(a.mappale, ''::character varying)), ','::text) AS mappali
           FROM (SELECT DISTINCT pratica,sezione,foglio,mappale from pe.curbano) a
      LEFT JOIN nct.sezioni b USING (sezione)
     GROUP BY a.pratica, b.nome, COALESCE(a.foglio, ''::character varying)) foo) H USING(pratica) LEFT JOIN
(SELECT DISTINCT pratica, coalesce(via,'') as via, coalesce(civico,'s.c.') as civico,coalesce(interno,'') as interno FROM pe.indirizzi WHERE %s) I USING(pratica)
WHERE pratica IN (%s) 
ORDER BY via,civico,interno,data_prot DESC               
EOT;
/*QUERY di ricerca e raggruppamento catasto terreni*/
$query["terreni"]=<<<EOT
SELECT DISTINCT A.pratica,A.numero,A.protocollo,A.data_prot,A.data_presentazione,A.oggetto,B.nome as tipo_pratica,C.descrizione as tipo_intervento,coalesce(D.nome,'non assegnata')  as responsabile,E.richiedente,F.progettista,H.elenco_cu,G.sezione,G.foglio,G.mappale,G.sub,I.ubicazione
FROM pe.avvioproc A LEFT JOIN 
pe.e_tipopratica B ON(A.tipo=B.id) LEFT JOIN
pe.e_intervento C ON (A.intervento=C.id) LEFT JOIN
admin.users D ON(A.resp_proc=D.userid) LEFT JOIN 
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as richiedente FROM pe.soggetti WHERE richiedente=1 AND voltura=0 GROUP BY pratica) E USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as progettista FROM pe.soggetti WHERE progettista=1 AND voltura=0 GROUP BY pratica) F USING(pratica) LEFT JOIN
( SELECT DISTINCT pratica,coalesce(sezione,'') as sezione,coalesce(foglio,'') as foglio,coalesce(mappale,'') as mappale,coalesce(sub,'') as sub FROM pe.cterreni WHERE %s) G USING(pratica) LEFT JOIN
( SELECT DISTINCT foo.pratica, btrim((COALESCE('Sezione: '::text || foo.sezione::text, ''::text) || COALESCE(' Foglio: '::text || foo.foglio::text, ''::text)) || COALESCE(' Mappali: '::text || foo.mappali, ''::text)) AS elenco_cu
   FROM ( SELECT DISTINCT a.pratica, b.nome AS sezione, COALESCE(a.foglio, ''::character varying) AS foglio, array_to_string(array_agg(COALESCE(a.mappale, ''::character varying)), ','::text) AS mappali
           FROM (SELECT DISTINCT pratica,sezione,foglio,mappale from pe.curbano) a
      LEFT JOIN nct.sezioni b USING (sezione)
     GROUP BY a.pratica, b.nome, COALESCE(a.foglio, ''::character varying)) foo) H USING(pratica) LEFT JOIN
(SELECT indirizzi.pratica, array_to_string(array_agg((COALESCE(indirizzi.via, ''::character varying)::text || COALESCE(' '::text || indirizzi.civico::text)) || COALESCE(' int.'::text || indirizzi.interno::text, ''::text)), ', '::text) AS ubicazione
   FROM pe.indirizzi
  GROUP BY indirizzi.pratica) I USING(pratica)
WHERE pratica IN (%s) 
ORDER BY sezione,foglio,mappale,data_prot DESC               
EOT;
/*QUERY di ricerca e raggruppamento catasto urbano*/
$query["urbano"]=<<<EOT
SELECT DISTINCT A.pratica,A.numero,A.protocollo,A.data_prot,A.data_presentazione,A.oggetto,B.nome as tipo_pratica,C.descrizione as tipo_intervento,coalesce(D.nome,'non assegnata')  as responsabile,E.richiedente,F.progettista,G.elenco_ct,H.sezione,H.foglio,H.mappale,H.sub,I.ubicazione
FROM pe.avvioproc A LEFT JOIN 
pe.e_tipopratica B ON(A.tipo=B.id) LEFT JOIN
pe.e_intervento C ON (A.intervento=C.id) LEFT JOIN
admin.users D ON(A.resp_proc=D.userid) LEFT JOIN 
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as richiedente FROM pe.soggetti WHERE richiedente=1 AND voltura=0 GROUP BY pratica) E USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as progettista FROM pe.soggetti WHERE progettista=1 AND voltura=0 GROUP BY pratica) F USING(pratica) LEFT JOIN
( SELECT DISTINCT foo.pratica, btrim((COALESCE('Sezione: '::text || foo.sezione::text, ''::text) || COALESCE(' Foglio: '::text || foo.foglio::text, ''::text)) || COALESCE(' Mappali: '::text || foo.mappali, ''::text)) AS elenco_ct
   FROM ( SELECT DISTINCT a.pratica, b.nome AS sezione, COALESCE(a.foglio, ''::character varying) AS foglio, array_to_string(array_agg(COALESCE(a.mappale, ''::character varying)), ','::text) AS mappali
           FROM (SELECT DISTINCT pratica,sezione,foglio,mappale from pe.cterreni) a
      LEFT JOIN nct.sezioni b USING (sezione)
     GROUP BY a.pratica, b.nome, COALESCE(a.foglio, ''::character varying)) foo) G USING(pratica) LEFT JOIN
( SELECT DISTINCT pratica,coalesce(sezione,'') as sezione,coalesce(foglio,'') as foglio,coalesce(mappale,'') as mappale,coalesce(sub,'') as sub FROM pe.curbano WHERE %s) H USING(pratica) LEFT JOIN
(SELECT indirizzi.pratica, array_to_string(array_agg((COALESCE(indirizzi.via, ''::character varying)::text || COALESCE(' '::text || indirizzi.civico::text)) || COALESCE(' int.'::text || indirizzi.interno::text, ''::text)), ', '::text) AS ubicazione
   FROM pe.indirizzi
  GROUP BY indirizzi.pratica) I USING(pratica)WHERE pratica IN (%s) 
ORDER BY sezione,foglio,mappale,data_prot DESC           
EOT;
$query["scadenze"]=<<<EOT
SELECT DISTINCT A.pratica,A.numero,A.protocollo,A.data_prot,A.data_presentazione,A.oggetto,B.nome as tipo_pratica,C.descrizione as tipo_intervento,coalesce(D.nome,'non assegnata')  as responsabile,E.richiedente,F.progettista,G.scadenza,G.testo,G.uidins,G.diff,G.cod_scadenza
FROM pe.avvioproc A LEFT JOIN 
pe.e_tipopratica B ON(A.tipo=B.id) LEFT JOIN
pe.e_intervento C ON (A.intervento=C.id) LEFT JOIN
admin.users D ON(A.resp_proc=D.userid) LEFT JOIN 
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as richiedente FROM pe.soggetti WHERE richiedente=1 AND voltura=0 GROUP BY pratica) E USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as progettista FROM pe.soggetti WHERE progettista=1 AND voltura=0 GROUP BY pratica) F USING(pratica) LEFT JOIN
(SELECT a.id, a.pratica,a.completata,a.codice as cod_scadenza, a.scadenza, aaa.nome, COALESCE(b.nome, ''::character varying)::text || COALESCE(' - '::text || a.note, ''::text) AS testo, a.uidins,(a.scadenza-CURRENT_DATE) as diff
    FROM pe.scadenze a
    LEFT JOIN admin.users aaa ON aaa.userid = COALESCE(a.uidins, a.uidupd)
    LEFT JOIN pe.e_scadenze b USING (codice) WHERE (%s)
) G USING (pratica)    
WHERE completata=0 AND pratica IN (%s) 
%s %s LIMIT %s OFFSET %s     
EOT;
$query["pratiche-civico"]=<<<EOT
SELECT A.pratica,A.numero,B.nome as tipo,C.nome as categoria,coalesce(data_presentazione,data_prot) as data,oggetto,richiedente,via,civico,interno,coalesce(cartella,A.pratica::varchar) as cartella
FROM pe.avvioproc A LEFT JOIN 
pe.e_tipopratica B ON(A.tipo=B.id) LEFT JOIN
pe.e_categoriapratica C ON(A.categoria=C.id) LEFT JOIN 
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as richiedente FROM pe.soggetti WHERE richiedente=1 AND voltura=0 GROUP BY pratica) E USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as progettista FROM pe.soggetti WHERE progettista=1 AND voltura=0 GROUP BY pratica) F USING(pratica) INNER JOIN
(SELECT DISTINCT pratica, coalesce(via,'') as via, coalesce(civico,'s.c.') as civico,coalesce(interno,'') as interno FROM pe.indirizzi WHERE %s) I USING(pratica)
ORDER BY via,civico,interno,data_prot DESC               
EOT;
?>