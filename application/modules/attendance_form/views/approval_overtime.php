
<div class="row column-seperation">
  <div class="col-md-12">
  	<div class="panel-group" id="accordion" data-toggle="collapse">
		  <div class="panel panel-default">
				<div class="panel-heading">
				  <h4 class="panel-title">
					<a class="" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
					   Search
					</a>
				  </h4>
				</div>
				<div id="collapseOne" class="panel-collapse collapse in">
				  <div class="panel-body">
					 <form id="search_ovt" action="<?php echo site_url($path_file.'/approval_overtime');?>" method="post">
		        <div class="row">
		          <div class="col-sm-12" style="padding:0px;">
	              <div class="col-md-2">
                  <label>Period</label>
                  <div class="input-append success no-padding">
                    <input type="text" class="form-control periode s_periode span2" value="<?php echo $period;?>" required>
                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
                  </div>
	              </div>
	              <div class="col-md-2">
                  <label>Date</label>
                  <div id="datepicker_start" class="input-append date success no-padding">
                    <input type="text" class="form-control start_att tgl span2" name="start_att" value="<?php echo $start_date;?>" required>
                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
                  </div>
	              </div>
	              <div class="to">to</div>
	              <div class="col-md-2">
	              	<label>&nbsp;</label>
                  <div id="datepicker_end" class="input-append date success no-padding">
                      <input type="text" class="form-control end_att tgl span2" name="end_att" value="<?php echo $end_date;?>">
                      <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
                  </div>
	              </div>
	
	              <div class="col-md-2">
	                  <label>&nbsp;</label>
	                  <div class="row">
	                    <div class="col-md-12">
	                        <button type="submit" class="btn btn-info btn-search"><i class="fa fa-search"></i></button>
	                    </div>
	                  </div>
	              </div>
		          </div>
		        </div>
		       </form>
				  </div>
				</div>
		  </div>
		</div>
		
		<div class="grid-body ">
    	<table class="table table-hover table-condensed" id="table">
        <thead>
          <tr>
            <th style="width:5%">No</th>
            <th>Date</th>
            <th>Request OT</th>
            <th>Approval OT</th>
            <th>Actual Hour</th>
            <th>Reason</th>
            <th>Feedback</th>
            <th>Status</th>
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
      "scrollY": "290px",
      "order" : [],
      "searching": true,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "scrollX" : true,
      "paging" : false,
      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": $("#base_url").val()+"attendance_form/ajax_list_ovt_app/<?php echo $start_date.'~'.$end_date;?>",
          "type": "POST"
      },
      "createdRow": function ( row, data, index ) {
        if (data[7].match('Waiting')) {
        	$('td', row).addClass('bg_waiting');
        }
      },
      //Set column definition initialisation properties.
      "columnDefs": [
      {
          "targets": [0, 2, 3, 6], //index column
          "orderable": false, //set not orderable
      },
      ],
  });
});
</script>
<script src="<?php echo assets_url('modules/js/attendance_form.js')?>"></script>