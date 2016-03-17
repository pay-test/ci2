
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
					 <form id="search_att" action="<?php echo site_url($path_file.'/list_ovt');?>" method="post">
		        <div class="row">
		          <div class="col-sm-12" style="padding:0px;margin-bottom:15px;">
	              <div class="col-md-2">
                  <label>Reguler/Shift</label>
                  <?php echo form_dropdown("s_regs", $opt_regs, $s_regs, "class='span2_2'");?>
	              </div>
	              <div class="col-md-2">
                  <label>Division</label>
                  <?php echo form_dropdown("s_div", $opt_divisi, $s_div, "class='span2_2' onChange='get_section(this.value);'");?>
	              </div>
	              <div class="col-md-2">
                  <label>Section</label>
                  <div id="id_section">
                  	<?php echo form_dropdown("s_sec", array(""=> "- Section -"), "", "class='span2_2'");?>
                	</div>
	              </div>
	              <div class="col-md-2">
                  <label>Position Level</label>
                  <!--<div id="id_position">-->
                  	<?php echo form_dropdown("s_pos", $opt_pos, $s_pos, "class='span2_2'");?>
                	<!--</div>-->
	              </div>
	              <div class="col-md-2">
                  <label>Grade</label>
                  <?php echo form_dropdown("s_grade", $opt_grade, $s_grade, "class='span2_2'");?>
	              </div>
	            </div>
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

var id_division = '<?php echo $s_div;?>';
var id_section = '<?php echo $s_sec;?>';
if(id_division > 0) get_section(id_division, id_section);
function get_section(val, val_2)
{
	$.post('<?php echo base_url();?>load/get_section',{id_division: val, id_section: val_2},function(data){
		$("#id_section").html(data);
	});
}
</script>
<script src="<?php echo assets_url('modules/js/'.$path_file.'.js');?>"></script>