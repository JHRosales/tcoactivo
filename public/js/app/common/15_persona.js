var Persona = {
    item: {
        cidpers: '',
        vnrodoc: '',
        vnombre: '',
        dfecnac: '',
        nestado: '',
        ctelfij: '',
        ctelmov: '',
        vcorreo: '',
        cdenomi: '',
        vnumero: '',
        vdpto: '',
        vmanzan: '',
        vlote: '',
        vreferen: '',
        vobserv: '',
        ctipdoc: '1000000529',
        ctipper: '1000000093',
        csexo: '1',
        cestciv: '0000000251',
        cubigeo: '0000001561',
        mpredio: '',
        mviadis: '',
        mpoblad: '',
        vinterior: '',
        vletra: '',
        vestacionto: '',
        vdeposito: '',
        vbloque: '',
        vseccion: '',
        vunidinmob: ''
    },
    ubigeo: '',
    origen: 'BuscadorContribuyente',
    seleccionarPredio: function(rowid) {
        var row = $("#tblResultPredio").jqGrid('getRowData', rowid);

        $("#mpredio").val(row.idsigma);
        $("#mviadis").val(row.mviadis);
        $("#mpoblad").val(row.mpoblad);
        $("#denominacion").val(row.tnomvia);
        $("#direcnumero").val(row.dnumero);
        $("#departamen").val(row.ddepart);
        $("#manzana").val(row.dmanzan);
        $("#lote").val(row.dnlotes);
        $("#interior").val(row.dinteri);
        $("#letra").val(row.dletras);
        $("#estac").val(row.destaci);
        $("#depos").val(row.ddeposi);
        $("#bloque").val(row.dbloque);
        $("#secc").val(row.desccio);
        $("#unid").val(row.dunidad);

        $("#dialogBuscarContribuyentePredioPredio").dialog("close");
    },
    navPanelPredio: function() {
	    $("#tblResultPredio").jqGrid('navGrid', '#ptblResultPredio', {edit: false, add: false, del: false, search: false, refresh: false});
	    $("#tblResultPredio").jqGrid('navButtonAdd', '#ptblResultPredio', btnInsertarPredio);
    },
    init: function() {
        var options = '<option value="9999999999">SELECCIONAR</option>';

        $.each(Persona.ubigeo, function(i, columns) {
            var value = columns.idsigma,
                label = columns.vnombres;
            options += '<option value="' + value + '">' + label + '</option>';
        });
        $("#distrito").html(options);

        $("#dialogEditarPersona").dialog({
            title: "Mantenimiento de Personas",
            modal: true,
            minHeight: 'auto',
            width: 950,
            position: ['center', 'center'],
            resizable: false,
            autoOpen: false,
            closeOnEscape: true
        });

        $("#dialogPredio").dialog({
            title: "Mantenimiento de Predios",
            modal: true,
            height: 515,
            width: 1025,
            position: ['center', 'center'],
            resizable: false,
            autoOpen: false,
            closeOnEscape: true
        });

        $("#fecnace").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            showOn: "button",
            buttonImage: pathImage + "calendar.gif",
            buttonImageOnly: true
        });

        $("#btnActivaEdicionPersona").button({icons: {primary: 'ui-icon-pencil'}}).click(function() {
            openDialogData1(
              "mantenimientos/logineditarpersona",
              {},
              "400",
              "130",
              "Intentificaci&oacute;n de Usuario autorizado"
            );
            return;

            Persona.bloquearControles(false);
            $("#btnGuardarPersona").show();
            $("#btnCancelaEdicionPersona").show();

            $('#btnActivaEdicionPersona').hide();
        });

        $("#btnCancelaEdicionPersona").button({icons: {primary: 'ui-icon-cancel'}}).click(function() {

            $("#btnGuardarPersona").hide();
            $("#btnCancelaEdicionPersona").hide();

            $('#btnActivaEdicionPersona').show();

            Persona.bloquearControles(true);
        });

        $("#btnGuardarPersona").button({icons: {primary: 'ui-icon-disk'}}).click(function() {
            openDialogConfirm1(
                "Los datos consignados en la presente ficha se registraran en un historial y tendran validez de declaraci&oacute;n jurada",
                350,
                {
                    "Aceptar y Guardar" : function() {
                        Persona.grabar();
                        Persona.verHistorial();
                        closeDialog("jqDialogConfirmacion1");
                    },
                    "Cancelar" : function() {
                        closeDialog("jqDialogConfirmacion1");
                    }
                }
            );
        });
        $('#btnCancelarPersona').button({icons: {primary: 'ui-icon-arrowreturnthick-1-w'}}).click(function() {
          $("#btnGuardarPersona").hide();
          $("#btnCancelaEdicionPersona").hide();

          $('#btnActivaEdicionPersona').show();
            Persona.currentData = null;
            closeDialog("dialogEditarPersona");
        });
        $("#btnbuscarpredio").button({icons: {primary: 'ui-icon-search'}}).click(function() {
            Persona.buscarPredio();
        });

        _grifConfig = $.extend({}, __gridConfigViaPredio);
        _grifConfig.ondblClickRow = Persona.seleccionarPredio;
        BuscadorPredio.gridConfigPredio = _grifConfig;
        BuscadorPredio.bindkeysPredio = {"onEnter": Persona.seleccionarPredio};
        BuscadorPredio.init(false);
        BuscadorPredio.navPanelPredio = Persona.navPanelPredio;
        BuscadorPredio.configBeforeGrid = function(request){
            Persona.navPanelPredio();
        };
        BuscadorPredio.configBeforeGrid(null);
        BuscadorPredio.configurarDialog();
    },
    selectedTipoPersona: function(event, ui) {
        // $("#cboestcivil").combobox("destroy");
        // $("#cboestcivil option[value='0000000251']").attr("selected", true);
        // $("#cboestcivil").combobox();
    },
    selectedTipoDocumento: function(event, ui) {
        $("#nrodoc").attr('maxlength', $("#cbotipdoc").val());
        $("#nrodoc").val("");
        $("#nrodoc").focus();
    },
    selectedDistrito: function(event, ui) {
        if(ui.item) {
            if($(ui.item).val() != "0000001561") {
                if(!$("#btnbuscarpredio").hasClass("row-hide")) {
                    $("#btnbuscarpredio").addClass("row-hide");
                }
                if($(".panel-direccion").attr("disabled") == "disabled") {
                    $(".panel-direccion").removeAttr("disabled");
                    $(".panel-direccion").removeClass("ui-text-disable");
                }
            } else {
                if($("#btnbuscarpredio").hasClass("row-hide")) {
                    $("#btnbuscarpredio").removeClass("row-hide");
                }
                if($(".panel-direccion").attr("disabled") != "disabled") {
                    $(".panel-direccion").attr("disabled", "disabled");
                    $(".panel-direccion").addClass("ui-text-disable");
                }
            }
        }
    },
    bloquearControles: function(disabledState){

        /*
        $(".ui-text, .ui-combobox-input").attr("disabled", disabledState);
        $(".ui-text, .ui-combobox-input").toggleClass("ui-text-disable");
        $(".ui-combobox a").button("option", "disabled", disabledState);
        */

        if(disabledState){
          $('.panel-direccion').addClass('n-panel-direccion');
          $('.n-panel-direccion').removeClass('panel-direccion');

          $('.ui-datepicker-trigger').hide();
        }else{
          $('.n-panel-direccion').addClass('panel-direccion');
          $('.panel-direccion').removeClass('n-panel-direccion');

          $('.ui-datepicker-trigger').show();
        }

        $('.tpersona').attr('disabled', disabledState);

        if (disabledState){
            $('.tpersona').addClass('ui-text-disable');
            //$('.tpersona').addClass('ui-text-auchanged');
            $('#panelPersona1 .ui-combobox-input').addClass('ui-text-disable');
            $('#panelPersona2 .ui-combobox-input').addClass('ui-text-disable');
        }else{
            $('.tpersona').removeClass('ui-text-disable');
            $('#panelPersona1 .ui-combobox-input').removeClass('ui-text-disable');
            $('#panelPersona2 .ui-combobox-input').removeClass('ui-text-disable');
        }

        $('#panelPersona1 .ui-text').attr('disabled', disabledState);
        $('#panelPersona1 .ui-combobox-input').attr('disabled', disabledState);
        $('#panelPersona1 .ui-combobox a').button('option', 'disabled', disabledState);

        $('#panelPersona2 .ui-text').attr('disabled', disabledState);
        $('#panelPersona2 .ui-combobox-input').attr('disabled', disabledState);
        $('#panelPersona2 .ui-combobox a').button('option', 'disabled', disabledState);

        /*
        if(disabledState){
          $('.tpersona').addClass('ui-text-disable');
        }else{
          $('.tpersona').removeClass('ui-text-disable');
        }
        */
    },
    asignarHistoria: function(h) {
        Persona.setDataToControls(h);
        $("#btnGuardarPersona").hide();
        $("#btnCancelaEdicionPersona").hide();

        $('#btnActivaEdicionPersona').show();
        Persona.bloquearControles(true);


        var strdet = h.dtrans;
        var arrdet = strdet.split(',');
        var carr = arrdet.length;

        for(var i=0; i < carr; i++){
            $('#' + arrdet[i]).removeClass('ui-text-disable');
            $('#' + arrdet[i]).addClass('ui-text-auchanged');
        }
    },
    currentData : null,
    setDataToControls : function(o){
        $('#txtcodigo').val(o.cidpers);
        $('#nrodoc').val(o.vnrodoc);
        $('#nombre').val(o.crazsoc);
        $('#fecnace').val(o.dfecnac);
        $('#st_estado').val(o.nestado);
        $('#nrotef').val(o.ctelfij);
        $('#nromovil').val(o.ctelmov);
        $('#mail').val(o.vcorreo);
        $('#ref').val(o.vreferen);
        $('#ds_observacion').html(o.vobserv);

        $('#denominacion').val(o.cdenomi);
        // $('#direccion').val(o.cdenomi);
        $('#direcnumero').val(o.vnumero);
        $('#departamen').val(o.vdpto);
        $('#manzana').val(o.vmanzan);
        $('#lote').val(o.vlote);
        $("#interior").val(o.vinterior);
        $("#letra").val(o.vletra);
        $("#estac").val(o.vestacionto);
        $("#depos").val(o.vdeposito);
        $("#bloque").val(o.vbloque);
        $("#secc").val(o.vseccion);
        $("#unid").val(o.vunidinmob);
        $("#mpredio").val(o.mpredio);
        $("#mviadis").val(o.mviadis);
        $("#mpoblad").val(o.mpoblab);
        $("#txtvdeclara").val(o.vdeclaracion);

        if(o.cubigeo == '0000001561') {
            if($("#btnbuscarpredio").hasClass("row-hide")) {
                $("#btnbuscarpredio").removeClass("row-hide");
            }
            if($(".panel-direccion").attr("disabled") != "disabled") {
                $(".panel-direccion").attr("disabled", "disabled");
                $(".panel-direccion").addClass("ui-text-disable");
            }
        } else {
            if(!$("#btnbuscarpredio").hasClass("row-hide")) {
                $("#btnbuscarpredio").addClass("row-hide");
            }
            if($(".panel-direccion").attr("disabled") == "disabled") {
                $(".panel-direccion").removeAttr("disabled");
                $(".panel-direccion").removeClass("ui-text-disable");
            }
        }

        $("#distrito").combobox("destroy");
        $("#distrito option[value='" + o.cubigeo + "']").attr("selected", true);
        $("#distrito").combobox({selected: Persona.selectedDistrito});

        $("#cbotipper").combobox("destroy");
        $("#cbotipper option[value='" + o.ctipper + "']").attr("selected", true);
        $("#cbotipper").combobox({selected: Persona.selectedTipoPersona});

        $("#cbotipdoc").combobox("destroy");
        $("#cbotipdoc option[data-idsigma='" + o.ctipdoc + "']").attr("selected", true);
        $("#cbotipdoc").combobox({selected: Persona.selectedTipoDocumento});

        $("#cbosexo").combobox("destroy");
        $("#cbosexo option[value='" + o.csexo + "']").attr("selected", true);
        $("#cbosexo").combobox();

        $("#cboestcivil").combobox("destroy");
        $("#cboestcivil option[value='" + o.cestciv + "']").attr("selected", true);
        $("#cboestcivil").combobox();
    },

    asignar: function(o) {
        if(o == undefined || o == null) {
            o = Persona.item;
        }
        if(Persona.currentData == null){

            Persona.bloquearControles(true);
            Persona.currentData = o;
        }

        Persona.setDataToControls(Persona.currentData);
        Persona.verHistorial();

        //console.log(Persona.currentData);
        $("#dialogEditarPersona").dialog("open");
    },
    verHistorial: function(){
        btnImprimirDJPersona = {
            caption : '',
            text: false,
            title : 'Imprimir DDJJ Persona',
            buttonicon : 'ui-icon-print',
            onClickButton : function(){
                var _grid = $('#tblHpersona');
                var _selid = _grid.getGridParam('selrow');
                var _row = _grid.getRowData(_selid);

                var openReport = pathReportPDF + 'declaracionJuradaContri.php?';
                openReport += 'cidtipo=' + _row.nivel;
                openReport += '&usuario=' + $('#hd_usuario_login').val();
                if (_row.nivel == '1'){
                    openReport += '&cidpers=' + _row.cidpers;
                } else {
                    openReport += '&cidpers=' + _row.idtrans;
                }
                window.open(openReport);
            }
        };
        navPanelHistorial = function(){
            $('#tblHpersona').jqGrid(
                'navGrid',
                '#ptblHpersona',
                {edit: false, add: false, del: false, search: false,	refresh: false}
            );
            $('#tblHpersona').jqGrid('navButtonAdd', '#ptblHpersona', btnImprimirDJPersona);
        };
        var optionHpersona = {
            height: 140,
            width: 280,
            rowNum: 10000,
            colNames: [
                'Codigo',
                'Codigo',
                'nivel',
                'dtrans',
                'cidpers',
                'crazsoc',
                'direccf',
                'vtipdoc',
                'vnrodoc',
                'vpatern',
                'vmatern',
                'vnombre',
                'ctipper',
                'vtipper',
                'nestado',
                'vhostnm',
                'Usuario',
                'Fecha Reg.',
                'ntipers',
                'ntipper',
                'cubigeo',
                'cdenomi',
                'vdirecc',
                'vnumero',
                'vlote',
                'vmanzan',
                'vdpto',
                'vreferen',
                'ctipdoc',
                'vtipdoc',
                'vnrodoc',
                'dfecnac',
                'csexo',
                'csexo',
                'cestciv',
                'ctelfij',
                'ctelmov',
                'vcorreo',
                'vobserv',
                'mpredio',
                'mviadis',
                'mpoblad',
                'vinterior',
                'vletra',
                'vestacionto',
                'vdeposito',
                'vbloque',
                'vseccion',
                'vunidinmob'
            ],
            colModel: [
                {name:'vdeclaracion', index:'vdeclaracion', width: 68, hidden: false, sortable: false},
                {name:'idtrans', index:'idtrans', width: 68, hidden: true, sortable: false},
                {name:'nivel', index:'nivel', width: 68, hidden: true, sortable: false},
                {name:'dtrans', index:'dtrans', width: 68, hidden: true, sortable: false},
                {name:'cidpers', index:'cidpers', width: 50, hidden: true},
                {name:'crazsoc', index:'crazsoc', width: 50, hidden: true},
                {name:'direccf', index:'direccf', width: 50, hidden: true},
                {name:'vtipdoc', index:'vtipdoc', width: 50, hidden: true},
                {name:'vnrodoc', index:'vnrodoc', width: 50, hidden: true},
                {name:'vpatern', index:'vpatern', width: 50, hidden: true},
                {name:'vmatern', index:'vmatern', width: 50, hidden: true},
                {name:'vnombre', index:'vnombre', width: 50, hidden: true},
                {name:'ctipper', index:'ctipper', width: 50, hidden: true},
                {name:'vtipper', index:'vtipper',   width: 50, hidden: true},
                {name:'nestado', index:'nestado', width: 50, hidden: true},
                {name:'vhostnm', index:'vhostnm', width: 50, hidden: true},
                {name:'vusernm', index:'vusernm', width: 76, hidden: false, sortable: false},
                {name:'ddatetm', index:'ddatetm', width: 100, hidden: false, sortable: false},
                {name:'ntipers', index:'ntipers', width: 50, hidden: true},
                {name:'ntipper', index:'ntipper', width: 50, hidden: true},
                {name:'cubigeo', index:'cubigeo', width: 50, hidden: true},
                {name:'cdenomi', index:'cdenomi', width: 50, hidden: true},
                {name:'vdirecc', index:'vdirecc', width: 50, hidden: true},
                {name:'vnumero', index:'vnumero', width: 50, hidden: true},
                {name:'vlote', index:'vlote', width: 50, hidden: true},
                {name:'vmanzan', index:'vmanzan', width: 50, hidden: true},
                {name:'vdpto', index:'vdpto', width: 50, hidden: true},
                {name:'vreferen', index:'vreferen', width: 50, hidden: true},
                {name:'ctipdoc', index:'ctipdoc', width: 50, hidden: true},
                {name:'vtipdoc', index:'vtipdoc', width: 50, hidden: true},
                {name:'vnrodoc', index:'vnrodoc', width: 50, hidden: true},
                {name:'dfecnac', index:'dfecnac', width: 50, hidden: true},
                {name:'csexo', index:'csexo', width: 50, hidden: true},
                {name:'csexo', index:'dfecinic', width: 50, hidden: true},
                {name:'cestciv', index:'cestciv', width: 50, hidden: true},
                {name:'ctelfij', index:'ctelfij', width: 50, hidden: true},
                {name:'ctelmov', index:'ctelmov', width: 50, hidden: true},
                {name:'vcorreo', index:'vcorreo', width: 50, hidden: true},
                {name:'vobserv', index:'vobserv', width: 50, hidden: true},
                {name:'mpredio', index:'mpredio', width: 50, hidden: true},
                {name:'mviadis', index:'mviadis', width: 50, hidden: true},
                {name:'mpoblad', index:'mpoblad', width: 50, hidden: true},
                {name:'vinterior', index:'vinterior', width: 50, hidden: true},
                {name:'vletra', index:'vletra', width: 50, hidden: true},
                {name:'vestacionto', index:'vestacionto', width: 50, hidden: true},
                {name:'vdeposito', index:'vdeposito', width: 50, hidden: true},
                {name:'vbloque', index:'vbloque', width: 50, hidden: true},
                {name:'vseccion', index:'vseccion', width: 50, hidden: true},
                {name:'vunidinmob', index:'vunidinmob', width: 50, hidden: true}
            ],
            afterInsertRow : function(rowid, adata){
                switch (adata.nivel) {
                    case '1':
                        $("#tblHpersona").jqGrid('setCell',rowid,'vdeclaracion','',{color:'blue'});
                        $("#tblHpersona").jqGrid('setCell',rowid,'vusernm','',{color:'blue'});
                        $("#tblHpersona").jqGrid('setCell',rowid,'ddatetm','',{color:'blue'});
                        break;
                    case '2':
                        break;
                }
            },
            caption: "DJ Persona",
            pgbuttons : false,
            viewrecords : false,
            pgtext : "",
            pginput : false,
            ondblClickRow: function(){
                //alert('ello');
                var grid = $('#tblHpersona');
                var rowKey = grid.jqGrid('getGridParam','selrow');
                var row = grid.jqGrid('getRowData', rowKey);

                //console.log(row);
                Persona.asignarHistoria(row);
            }
        };

        var params = {
            "name": "tblHpersona",
            "procedure": "auditoria.mperson_listar",
            "print": "true",
            "parameters": '{' +
                '"p_mperson":"' + $("#txtcodigo").val() + '"' +
                '}'
        };
        procesarProcedimientoJSON('panelHpersona', 'tblHpersona', optionHpersona, params, null, navPanelHistorial);

    },
    agregar: function() {
        Persona.item.crazsoc = $('#c_nombrecontrib').val();
        Persona.asignar(null);

        Persona.bloquearControles(false);
        $("#btnGuardarPersona").show();

        $('#btnActivaEdicionPersona').hide();
    },
    editar: function(rowid) {
        var _post, params, row;

        row = $("#tblResultPersona").jqGrid('getRowData', rowid);

        params = {id_person: row.cidpers};

        _post = $.post(path + "mantenimientos/verpersona", params);
        _post.success(function(request){
            row = null;
            if(request.rowPersona != undefined || request.rowPersona != null) {
                row = request.rowPersona[0];
            }
            if(request.rowPersona != undefined || request.rowPersona != null) {
                row = request.rowPersona[0];
            }

            Persona.asignar(row);
            Persona.bloquearControles(true);
        });
    },
    eliminar: function(rowid) {
    },
    validar: function() {
        $.validity.start();
        $("#txttipper").assert($("#txttipper").val().toUpperCase() != 'SELECCIONE', "Seleccione el tipo de persona");
        $("#txttipdoc").assert($("#txttipdoc").val().toUpperCase() != 'SELECCIONE', "Seleccione el tipo de Documento");
        $("#nrodoc")
            .require('Ingrese el número de documento')
            .match("number", "El número de documento tiene que ser un número valido")
            .minLength(parseInt($("#nrodoc").attr("maxlength")), "El número de documento debe contener " + $("#nrodoc").attr("maxlength") + " caracteres");
        $("#nombre").require('Ingrese el nombre');
        $("#fecnace")
            .require('Ingrese la fecha de nacimiento')
            .match("date", "La fecha tiene estar en el formato dd/MM/yyyy");
        $("#mail").match("email", "Ingrese correctamente el e-mail");
        var result = $.validity.end();
        return result.valid;
    },
    grabar: function() {
        if(Persona.validar()) {
            var _post = null, datos={
                cbotipingreso: $("#cbotipingreso option:selected").val(),
                txtcodigo: $("#txtcodigo").val(),
                st_estado: $("#st_estado").val(),
                cbotipdoc: $("#cbotipdoc option:selected").attr("data-idsigma"),
                nrodoc: $("#nrodoc").val(),
                cbotipper: $("#cbotipper").val(),
                apatern: '',
                amatern: '',
                nombre: $("#nombre").val(),
                cbosexo: $("#cbosexo option:selected").val(),
                cboestcivil: $("#cboestcivil").val(),
                fecnace: $("#fecnace").val(),
                nrotef: $("#nrotef").val(),
                nromovil: $("#nromovil").val(),
                mail: $("#mail").val(),
                distrito: $("#distrito option:selected").val(),
                denominacion: $("#denominacion").val(),
                direccion: '', // $("#direccion").val(),
                direcnumero: $("#direcnumero").val(),
                departamen: $("#departamen").val(),
                manzana: $("#manzana").val(),
                lote:$("#lote").val(),
                ref: $("#ref").val(),
                ds_observacion: $("#ds_observacion").val() ,
                mpredio: $("#mpredio").val(),
                mviadis: $("#mviadis").val(),
                mpoblad: $("#mpoblad").val(),
                interior: $("#interior").val(),
                letra: $("#letra").val(),
                estac: $("#estac").val(),
                depos: $("#depos").val(),
                bloque: $("#bloque").val(),
                secc: $("#secc").val(),
                unid: $("#unid").val()
            };

            _post = $.post(path + "mantenimientos/personasave", datos);
            _post.success(function(request){
                openDialogInfo("Datos guardados con éxito.", 350, null, null, function() {
                	if(Persona.origen == 'BuscadorContribuyente') {
                		$('#c_codigocontribPersona').focus();
                        $("#c_codigocontribPersona").val(request[0][1]);
                        $("#btnbuscarPersona").click();
                        //$("#dialogEditarPersona").dialog("close");
                	} else {
                		$('#c_codigocontrib').focus();
                        $("#c_codigocontrib").val(request[0][1]);
                        $("#btnbuscar").click();
                        $("#dialogEditarPersona").dialog("close");
                	}
                  $("#btnGuardarPersona").hide();
                  $("#btnCancelaEdicionPersona").hide();

                  $('#btnActivaEdicionPersona').show();

                  Persona.bloquearControles(true);
                });

                Persona.currentData = null;
            });
        }
    },
    buscarPredio: function() {
        $("#tblResultPredio").jqGrid("clearGridData");
        $("#c_predialPredio").val('');
        $("#c_viacontribPredio").val('');
        $("#c_nroviacontribPredio").val('');
        $("#c_mzacontribPredio").val('');
        $("#c_lotecontribPredio").val('');
    	$("#dialogBuscarContribuyentePredioPredio").dialog("open");
    }
};
