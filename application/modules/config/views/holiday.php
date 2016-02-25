
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
					 <form id="search" action="<?php echo site_url($path_file.'/holiday_list');?>" method="post">
		        <div class="row">
		          <div class="col-sm-12" style="padding:0px;margin-bottom:15px;">
	              <div class="col-md-2">
                  <label>Year</label>
                  <div class="input-append success no-padding">
                    <input type="text" class="form-control periode s_tahun span2" id="years" name="s_year" value="<?php echo $s_year;?>" required>
                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
                  </div>
	              </div>
	              
	              <div class="col-md-1">
                  <label>&nbsp;</label>
                  <div class="row">
                    <div class="col-md-12">
                    	<button type="submit" class="btn btn-info btn-search-holiday"><i class="fa fa-search"></i></button>
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
		
		<div id="coba" class="grid simple transparent">
			<div class="grid-title">
				<h4>List of <span class="semi-bold"><?php echo ucwords(lang('holiday'))?></span></h4>
			</div>
		</div>

    <div class="grid-body ">
    	<table class="table table-hover table-condensed" id="table">
        <thead>
          <tr>
            <th style="width:5%">No</th>
            <th style="width:20%">Date</th>
            <th>Description</th>
            <th style="width:5%">Action</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      
      <div class="clearfix_button pull-right">
      	<br><br>
				<button type="submit" class="btn btn-success" onClick="editHoliday('0')" href="javascript:void(0);">&nbsp;Add New</button>
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
          "url": "config/ajax_list_holiday/<?php echo $s_year;?>",
          "type": "POST"
      },
      //Set column definition initialisation properties.
      "columnDefs": [
      {
          "targets": [0], //index column
          "orderable": false, //set not orderable
      },
      {
          "targets": [3], //last column
          "orderable": false, //set not orderable
      },
      ],
  });
});
</script>
<script src="<?php echo assets_url('modules/js/config.js')?>"></script>