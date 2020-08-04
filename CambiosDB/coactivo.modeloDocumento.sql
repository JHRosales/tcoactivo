CREATE TABLE coactivo.modelodocumento(
	id int  NOT NULL,
	ffecDocumento timestamp with time zone NULL,
	mdocumento char(10) NOT NULL,
	cnroDocumento varchar(150) NULL,	
	cAsunto text NULL,
	notas varchar(4000)  NULL,
	descripcion text NULL,	
	mensageobs text NULL,	
	estado char(1),
	fechaRegistro  timestamp with time zone NULL
);


