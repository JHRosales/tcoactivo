function detallenodomconten(_idnodo, _idpadre) {
    var parameters = {
        idnodo: _idnodo,
        idpadre: _idpadre
    };
    var _post = $.post(path + "sistema/objetonodo/", parameters);
    _post.success(function (requestData) {
        $("#cont_form").html(requestData);
    });

    _post.error(postError);

    if (_idnodo == '' || _idnodo == '0000000001' || _idnodo == '0000000000') {
        $('#btnbotones').button('option', 'disabled', true);
    } else {
        $('#btnbotones').button('option', 'disabled', false);
    }

}

function agregarBotones() {
    var cod = $('#c_codigo').html();
    if (cod == undefined || cod == '' || cod == '0000000001') {
        alert('selecccione Objeto');

    } else {
        //alert($('#c_codigo').html());
        /*rowid= jQuery("#tblResultrequerimiento").jqGrid('getGridParam','selrow');
        if (rowid == undefined){
            openDialogInfo("Seleccione una fila",300);
            return false;
        }
    row = $("#tblResultrequerimiento").jqGrid('getRowData', rowid);*/

        openDialogDataFunction1("sistema/listarbotonesobj", { idsigma: cod }, "500", "370", "Listado de Botones");

    }
}

function objetossave() {
    var cidobjeto = $('#c_codigo').html();
    var vobjeto = $('#txt_descripcion').val();
    var cidtipo = "";
    var cidobjetopadre = $('#cb_padre').val();
    var accion = $('#txa_observ').val();
    var orden = $('#txt_long').val();
    var objid = $('#txt_decimal').val();
    var estado = $('#cb_estado').val();

    $.ajax({
        dataType: "html",
        type: "POST",
        url: path + "sistema/objetossave/", // Ruta donde se encuentra nuestro action que procesa la peticion XmlHttpRequest
        data: "cidobjeto=" + cidobjeto
            + "&vobjeto=" + vobjeto
            + "&cidtipo=" + cidtipo
            + "&cidobjetopadre=" + cidobjetopadre
            + "&accion=" + accion
            + "&orden=" + orden
            + "&objid=" + objid
            + "&estado=" + estado,
        success: function (requestData) {
            //console.log(requestData);
            if (requestData == 'Guardado Correctamente' || requestData == 'Actualizado Correctamente') {
                openDialogInfo(requestData);
                window.open(path + 'sistema/objetos', '_self');
            } else {
                openDialogInfo("Ocurrio un erro al Guardar los Datos, Contacte con su administrador de sistemas.<br>DETALLE: <br>" + requestData);
            }

            //  $("#div_mensaje").html(requestData);           
        },
        error: function (requestData, errNumber, errMessage) {
            if (errNumber == '') {
                openDialogError("No se puede determinar el error.");
            } else {
                openDialogError(errNumber + ': ' + errMessage);
            }
        }
    });
}

function selectEsquemaConten() {
    $.ajax({
        dataType: "html",
        type: "POST",
        url: path + "sistema/mcontenesquema/", // Ruta donde se encuentra nuestro action que procesa la peticion XmlHttpRequest
        data: "schemaid=" + $("#cboesquema").val(),
        success: function (requestData) { 	//Llamada exitosa)
            window.open(path + 'mantenimientos/mconten', '_self');
        },
        error: function (requestData, errNumber, errMessage) {
        }
    });
}

function imprimirmconten(id) {
    window.open(pathReport + "reporte=report_contenedores&opt=P_cidtabl^" + id, '_blank');
}

function pintarregistrospagosarbitriospredios(registros, reajustecentimos, url) {

    var data
    $.ajax(
        {
            dataType: "html",
            type: "POST",
            url: jQuery.scriptPath + "index.php/sistema/guardarpermisos",
            data: "reajustecentimos=" + reajustecentimos + "&registros=" + registros + "&url=" + url,
            beforeSend: function (data) {
                $("#div_detalle_recibo").html("<tr><td colspan=\'9\'>Cargando...</td></tr>");
            },
            success: function (requestData) {
                $("#div_detalle_recibo").html(requestData);
            },
            error: function (requestData, strError, strTipoError) {
                $("#div_detalle_recibo").html("Error " + strTipoError + ": " + strError);
            },
            complete: function (requestData, exito) {
            }
        });
}

