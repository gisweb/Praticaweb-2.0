<?php
$query=Array();
$query["default"]=<<<EOT
SELECT
    A.pratica,coalesce(coalesce(data_prot,data_presentazione),'01/01/1970'::date) as data_ordinamento,A.numero,A.protocollo,A.data_prot,A.data_presentazione,A.oggetto,A.online,
    B.nome as tipo_pratica,C.descrizione as tipo_intervento,coalesce(D.nome,'non assegnata') as responsabile,
    E.richiedente,F.progettista,L.esecutore,G.elenco_ct,H.elenco_cu,I.ubicazione,
    CASE WHEN (coalesce(A.resp_it,coalesce(A.resp_ia,0)) = 0) THEN 0 ELSE 1 END as assegnata_istruttore
    ,coalesce(O.nome,'non assegnata') as responsabile_it,M.titolo,M.data_rilascio,A.sportello,Q.opzione as vincolo_paes
    ,coalesce(R.nome,'non assegnata') as responsabile_ia
FROM pe.avvioproc A LEFT JOIN 
pe.e_tipopratica B ON(A.tipo=B.id) LEFT JOIN
pe.e_intervento C ON (A.intervento=C.id) LEFT JOIN
admin.users D ON(A.resp_proc=D.userid) LEFT JOIN 
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as richiedente FROM pe.soggetti WHERE richiedente=1 AND coalesce(voltura,0)=0 GROUP BY pratica) E USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as progettista FROM pe.soggetti WHERE progettista=1 AND coalesce(voltura,0)=0 GROUP BY pratica) F USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as esecutore FROM pe.soggetti WHERE esecutore=1 AND coalesce(voltura,0)=0 GROUP BY pratica) L USING(pratica) LEFT JOIN
(SELECT * FROM pe.grp_particelle_ct) G USING(pratica) LEFT JOIN
(SELECT * FROM pe.grp_particelle_cu) H USING(pratica) LEFT JOIN
(SELECT indirizzi.pratica, array_to_string(array_agg((COALESCE(indirizzi.via, ''::character varying)::text || COALESCE(' '::text || indirizzi.civico::text,'')) || COALESCE(' int.'::text || indirizzi.interno::text, ''::text)), ', '::text) AS ubicazione
   FROM pe.indirizzi
  GROUP BY indirizzi.pratica) I USING(pratica) LEFT JOIN
(SELECT pratica,titolo,data_rilascio FROM pe.titolo) M USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(cip::varchar),',')) as cip FROM pe.soggetti WHERE esecutore=1 AND coalesce(voltura,0)=0 GROUP BY pratica) N USING(pratica) LEFT JOIN
(SELECT pratica,il,fl,protocollo_il,protocollo_fl FROM pe.lavori) P USING(pratica)
LEFT JOIN admin.users O ON(A.resp_it=O.userid)
--LEFT JOIN (SELECT id,pratica,tipo as tipo_verifica,data_avvio as data_avvio_verifica,esito FROM pe.verifiche AP INNER JOIN pe.e_verifiche BP ON(AP.tipo = BP.id)) P USING(pratica) 
LEFT JOIN admin.users R ON(A.resp_ia=R.userid)
LEFT JOIN pe.elenco_opzione_ap Q ON (vincolo_paes=Q.id)
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
$query["search-ce"]=<<<EOT
SELECT A.pratica,A.data_convocazione,sede1,B.nome as tipo_commissione FROM 
ce.commissione A INNER JOIN pe.e_enti B ON(A.tipo_comm=B.id)
WHERE pratica IN(%s)
%s %s LIMIT %s OFFSET %s  
EOT;
$query["search-cdu"]=<<<EOT

EOT;
$query["search-vigi"]=<<<EOT
SELECT DISTINCT 
    A.pratica,A.numero,A.protocollo,A.data_prot,A.data_presentazione,A.oggetto,
    B.nome as tipo_pratica,C.descrizione as tipo_intervento,coalesce(D.nome,'non assegnata') as responsabile,
    E.richiedente,F.progettista,L.esecutore,G.elenco_ct,H.elenco_cu,I.ubicazione,
    CASE WHEN (coalesce(A.resp_it,coalesce(A.resp_ia,0)) = 0) THEN 0 ELSE 1 END as assegnata_istruttore
    ,coalesce(O.nome,'non assegnata') as responsabile_it
    --,coalesce(P.nome,'non assegnata') as responsabile_ia
