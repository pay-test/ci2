
<div class="row column-seperation">
  <div class="col-md-12">
  	<div class="col-md-12">
		    <div class="row">
		        <div class="tiles white col-md-12 no-padding info_header">         
	            <div class="tiles-body">
	            	<?php
	            	foreach($emp->result_array() as $r) {
              	?>
	            	<div class="col-md-2 pull-left">
                  <div class="col-md-12">
                  	<?php
                  	if(file_exists('assets/assets/img/profiles/PICTURE_'.$r['person_id'].'.JPG')) {?>
                  	<img height="135" src="<?php echo assets_url('assets/img/profiles/PICTURE_'.$r['person_id'].'.JPG')?>" alt="<?php echo $r['person_nm'];?>" title="<?php echo $r['person_nm'];?>">
                  	<?php } else { ?>
                  	<img height="135" src="<?php echo assets_url('assets/img/profiles/photo-default.png')?>" alt="<?php echo $r['person_nm'];?>" title="<?php echo $r['person_nm'];?>">
                  	<?php }?>
                  </div>
                </div>
                    
                <div class="col-md-5">
                  <div class="col-md-3">
                      <span class="semi-bold">Name</span>
                  </div>
                  <div class="col-md-9">
                      <span>: <?php echo $r['person_nm'];?></span>
                  </div>
                  <br/><br/>
                  <div class="col-md-3">
                      <span class="semi-bold">Division</span>
                  </div>
                  <div class="col-md-9">
                      <span>: <?php echo GetDivision($r['org_id']);?></span>
                  </div>
                  <br/><br/>
                  <div class="col-md-3">
                      <span class="semi-bold">Section</span>
                  </div>
                  <div class="col-md-9">
                      <span>: <?php echo $r['org_nm'];?></span>
                  </div>
                  <br/><br/>
                  <div class="col-md-3">
                      <span class="semi-bold">Job Title</span>
                  </div>
                  <div class="col-md-9">
                      <span>: <?php echo $r['job_nm'];?></span>
                  </div>
                </div>
                
                <div class="col-md-5">
                  <div class="col-md-3">
                      <span class="semi-bold">NIK</span>
                  </div>
                  <div class="col-md-9">
                      <span>: <?php echo $r['ext_id'];?></span>
                  </div>
                  <br/><br/>
                  <div class="col-md-3">
                      <span class="semi-bold">Group</span>
                  </div>
                  <div class="col-md-9">
                      <span>: <?php echo CekGroup($r['group_shift']);?></span>
                  </div>
                  <br/><br/>
                  <div class="col-md-3">
                      <span class="semi-bold">Grade</span>
                  </div>
                  <div class="col-md-9">
                      <span>: <?php echo $r['grade_job_class'];?></span>
                  </div>
                  <br/><br/>
                  <div class="col-md-3">
                      <span class="semi-bold">Period</span>
                  </div>
                  <div class="col-md-9">
                      <span>: <?php echo GetPeriodFull($period);?></span>
                  </div>
                </div>
                <?php
                }
                ?>
	            </div>
		        </div>
		    </div>
		</div>
		
		<!--<div class="grid simple transparent">
			<div class="grid-title">
				<h4>Attendance <span class="semi-bold">Detail</span></h4>
				<div class="actions">
					<a href="javascript:void(0);" onclick="backAtt('<?php echo $period;?>')"><i class='fa fa-chevron-circle-left'></i> Back</a>
				</div>
			</div>
		</div>-->

    <div class="grid-body ">
    	<table class="table table-hover table-condensed" id="table">
        <thead>
          <tr>
            <th style="width:5%">No</th>
            <th style="width:15%">Date</th>
            <th>Actual Hour</th>
            <th>Calculation Hour</th>
            <th>Reason</th>
            <th>Detail Reason</th>
            <!--<th style="width:5%">Action</th>-->
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
    
    <div class="clearfix_button pull-right">
			<button type="submit" class="btn btn-cancel-ovt" rel="<?php echo urldecode($period);?>">&nbsp;Back</button>
		</div>
  </div>
</div>

<!--<script src="<?php echo assets_url('assets/plugins/data-tables/jquery.dataTables.min.js')?>"></script>-->
<link href="<?php echo assets_url('assets/plugins/data-tables/datatables.min.css')?>" rel="stylesheet" type="text/css" />
<script>
var table;
$(document).ready(function() {
  //datatables
  table = $('#table').DataTable({
  		//"lengthMenu": [[5, 30, -1], [5, 30, "All"]],
      "scrollY": "290px",
      "order" : [],
      "searching": false,
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "scrollX" : true, "paging": false,
      

      // Load data for the table's content from an Ajax source
      "ajax": {
          "url": "report/ajax_list_detail_ovt/<?php echo $period.'/'.$id_emp;?>",
          "type": "POST"
      },
			"createdRow": function ( row, data, index ) {
        /*if (data[10].match('OFF')) {
        	$('td', row).addClass('bg_libur');
        }*/
      },
      //Set column definition initialisation properties.
      "columnDefs": [
      {
          "targets": [0], //index column
          "orderable": false, //set not orderable
      }
      ],
      /*"initComplete": function () {
        $("tr td:contains('Sun,')").each(function(){
			      $(this).parent().find('td').addClass("bg_libur");
				});
      }*/
  });
});
</script>
<script src="<?php echo assets_url('modules/js/'.$path_file.'.js');?>"></script>
