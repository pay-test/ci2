<script src="<?php echo assets_url('assets/plugins/jquery/jquery-1.11.3.min.js')?>" type="text/javascript"></script>
<div id="coba" class="grid simple transparent">
	<div class="grid-title">
		<h4>List of <span class="semi-bold">Overtime</span></h4>
		<div class="actions">
			<a href="javascript:void(0);" onclick="window.location='<?php echo site_url("report/export_ovt_rekap/".$start_date."~".$end_date."/".$s_regs."/".$s_div."/".$s_sec."/".$s_pos."/".$s_grade);?>'"> Export Rekap</a> |
			<a href="javascript:void(0);" onclick="window.location='<?php echo site_url("report/export_ovt_full/".$start_date."~".$end_date."/".$s_regs."/".$s_div."/".$s_sec."/".$s_pos."/".$s_grade);?>'"> Export Full</a>
		</div>
	</div>
</div>

<div class="grid-body ">
	<table class="table table-hover table-condensed" id="table">
    <thead>
      <tr>
        <th style="width:5%">No</th>
        <th style="width:5%">NIK</th>
        <th style="width:15%">Name</th>
        <th>Period</th>
        <th>Actual Hours</th>
        <th>Calculation Hours</th>
        <th>Overtime Rasio</th>
        <th>Amount</th>
        <th style="width:5%">Action</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>

<script src="<?php echo assets_url('assets/plugins/data-tables/jquery.dataTables.min.js')?>"></script>
<link href="<?php echo assets_url('assets/plugins/data-tables/datatables.min.css')?>" rel="stylesheet" type="text/css" />
<script>
var table;
$(document).ready(function() {
  //datatables
  table = $('#table').DataTable({
      "scrollY": "290px",
      "order" : [],
      "searching": true,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "scrollX" : true,
      
      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": "report/ajax_list_ovt/<?php echo $start_date.'~'.$end_date.'/'.$s_regs.'/'.$s_div.'/'.$s_sec.'/'.$s_pos.'/'.$s_grade;?>",
          "type": "POST"
      },
      //Set column definition initialisation properties.
      "columnDefs": [
      {
          "targets": [0,6,7,8], //index column
          "orderable": false, //set not orderable
      },
      ],
  });
});
</script>