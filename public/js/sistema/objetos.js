newUsuario=function(){
    openDialogDataFunction1("sistema/listardatosusuario",{cidusuario: '',opt:"1"},"820", "520", "Agregar Usuario");
};

editUsuario=function(_parameters){
    rowid= jQuery("#tblResultUsuario").jqGrid('getGridParam','selrow');
    if (rowid == undefined){
        openDialogInfo("Seleccione una fila",300);
        return false;
    }
    row = $("#tblResultUsuario").jqGrid('getRowData', rowid);
  openDialogDataFunction1("sistema/listardatosusuario",{
      cidusuario:row.cidusuario,
      cidarea:row.cidarea,
      cidpers:row.cidpers,
      nombrepers:row.nombrepers,
      tipou:row.tipousuario,
      usuario:row.usuario,
      nrocaja:row.nrocaja,
      clave:row.clave,
      fechaini:row.fecha_inicio,
      fechafin:row.fecha_fin,
      fechavenc:row.fecha_vencimiento,
      nestado:row.estado
  },"820", "520", "Modificar Usuario");
};
agregarPermisos=function(_parameters){
    rowid= jQuery("#tblResultUsuario").jqGrid('getGridParam','selrow');
    if (rowid == undefined){
        openDialogInfo("Seleccione una fila",300);
        return false;
    }
    row = $("#tblResultUsuario").jqGrid('getRowData', rowid);
    openDialogDataFunction2("sistema/listardatospermisosusu",{
        cidusuario:row.cidusuario,
        nombrepers:row.nombrepers,
        usuario:row.usuario
    },"680", "720", "Agregar Permisos Usuario: "+row.cidusuario);
};

xsavemantemcajero = function(){
	datos=$('#dvaccion :input,hidden').serialize();
	console.log(datos);
	_post = $.post(path + "mantenimientos/cajerossave", datos);
	_post.success(function(request){
		 openDialogInfo(request, 400, 150);
	});
	_post.error(postError);
};

limpiarobjetos = function(){
	$('input').removeAttr('readonly');
	$('#idsigma').val("");
	$('#usuario').val("");
	$('#ciduser').val("");
	$('#nomusuario').val("");
	$('#idsigmamcaja').val("");
	$('#cnrocaja').val("");
	$('#dlocal').val("");
	$('#cidlocal').val("");
	$('#ccodcos').val("");
	$('#nestado').attr('checked', true);
};

xmanteUpdateUsuario = function(numberrow,_row){
	if(numberrow == undefined || numberrow == null) {
		row = _row
	}else{
		row = $(this).getRowData(numberrow);
	}
	row.action='update';
    editUsuario(row);
};

bindkeysUsuario = {"onEnter": xmanteUpdateUsuario};


buscarUsuario = function() {
    valid = true;
    if(valid==true) {
    parameters = {
        "name": "tblResultUsuario",
        "procedure": "seguridad.buscar_usuario",
        "print":"true",
        "parameters": '{' +
        '"p_cidusuario":"",' +
        '"p_usuario":"",' +
        '"p_cidpers":"",' +
        '"p_cidarea":"",' +
        '"p_estado":"",' +
        '"p_tipousuario":""' +
        '}'
    };

     procesar = function(requestData){
        options = $.extend(optionusuario, {
            data: requestData,
            datatype: "local",
            gridComplete: function(){
                $("#loading").remove();
                isGridComplete = true;
                $("#tblResultUsuario").jqGrid('setSelection', 1, true);
            }
        });
        procesarProcedimientoJSON('panelResultUsuario', 'tblResultUsuario', optionusuario, parameters, bindkeysUsuario,navPanelUsuario);
    };
    procesarConsultaSubProceso("registrar", parameters, procesar, 'json');

      /*  proceso = function(requestData){
            actualizarGrid("tblResultUsuario", optionusuario, bindkeysUsuario);
           // procesarProcedimientoJSON('panelResultUsuario', 'tblResultUsuario', optionusuario, parameters, bindkeysUsuario);
            navPanelUsuario();
        };
        procesarConsultaSubProceso('registrar', parameters, proceso);*/


    } else {
        openDialogWarning("Ingrese un valor en los campos de busqueda.", 380, 150);
    }
};

btnAgregaUsuario = {
    caption: '&nbsp; Agregar &nbsp;',
    title: 'Agregar Usuario',
    id:'btnnuevo',
    buttonicon: 'ui-icon-plusthick',
    onClickButton: function () {
        newUsuario();
    }
};

btnEditarUsuario = {
    caption: '&nbsp; Editar &nbsp;',
    title: 'Editar Usuario',
    buttonicon: 'ui-icon-pencil',
    onClickButton: function () {
        editUsuario();
    }
};
btnAgregarPermisos = {
    caption: '&nbsp; Agregar Permisos &nbsp;',
    title: 'Agregar Permisos',
    buttonicon: 'ui-icon-circle-plus',
    onClickButton: function () {
        agregarPermisos();
    }
};

