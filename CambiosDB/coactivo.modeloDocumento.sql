CREATE TABLE coactivo.modeloDocumento(
	id int  NOT NULL,
	ffecDocumento timestamp with time zone NULL,
	mdocumento char(10) NOT NULL,
	cnroDocumento varchar(150) NULL,	
	cAsunto text NULL,
	cObservaciones varchar(4000)  NULL,
	descripcion text NULL,	
	MensageObs text NULL,	
	Estado char(1),
	fechaRegistro  timestamp with time zone NULL
);


