
	function cekLogin(){
		$.ajax({
	        url : "login/cek_login/",
	        type: "POST",
	        data: $('#form').serialize(),
	        dataType: "JSON",
	        success: function(data)
	        {
	            if(data.status)
	            {
	                location.href='dashboard';
	            }
	            else
	            {
	            	$("#error").fadeIn(5000);
	            	$("#error").fadeOut("slow");
	                //$("#error").fadeout(13000).fadeout(13000);
	                
	            }
	        },
	        error: function (jqXHR, textStatus, errorThrown)
	        {
	            alert('Ups..!!, Something Wrong');
	        }
    	});
	}