panelPersona = function(parameters, functionSuccess) {
    _post = $.post(path + "panel/persona", parameters);
    _post.success(function(request){  	
        $("#layoutPanelInformativo").html(request);
        if(functionSuccess != undefined && functionSuccess != null && functionSuccess != false) {
        	functionSuccess();
        }
    });
};

