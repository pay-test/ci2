
<div class="row column-seperation">
  <div class="col-md-12">
  	<div id="coba" class="grid simple transparent">
			<div class="grid-title">
				<h4>List of <span class="semi-bold"><?php echo ucwords(lang('email_alert'))?></span></h4>
			</div>
		</div>

    <div class="grid-body ">
    	<table class="table table-hover table-condensed" id="table">
        <thead>
          <tr>
            <th style="width:5%">No</th>
            <th style="width:20%">Title</th>
            <th>Template</th>
            <th>Days</th>
            <th style="width:5%">Action</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      
      <div class="clearfix_button pull-right">
      	<br><br>
				<button type="submit" class="btn btn-success" onClick="editAlert('0')" href="javascript:void(0);">&nbsp;Add New</button>
				<br><br>
			</div>
    </div>
  </div>
</div>

<script src="<?php echo assets_url('assets/plugins/data-tables/jquery.dataTables.min.js')?>"></script>
<link href="<?php echo assets_url('assets/plugins/data-tables/datatables.min.css')?>" rel="stylesheet" type="text/css" />
<script>
var table;
$(document).ready(function() {
  //datatables
  table = $('#table').DataTable({
      //"scrollY": "200px",
      "order" : [],
      "searching": false,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "scrollX" : true,
      
      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": "config/ajax_list_email_alert/",
          "type": "POST"
      },
      //Set column definition initialisation properties.
      "columnDefs": [
      {
          "targets": [0,4], //index column
          "orderable": false, //set not orderable
      },
      ],
  });
});
</script>
<script src="<?php echo assets_url('modules/js/config.js')?>"></script>