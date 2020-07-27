--CAMBIOS 

--AGREGANDO NUEVO ASUNTO
    INSERT INTO coactivo.masunto(
            idsigma, vdescri, nestado, dfecini, dfecfin, varticulo, vhostnm, 
            vusernm, ddatetm)
    VALUES ('0000000002', 'TRAMITE INTERNO', '1', '2015-07-27','2021-07-27' , '0', '', 
            'ADMIN', '2020-07-27');

    INSERT INTO coactivo.dasunto(
            idsigma, masunto, ctipasunto, ndias, ctipccos, ctiptra, nestado, 
            vhostnm, vusernm, ddatetm)
    VALUES ('0000000011','0000000002', '0000001010',30, '0000000207', '0000000120', '1', 
            'MDR', 'ADMIN', '2020-05-27');

