function habpredio() {
    if ($("#txt_mpredio").val() != "") {
        $("#txttipopredio").toggleClass("ui-text-disable");
        $("#linktipopredio").button("option", "disabled", true);
        //$("#txtviacentrpob").toggleClass("ui-text-disable");
        //$("#txtletra").toggleClass("ui-text-disable");
        //$("#txtestacio").toggleClass("ui-text-disable");
        //$("#txtdeposito").toggleClass("ui-text-disable");
        //$("#txtmanzana").toggleClass("ui-text-disable");
        //$("#txtlote").toggleClass("ui-text-disable");
        //$("#txtbloque").toggleClass("ui-text-disable");
        //$("#txtseccion").toggleClass("ui-text-disable");
       // $("#txtunidinmob").toggleClass("ui-text-disable");
        //$("#txtreferencia").toggleClass("ui-text-disable");
        //$("#txtviacentrpob").attr("disabled", true);
        //$("#txtletra").attr("disabled", true);
        //$("#txtestacio").attr("disabled", true);
        //$("#txtdeposito").attr("disabled", true);
        //$("#txtmanzana").attr("disabled", true);
        //$("#txtlote").attr("disabled", true);
        //$("#txtbloque").attr("disabled", true);
        //$("#txtseccion").attr("disabled", true);
        //$("#txtunidinmob").attr("disabled", true);
        //$("#txtreferencia").attr("disabled", true);
    }
}

function mapa(latitud, longitud, zoom) {
    var
            map = null,
            image = pathImage + '../img/casa.png',
            mapOptions = null,
            latLng = new google.maps.LatLng(latitud, longitud),
            geocoder = new google.maps.Geocoder();

    function geocodePosition(pos) {
        geocoder.geocode({latLng: pos},
        function(responses) {
            if (responses && responses.length > 0) {
                updateMarkerAddress(responses[0].formatted_address);
            } else {
                updateMarkerAddress('No se puede determinar la direcci\u00F3n actual.');
            }
        }
        );
    }

    function updateMarkerStatus(str) {
        $('#markerStatus').innerHTML = str;
    }

    function updateMarkerPosition(latLng) {
        document.getElementById('info').innerHTML = [latLng.lat(), latLng.lng()].join(', ');
        $("#txt_nlatitu").val(latLng.lat());
        $("#txt_nlongit").val(latLng.lng());
    }

    function updateMarkerAddress(str) {
        document.getElementById('address').innerHTML = str;
    }

    zoom = parseInt(zoom);

    mapOptions = {
        scaleControl: true,
        zoom: zoom,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        center: latLng
    };

    initMap = function() {
        map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

        var marker = new google.maps.Marker({
            map: map,
            draggable: true,
            icon: image,
            position: latLng
        });

        var infowindow = new google.maps.InfoWindow();
        infowindow.setContent('<b>Mapa</b>');
        google.maps.event.addListener(marker, 'click', function() {
            infowindow.open(map, marker);
        });

        // Update current position info.
        updateMarkerPosition(latLng);
        geocodePosition(latLng);

        // Add dragging event listeners.
        google.maps.event.addListener(marker, 'dragstart', function() {
            updateMarkerAddress('Dragging...');
        });

        google.maps.event.addListener(marker, 'drag', function() {
            updateMarkerStatus('Dragging...');
            updateMarkerPosition(marker.getPosition());
        });

        google.maps.event.addListener(marker, 'dragend', function() {
            updateMarkerStatus('Drag ended');
            geocodePosition(marker.getPosition());
        });

        google.maps.event.addListener(map, 'zoom_changed', function() {
            var zoomLevel = map.getZoom();
            $("#txt_nzoom").val(zoomLevel);
        });
    };

    initMap();
}

