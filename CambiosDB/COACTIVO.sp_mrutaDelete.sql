CREATE OR REPLACE FUNCTION COACTIVO.sp_mrutaDelete(
pmruta character varying,
puserlogin  character varying,
p_ref refcursor DEFAULT NULL::refcursor)
  RETURNS refcursor AS
$BODY$ 
DECLARE
	p_id integer;        
 BEGIN
	select  coalesce(max(id), 0) + 1 into p_id from coactivo.mruta_audit;

	INSERT INTO coactivo.mruta_audit
           (id ,idsigma ,mdocumento ,ccosini  ,ccosdes ,dfecenv ,dfecrecep ,vobserv ,ctipacc,mruta,vhostnm ,vusernm ,ddatetm ,stcierre ,usertDelete ,ddateDelete)
	SELECT p_id,idsigma,mdocumento,ccosini ,ccosdes ,dfecenv ,dfecrecep,vobserv ,ctipacc ,mruta  ,vhostnm ,vusernm ,ddatetm  ,stcierre ,puserlogin  ,now()  
	FROM coactivo.mruta where idsigma=pmruta;

	delete from coactivo.mruta where idsigma=pmruta;
  
open p_ref for
SELECT '1' st ,'Transaccion correcta.' msj , pmruta AS  idsigma;

   return p_ref;  
END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION COACTIVO.sp_mrutaDelete(character varying, character varying,  refcursor)
  OWNER TO postgres;