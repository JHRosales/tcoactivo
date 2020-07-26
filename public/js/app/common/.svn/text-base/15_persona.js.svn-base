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
        $("#btnGuardarPersona").button({icons: {primary: 'ui-icon-disk'}}).click(function() {
            Persona.grabar();
        });
        $('#btnCancelarPersona').button({icons: {primary: 'ui-icon-arrowreturnthick-1-w'}}).click(function() {
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
    asignar: function(o) {
        if(o == undefined || o == null) {
            o = Persona.item;
        }
        
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
        
        $("#dialogEditarPersona").dialog("open");
    },
    agregar: function() {
        Persona.item.crazsoc = $('#c_nombrecontrib').val();
        Persona.asignar(null);
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
        $("#mail").match("date", "Ingrese correctamente el e-mail");
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
                        $("#dialogEditarPersona").dialog("close");	
                	} else {
                		$('#c_codigocontrib').focus();
                        $("#c_codigocontrib").val(request[0][1]);
                        $("#btnbuscar").click();
                        $("#dialogEditarPersona").dialog("close");
                	}
                });
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

