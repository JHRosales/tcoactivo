-- Function: coactivo.listar_expedientes_1(character varying, character varying, character varying, character varying, character varying, character varying, character varying, character varying, character varying, character varying, refcursor)

-- DROP FUNCTION coactivo.listar_expedientes_1(character varying, character varying, character varying, character varying, character varying, character varying, character varying, character varying, character varying, character varying, refcursor);

CREATE OR REPLACE FUNCTION coactivo.listar_expedientes_1(
    p_idsigma character varying DEFAULT ''::character varying,
    p_ccosdes character varying DEFAULT ''::character varying,
    p_vestado character varying DEFAULT ''::character varying,
    p_vnrodocu character varying DEFAULT ''::character varying,
    p_mperson character varying DEFAULT ''::character varying,
    p_centrega character varying DEFAULT ''::character varying,
    p_dtini character varying DEFAULT ''::character varying,
    p_dtfin character varying DEFAULT ''::character varying,
    p_documento character varying DEFAULT ''::character varying,
    p_documentoext character varying DEFAULT ''::character varying,
    p_ref refcursor DEFAULT NULL::refcursor)
  RETURNS refcursor AS
$BODY$         
 BEGIN
          
          
 IF p_dtini = ''              
	then        
		IF p_idsigma = '' and        
		   p_ccosdes = '' and        
		   p_vestado = '' and        
		   p_vnrodocu = '' and        
		   p_mperson = '' and        
		   p_centrega = '' and        
		   p_documento = '' and
		   p_documentoext = '' then			     
			p_dtini := to_char(now()- interval '7 day', 'dd/MM/yyyy');          
			p_dtfin := to_char(now(), 'dd/MM/yyyy');             
		ELSE
			p_dtini := null;        
			p_dtfin := null;   
		end if;     
			     
END if;        
                 
  create temporary table tmpExpedientes  as      
 SELECT           
	  a.mdocumento AS mdocumento,          
	  a.estado,       
	  idsigma as mruta                      
 FROM           
	  (          
	   SELECT          
		MAX(a.idsigma) idsigma,          
		a.mdocumento,        
		coactivo.fnEstadoDocum(da.ndias, b.dfecdocu) estado      
	   FROM coactivo.mruta a              
	   INNER JOIN coactivo.mdocumento b on a.mdocumento = b.idsigma          
	   INNER JOIN coactivo.dasunto da ON b.dasunto = da.idsigma               
	   WHERE          
		a.ccosdes LIKE '%' || p_ccosdes || '%' AND          
		b.idsigma LIKE '%' || p_idsigma || '%' AND          
		b.vnrodocu LIKE '%' || p_vnrodocu || '%' AND
		b.vnrodocini LIKE '%' || p_documentoext || '%' AND
		b.mperson LIKE '%' || p_mperson || '%' AND          
		b.centrega LIKE '%' || coalesce(p_centrega, '') || '%' AND          
		cast(b.dfecdocu as date) BETWEEN cast(COALESCE(p_dtini::date, b.dfecdocu) as date) AND cast(COALESCE(p_dtfin::date, b.dfecdocu) as date)
	   GROUP BY            
		a.mdocumento,        
		coactivo.fnEstadoDocum(da.ndias, b.dfecdocu)               
	) a                  
 WHERE a.estado like '%' || p_vestado || '%';
 
         
 create index ix_tmpexpedientes_mdocumento on tmpExpedientes (mdocumento);     
  
	 create temporary table query as   
		 SELECT          
			a.idsigma,          
			b1.vdescri masunto,          
			b2.vdescri dasunto_tipasunto,          
			b3.vdescri dasunto_ccos,          
			b4.vdescri dasunto_tiptra,            
			case when a.mperson = '0000000001' then           
			coalesce(a.dentrega, 'ENTREGA')        
			else           
			trim(coalesce(c.vpatern, '')) || ' ' || trim(coalesce(c.vmatern, '')) || ' ' || trim(coalesce(c.vnombre, ''))          
			end vperson,           
			to_char(a.dfecdocu, 'dd/MM/yyyy HH24:MI'::text) as dfecdocu,           
			a.dfecdocu realdate,           
			a.nfolios,           
			coactivo.limpiar_cad(a.vasunto) vasunto,          
			substring( a.vobserv,1,9) as vobserv,          
			a.vnrodocu,           
			coalesce((select min(mruta) mruta from coactivo.mruta where mdocumento = a.idsigma ), '')  mruta,           
			coalesce(a.mdocumento,'') mdocumento,          
			coalesce(a1.vnrodocu,'') vmdocumento,           
			a.dasunto,          
			a.vnrodocini,
			(select ccosdes  from coactivo.mruta xz where xz.idsigma = x.mruta) ccocsiniultimaruta,
			(select mruta  from coactivo.mruta xz where xz.idsigma =x.mruta) mrutaultimo,
			x.mruta idsigamrutaultimo             
		 FROM coactivo.mdocumento a          
		 INNER JOIN tmpExpedientes x ON a.idsigma = x.mdocumento          
		 LEFT JOIN coactivo.dasunto b ON a.dasunto = b.idsigma          
		 LEFT JOIN coactivo.masunto b1 ON b.masunto = b1.idsigma          
		 LEFT JOIN coactivo.mconten b2 ON b.ctipasunto = b2.idsigma          
		 LEFT JOIN public.mconten b3 ON b.ctipccos = b3.idsigma         
		 LEFT JOIN coactivo.mconten b4 ON b.ctiptra = b4.idsigma         
		 INNER JOIN public.mperson c ON a.mperson = c.idsigma          
		 LEFT JOIN coactivo.mdocumento a1 ON a.mdocumento = a1.idsigma;          

    

  open p_ref for    
	SELECT *       
	FROM query                     
	ORDER BY realdate DESC;          
		
     
     return p_ref;   
       
 DROP TABLE query;          
 DROP TABLE tmpExpedientes;   
        
   
END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION coactivo.listar_expedientes_1(character varying, character varying, character varying, character varying, character varying, character varying, character varying, character varying, character varying, character varying, refcursor)
  OWNER TO postgres;
