$(document).ready(function() {
    //Date Pickers
    $('.tgl').datepicker({
        format: 'yyyy-mm-dd', 
        autoclose: true,
        todayHighlight: true
    });
    
    $('#years').datepicker({
        format: 'yyyy', 
        minViewMode: 2,
				autoclose: true,
				todayHighlight: true
    });
		
		$(".btn-submit").click(function(){
    	var act = $("#form_edit_config").attr("action");
    	var dataz = $("#form_edit_config").serialize();
			$("#content").html('<img src="assets/assets/img/loading.gif"> loading...');
			$.post(act, dataz,  function(response) {
				//$("#content").html(response);
		  	$("#content").load('config/detail');
		  });
		});
		
		$(".btn-search-holiday").click(function(){
			var act = $("#search").attr("action");
    	var dataz = $("#search").serialize();
			$("#content").html('<img src="assets/assets/img/loading.gif"> loading...');
			$.post(act, dataz,  function(response) {
				$("#content").html(response);
		  });
		});
		
		$(".btn-submit-holiday").click(function(){
    	var act = $("#form_edit_holiday").attr("action");
    	var dataz = $("#form_edit_holiday").serialize();
			$("#content").html('<img src="assets/assets/img/loading.gif"> loading...');
			$.post(act, dataz,  function(response) {
				//$("#content").html(response);
		  	$("#content").load('config/holiday_list');
		  });
		});
});

function loadConfig()
{
    $("#content").html('<img src="assets/assets/img/loading.gif"> loading...').load('config/detail');
}

function loadHoliday()
{
    $("#content").html('<img src="assets/assets/img/loading.gif"> loading...').load('config/holiday_list');
}

function editHoliday(id)
{
  $("#content").html('<img src="assets/assets/img/loading.gif"> loading...').load('config/holiday_edit/'+id);
}