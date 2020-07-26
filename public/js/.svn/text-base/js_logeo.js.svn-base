function ValidarLogeo() {
	var user = $('#txtuser').val();
	var pass = $('#txtpass').val();
	var nivel = $('#cbnivel').val();
	
	var parameters = {user: escape(user), pass: escape(pass), nivel: nivel};
	var _post = $.post(path + "logeo/validarlogeo/", parameters);
	
	_post.success(function(requestData){
		$("#div_resp_logeo").html(requestData);
	});
}

function acccambiarpasswd(){
	
	
	var passwdant= $('#passwdant').val();
	var pass = $('#passwdnueva').val();
	var passwdnuevaval = $('#passwdnuevaval').val();
	
	if (passwdant==''){
		openDialogWarning("Digite su contrase\u00f1a Actual");
		$('#passwdant').focus();
		return true;
	}
	if (pass==''){
		openDialogError("Digite su Nueva contrase\u00f1a");
		$('#passwdnueva').focus();
		return true;
	}
	/*if (passwdnuevaval==''){
		openDialogError("Digite su contrase\u00f1a");
		$('#passwdnuevaval').focus();
		return true;
	}*/
	
	if (pass!=passwdnuevaval){
		openDialogError("Vuelva a escribir su contrase\u00f1a");
		$('#passwdnuevaval').focus();
	}else{	
		var parameters = {passwd: pass,passwdant:passwdant};
		var _post = $.post(path + "logeo/acccambiarpasswd/", parameters);
		
		_post.success(function(requestData){
			//console.log(requestData);
			json_result= JSON.parse(requestData);
			openDialogInfo(json_result[0][1]);
			console.log(json_result);
			if (json_result[0][0]=='1')
				$('#btnsalir').click();
			
			if (json_result[0][0]=='2')
				$('#passwdant').focus();
		});
	}
}