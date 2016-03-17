$(document).ready(function() {
    $('.tgl').datepicker({
        format: 'yyyy-mm-dd', 
        autoclose: true,
        todayHighlight: true
    });
    
    $('.periode').datepicker({
        format: 'M yyyy', 
        minViewMode: 1,
				autoclose: true,
				todayHighlight: true
    });
    
    $('.s_periode').change(function(){
    	$.ajax({
			 type: "POST",
			 url: $("#base_url").val()+"attendance_form/get_period/"+$(this).val(),
			 data: 'flag=hitung',
			 cache: false,
			 success: function(data) 
			 {
				$(".start_att").val(data.substr(0,10));
    		$(".end_att").val(data.substr(11,10));
			 }
			});
    });
    
    $('.tgl_limit').change(function(){
    	$.ajax({
			 type: "POST",
			 url: $("#base_url").val()+"attendance_form/get_current_schedule/"+$(this).val(),
			 data: 'flag=hitung',
			 cache: false,
			 success: function(data) 
			 {
				$("#time_in").val(data.substr(0,5));
    		$("#time_out").val(data.substr(6,5));
    		$("#scan_in").val(data.substr(12,5));
    		$("#scan_out").val(data.substr(18,5));
			 }
			});
			
			$.ajax({
			 type: "POST",
			 url: $("#base_url").val()+"attendance_form/get_detail_ot/"+$(this).val(),
			 data: 'flag=hitung',
			 cache: false,
			 success: function(data) 
			 {
				/*$("#time_in").val(data.substr(0,5));
    		$("#time_out").val(data.substr(6,5));
    		$("#scan_in").val(data.substr(12,5));
    		$("#scan_out").val(data.substr(18,5));*/
			 }
			});
    });

    //Time pickers
    $('.clockpicker ').clockpicker({
       autoclose: true
    });
    
    //Search
    $(".btn-search").click(function(){
			var act = $("#search_ovt").attr("action");
    	var dataz = $("#search_ovt").serialize();
			$("#content").html('<img src="'+$("#base_url").val()+'assets/assets/img/loading.gif"> loading...');
			$.post(act, dataz,  function(response) {
				$("#content").html(response);
		  });
		});
		
		$(".btn-back").click(function(){
			$("#content").html('<img src="'+$("#base_url").val()+'assets/assets/img/loading.gif"> loading...').load($("#base_url").val()+'attendance_form/approval_overtime');
		});
    
});


function loadFormOvt(flag="")
{
	$("#content").html('<img src="'+$("#base_url").val()+'assets/assets/img/loading.gif"> loading...').load($("#base_url").val()+'attendance_form/form_overtime/'+flag);
}

function loadHistoryOvt()
{
	$("#content").html('<img src="'+$("#base_url").val()+'assets/assets/img/loading.gif"> loading...').load($("#base_url").val()+'attendance_form/history_overtime');
}

function loadApprovalOvt()
{
	$("#content").html('<img src="'+$("#base_url").val()+'assets/assets/img/loading.gif"> loading...').load($("#base_url").val()+'attendance_form/approval_overtime');
}

function detailOvt(flag="")
{
	$("#content").html('<img src="'+$("#base_url").val()+'assets/assets/img/loading.gif"> loading...').load($("#base_url").val()+'attendance_form/form_overtime/'+flag);
}