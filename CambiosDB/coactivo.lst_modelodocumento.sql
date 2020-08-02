-- Function: coactivo.lst_dcostasprocesales(character varying, refcursor)

-- DROP FUNCTION coactivo.lst_dcostasprocesales(character varying, refcursor);

CREATE OR REPLACE FUNCTION coactivo.lst_modelodocumento(p_idsigma character varying DEFAULT ''::character varying, p_ref refcursor DEFAULT NULL::refcursor)
  RETURNS refcursor AS
$BODY$DECLARE

BEGIN

if p_idsigma = ''  then 
	p_idsigma := '0';
end if;
		OPEN p_ref  FOR
			select 	a.id	,b.idsigma as mdocumento,a.cnrodocumento,a.casunto,a.cobservaciones, a.descripcion, a.mensageobs, b.vdescri			
			from coactivo.mconten b 
			left join coactivo.modelodocumento a on a.mdocumento=b.idsigma			
			where b.idsigma	= p_idsigma;
     return p_ref;  
END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION coactivo.lst_modelodocumento(character varying, refcursor)
  OWNER TO postgres;
