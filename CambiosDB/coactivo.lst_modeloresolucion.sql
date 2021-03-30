-- Function: coactivo.lst_modeloresolucion(character varying, refcursor)

-- DROP FUNCTION coactivo.lst_modeloresolucion(character varying, refcursor);

CREATE OR REPLACE FUNCTION coactivo.lst_modeloresolucion(p_idsigma character varying DEFAULT ''::character varying, p_ref refcursor DEFAULT NULL::refcursor)
  RETURNS refcursor AS
$BODY$DECLARE

BEGIN

if p_idsigma = '0000000000'  then 
	p_idsigma := '';
end if;

		OPEN p_ref  FOR
			select a.idsigma,a.vdescri,a.nestado ,case when a.nestado='0' then 'Inactivo' else 'Activo' end as destado,  
			case when coalesce((select count(*) from coactivo.modelodocumento where mdocumento=a.idsigma),0)>0 then 'SI' else 'NO' end formato
			from  coactivo.mconten a
			where cidtabl in('0000000008' ,'0000000012','0000015192')
			and idsigma not in('0000000008' ,'0000000012')		
			and a.idsigma like '%'|| p_idsigma ||'%'			
			order by a.idsigma;

			

     return p_ref;  
END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION coactivo.lst_modeloresolucion(character varying, refcursor)
  OWNER TO postgres;
