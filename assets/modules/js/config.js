$(document).ready(function() {
    $('.select2').select2();

    //Date Pickers
    $('.tgl').datepicker({
        format: 'yyyy-mm-dd', 
        autoclose: true,
        todayHighlight: true
    });
    
    $('#periode').datepicker({
        format: 'M yyyy', 
        minViewMode: 1,
				autoclose: true,
				todayHighlight: true
    });
    
    //Time pickers
    $('.clockpicker ').clockpicker({
        autoclose: true
    });
		
		//Search Edit Config
    $(".btn-submit").click(function(){
    	var act = $("#form_edit_config").attr("action");
    	var dataz = $("#form_edit_config").serialize();
			$("#content").html('<img src="assets/assets/img/loading.gif"> loading...');
			$.post(act, dataz,  function(response) {
				//$("#content").html(response);
		  	$("#content").load('config/detail');
		  });
		});
});

function loadConfig()
{
    $("#content").html('<img src="assets/assets/img/loading.gif"> loading...').load('config/detail');
}