FROM vigi.avvioproc A LEFT JOIN 
vigi.e_tipopratica B ON(A.tipo=B.id) LEFT JOIN
vigi.e_intervento C ON (A.intervento=C.id) LEFT JOIN
admin.users D ON(A.resp_proc=D.userid) LEFT JOIN 
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as richiedente FROM vigi.soggetti WHERE richiedente=1 AND coalesce(voltura,0)=0 GROUP BY pratica) E USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as progettista FROM vigi.soggetti WHERE progettista=1 AND coalesce(voltura,0)=0 GROUP BY pratica) F USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as esecutore FROM vigi.soggetti WHERE esecutore=1 AND coalesce(voltura,0)=0 GROUP BY pratica) L USING(pratica) LEFT JOIN
(SELECT * FROM vigi.grp_particelle_ct) G USING(pratica) LEFT JOIN
(SELECT * FROM vigi.grp_particelle_cu) H USING(pratica) LEFT JOIN
(SELECT indirizzi.pratica, array_to_string(array_agg((COALESCE(indirizzi.via, ''::character varying)::text || COALESCE(' '::text || indirizzi.civico::text,'')) || COALESCE(' int.'::text || indirizzi.interno::text, ''::text)), ', '::text) AS ubicazione
   FROM vigi.indirizzi
  GROUP BY indirizzi.pratica) I USING(pratica) LEFT JOIN

(SELECT pratica,trim(array_to_string(array_agg(cip::varchar),',')) as cip FROM vigi.soggetti WHERE esecutore=1 AND coalesce(voltura,0)=0 GROUP BY pratica) N USING(pratica) 
LEFT JOIN admin.users O ON(A.resp_it=O.userid) 
--LEFT JOIN admin.users P ON(A.resp_ia=D.userid)
WHERE pratica IN (%s)
%s %s LIMIT %s OFFSET %s                 
EOT;
$query["search-agi"]=<<<EOT
SELECT DISTINCT 
    A.pratica,A.numero,A.protocollo,A.data_prot,A.data_presentazione,A.oggetto,
    B.nome as tipo_pratica,C.descrizione as tipo_intervento,coalesce(D.nome,'non assegnata') as responsabile,
    E.richiedente,F.progettista,L.esecutore,G.elenco_ct,H.elenco_cu,I.ubicazione,
    CASE WHEN (coalesce(A.resp_it,coalesce(A.resp_ia,0)) = 0) THEN 0 ELSE 1 END as assegnata_istruttore
    ,coalesce(O.nome,'non assegnata') as responsabile_it
    --,coalesce(P.nome,'non assegnata') as responsabile_ia
FROM agi.avvioproc A LEFT JOIN 
agi.e_tipopratica B ON(A.tipo=B.id) LEFT JOIN
agi.e_intervento C ON (A.intervento=C.id) LEFT JOIN
admin.users D ON(A.resp_proc=D.userid) LEFT JOIN 
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as richiedente FROM agi.soggetti WHERE richiedente=1 AND coalesce(voltura,0)=0 GROUP BY pratica) E USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as progettista FROM agi.soggetti WHERE progettista=1 AND coalesce(voltura,0)=0 GROUP BY pratica) F USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as esecutore FROM agi.soggetti WHERE esecutore=1 AND coalesce(voltura,0)=0 GROUP BY pratica) L USING(pratica) LEFT JOIN
(SELECT * FROM agi.grp_particelle_ct) G USING(pratica) LEFT JOIN
(SELECT * FROM agi.grp_particelle_cu) H USING(pratica) LEFT JOIN
(SELECT indirizzi.pratica, array_to_string(array_agg((COALESCE(indirizzi.via, ''::character varying)::text || COALESCE(' '::text || indirizzi.civico::text,'')) || COALESCE(' int.'::text || indirizzi.interno::text, ''::text)), ', '::text) AS ubicazione
   FROM agi.indirizzi
  GROUP BY indirizzi.pratica) I USING(pratica) LEFT JOIN
(SELECT pratica,titolo,data_rilascio FROM agi.titolo) M USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(cip::varchar),',')) as cip FROM agi.soggetti WHERE esecutore=1 AND coalesce(voltura,0)=0 GROUP BY pratica) N USING(pratica) 
LEFT JOIN admin.users O ON(A.resp_it=O.userid) 
--LEFT JOIN admin.users P ON(A.resp_ia=D.userid)
WHERE pratica IN (%s)
%s %s LIMIT %s OFFSET %s                 
EOT;

