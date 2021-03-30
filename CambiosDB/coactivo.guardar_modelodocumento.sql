<<<<<<< HEAD
﻿-- Function: coactivo.guardar_dcostasprocesales(character varying, character varying, character varying, character varying, character varying, character varying, refcursor)

-- DROP FUNCTION coactivo.guardar_dcostasprocesales(character varying, character varying, character varying, character varying, character varying, character varying, refcursor);

CREATE OR REPLACE FUNCTION coactivo.guardar_modelodocumento(
    p_mdocumento character varying,
    p_cnrodocumento character varying,
    p_casunto character varying,
    p_cobservaciones character varying,
    p_descripcion character varying,
    p_mensageobs character varying,
    p_ref refcursor)
=======
﻿-- Function: coactivo.guardar_modelodocumento(character varying, character varying, character varying, character varying, character varying, character varying, refcursor)

-- DROP FUNCTION coactivo.guardar_modelodocumento(character varying, character varying, character varying, character varying, character varying, character varying, refcursor);

CREATE OR REPLACE FUNCTION coactivo.guardar_modelodocumento(p_mdocumento character varying, p_cnrodocumento character varying, p_casunto character varying, p_notas character varying, p_descripcion character varying, p_mensageobs character varying, p_ref refcursor)
>>>>>>> 7f07bc9159562078652863367456184b07fbd494
  RETURNS refcursor AS
$BODY$
 DECLARE p_id integer;   
BEGIN
if exists(select * from coactivo.modelodocumento where mdocumento=p_mdocumento) then
	UPDATE coactivo.modelodocumento
	   SET ffecdocumento=now(), 
	    cnrodocumento=p_cnrodocumento, casunto=p_casunto, 
<<<<<<< HEAD
	       cobservaciones=p_cobservaciones, descripcion=p_descripcion, mensageobs=p_mensageobs, estado='1'
=======
	       notas=p_notas, descripcion=p_descripcion, mensageobs=p_mensageobs, estado='1'
>>>>>>> 7f07bc9159562078652863367456184b07fbd494
	 WHERE mdocumento=p_mdocumento;

else
	 p_id:=(select coalesce(max(id),0)+1 from coactivo.modelodocumento);
	INSERT INTO coactivo.modelodocumento(
<<<<<<< HEAD
		    id, ffecdocumento, mdocumento, cnrodocumento, casunto, cobservaciones, 
		    descripcion, mensageobs, estado, fecharegistro)
	    VALUES ( p_id, now(), p_mdocumento,p_cnrodocumento, p_casunto, p_cobservaciones, 
=======
		    id, ffecdocumento, mdocumento, cnrodocumento, casunto, notas, 
		    descripcion, mensageobs, estado, fecharegistro)
	    VALUES ( p_id, now(), p_mdocumento,p_cnrodocumento, p_casunto, p_notas, 
>>>>>>> 7f07bc9159562078652863367456184b07fbd494
		    p_descripcion, p_mensageobs, '1', now());
end if;
          
open p_ref for
SELECT '1' a ,'Envio Correcto' b;
   return p_ref ; 
END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION coactivo.guardar_modelodocumento(character varying, character varying, character varying, character varying, character varying, character varying, refcursor)
  OWNER TO postgres;
<<<<<<< HEAD

=======
>>>>>>> 7f07bc9159562078652863367456184b07fbd494
