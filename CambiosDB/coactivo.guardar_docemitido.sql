-- Function: coactivo.guardar_docemitido(character varying, character varying, character varying, character varying, character varying, character varying, character varying, refcursor)

-- DROP FUNCTION coactivo.guardar_docemitido(character varying, character varying, character varying, character varying, character varying, character varying, character varying, refcursor);

CREATE OR REPLACE FUNCTION coactivo.guardar_docemitido(p_idsigma character varying DEFAULT ''::character varying, p_tipodoc character varying DEFAULT ''::character varying, p_nrodoc character varying DEFAULT ''::character varying, p_vhost character varying DEFAULT ''::character varying, p_vuser character varying DEFAULT ''::character varying, p_estado character varying DEFAULT ''::character varying, p_mdocumento character varying DEFAULT ''::character varying, p_ref refcursor DEFAULT NULL::refcursor)
  RETURNS refcursor AS
$BODY$  
DECLARE 
	codcontrib character(10);
 BEGIN
	 if exists(select idsigma from coactivo.doc_emitidos where idsigma = p_idsigma)  
	 THEN 
		open p_ref for	
		SELECT '0' as a, 'No se Pudo Actualizar El Documento'  as b;
	 ELSE
		IF NOT EXISTS(select idsigma from coactivo.doc_emitidos where tipodoc = p_tipodoc)  
		THEN
			select   substring('0000000000' || cast(cast(coalesce(max(idsigma),'0') 
			as integer)+1 as varchar) from '..........$') into p_idsigma
			from coactivo.doc_emitidos; 

			insert into coactivo.doc_emitidos
			(idsigma, tipodoc,nrodoc,fecha_creacion,vhostnm,vusernm,ddatetm,estado,mdocumento)
			values
			(p_idsigma, p_tipodoc, p_nrodoc,now(), p_vhost, p_vuser,now(),p_estado, p_mdocumento);

				IF exists (select * from coactivo.mconten where cidtabl='0000000008' and idsigma=p_tipodoc) --Si es alguna REC
				THEN
					select mperson into codcontrib from coactivo.mdocumento where idsigma=p_mdocumento;
					insert into coactivo.doc_emitidodet
					--(iddocemitido,concepto,cperanio,cperiod,insoluto,reajuste,interes,emision,estado,vhostnm,vusernm,ddatetm)
					select p_idsigma,ctiprec,cperanio,cperiod,imp_insol,imp_reaj,imp_mora,costo_emis,nestado,p_vhost, p_vuser,now() from tesoreria.mestcta
					where cidpers=codcontrib and nestado in ('0','F','J','K','P','N','R','B','D');

					update tesoreria.mestcta set nestado='D'  where cidpers=codcontrib and nestado in ('0','F','J','K','P','N','R','B','D');
				END IF;

			open p_ref for	
			SELECT '1' as a, 'Registrado Correctamente'  as b,* from coactivo.doc_emitidos where idsigma=p_idsigma;
		ELSE
			open p_ref for	
			SELECT '0' as a, 'No se Puede Crear el Mismo Tipo de Documento'  as b;
		
		END IF;
	end if;	


return p_ref;
        
END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION coactivo.guardar_docemitido(character varying, character varying, character varying, character varying, character varying, character varying, character varying, refcursor)
  OWNER TO postgres;