var verPredioEdit = function(rowid, iRow, iCol, e) {
    if (rowid != undefined && rowid != null && rowid !== false) {
        row = $("#tblResult").jqGrid('getRowData', rowid);
    } else {
        row = undefined;
    }

    openDialogDataFunction1("registro/verprediomantt", {}, "750", "625", "Predio", function() {
        if (row != undefined) {
            $("#txt_idsigma").val(row.idsigma);
            $("#cbotipopredio").combobox("destroy");
            $("#cbotipopredio option[value=" + row.ctippre + "]").attr("selected", "selected");
            $("#cbotipopredio").combobox();
            $("#txtnumero").val(row.dnumero);
            $("#txtviacentrpob").val(row.tnomvia);
            $("#txtczoncat").val(row.czoncat);
            $("#txtcmzacat").val(row.cmzacat);
            $("#txtcseccat").val(row.cseccat);
            $("#txtcltecat").val(row.cltecat);
            $("#txtcundcat").val(row.cundcat);
            $("#txtinterior").val(row.dinteri);
            $("#txtletra").val(row.dletras);
            $("#txtcodpre").val(row.ccodpre);
            $("#txtdepart").val(row.ddepart);
            $("#txtmanzana").val(row.dmanzan);
            $("#txtlote").val(row.dnlotes);
            $("#txtreferencia").val(row.drefere);
            $("#txtestacio").val(row.destaci);
            $("#txtdepart").val(row.ddepart);
            $("#txtdeposito").val(row.ddeposi);
            $("#txtbloque").val(row.dbloque);
            $("#txtseccion").val(row.dseccio);
            $("#txtunidinmob").val(row.dunidad);
            $("#txt_mviadis").val(row.mviadis);
            $("#txt_mpoblad").val(row.mpoblad);
            $("#txt_nlatitu").val(row.nlatitu);
            $("#txt_nlongit").val(row.nlongit);
            $("#txt_nzoom").val(row.nzoom);
            $("#txt_mpredio").val(row.mpredio);
            habpredio();
        } else {
            $("#txt_idsigma").val('-1');
        }
        mapa($("#txt_nlatitu").val(), $("#txt_nlongit").val(), $("#txt_nzoom").val());
    });
},

btnInsertarPredio = {
    caption: "Agregar&nbsp;&nbsp;",
    title: "Agregar predio",
    buttonicon: "ui-icon-plus",
    onClickButton: function() {
        verPredioEdit();
    }
},

btnEditarPredio = {
    caption: "Editar&nbsp;&nbsp;",
    title: "Editar predio seleccionado",
    buttonicon: "ui-icon-pencil",
    onClickButton: function() {
        var gsr = $("#tblResult").jqGrid('getGridParam', 'selrow');
        if (gsr) {
            verPredioEdit(gsr, -1, -1, null);
        } else {
            openDialogWarning("Seleccione la fila a editar.", 380, 150);
        }
    }
},

btnEliminarPredio = {
    caption: "Eliminar&nbsp;&nbsp;",
    title: "Eliminar el predio seleccionado",
    buttonicon: "ui-icon-trash",
    onClickButton: function() {
        var gsr = $("#tblResult").jqGrid('getGridParam', 'selrow');
        indexRow = $("#ctblResult").val();
        if (gsr) {
            openDialogConfirm1("\u00BFEst\u00E1 seguro de eliminar", 350, {
                "Si": function() {
                    var row2 = $("#tblResult").jqGrid('getRowData', gsr);
                    row = {
                        idsigma: row2.idsigma,
                        dnumero: row2.dnumero,
                        zona: row2.zona,
                        ccatast: row2.ccatast,
                        cplanos: row2.cplanos,
                        czoncat: row2.czoncat,
                        cmzacat: row2.cmzacat,
                        cseccat: row2.cseccat,
                        cltecat: row2.cltecat,
                        cundcat: row2.cundcat,
                        dinteri: row2.dinteri,
                        dletras: row2.dletras,
                        ctipmer: row2.ctipmer,
                        dnummer: row2.dnummer,
                        cdiscat: row2.cdiscat,
                        vdirpre: row2.vdirpre,
                        ctippre: row2.ctippre,
                        idanexo: row2.idanexo,
                        ccodcuc: row2.ccodcuc,
                        ccodpre: row2.ccodpre,
                        ddepart: row2.ddepart,
                        dmanzan: row2.dmanzan,
                        dnlotes: row2.dnlotes,
                        drefere: row2.drefere,
                        destaci: row2.destaci,
                        ddeposi: row2.ddeposi,
                        dbloque: row2.dbloque,
                        dseccio: row2.dseccio,
                        dunidad: row2.dunidad,
                        mviadis: row2.mviadis,
                        mpoblad: row2.mpoblad,
                        nlatitu: row2.nlatitu,
                        nlongit: row2.nlongit,
                        nzoom: row2.nzoom,
                        nestado: "0",
                        mpredio: row2.mpredio
                    };

                    _post = $.post(path + "registro/guardarmpredio", row);
                    _post.success(function(data) {
                        location.reload();
                    });

                    $("#ctblResult").val(indexRow - 1);
                    closeDialog("jqDialogConfirmacion1");
                },
                "No": function() {
                    closeDialog("jqDialogConfirmacion1");
                }
            });
        } else {
            openDialogWarning("Seleccione la fila a eliminar.", 380, 150);
        }
    }
},

