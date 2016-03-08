$(document).ready(function() {
    //Date Pickers
    $('.tgl').datepicker({
        format: 'yyyy-mm-dd', 
        autoclose: true,
        todayHighlight: true
    });
		
		//Search Edit Att
    $(".btn-submit-emp").click(function(){
    	var act = $("#form_edit_emp").attr("action");
    	var dataz = $("#form_edit_emp").serialize();
			$("#content").html('<img src="assets/assets/img/loading.gif"> loading...');
			$.post(act, dataz,  function(response) {
				$("#content").html(response);
		  	//$("#content").load('employee/list_employee');
		  });
		});
		
		//Back
		$(".btn-cancel-emp").click(function(){
			$("#content").html('<img src="assets/assets/img/loading.gif"> loading...').load('employee/list_employee');
		});
});

function loadEmp()
{
	$("#content").html('<img src="assets/assets/img/loading.gif"> loading...').load('employee/list_employee');
}

function detailEmp(id)
{
	$("#content").html('<img src="assets/assets/img/loading.gif"> loading...').load('employee/detail_employee/'+id);
}