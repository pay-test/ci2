$(document).ready(function() {
    //Date Pickers
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
			 url: "attendance/get_period/"+$(this).val(),
			 data: 'flag=hitung',
			 cache: false,
			 success: function(data) 
			 {
				$(".start_att").val(data.substr(0,10));
    		$(".end_att").val(data.substr(11,10));
			 }
			});
    });

    //Time pickers
    $('.clockpicker ').clockpicker({
       autoclose: true
    });
    
    //Search Att
    $(".btn-search").click(function(){
			var act = $("#search_att").attr("action");
    	var dataz = $("#search_att").serialize();
			$("#content").html('<img src="assets/assets/img/loading.gif"> loading...');
			$.post(act, dataz,  function(response) {
				$("#content").html(response);
		  	//$("#content").load('attendance/list_attendance');
		  });
		});
		
		//Submit Edit Att
    $(".btn-submit-att").click(function(){
    	var rel=$(this).attr("rel");
    	var act = $("#form_edit_att").attr("action");
    	var dataz = $("#form_edit_att").serialize();
			$("#detail_att").html('<img src="assets/assets/img/loading.gif"> loading...');
			$.post(act, dataz,  function(response) {
				//$("#content").html(response);
		  	$("#content").load('attendance/detail_attendance/'+rel);
		  });
		});
		
		//Back Att
		$(".btn-cancel-att").click(function(){
			var rel=$(this).attr("rel");
			$("#content").html('<img src="assets/assets/img/loading.gif"> loading...').load('attendance/list_attendance/'+rel);
		});
		
		$(".btn-cancel-att-detail").click(function(){
			var rel=$(this).attr("rel");
			$("#content").html('<img src="assets/assets/img/loading.gif"> loading...').load('attendance/detail_attendance/'+rel);
		});
		
		//Search Shift
    $(".btn-search-shift").click(function(){
			var period = $("#periode").val();
			$("#content").html('<img src="assets/assets/img/loading.gif"> loading...');
			
			$.ajax({
			 type: "POST",
			 url: "attendance/shift/0/"+period,
			 data: 'flag=hitung',
			 cache: false,
			 success: function(data) 
			 {
				$("#content").html(data);
			 }
			});
		});
		
		//Search Edit Shift
    $(".btn-submit-shift").click(function(){
    	var act = $("#form_edit_shift").attr("action");
    	var dataz = $("#form_edit_shift").serialize();
			$("#content").html('<img src="assets/assets/img/loading.gif"> loading...');
			
			$.ajax({
			 type: "POST",
			 url: act,
			 data: dataz,
			 cache: false,
			 success: function(data) 
			 {
				$("#content").html(data);
			 }
			});
		});
		
		//Back Shift
		$(".btn-cancel-shift").click(function(){
			var rel=$(this).attr("rel");
			$("#content").html('<img src="assets/assets/img/loading.gif"> loading...').load('attendance/list_shift/'+rel);
		});
		
		//Search Ovt
    $(".btn-search-ovt").click(function(){
			var start = $(".start_att").val();
			var end = $(".end_att").val();
			$("#content").html('<img src="assets/assets/img/loading.gif"> loading...');
			
			$.ajax({
			 type: "POST",
			 url: "attendance/overtime/0/"+start+"~"+end,
			 data: 'flag=hitung',
			 cache: false,
			 success: function(data) 
			 {
				$("#content").html(data);
			 }
			});
		});
		
		$(".detail-OT").click(function(){
			var rel = $(this).attr("rel");
			var rel_temp = $("#temp_open").attr("rel");
			//$("#listz-"+rel).find("td").attr("style", "font-weight:bold;");
			if(rel_temp) $("#OT"+rel_temp).slideUp(500);
			$("#OT"+rel).slideDown(500);
			$("#temp_open").attr("rel", rel);
			//$("#listz-"+rel_temp).find("td").attr("style", "font-weight:normal;");
		});
});

/* ATT */
function loadAttendance()
{
    $("#content").html('<img src="assets/assets/img/loading.gif"> loading...').load('attendance/list_attendance');
}

function detailAtt(id)
{
	var start = $(".start_att").val();
	var end = $(".end_att").val();
  $("#content").html('<img src="assets/assets/img/loading.gif"> loading...').load('attendance/detail_attendance/'+id+'/'+start+'~'+end);
}

function editAtt(id, period)
{
  $("#detail_att").html('<img src="assets/assets/img/loading.gif"> loading...').load('attendance/edit_attendance/'+id+'/'+period);
}


function loadShift()
{
    $("#content").html('<img src="assets/assets/img/loading.gif"> loading...').load('attendance/list_shift');
}

function detailShift(id, tgl)
{
	$("#content").html('<img src="assets/assets/img/loading.gif"> loading...').load('attendance/detail_shift/'+id+'/'+tgl);
}

function loadOvertime()
{
    $("#content").html('<img src="assets/assets/img/loading.gif"> loading...').load('attendance/list_ovt');
}

function detailOvertime(id)
{
	var start = $(".start_att").val();
	var end = $(".end_att").val();
  $("#content").html('<img src="assets/assets/img/loading.gif"> loading...').load('attendance/detail_ovt/'+id+'/'+start+'~'+end);
}

function backAtt(start, end)
{
	$("#content").html('<img src="assets/assets/img/loading.gif"> loading...').load('attendance/list_attendance/0/'+start+'~'+end);
}

$('.ot_inc').change(function(){
	var jam = $(this).val();
	var hr = $("#hariraya").is(':checked');
	if(jam > 0) {
		overtime = (jam * 2) - 0.5;
	} else overtime = 0;
	
	$("#acc_ot_incidental").attr("value", overtime);
});