btnIndependizar = {
    caption: "Independizar&nbsp;&nbsp;",
    title: "Dividir un predio",
    buttonicon: "ui-icon-plus",
    onClickButton: function() {
        var gsr = $("#tblResult").jqGrid('getGridParam', 'selrow');
        indexRow = $("#ctblResult").val();
        if (gsr) {
            var row2 = $("#tblResult").jqGrid('getRowData', gsr);
            //if (row2.mpredio == "") {
                openDialogConfirm1("\u00BFEst\u00E1 seguro de independizar el predio", 350, {
                    "Si": function() {
                        row = {
                            idsigma: "-1",
                            dnumero: row2.dnumero,
                            zona: row2.zona,
                            ccatast: row2.ccatast,
                            cplanos: row2.cplanos,
                            czoncat: row2.czoncat,
                            cmzacat: row2.cmzacat,
                            cseccat: row2.cseccat,
                            cltecat: row2.cltecat,
                            cundcat: row2.cundcat,
                            dinteri: row2.dinteri,
                            dletras: row2.dletras,
                            ctipmer: row2.ctipmer,
                            dnummer: row2.dnummer,
                            cdiscat: row2.cdiscat,
                            vdirpre: row2.vdirpre,
                            ctippre: row2.ctippre,
                            idanexo: row2.idanexo,
                            ccodcuc: row2.ccodcuc,
                            ccodpre: row2.ccodpre,
                            ddepart: row2.ddepart,
                            dmanzan: row2.dmanzan,
                            dnlotes: row2.dnlotes,
                            drefere: row2.drefere,
                            destaci: row2.destaci,
                            ddeposi: row2.ddeposi,
                            dbloque: row2.dbloque,
                            dseccio: row2.dseccio,
                            dunidad: row2.dunidad,
                            mviadis: row2.mviadis,
                            mpoblad: row2.mpoblad,
                            nlatitu: row2.nlatitu,
                            nlongit: row2.nlongit,
                            nzoom: row2.nzoom,
                            nestado: row2.nestado,
                            mpredio: row2.idsigma
                        };

                        _post = $.post(path + "registro/guardarmpredio", row);
                        _post.success(function(data) {
                            optionViaPredio = $.extend(optionViaPredio, {data: data.data});
                            procesarJSON("panelResult", "tblResult", optionViaPredio, null, navPanelPredio);
                        });

                        closeDialog("jqDialogConfirmacion1");
                    },
                    "No": function() {
                        closeDialog("jqDialogConfirmacion1");
                    }
                });
           // } else {
             //   openDialogWarning("El predio ya se encuentra independizado.", 380, 150);
            //} 
        } else {
            openDialogWarning("Seleccione el predio a independizar.", 380, 150);
        }
    }
};