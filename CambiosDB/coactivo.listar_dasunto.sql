-- Function: coactivo.listar_dasunto(character varying, character varying, character varying, character varying, refcursor)

-- DROP FUNCTION coactivo.listar_dasunto(character varying, character varying, character varying, character varying, refcursor);

CREATE OR REPLACE FUNCTION coactivo.listar_dasunto(p_masunto_estado character varying DEFAULT ''::character varying, p_idsigma character varying DEFAULT ''::character varying, p_masunto character varying DEFAULT ''::character varying, p_ctiptra character varying DEFAULT '%'::character varying, p_ref refcursor DEFAULT NULL::refcursor)
  RETURNS refcursor AS
$BODY$         
 BEGIN
 open p_ref for
      select
        a.idsigma
        , a.masunto
        , b.vdescri as masunto_descri
        , a.ctipasunto
        , a.ndias
        , a.ctipccos
        , a.ctiptra
        , a.nestado
        , a.vhostnm
        , a.vusernm
        , a.ddatetm
        , b.nestado as masunto_estado
    from coactivo.dasunto a
    inner join coactivo.masunto b on a.masunto = b.idsigma and b.nestado  like '%' || p_masunto_estado || '%'
    where a.idsigma like p_idsigma
    and a.masunto like p_masunto
    and a.ctiptra like p_ctiptra
    order by idsigma;
 

   return p_ref;  
END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION coactivo.listar_dasunto(character varying, character varying, character varying, character varying, refcursor)
  OWNER TO postgres;
