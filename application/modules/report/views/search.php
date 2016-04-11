<form id="search_att" action="<?php echo site_url($path_file.'/'.$url);?>" method="post" target="_blank">
<div class="row">
	<?php
	if($url) {
  	$exp = explode("\r\n", GetValue("attrib", "kg_menu_report", array("url"=> "where/".$url)));
  	foreach($exp as $key=> $e) {
  		echo "<div class='col-sm-12' style='padding:0px;margin-bottom:15px;'>";
  		$exp_2 = explode(", ", $e);
  		foreach($exp_2 as $f) {
  			$this->load->view('report/s_'.$f);
  		}
  		if($key==0) echo "</div>";
  	}
  	?>
      <div class="col-md-2">
          <label>&nbsp;</label>
          <div class="row">
            <div class="col-md-12">
              <button type="submit" class="btn btn-info btn-search"><i class="fa fa-search"></i></button>
            </div>
          </div>
      </div>
    <?php
    if($key > 0) echo "</div>";
  }
  ?>
</div>
</form>

<script>
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
});

function get_section(val, val_2)
{
	$.post('<?php echo base_url();?>load/get_section',{id_division: val, id_section: val_2},function(data){
		$("#id_section").html(data);
	});
}
</script>