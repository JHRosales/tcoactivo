-- Table: coactivo.mruta_audit

-- DROP TABLE coactivo.mruta_audit;

CREATE TABLE coactivo.mruta_audit
(
  id integer NOT NULL,
  idsigma character(10) NOT NULL,
  mdocumento character(10) NOT NULL,
  ccosini character(10) NOT NULL,
  ccosdes character(10) NOT NULL,
  dfecenv timestamp with time zone,
  dfecrecep timestamp with time zone,
  vobserv character varying(4000),
  ctipacc character varying(50) NOT NULL,
  mruta character(10) NOT NULL,
  vhostnm character varying(30) NOT NULL,
  vusernm character varying(60) NOT NULL,
  ddatetm timestamp with time zone NOT NULL,
  stcierre character varying(1) NOT NULL,
  usertdelete character varying(100) NOT NULL,
  ddatedelete timestamp with time zone,
  CONSTRAINT pk__mruta_audit__3 PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE coactivo.mruta_audit
  OWNER TO postgres;
