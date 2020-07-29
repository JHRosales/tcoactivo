CREATE OR REPLACE FUNCTION coactivo.limpiar_cad(p_text character varying)
  RETURNS character varying AS
$BODY$
declare
	v_cad varchar;
begin
	v_cad := replace( p_text , chr(10), '');
	v_cad := replace( v_cad  , chr(13), '');
	v_cad := replace( v_cad  ,'<P>', '');
	v_cad := replace( v_cad  , '</P>', '');
	v_cad := replace( v_cad  , '&NBSP;', '');
	v_cad := replace( v_cad  , '&NTILDE;', '');	
	
	return v_cad;
end
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION limpiar_cad(character varying)
  OWNER TO postgres;