/************************************************************************************/
/*		IMPORTAZIONE DELLE PRATICHE EDILIZIE DI EDILIZIA PRIVATA IN PRATICAWEB 2.0	*/
/************************************************************************************/
CREATE OR REPLACE FUNCTION getSequence(tableName varchar) RETURNS varchar AS $$
DECLARE
	res varchar;
BEGIN
SELECT DISTINCT a.relname AS sequence_name INTO res
FROM
(
SELECT CASE 
WHEN UPPER(c.relname) LIKE E'%\\_ID\\_%' -- for the sequence named table_name_ID_seq
THEN SUBSTRING(c.relname from 1 for char_length(c.relname) - 7)
WHEN UPPER(c.relname) LIKE E'%\\_KRKEY\\_%' -- for sequence named table_name_krkey_seq
THEN SUBSTRING(c.relname from 1 for char_length(c.relname) - 10)
ELSE SUBSTRING(c.relname from 1 for char_length(c.relname) - 4) -- all other sequences
END AS table_name,
c.relname
FROM pg_class c WHERE c.relkind = 'S'
) a
JOIN pg_class AS rl -- checks that such table exists
ON rl.relname = a.table_name
WHERE table_name=tableName
ORDER BY 1 LIMIT 1;
return res;
END;
$$
LANGUAGE 'plpgsql';

DROP function setSequence(varchar,varchar,varchar)
CREATE OR REPLACE function setSequence(schemaName varchar,tableNam varchar,fieldName varchar) RETURNS varchar AS $$
    DECLARE
	seq varchar;
	query text; 
	maxid integer;
    BEGIN
	select into seq getSequence(tableNam);
	query := 'select coalesce(max('||fieldName||'),0)+1 from '||schemaName||'.'||tableNam||';';
	EXECUTE query INTO maxid;
	query := 'ALTER SEQUENCE '||schemaName||'.'||seq||' restart with '||maxid;
	EXECUTE query;
	return maxid::varchar;
    END;
$$
LANGUAGE 'plpgsql';

/*************************************************************/
/*                IMPORTAZIONE DEGLI ELENCHI                 */
/*************************************************************/
/* IMPORTAZIONE TIPI PRATICA */
DROP TABLE IF EXISTS e_tipopratica;
CREATE TEMP TABLE e_tipopratica AS
SELECT "CODICE" as id, "NOME" as nome, "GIORNI" as gg
  FROM import."ELENCO_TIPOPRATICA" WHERE "CODICE" NOT IN (SELECT DISTINCT id from pe.e_tipopratica);

INSERT INTO pe.e_tipopratica(id, nome) (SELECT id,nome from e_tipopratica);

DELETE FROM pe.e_tipopratica WHERE id not in (SELECT DISTINCT "CODICE" FROM import."ELENCO_TIPOPRATICA");

/*IMPORTANTE!!!!!
RICORDARSI DI SETTARE LE TIPOLOGIE DI PRATICHE E I MENUFILE
*/

/* IMPORTAZIONE ELENCO UTENTI */
UPDATE admin.users SET gisclient=0 WHERE userid >10;
DELETE FROM admin.users WHERE userid>10;
SELECT setval('admin.users_userid_seq', 10, true);
insert into admin.users(app,cognome,nominativo,gruppi,username,pwd,enc_pwd)
(SELECT 
trim(split_part("NOME",' ',1)) as app,
trim(split_part("NOME",' ',3)) as cognome,
trim(split_part("NOME",' ',2)) as nominativo,
CASE WHEN ("RESPPROC"=1) THEN '1,3,4' ELSE '3,4' END AS gruppi, 
CASE WHEN (trim(split_part("NOME",' ',3))='') THEN lower(trim(split_part("NOME",' ',2))) ELSE substr(lower(trim(split_part("NOME",' ',2))) , 1 , 1)||lower(trim(split_part("NOME",' ',3))) END as username,
CASE WHEN (trim(split_part("NOME",' ',3))='') THEN lower(trim(split_part("NOME",' ',2))) ELSE substr(lower(trim(split_part("NOME",' ',2))) , 1 , 1)||lower(trim(split_part("NOME",' ',3))) END as pwd,
CASE WHEN (trim(split_part("NOME",' ',3))='') THEN md5(lower(trim(split_part("NOME",' ',2)))) ELSE md5(substr(lower(trim(split_part("NOME",' ',2))) , 1 , 1)||lower(trim(split_part("NOME",' ',3)))) END as enc_pwd
FROM import."ELENCO_UTENTI" WHERE "ID">0 AND "ID"<>35
) 

