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
			 	var expl = explode("~", data);
			 	$("#time_in").val(expl[0]);
    		$("#time_out").val(expl[1]);
    		$("#scan_in").val(expl[2]);
    		$("#scan_out").val(expl[3]);
    		$("#notif_error").html(expl[4]);
    		if(expl[4]) $(".btn-success").attr("disabled",true);
    		else $(".btn-success").attr("disabled",false);
				/*$("#time_in").val(data.substr(0,5));
    		$("#time_out").val(data.substr(6,5));
    		$("#scan_in").val(data.substr(12,5));
    		$("#scan_out").val(data.substr(18,5));*/
			 }
			});
			
			/*$.ajax({
			 type: "POST",
			 url: $("#base_url").val()+"attendance_form/get_detail_ot/"+$(this).val(),
			 data: 'flag=hitung',
			 cache: false,
			 success: function(data) 
			 {
				$("#time_in").val(data.substr(0,5));
    		$("#time_out").val(data.substr(6,5));
    		$("#scan_in").val(data.substr(12,5));
    		$("#scan_out").val(data.substr(18,5));
			 }
			});*/
    });
    
    $('.tgl_limit_cuti').change(function(){
    	var tgl1 = $(".tgl_start").val();
    	var tgl2 = $(".tgl_end").val();
    	if(tgl2) {
    		var selisih = hitungSelisihHari(tgl1, tgl2);
    		$("#durasi").html(selisih);
    		$(".duration").val(selisih);
    		
    		if(selisih > $(".max_leave").val()) {
	    		$("#notif_error").html("Leave Duration not allowed");
	    		$(".btn-success").attr("disabled",true);
	    	} else {
	    		$("#notif_error").html("");
	    		$(".btn-success").attr("disabled",false);
	    	}
    	}
    });


    //Time pickers
    $('.clockpicker ').clockpicker({
       autoclose: true
    });
    $('.clock_start').change(function(){
    	var time_out = $("#time_out").val();
    	if($(this).val() < time_out) {
    		$("#notif_error2").html("Start Overtime not allowed");
    		$(".btn-success").attr("disabled",true);
    	} else {
    		$("#notif_error2").html("");
	    	$(".btn-success").attr("disabled",false);
    	}
    });
    $('.clock_end').change(function(){
    	var scan_out = $("#scan_out").val();
    	if($(this).val() > scan_out) $(".clock_end").val(scan_out);
    	var clock_start = $(".clock_start").val();
    	if($(this).val() <= clock_start) {
    		$("#notif_error2").html("End Overtime not allowed");
    		$(".btn-success").attr("disabled",true);
    	} else {
    		$("#notif_error2").html("");
	    	$(".btn-success").attr("disabled",false);
    	}
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
		
		$(".btn-search-cuti").click(function(){
			var act = $("#search_cuti").attr("action");
    	var dataz = $("#search_cuti").serialize();
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


function loadFormCuti(flag="")
{
	$("#content").html('<img src="'+$("#base_url").val()+'assets/assets/img/loading.gif"> loading...').load($("#base_url").val()+'attendance_form/form_cuti/'+flag);
}

function loadHistoryCuti()
{
	$("#content").html('<img src="'+$("#base_url").val()+'assets/assets/img/loading.gif"> loading...').load($("#base_url").val()+'attendance_form/history_cuti');
}

function loadApprovalCuti()
{
	$("#content").html('<img src="'+$("#base_url").val()+'assets/assets/img/loading.gif"> loading...').load($("#base_url").val()+'attendance_form/approval_cuti');
}

function detailCuti(flag="")
{
	$("#content").html('<img src="'+$("#base_url").val()+'assets/assets/img/loading.gif"> loading...').load($("#base_url").val()+'attendance_form/form_cuti/'+flag);
}


function hitungSelisihHari(tgl1, tgl2){
  // varibel miliday sebagai pembagi untuk menghasilkan hari
  var miliday = 24 * 60 * 60 * 1000;
  //buat object Date
  var tanggal1 = new Date(tgl1);
  var tanggal2 = new Date(tgl2);
  // Date.parse akan menghasilkan nilai bernilai integer dalam bentuk milisecond
  var tglPertama = Date.parse(tanggal1);
  var tglKedua = Date.parse(tanggal2);
  var selisih = (tglKedua - tglPertama) / miliday;
  return selisih;
}