$query["storage"]=<<<EOT
SELECT DISTINCT
A.pratica,A.data_invio,A.numero,A.protocollo,A.data_protocollo, coalesce(cognome,'')||' '||coalesce(nome,'') || coalesce(' - '||ragsoc,'') as soggetto,A.codfis, A.piva,A.oggetto
FROM storage.invio A LEFT JOIN
storage.documentazione_inviata B USING(pratica) LEFT JOIN
storage.associazioni C USING (pratica)
WHERE pratica IN (%s)
%s %s LIMIT %s OFFSET %s  
EOT;


$query["search-online-original"]=<<<EOT
WITH istanze_online AS (
select id,pratica,prot_integ::varchar as protocollo,data_integ as data_protocollo,'Integrazione'::varchar as tipo,2 as ordine from pe.integrazioni where online=1
UNION ALL
select id,pratica,protocollo_il::varchar as protocollo,data_prot_il as data_protocollo,'Inizio Lavori'::varchar as tipo,3 as ordine from pe.lavori where il_online=1
UNION ALL
select id,pratica,protocollo_fl::varchar as protocollo,data_prot_fl as data_protocollo,'Fine Lavori'::varchar as tipo,4 as ordine from pe.lavori where fl_online=1
UNION ALL
SELECT id,pratica,protocollo::varchar,data_prot as data_protocollo,'Istanza'::varchar as tipo,1 as ordine from pe.avvioproc WHERE online=1)
SELECT
    XX.tipo as tipo_istanza,A.pratica,XX.data_protocollo as data_ordinamento,A.numero,XX.protocollo,XX.data_protocollo as data_prot,A.data_presentazione,A.oggetto,1 as online,
    B.nome as tipo_pratica,C.descrizione as tipo_intervento,coalesce(D.nome,'non assegnata') as responsabile,
    E.richiedente,F.progettista,L.esecutore,G.elenco_ct,H.elenco_cu,I.ubicazione,
    CASE WHEN (coalesce(A.resp_it,coalesce(A.resp_ia,0)) = 0) THEN 0 ELSE 1 END as assegnata_istruttore
    ,coalesce(O.nome,'non assegnata') as responsabile_it,M.titolo,M.data_rilascio,A.sportello,Q.opzione as vincolo_paes
    ,coalesce(R.nome,'non assegnata') as responsabile_ia
FROM 
istanze_online XX INNER JOIN 
pe.avvioproc A USING (pratica) LEFT JOIN 
pe.e_tipopratica B ON(A.tipo=B.id) LEFT JOIN
pe.e_intervento C ON (A.intervento=C.id) LEFT JOIN
admin.users D ON(A.resp_proc=D.userid) LEFT JOIN 
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as richiedente FROM pe.soggetti WHERE richiedente=1 AND coalesce(voltura,0)=0 GROUP BY pratica) E USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as progettista FROM pe.soggetti WHERE progettista=1 AND coalesce(voltura,0)=0 GROUP BY pratica) F USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as esecutore FROM pe.soggetti WHERE esecutore=1 AND coalesce(voltura,0)=0 GROUP BY pratica) L USING(pratica) LEFT JOIN
(SELECT * FROM pe.grp_particelle_ct) G USING(pratica) LEFT JOIN
(SELECT * FROM pe.grp_particelle_cu) H USING(pratica) LEFT JOIN
(SELECT indirizzi.pratica, array_to_string(array_agg((COALESCE(indirizzi.via, ''::character varying)::text || COALESCE(' '::text || indirizzi.civico::text,'')) || COALESCE(' int.'::text || indirizzi.interno::text, ''::text)), ', '::text) AS ubicazione
   FROM pe.indirizzi
  GROUP BY indirizzi.pratica) I USING(pratica) LEFT JOIN
(SELECT pratica,titolo,data_rilascio FROM pe.titolo) M USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(cip::varchar),',')) as cip FROM pe.soggetti WHERE esecutore=1 AND coalesce(voltura,0)=0 GROUP BY pratica) N USING(pratica) LEFT JOIN
(SELECT pratica,il,fl,protocollo_il,protocollo_fl FROM pe.lavori) P USING(pratica)
LEFT JOIN admin.users O ON(A.resp_it=O.userid)
--LEFT JOIN (SELECT id,pratica,tipo as tipo_verifica,data_avvio as data_avvio_verifica,esito FROM pe.verifiche AP INNER JOIN pe.e_verifiche BP ON(AP.tipo = BP.id)) P USING(pratica) 
LEFT JOIN admin.users R ON(A.resp_ia=R.userid)
LEFT JOIN pe.elenco_opzione_ap Q ON (vincolo_paes=Q.id)
%s %s LIMIT %s OFFSET %s     
EOT;