function ColocarValorObjeto(Objeto, Valor, Propiedad, TipObjeto, NroItems, Indice, Formulario) {

    //alert(Objeto+'-'+Valor+'-'+Propiedad+'-'+TipObjeto+'-'+NroItems+'-'+Indice+'-'+Formulario);
    switch (TipObjeto) {
        case 'TEXT':
            break;
        case 'CHECKBOX':
            if (Propiedad != undefined && Propiedad != 'undefined' && Propiedad != '') {
                if (Valor == true || Valor == false) {
                    ctrl = eval("document." + Formulario + "['" + Objeto + "[" + Indice + "]']");
                    eval("ctrl." + Propiedad + "=" + Valor);
                }
                else {
                    ctrl = eval("document." + Formulario + "['" + Objeto + "[" + Indice + "]']");
                    eval("ctrl." + Propiedad + "='" + Valor + "'");
                }

            }
            else {
                ctrl = eval("document." + Formulario + "['" + Objeto + "[" + Indice + "]']");
                ctrl.value = Valor;
            }
            break;
        case 'SELECT':
            break;
        case 'RADIO':
            if (Propiedad != undefined && Propiedad != 'undefined' && Propiedad != '') {
                if (Valor == true || Valor == false) {
                    if (document.getElementsByName(Objeto) != null) {
                        var ctrl = document.getElementsByName(Objeto);
                        eval("ctrl[" + Indice + "]." + Propiedad + "=" + Valor);
                    }
                    else if (document.getElementById(Objeto) != null) {
                        var ctrl = document.getElementById(Objeto);
                        eval("ctrl[" + Indice + "]." + Propiedad + "=" + Valor);
                    }
                }
                else {
                    if (document.getElementsByName(Objeto) != null) {
                        var ctrl = document.getElementsByName(Objeto);
                        eval("ctrl[" + Indice + "]." + Propiedad + "='" + Valor + "'");
                    }
                    else if (document.getElementById(Objeto) != null) {
                        var ctrl = document.getElementById(Objeto);
                        eval("ctrl[" + Indice + "]." + Propiedad + "='" + Valor + "'");
                    }
                }
            }
            else {

                if (document.getElementsByName(Objeto) != null) {
                    var ctrl = document.getElementsByName(Objeto);
                    ctrl.value = Valor;
                }
                else if (document.getElementById(Objeto) != null) {
                    var ctrl = document.getElementById(Objeto);
                    ctrl.value = Valor;
                }
            }

            break;
        default:
            var a = new Array();
            a = convertir_a_arreglo(Objeto, ',');
            // alert(a);
            for (x = 0; x < a.length; x++) {
                Objeto = a[x];
                //alert(Objeto+Propiedad+Valor);
                if (Propiedad != undefined && Propiedad != 'undefined' && Propiedad != '') {
                    if (Valor == true || Valor == false) {
                        if (document.getElementById(Objeto) != null)
                            eval("document.getElementById('" + Objeto + "')." + Propiedad + "=" + Valor);
                        else if (document.getElementsByName(Objeto) != null && document.getElementsByName(Objeto).value != undefined)
                            eval("document.getElementsByName('" + Objeto + "')." + Propiedad + "=" + Valor);
                    }
                    else {
                        if (document.getElementById(Objeto) != null)
                            eval("document.getElementById('" + Objeto + "')." + Propiedad + "='" + Valor + "'");
                        else if (document.getElementsByName(Objeto) != null && document.getElementsByName(Objeto).value != undefined)
                            eval("document.getElementsByName('" + Objeto + "')." + Propiedad + "='" + Valor + "'");
                    }
                }
                else {
                    if (document.getElementById(Objeto) != null)
                        document.getElementById(Objeto).value = Valor;
                    else if (document.getElementsByName(Objeto) != null)
                        document.getElementsByName(Objeto).value = Valor;
                }
            }
            break;
    }
}