/*IMPORTAZIONE ELENCO ENTI*/
DO $$ 
    BEGIN
	BEGIN
		ALTER TABLE pe.e_enti ADD COLUMN testo_stampa text;
	EXCEPTION	
		WHEN duplicate_column THEN RAISE NOTICE 'column testo_stampa already exists in pe.e_enti.';
	END;
    END;
$$
DELETE FROM pe.e_enti;
INSERT INTO pe.e_enti(id,nome,ordine,stampa,testo_stampa) (select distinct "ID","NOME","ORDINE","STAMPA"::int,"TESTO_STAMPA" FROM import."ELENCO_ENTI");

/*IMPORTANTE!!!!!!
RICORDARSI DI SETTARE PARERI NON PIU' VALIDI, PARERI INTERNI*/

/*IMPORTAZIONE ELENCO DOCUMENTI*/
DELETE FROM pe.e_documenti;
INSERT INTO pe.e_documenti(id,iter, nome, descrizione)
(SELECT "ID",("PROG_ITER"+1)*10, "NOME", "DESCRIZIONE"
  FROM import."ELENCO_DOCUMENTI");

/*IMPORTAZIONE ELENCO TARIFFE ONERI*/

/* IMPORTAZIONE PRATICHE (pe.avvioproc)*/

DELETE FROM pe.avvioproc;
select setSequence('pe','avvioproc','id');
DELETE FROM pe.scadenze;
select setSequence('pe','scadenze','id');
INSERT INTO pe.avvioproc(pratica, numero,data_presentazione,  protocollo, data_prot, tipo, oggetto,data_chiusura, note, anno)
(SELECT "ID","NUMERO", "DATA", "PROTOCOLLO", "DATAPROT", "TIPO", "OGGETTO", "DATACHIUSA",  coalesce("NOTE_SCADENZE"||E'\n','')|| coalesce("NOTE_PROGETTO"||E'\n','')|| coalesce("NOTE_SORVEGLIANZA"||E'\n',''),date_part('year',"DATA"::date)
  FROM import."PRATICHE"
);

INSERT INTO pe.menu(pratica,menu_file,menu_list) (SELECT pratica,'pratica',menu_default FROM pe.avvioproc inner join pe.e_tipopratica B on (tipo=B.id));
/* INSERIMENTO DELLA DESTINAZIONE D'USO (pe.progetto)*/

/*INSERIMENTO DATI DEL TITOLO (pe.titolo)*/			
delete from pe.titolo;
select setSequence('pe','titolo','id');
INSERT INTO pe.titolo(pratica,titolo,protocollo,data_rilascio,data_ritiro,intervento) (SELECT "ID","TITOLO","PROT_TITOLO","DATARILASCIO","DATARITIRO","INTERVENTO" FROM  import."PRATICHE" WHERE NOT "TITOLO" IS NULL);


/* INSERIMENTO SCADENZE DEI LAVORI (pe.lavori)*/
delete from pe.lavori;
select setSequence('pe','lavori','id');
INSERT INTO pe.lavori(pratica,scade_il,scade_fl,il,fl) (SELECT "ID","SCADE_IL","SCADE_FL","IL","FL" FROM  import."PRATICHE" WHERE (NOT "SCADE_FL" IS NULL) or (NOT "SCADE_IL" IS NULL) or (NOT "IL" IS NULL) or (NOT "FL" IS NULL));


/* INSERIMENTO PARERI (pe.pareri)*/
delete from pe.pareri;
select setSequence('pe','pareri','id');
INSERT INTO pe.pareri(
            pratica, ente, prot_rich, data_rich, prot_soll, data_soll, 
            parere, numero_doc, prot_ril, data_ril,
            prescrizioni, testo)