$query["search-online"]=<<<EOT
WITH istanze_online AS (
select id,pratica,prot_integ::varchar as protocollo,data_integ as data_protocollo,'Integrazione'::varchar as tipo,2 as ordine from pe.integrazioni where online=1
UNION ALL
select id,pratica,protocollo,data_protocollo,'Inizio Lavori'::varchar as tipo,3 as ordine from pe.comunicazioni_lavori where online=1 and tipo_comunicazione=1
UNION ALL
select id,pratica,protocollo,data_protocollo,'Fine Lavori'::varchar as tipo,4 as ordine from pe.comunicazioni_lavori where online=1 and tipo_comunicazione=2
UNION ALL
SELECT id,pratica,protocollo::varchar,data_prot as data_protocollo,'Istanza'::varchar as tipo,1 as ordine from pe.avvioproc WHERE online=1)
SELECT
    XX.tipo as tipo_istanza,A.pratica,XX.data_protocollo as data_ordinamento,A.numero,XX.protocollo,XX.data_protocollo as data_prot,A.data_presentazione,A.oggetto,1 as online,
    B.nome as tipo_pratica,C.descrizione as tipo_intervento,coalesce(D.nome,'non assegnata') as responsabile,
    E.richiedente,F.progettista,L.esecutore,G.elenco_ct,H.elenco_cu,I.ubicazione,
    CASE WHEN (coalesce(A.resp_it,coalesce(A.resp_ia,0)) = 0) THEN 0 ELSE 1 END as assegnata_istruttore
    ,coalesce(O.nome,'non assegnata') as responsabile_it,M.titolo,M.data_rilascio,A.sportello,Q.opzione as vincolo_paes
    ,coalesce(R.nome,'non assegnata') as responsabile_ia
FROM 
istanze_online XX INNER JOIN 
pe.avvioproc A USING (pratica) LEFT JOIN 
pe.e_tipopratica B ON(A.tipo=B.id) LEFT JOIN
pe.e_intervento C ON (A.intervento=C.id) LEFT JOIN
admin.users D ON(A.resp_proc=D.userid) LEFT JOIN 
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as richiedente FROM pe.soggetti WHERE richiedente=1 AND coalesce(voltura,0)=0 GROUP BY pratica) E USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as progettista FROM pe.soggetti WHERE progettista=1 AND coalesce(voltura,0)=0 GROUP BY pratica) F USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(coalesce(app||' ','')||coalesce(' '||nome,'')||coalesce(' '||cognome)||coalesce(' - '||ragsoc,'')),',')) as esecutore FROM pe.soggetti WHERE esecutore=1 AND coalesce(voltura,0)=0 GROUP BY pratica) L USING(pratica) LEFT JOIN
(SELECT * FROM pe.grp_particelle_ct) G USING(pratica) LEFT JOIN
(SELECT * FROM pe.grp_particelle_cu) H USING(pratica) LEFT JOIN
(SELECT indirizzi.pratica, array_to_string(array_agg((COALESCE(indirizzi.via, ''::character varying)::text || COALESCE(' '::text || indirizzi.civico::text,'')) || COALESCE(' int.'::text || indirizzi.interno::text, ''::text)), ', '::text) AS ubicazione
   FROM pe.indirizzi
  GROUP BY indirizzi.pratica) I USING(pratica) LEFT JOIN
(SELECT pratica,titolo,data_rilascio FROM pe.titolo) M USING(pratica) LEFT JOIN
(SELECT pratica,trim(array_to_string(array_agg(cip::varchar),',')) as cip FROM pe.soggetti WHERE esecutore=1 AND coalesce(voltura,0)=0 GROUP BY pratica) N USING(pratica) LEFT JOIN
(SELECT pratica,il,fl,protocollo_il,protocollo_fl FROM pe.lavori) P USING(pratica)
LEFT JOIN admin.users O ON(A.resp_it=O.userid)
LEFT JOIN admin.users R ON(A.resp_ia=R.userid)
LEFT JOIN pe.elenco_opzione_ap Q ON (vincolo_paes=Q.id)
%s %s LIMIT %s OFFSET %s     
EOT;
?>
