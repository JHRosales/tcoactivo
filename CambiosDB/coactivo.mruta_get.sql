-- Function: coactivo.mruta_get(character varying, character varying, character varying, character varying, character varying, character varying, character varying, character varying, refcursor)

-- DROP FUNCTION coactivo.mruta_get(character varying, character varying, character varying, character varying, character varying, character varying, character varying, character varying, refcursor);

CREATE OR REPLACE FUNCTION coactivo.mruta_get(
    pccosto character varying DEFAULT ''::character varying,
    pfdesde character varying DEFAULT ''::character varying,
    pfhasta character varying DEFAULT ''::character varying,
    pvnroexp character varying DEFAULT ''::character varying,
    pvnrodocu character varying DEFAULT ''::character varying,
    pvnrodocuex character varying DEFAULT ''::character varying,
    popbusdefault character varying DEFAULT '%'::character varying,
    pcidusuario character varying DEFAULT '%'::character varying,
    p_ref refcursor DEFAULT NULL::refcursor)
  RETURNS refcursor AS
$BODY$
    declare nrol varchar(1);          
 BEGIN
 
pfdesde := (SELECT cast(case when pfdesde !='' THEN pfdesde else '01/01/1900' end as varchar));        
pfhasta := (SELECT cast(case when pfhasta !='' then pfhasta else '31/12/3000' end as varchar));         

  
--select nrol = ntramite from seguridad.usuario where cidusuario=pcidusuario;    
open p_ref for         
select * from(      
SELECT CASE WHEN pccosto = a.ccosini THEN 'S' ELSE CASE WHEN pccosto = a.ccosdes THEN 'E' ELSE '<>' END  END bnd                 
  , a.idsigma              
      ,a.mdocumento              
      ,a.vnrodocu              
      ,a.st_recep              
      ,a.st_recep AS st_recep2              
      ,a.ccosini              
      ,a.vcocsini              
      ,a.ccosdes              
      ,a.vcocsdes              
      ,    
   CASE WHEN pccosto = a.ccosini THEN a.vcocsdes ELSE CASE WHEN pccosto = a.ccosdes THEN     
    case when COACTIVO.fx_primerPasoxArea(a.mdocumento,a.ccosdes) =a.idsigma  and a.ctiprtram = '0000000287'     
     then COACTIVO.fx_NomUsuario(a.usuario_registro)     
     else  a.vcocsini      
    end    
    ELSE '<>' END  END             
    orgdes     
      --,convert(char(10),cast(a.dfecenv  as date),103)dfecenv          
      ,a.dfecenv    
      --,case when a.dfecrecep !='' then convert(char(10),cast(a.dfecrecep  as date),103) else a.dfecrecep end dfecrecep          
      ,a.dfecrecep     
      ,a.vobserv        
      ,a.ctipacc        
      ,a.vtipacc        
      ,a.mruta        
      ,a.stcierre  
   ,a.stconcluido  
   ,a.stconcluido as  stconcluido2  
   ,a.ctiprtram       
   ,coactivo.limpiar_cad(upper(trim(vasunto)))as vasunto   
   ,ctipasunto    
   ,vtipasunto    
   ,COACTIVO.fx_NombreCompletoPersona(a.mperson) remitente 
   ,vnrodocini 
  FROM COACTIVO.vw_mruta a              
WHERE (a.ccosini=pccosto OR a.ccosdes=pccosto)              
       
and a.vnrodocu  like '%'||pvnroexp||'%'     
--and a.ctiprtram = case when nrol ='4' then '0000000287' else a.ctiprtram end    
--and usuario_registro = case when nrol ='4' then COACTIVO.fx_usuarioCreoDocumento(a.mdocumento) else pcidusuario end    
 ) a      
where a.bnd like '%'||popbusdefault||'%'  AND (        
  cast(a.dfecenv as date) BETWEEN         
   cast(pfdesde as date) AND cast(pfhasta as date)    
 OR         
  cast(case when a.dfecrecep='' then a.dfecenv else a.dfecrecep end  as date) BETWEEN            
   cast(pfdesde as date) AND cast(pfhasta    as date)    
 )    
ORDER BY a.idsigma DESC;     

   return p_ref ; 
END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION coactivo.mruta_get(character varying, character varying, character varying, character varying, character varying, character varying, character varying, character varying, refcursor)
  OWNER TO postgres;