(SELECT "PRATICA", "ENTE", "PROT_RICH", "DATA_RICH", "PROT_SOLL", "DATA_SOLL", 
       CASE 
WHEN ("PARERE" ILIKE '%non favorevole%' or "PARERE" ILIKE '%negativo%' or "PARERE" ILIKE '%contrario%' or "PARERE" ILIKE '%diniego%') THEN 2 
WHEN ("PARERE" ILIKE '%favorevole con%' or "PARERE" ILIKE '%favorevole a%' or "PARERE" ILIKE '%prescrizioni%') THEN 3 
WHEN ("PARERE" ILIKE '%favorevole con%' or "PARERE" ILIKE '%favorevole a%' or "PARERE" ILIKE '%prescrizioni%') THEN 7 
WHEN ("PARERE" ILIKE '%integrazion%' ) THEN 4 
WHEN ("PARERE" IS NULL) THEN 6 
WHEN ("PARERE" ILIKE '%favorevole%' or "PARERE" ILIKE '%positivo%' or "PARERE" ILIKE '%autorizzazione%') THEN 1 
ELSE NULL END as parere ,
 "NUMERO", "PROT_RIL", "DATA_RIL",  "PRESCRIZIONI","PARERE"
  FROM import."PARERI" WHERE "PRATICA" IN (SELECT DISTINCT pratica from pe.avvioproc));



 /* INSERIMENTO DEGLI INDIRIZZI DELLA PRATICA (pe.indirizzi) */
delete from pe.curbano; 
delete from pe.indirizzi;
delete from pe.cterreni;
select setSequence('pe','curbano','id');
select setSequence('pe','cterreni','id');
select setSequence('pe','indirizzi','id');

 INSERT INTO pe.indirizzi(pratica, via, civico, interno, id_via, id_civico)
(SELECT "PRATICA", "VIA", "CIVICO", "INTERNO", "ID_VIA", "ID_CIVICO" FROM import."PRATICHE_INDIRIZZI" WHERE "PRATICA" IN (SELECT DISTINCT pratica from pe.avvioproc)); 

/* INSERIMENTO DEI DATI CATASTALI (pe.cterreni pe.curbano) */

INSERT INTO pe.cterreni(pratica, sezione, foglio, mappale, sub)
(SELECT "PRATICA", "SEZIONE", "FOGLIO", "NUMERO", "SUBALTERNO" FROM import."PRATICHE_TERRENI"  WHERE "PRATICA" IN (SELECT DISTINCT pratica from pe.avvioproc));

           
INSERT INTO pe.curbano(pratica, sezione, foglio, mappale, sub)
(SELECT "PRATICA", "SEZIONE", "FOGLIO", "NUMERO", "SUBALTERNO" FROM import."PRATICHE_URBANO"  WHERE "PRATICA" IN (SELECT DISTINCT pratica from pe.avvioproc)); 

 /* INSERIMENTO DEGLI ASSERVIMENTI DELLA PRATICA (pe.asservimenti pe.asservimenti_map) */



DELETE FROM pe.asservimenti;
select setSequence('pe','asservimenti','id');
 INSERT INTO pe.asservimenti(
            pratica, tipo, notaio, repertorio, loc_reg, loc_tras, data_reg, 
            data_tras, reg_part, reg_ord,  note,  numero, sup_particelle, sup_asservita,loc)
(
SELECT B."PRATICA", "TIPO", "NOTAIO", "REPERTORIO", "LOC_REG", "LOC_TRAS", 
       "DATA_REG", "DATA_TRAS", "REG_PART", "REG_ORD", "NOTE", "NUMERO","TOTSUP", 
       "SUPASS", "LOC"
  FROM import."ASSERVIMENTI" A INNER JOIN import."ASSERVIMENTI_PRATICHE" B ON("ASSERVIMENTO"="NUMERO")   WHERE "PRATICA" IN (SELECT DISTINCT pratica from pe.avvioproc)
); 

INSERT INTO pe.asservimenti_map(pratica, asservimento, sezione, foglio, mappale, supass)
(SELECT B."PRATICA",A."ASSERVIMENTO", "SEZIONE", "FOGLIO", "NUMERO",  "SUPASS"
  FROM import."MAPPALI_ASSERVITI"A INNER JOIN import."ASSERVIMENTI_PRATICHE" B USING("ASSERVIMENTO")   WHERE "PRATICA" IN (SELECT DISTINCT pratica from pe.avvioproc)
) ;

