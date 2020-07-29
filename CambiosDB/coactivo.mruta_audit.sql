CREATE TABLE coactivo.mruta_audit(
	id int  NOT NULL,
	idsigma char(10) NOT NULL,
	mdocumento char(10) NOT NULL,
	ccosini char(10) NOT NULL,
	ccosdes char(10) NOT NULL,
	dfecenv timestamp with time zone NULL,
	dfecrecep timestamp with time zone NULL,
	vobserv varchar(4000) NULL,
	ctipacc varchar(50) NOT NULL,
	mruta char(10) NOT NULL,
	vhostnm varchar(30) NOT NULL,
	vusernm varchar(60) NOT NULL,
	ddatetm timestamp with time zone NOT NULL,
	stcierre varchar(1) NOT NULL,
	usertDelete varchar(100) NOT NULL,
	ddateDelete timestamp with time zone NULL,
 CONSTRAINT PK__mruta_audit__3 PRIMARY KEY (id)
)
WITH (
OIDS=FALSE);