function convertir_a_arreglo(cadena, parametro) {
    var temp = "" + cadena;
    if (temp.length > 0) {
        var a = new Array();
        var pos = temp.indexOf(parametro);
        var len = parametro.length;
        while (pos != -1) {
            //  alert(pos);
            a.push(temp.substring(0, pos));
            // alert(temp.substring(0, pos));
            pos = parseInt(pos) + parseInt(len);
            temp = "" + temp.substring(pos, temp.length);
            pos = temp.indexOf(parametro);
        }
        a.push(temp.substring(0, temp.length));
    }
    return a;
}

function ObtenerValorObjeto(Objeto, Propiedad, TipObjeto, NroItems, Indice, Formulario) {
    Valor = false;
    //alert(Objeto+'-'+Propiedad+'-'+TipObjeto+'-'+NroItems);
    switch (TipObjeto) {
        case 'TEXT':
            break;
        case 'CHECKBOX':
            if (Propiedad != undefined && Propiedad != 'undefined' && Propiedad != '') {

                if (Indice != undefined) {
                    var ctrl = eval("document." + Formulario + "['" + Objeto + "[" + Indice + "]']");
                    if (ctrl.checked) Valor = eval("ctrl." + Propiedad); else Valor = false;
                }
                else if (NroItems != undefined) {
                    for (i = 0; i < NroItems; i++) {
                        ctrl = eval("document." + Formulario + "['" + Objeto + "[" + i + "]']");
                        if (ctrl.checked) if (Valor == false) Valor = eval("ctrl." + Propiedad); else Valor = Valor + "|" + eval("ctrl." + Propiedad);
                    }

                }
            }
            else {
                if (document.getElementsByName(Objeto) != null && Indice != undefined) {
                    var ctrl = document.getElementsByName(Objeto);
                    if (ctrl[Indice].checked) Valor = ctrl.value; else Valor = false;
                }
                else if (document.getElementById(Objeto) != null && Indice != undefined) {
                    var ctrl = document.getElementById(Objeto);
                    if (ctrl[Indice].checked) Valor = ctrl.value; else Valor = false;
                }
                else if (NroItems != undefined) {
                    for (i = 0; i < NroItems; i++) {
                        ctrl = eval("document." + Formulario + "['" + Objeto + "[" + i + "]']");
                        if (ctrl.checked) if (Valor == false) Valor = ctrl.value; else Valor = Valor + "," + ctrl.value;
                    }
                }
            }
            break;
        case 'SELECT':
            break;
        case 'RADIO':
            if (Propiedad != undefined && Propiedad != 'undefined' && Propiedad != '') {
                if (document.getElementsByName(Objeto) != null) {
                    var ctrl = document.getElementsByName(Objeto);
                    for (i = 0; i < ctrl.length; i++)
                        if (ctrl[i].checked) Valor = eval("ctrl['" + i + "']." + Propiedad);
                }
                else if (document.getElementById(Objeto) != null) {
                    var ctrl = document.getElementById(Objeto);
                    for (i = 0; i < ctrl.length; i++)
                        if (ctrl[i].checked) Valor = eval("ctrl['" + i + "']." + Propiedad);
                }
            }
            else {
                if (document.getElementsByName(Objeto) != null) {
                    var ctrl = document.getElementsByName(Objeto);
                    for (i = 0; i < ctrl.length; i++)
                        if (ctrl[i].checked) Valor = ctrl[i].value;
                }
                else if (document.getElementById(Objeto) != null) {
                    var ctrl = document.getElementById(Objeto);
                    for (i = 0; i < ctrl.length; i++)
                        if (ctrl[i].checked) Valor = ctrl[i].value;
                }
            }
            break;
        default:
            if (Propiedad != undefined && Propiedad != 'undefined' && Propiedad != '') {
                if (document.getElementById(Objeto) != null)
                    Valor = eval("document.getElementById('" + Objeto + "')." + Propiedad);
                else if (document.getElementsByName(Objeto) != null && document.getElementsByName(Objeto).value != undefined)
                    Valor = eval("document.getElementsByName('" + Objeto + "')." + Propiedad);
            }
            else {
                if (document.getElementById(Objeto) != null)
                    Valor = document.getElementById(Objeto).value;
                else if (document.getElementsByName(Objeto) != null && document.getElementsByName(Objeto).value != undefined)
                    Valor = document.getElementsByName(Objeto).value;
            }
            break;
    }
    return Valor;
}