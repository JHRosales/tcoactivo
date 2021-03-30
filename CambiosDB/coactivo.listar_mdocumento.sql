-- Function: coactivo.listar_mdocumento(character varying, character varying, character varying, character varying, character varying, character varying, refcursor)

-- DROP FUNCTION coactivo.listar_mdocumento(character varying, character varying, character varying, character varying, character varying, character varying, refcursor);

CREATE OR REPLACE FUNCTION coactivo.listar_mdocumento(p_idsigma character varying DEFAULT ''::character varying, p_ccosdes character varying DEFAULT ''::character varying, p_vestado character varying DEFAULT ''::character varying, p_vnrodocu character varying DEFAULT ''::character varying, p_mperson character varying DEFAULT ''::character varying, p_centrega character varying DEFAULT ''::character varying, p_ref refcursor DEFAULT NULL::refcursor)
  RETURNS refcursor AS
$BODY$         
 BEGIN
 DROP TABLE if exists  tmpExpedientes;
 create temporary table tmpExpedientes as
	SELECT d.idsigma mdocumento
	--INTO #tmpExpedientes
	FROM COACTIVO.mruta a  
	INNER JOIN COACTIVO.mdocumento d on a.mdocumento = d.idsigma  
	INNER JOIN COACTIVO.dasunto f ON d.dasunto = f.idsigma
	WHERE a.idsigma IN 
		(
			SELECT idsigma FROM 
				(
					SELECT MAX(a.idsigma) idsigma,a.mdocumento   
					FROM COACTIVO.mruta a  
					GROUP BY  a.mdocumento
				) tb
		) AND 
	a.ccosdes LIKE '%' || p_ccosdes || '%'AND
	(
		case when (extract(days from (now() -  d.dfecdocu)) * 100/f.ndias) < 75 then
			'PENDIENTES'
		when (extract(days from (now() -  d.dfecdocu)) * 100/f.ndias) < 100 then
			'OBSERVADOS'
		when (extract(days from (now() -  d.dfecdocu)) * 100/f.ndias) >= 100 then
			'VENCIDOS'
		end
	) LIKE  '%' || p_vestado || '%' AND
	d.idsigma like '%' || p_idsigma || '%' AND
	d.vnrodocu like '%' || p_vnrodocu || '%' AND
	d.mperson like '%' || p_mperson || '%' AND
	coalesce(d.centrega,'') LIKE '%' || p_centrega || '%';

open p_ref for
	select
		a.idsigma, 
		a.dasunto, 
		b1.vdescri masunto, 
		b2.vdescri dasunto_tipasunto,  
		a.mperson, 
			replace(replace(trim(c.cdenomi) || '-' || trim(c.vdirecc)	|| 
			(case when trim(c.vnumero)='' then '' else ' NRO. ' || trim(c.vnumero) end) || 
			(case when trim(c.vdpto)='' then '' else ' DPT. ' || trim(c.vdpto) end) || 
			(case when trim(c.vmanzan)='' then '' else ' MZA. ' || trim(c.vmanzan) end) || 
			(case when trim(c.vlote)='' then '' else ' LTE. ' || trim(c.vlote) end), '"', ''), ',', '') direccf, 
		trim(c.vpatern) || ' ' || trim(c.vmatern) || ' ' || trim(c.vnombre) vperson, 
		c.vdirecc, 
		to_char(a.dfecdocu,'dd/MM/yyyy') as dfecdocu, 
		a.nfolios, 
		a.ctiprtram, 
		d.vdescri as dtiprtram, 
		a.vasunto, 
		a.ccosini, 
		a.vobserv,
		a.vnrodocu, 
		(select min(idsigma) idsigma from coactivo.mruta where mdocumento = a.idsigma ) mruta,
		-- Asunto det
		b.ndias,
		extract(days from (now() -  a.dfecdocu)) ndias_transcurridos, -- * 100/b.ndias, 
		b.ndias - (extract(days from (now() -  a.dfecdocu)) ) ndias_restantes, --* 100/b.ndias)
			case when (extract(days from (now() -  a.dfecdocu)) * 100/b.ndias) < 75 then
				'00ff00.png'
			when (extract(days from (now() -  a.dfecdocu)) * 100/b.ndias) < 100 then
				'ffff00.png'
			when (extract(days from (now() -  a.dfecdocu)) * 100/b.ndias) >= 100 then
				'ff0000.png'
			end vindicador, 
		'' vaccion, 
		trim(a.crepres) crepres, 
		trim(c1.vpatern) || ' ' || trim(c1.vmatern) || ' ' || trim(c1.vnombre) vrepres, 
		c1.vnrodoc vdocrep, 
		trim(a.centrega) centrega,
			case when a.flagdentrega = '1' then 
				a.dentrega
			else
				trim(c2.vpatern) || ' ' || trim(c2.vmatern) || ' ' || trim(c2.vnombre)
			end ventrega, 
		a.usuario_registro, 
		coalesce(a.mdocumento, '') mdocumento, 
		coalesce(a1.vnrodocu, '') vmdocumento, 
		coalesce(a.flagdentrega,'0') flagdentrega, 
		coalesce(a.dentrega,'') dentrega, 
		a.ctiprele, 
		a.ds_administrado, 
		a.ndias,
		a.vnrodocini
	FROM COACTIVO.mdocumento a
	INNER JOIN COACTIVO.dasunto b ON a.dasunto = b.idsigma
	INNER JOIN COACTIVO.masunto b1 ON b.masunto = b1.idsigma
	INNER JOIN COACTIVO.mconten b2 ON b.ctipasunto = b2.idsigma-- and b2.cidtabl = '0000000010'

	INNER JOIN public.mperson c ON a.mperson = c.idsigma
	LEFT JOIN public.mperson c1 ON a.crepres = c1.idsigma
	LEFT JOIN public.mperson c2 ON a.centrega = c2.idsigma
	INNER JOIN COACTIVO.mconten d ON a.ctiprtram = d.idsigma --and d.cidtabl = '0000000002'

	LEFT JOIN COACTIVO.mdocumento a1 ON a.mdocumento = a1.idsigma
	WHERE a.idsigma IN (SELECT mdocumento FROM tmpExpedientes)
	ORDER BY a.dfecdocu DESC;

	
   return p_ref;  
--DROP TABLE tmpExpedientes;

END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION coactivo.listar_mdocumento(character varying, character varying, character varying, character varying, character varying, character varying, refcursor)
  OWNER TO postgres;
