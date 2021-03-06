
<div class="row column-seperation">
  <div class="col-md-12">		
		<div id="coba" class="grid simple transparent">
			<div class="grid-title">
				<h4>List of <span class="semi-bold"><?php echo ucwords(lang('employee'))?></span></h4>
			</div>
		</div>

    <div class="grid-body ">
    	<table class="table table-hover table-condensed" id="table">
        <thead>
          <tr>
            <th style="width:5%">No</th>
            <th style="width:5%">NIK</th>
            <th style="width:15%">Name</th>
            <th>Group</th>
            <th>Grade</th>
            <th>Gender</th>
            <th>DOB</th>
            <th style="width:5%">Action</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
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
          "url": "employee/ajax_list_employee/",
          "type": "POST"
      },
      //Set column definition initialisation properties.
      "columnDefs": [
      {
          "targets": [0], //index column
          "orderable": false, //set not orderable
      },
      {
          "targets": [7], //last column
          "orderable": false, //set not orderable
      },
      ],
  });
});
</script>
<script src="<?php echo assets_url('modules/js/employee.js')?>"></script>