select * from coactivo.masunto  	-- Tabla Maestra (Data estable) Asuntos para el expediente Coactivo
select * from coactivo.dasunto 		-- Tabla Maestra (Data estable) Detalle de asuntos
select * from coactivo.mconten  	-- Tabla Maestra (Data estable) Datos generales Depurar

select * from coactivo.auxiliarcoactivo -- Auxiliares Coactivos y Ejecutor Coactivo
select * from coactivo.correldocum	-- Correlativo de los documentos o Expedientes por años
select * from coactivo.costasprocesales -- Costas Procesales datos generales
select * from coactivo.dasunto_req 	-- Destalle de Asuntos requeridos ?
select * from coactivo.dcostasprocesales -- Detalle de las costas procesales (Tarifas)

select * from coactivo.ddocumento  	-- Detalle de los expedientes creados
select * from coactivo.ddocumestr  	-- ??
select * from coactivo.doc_emitidodet 	-- Documentos emitidos detalle, informacion de la deuda al momento de genera una resolucion en el expediente Coactivo
select * from coactivo.doc_emitidos 	-- Documentos emitidos, Resoluciones en el expediente Coactivo
select * from coactivo.druta 		-- Detalle de la ruta??
select * from coactivo.expedientescostas  -- Costas procesales agregados a cada expediente
select * from coactivo.madjunto  	-- Archivos Adjuntos al Expediente Coactivo
select * from coactivo.mconfigdoc  	-- ??
select * from coactivo.mcorusudoc  	-- ??
select * from coactivo.mdocumento 	-- datos generales del Expediente coactico
select * from coactivo.mruta  		-- detalle de los movimientos transferencias del expediente
select * from coactivo.mruta_audit  	-- registro de auditoria de la tala mruta (cuando se elimina una movimiento)
select * from coactivo.pagostesoreria   -- pagos para la pagina principal del Modulo Coactivo

--Actualizar
update coactivo.mconten set vdescri='EXPEDIENTE COACTIVO' where idsigma='0000001010' --Descripocion del Asunto
update coactivo.correldocum set nultgen=0 where cperiodo='2020'  --El correlativo de los expedientes por años

--Depurar Tabla Mconten
Delete from coactivo.mconten where cidtabl='0000000009' and idsigma not in ('0000000009','0000001010')
Delete from coactivo.mconten where cidtabl='0000000001'
Delete from coactivo.mconten where cidtabl='0000000005'
Delete from coactivo.mconten where cidtabl='0000014874'
Delete from coactivo.mconten where cidtabl='0000014900'



