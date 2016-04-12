$(document).ready(function() {
    $('.type_report').change(function(){
    	$("#content").html('<img src="'+$("#base_url").val()+'assets/assets/img/loading.gif"> loading...').load('report/search/'+$(this).val());
    });
    
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
			 url: $("#base_url").val()+"attendance/get_period/"+$(this).val(),
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
    
		
		//Search Ovt
    $(".btn-search").click(function(){
			var act = $("#search_att").attr("action");
    	var dataz = $("#search_att").serialize();
			$("#content").html('<img src="'+$("#base_url").val()+'assets/assets/img/loading.gif"> loading...');
			$.post(act, dataz,  function(response) {
				$("#content").html(response);
		  });
		});
		
		//Back Att
		$(".btn-cancel-ovt").click(function(){
			var rel=$(this).attr("rel");
			$("#content").html('<img src="'+$("#base_url").val()+'assets/assets/img/loading.gif"> loading...').load('report/list_ovt/'+rel);
		});
		
		
});

function loadOvertime()
{
	$("#content").html('<img src="'+$("#base_url").val()+'assets/assets/img/loading.gif"> loading...').load('report/list_ovt');
}

function detailOvertime(id)
{
	var start = $(".start_att").val();
	var end = $(".end_att").val();
  $("#content").html('<img src="'+$("#base_url").val()+'assets/assets/img/loading.gif"> loading...').load('report/detail_ovt/'+id+'/'+start+'~'+end);
}