var navPanelUsuario = function() {
    $("#tblResultUsuario").jqGrid('navGrid', '#ptblResultUsuario', {edit: false, add: false, del: false, search: false, refresh: false});
    $("#tblResultUsuario").jqGrid('navButtonAdd', '#ptblResultUsuario', btnAgregaUsuario);
    $("#tblResultUsuario").jqGrid('navSeparatorAdd', '#ptblResultUsuario');
    $("#tblResultUsuario").jqGrid('navButtonAdd', '#ptblResultUsuario', btnEditarUsuario);
    $("#tblResultUsuario").jqGrid('navSeparatorAdd', '#ptblResultUsuario');
    $("#tblResultUsuario").jqGrid('navButtonAdd', '#ptblResultUsuario', btnAgregarPermisos);
    $("#tblResultUsuario").jqGrid('filterToolbar', {searchOnEnter: false, stringResult: true, ignoreCase: true, searchOperators: false, defaultSearch: 'cn'});
};
optionusuario = {
    height: 450,
    width: 1060,
    colNames: [
        "ID",
        "Area",
        "IdArea",
        "IdPersona",
        "Nombre Persona",
        "Tipo",
        "Usuario",
        "Nro Caja",
        "Clave",
        "FechaIni",
        "FechaFin",
        "FechaVencimiento",
        "UltimoAcceso","Estado","estacion","Usuarioregistro","FechaReg"],
    colModel: [
        {name:'cidusuario', index:'cidusuario', width:70,editable: true, align: 'center', frozen: false,search: true,editoptions:{readonly:true,size:10}},
        {name:'varea', index:'varea', width:150,hidden:false},
        {name:'cidarea', index:'cidarea', width:70,editable: true, align: 'center',hidden:true},
        {name:'cidpers', index:'cidpers', width:70,editable: true,hidden:true},
        {name:'nombrepers', index:'nombrepers', width:260,editable: true,hidden:false},
        {name:'tipousuario', index:'tipousuario', width:40,editable: true, align: 'center'},
        {name:'usuario', index:'usuario', width:120,editable: true,hidden:false},
        {name:'nrocaja', index:'nrocaja', width:60,editable: true,hidden:false},
        {name:'clave', index:'clave', width:70,editable: true, align: 'center',hidden:true},
        {name:'fecha_inicio', index:'fecha_inicio', width:80,editable: true, align: 'center',hidden:false,formatter:'date',formatoptions: { newformat: 'd-m-Y'}},
        {name:'fecha_fin', index:'fecha_fin', width:80,editable: true, align: 'center',formatter:'date',formatoptions: { newformat: 'd-m-Y'}},
        {name:'fecha_vencimiento', index:'fecha_vencimiento', width:80,editable: true, align: 'center',hidden:false,formatter:'date',formatoptions: { newformat: 'd-m-Y'}},
        {name:'ultimo_acceso', index:'ultimo_acceso', width:80,hidden:true,editable: true,search: false,align:"center",formatter:'date',formatoptions: { newformat: 'd-m-Y'}},
        {name:'estado', index:'estado', width:50,editable: true,search: false, align: 'center',edittype:"checkbox",editoptions:{value:"1:0",defaultValue:"1"},formatter:'checkbox'},
        {name:'estacion', index:'estacion', width:80,hidden:true,editable: true,search: false,align:"center"},
        {name:'usuario_registro', index:'usuario_registro', width:90,editable: true,align:"right",hidden:true},
        {name:'fecha_registro', index:'fecha_registro', width:90,editable: true,hidden:true,align:"center",formatter:'date',formatoptions: { newformat: 'd-m-Y'}}
        ],
    caption: "&nbsp;&nbsp;&nbsp;Resultados de la busqueda Usuarios",
    ignoreCase:true,
    rowNum:9999999,
    ondblClickRow: xmanteUpdateUsuario,
    afterInsertRow: function(rowid, aData){
        switch (aData.estado) {
            case '1':
                break;
            case '0':
                jQuery("#tblResultUsuario").jqGrid('setCell',rowid,'cidusuario','',{color:'red'});
                jQuery("#tblResultUsuario").jqGrid('setCell',rowid,'cidarea','',{color:'red'});
                jQuery("#tblResultUsuario").jqGrid('setCell',rowid,'cidpers','',{color:'red'});
                jQuery("#tblResultUsuario").jqGrid('setCell',rowid,'nombrepers','',{color:'red'});
                jQuery("#tblResultUsuario").jqGrid('setCell',rowid,'tipousuario','',{color:'red'});
                jQuery("#tblResultUsuario").jqGrid('setCell',rowid,'usuario','',{color:'red'});
                jQuery("#tblResultUsuario").jqGrid('setCell',rowid,'fecha_inicio','',{color:'red'});
                jQuery("#tblResultUsuario").jqGrid('setCell',rowid,'fecha_fin','',{color:'red'});
                jQuery("#tblResultUsuario").jqGrid('setCell',rowid,'fecha_vencimiento','',{color:'red'});
                jQuery("#tblResultUsuario").jqGrid('setCell',rowid,'ultimo_acceso','',{color:'red'});
                jQuery("#tblResultUsuario").jqGrid('setCell',rowid,'estacion','',{color:'red'});
                jQuery("#tblResultUsuario").jqGrid('setCell',rowid,'usuario_registro','',{color:'red'});
                jQuery("#tblResultUsuario").jqGrid('setCell',rowid,'fecha_registro','',{Color:'red'});
                jQuery("#tblResultUsuario").jqGrid('setCell',rowid,'varea','',{color:'red'});
                jQuery("#tblResultUsuario").jqGrid('setCell',rowid,'nrocaja','',{color:'red'});
                break;
        }
    }
};

function loading(){
    var cargador = "";
    cargador += "<div id='loading' style='text-align:center'>";
    cargador += "<br /><br /><br />";
    cargador += "<p>Cargando ...</p>";
    cargador += "<img src='"+pathImage+"loading-bar.gif' />";
    cargador += "</div>";
    $("#panelGrid").append(cargador);
};

$(function(){
  loading();
  buscarUsuario();
});

