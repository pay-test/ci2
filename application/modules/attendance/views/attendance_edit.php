<div class="row column-seperation">
  <div class="col-md-12">
  	<div class="grid simple transparent">
			<div class="grid-title">
				<h4>Edit of <span class="semi-bold">Attendance</span></h4></a>
			</div>
		</div>
		
		<form id="form_edit_att" action="attendance/update_att" method="post" enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?php echo $val['id'];?>">
		<input type="hidden" name="id_employee" value="<?php echo $val['id_employee'];?>">
		<!--<table class="table table-striped">-->
		<div class="row">
		  <div class="col-md-7">
		    <div class="grid simple">
		      <div class="grid-body no-border">
		        <div class="row">
		          <div class="col-md-12 col-sm-12 col-xs-12">
		           	<div class="form-group">
		              <label class="form-label">Date</label>
		              <div class="row">
		              	<div class="col-md-12">
		                  <div id="datepicker_start" class="input-append date success no-padding">
		                  	<input type="text" class="tgl form-control" value="<?php echo $tgl;?>" name="tgl" required>
		                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
		                  </div>
		                </div>
		              </div>
		            </div>
		            <div class="form-group">
		              <label class="form-label">Attendance</label>
		              <div class="row">
			              <div class="col-md-12">
			                <div class="radio">
			                	<?php for($i=0;$i<count($absensi);$i++):
				                	$chk = $val[$absensi[$i][0]]==1 ? "checked='checked'" : ""; ?>
				                  <input id="<?php echo $absensi[$i][0]?>" type="radio" name="absen" value="<?php echo $absensi[$i][0]?>" <?php echo $chk;?>>
				                  <label for="<?php echo $absensi[$i][0]?>"><?php echo $absensi[$i][1]?></label>
				              	<?php endfor?>
			                </div>
			              </div>
				          </div>
		            </div>
	            </div>
	          </div>
	        </div>
	      </div>
		  </div>
		
		  <div class="col-md-5">
		    <div class="grid simple">
		      <div class="grid-body no-border">
		        <div class="row">
		          <div class="col-md-12 col-sm-12 col-xs-12">
		            <div class="form-group">
		          		<div class="row">
			            	<div class="col-md-12">
			                <label class="form-label">Scan In</label>
			                <div class="input-group transparent clockpicker col-md-4">
			                  <input type="text" class="form-control" placeholder="Pick a time" name="scan_masuk" value="<?php echo $val['scan_masuk'];?>">
			                  <span class="input-group-addon ">
			                   <i class="fa fa-clock-o"></i>
			                  </span>
			                </div>
			            	</div>
			            </div>
		            </div>
		            <div class="form-group">
		          		<div class="row">
			            	<div class="col-md-12">
			            		<label class="form-label">Scan Out</label>
			                <div class="input-group transparent clockpicker col-md-4">
			                  <input type="text" class="form-control" placeholder="Pick a time" name="scan_pulang" value="<?php echo $val['scan_pulang'];?>">
			                  <span class="input-group-addon ">
			                   <i class="fa fa-clock-o"></i>
			                  </span>
			                </div>
			              </div>
			            </div>
		            </div>
		          </div>
		        </div>
		      </div>
		    </div>
		  </div>
		  
		  <div class="clearfix_button pull-right">
				<button type="submit" class="btn btn-success btn-submit-att" rel="<?php echo $param_btn;?>">&nbsp;Submit</button>
				<button type="submit" class="btn btn-cancel-att-detail" rel="<?php echo $param_btn;?>">&nbsp;Back</button>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>
			</div>
		</div>
		</form>
	
	</div>
</div>

<script src="<?php echo assets_url('modules/js/attendance.js')?>"></script>