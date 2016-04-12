<div class="row column-seperation">
  <div class="col-md-12">
  	<div id="coba" class="grid simple transparent">
			<div class="grid-title">
				<h4>Form <span class="semi-bold"><?php echo ucwords(lang('overtime'))?></span></h4></a>
			</div>
		</div>
		
		<form id="form_edit_att" action="<?php echo site_url('attendance_form/update_overtime');?>" method="post" enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?php echo $id;?>">
		<input type="hidden" name="id_emp" value="<?php echo $id_emp;?>">
		<!--<table class="table table-striped">-->
		<div class="row">
			<?php
			if($ovt_status=="Approve" || $ovt_status=="Reject") $dis="disabled";
			else $dis="";
			$flashmessage = $this->session->userdata('message');
			if($flashmessage) {
				?>
				<div class="col-md-12">
					<div class="col-md-12">
						<div class="alert alert-error">
			        <button data-dismiss="alert" class="close"></button>
			        <?php 
			        echo $flashmessage;
							$this->session->unset_userdata('message');
							?>
						</div>
					</div>
	      </div>
	      <?php
	    }
	    ?>

		  <div class="col-md-6">
		    <div class="grid simple">
		      <div class="grid-body no-border">
		        <div class="row">
		          <div class="col-md-12 col-sm-12 col-xs-12">
		           	<?php
		           	if($flag) {?>
		           	<div class="form-group">
		           		<div class="row">
			           		<div class="col-md-12">
				            	<div class="col-md-6 no-padding">
					              <label class="form-label"><b>Employee</b></label>
					              <div class="row">
					              	<div class="col-md-12">
					                  <?php echo $employee_nm;?>
					                </div>
					              </div>
					            </div>
					            <div class="col-md-6 no-padding">
					            	<label class="form-label"><b>Date</b></label>
					              <div class="row">
					              	<div class="col-md-12">
					                  <?php echo FormatTanggalShort($tgl);?>
					                  <input type="hidden" name="date_full" value="<?php echo $tgl;?>">
					                </div>
					              </div>
					            </div>
					          </div>
					        </div>
		            </div>
		            <?php } else {?>
		            
		           	<div class="form-group">
		              <label class="form-label">Date</label>
		              <div class="row">
		              	<div class="col-md-12">
		                  <div id="datepicker_start" class="input-append date success no-padding">
		                  	<input type="text" class="tgl_limit form-control" value="<?php echo $tgl;?>" name="date_full" required <?php echo $dis;?>>
		                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
		                  </div>
		                </div>
		              </div>
		            </div>
		          	<?php }?>
		            <div class="form-group">
		          		<div class="row">
			            	<div class="col-md-12">
			            		<div class="col-md-12 no-padding">
			            			<label class="form-title"><b><?php if(!$flag) echo "Your";?> Current Schedule</b></label>
			            		</div>
			            		<div class="col-md-6 no-padding">
				                <label class="form-label">Time In</label>
				                <div class="input-group transparent col-md-10">
				                  <input type="text" class="form-control" id="time_in" name="time_in" value="<?php echo $time_in;?>" readonly >
				                  <span class="input-group-addon ">
				                   <i class="fa fa-clock-o"></i>
				                  </span>
				                </div>
				              </div>
				              <div class="col-md-6 no-padding">
				                <label class="form-label">Time Out</label>
				                <div class="input-group transparent col-md-10">
				                  <input type="text" class="form-control" id="time_out" name="time_out" value="<?php echo $time_out;?>" readonly >
				                  <span class="input-group-addon ">
				                   <i class="fa fa-clock-o"></i>
				                  </span>
				                </div>
				              </div>
				              <div class="col-md-6 no-padding">
				                <label class="form-label">Actual In</label>
				                <div class="input-group transparent col-md-10">
				                  <input type="text" class="form-control" id="scan_in" name="scan_in" value="<?php echo $scan_in;?>" readonly >
				                  <span class="input-group-addon ">
				                   <i class="fa fa-clock-o"></i>
				                  </span>
				                </div>
				              </div>
				              <div class="col-md-6 no-padding">
				                <label class="form-label">Actual Out</label>
				                <div class="input-group transparent col-md-10">
				                  <input type="text" class="form-control" id="scan_out" name="scan_out" value="<?php echo $scan_out;?>" readonly >
				                  <span class="input-group-addon ">
				                   <i class="fa fa-clock-o"></i>
				                  </span>
				                </div>
				              </div>
				              
				              <div class="col-md-12 no-padding">
			            			<label id="notif_error"></label>
			            		</div>
			            	</div>
			            </div>
		            </div>
		            
		            <div class="form-group">
		          		<div class="row">
			            	<div class="col-md-12">
			            		<div class="col-md-12 no-padding">
			            			<label class="form-title"><b><?php if(!$flag) echo "Your";?> Overtime Schedule <?php if($revisi) echo "<span class='red' style='display:inline;'>( change by supevisor )</span>";?></b></label>
			            		</div>
			            		<div class="col-md-6 no-padding">
				                <label class="form-label">Start Overtime</label>
				                <div class="input-group transparent clockpicker col-md-10">
				                  <input type="text" style="border-color:#0000ff;" class="form-control clock_start" placeholder="Pick a time" name="start_ovt" value="<?php echo $start_ovt;?>" required <?php echo $dis;?> readonly>
				                  <span class="input-group-addon" style="border-color:#0000ff;">
				                   <i class="fa fa-clock-o"></i>
				                  </span>
				                </div>
				              </div>
				              <div class="col-md-6 no-padding">
				                <label class="form-label">End Overtime</label>
				                <div class="input-group transparent clockpicker col-md-10">
				                  <input type="text" style="border-color:#0000ff;" class="form-control clock_end" placeholder="Pick a time" name="end_ovt" value="<?php echo $end_ovt;?>" required <?php echo $dis;?> readonly>
				                  <span class="input-group-addon" style="border-color:#0000ff;">
				                   <i class="fa fa-clock-o"></i>
				                  </span>
				                </div>
				              </div>
				              <div class="col-md-12 no-padding">
			            			<label id="notif_error2"></label>
			            		</div>
			            	</div>
			            </div>
		            </div>
		            
		            <?php if($flag) { ?>
		            <div class="form-group">
		            	<div class="row">
			            	<div class="col-md-12">
			            		<div class="col-md-12 no-padding">
			            			<label class="form-title"><b>Detail Overtime this Period</b></label>
			            		</div>
			            		<div class="col-md-12 no-padding">
		              			<label class="form-title"><span class="detail_ot">Overtime Rasio</span> : <b><?php echo $ot_rasio;?></b></label>
		              		</div>
		              		<div class="col-md-12 no-padding">
		              			<label class="form-title"><span class="detail_ot">Actual Hours</span> : <b><?php echo $actual_hours;?></b> hour(s)</label>
		              		</div>
		              		<div class="col-md-12 no-padding">
		              			<label class="form-title"><span class="detail_ot">Calculation Hours</span> : <b><?php echo $cal_hours;?></b> hour(s)</label>
		              		</div>
		              	</div>
		              </div>
		            </div>
		          	<?php } ?>
		          	
		          </div>
		        </div>
		      </div>
		  	</div>
		  </div>
		
		  <div class="col-md-6">
		    <div class="grid simple">
		      <div class="grid-body no-border">
		        <div class="row">
		          <div class="col-md-12 col-sm-12 col-xs-12">
		            <!--<div class="form-group" style="display:none;">
		              <label class="form-label">Flag</label>
		              <div class="row">
			              <div class="col-md-12">
			                <div class="radio">
			                	<?php for($i=0;$i<count($opt_ovt_flag);$i++):
				                	$chk = $opt_ovt_flag[$i][0]==$ovt_flag ? "checked='checked'" : ""; 
				                	?>
				                  <input id="<?php echo $opt_ovt_flag[$i][0]?>" type="radio" name="ovt_flag" value="<?php echo $opt_ovt_flag[$i][0]?>" <?php echo $chk;?>>
				                  <label for="<?php echo $opt_ovt_flag[$i][0]?>"><?php echo $opt_ovt_flag[$i][1]?></label>
				              	<?php endfor?>
			                </div>
			              </div>
				          </div>
		            </div>-->
		            <div class="form-group">
		              <label class="form-label"><b>Reason</b></label>
		              <div class="row">
		              	<div class="col-md-12">
		              		<?php if($dis) echo GetValue("title", "kg_ref_reason", array("id_reason"=> "where/".$ovt_reason));
		              		else echo form_dropdown("ovt_reason", $opt_reason, $ovt_reason, "required ");?>
		              	</div>
		              </div>
		            </div>
		            <div class="form-group">
		              <label class="form-label"><b>Detail Reason</b></label>
		              <div class="row">
		              	<div class="col-md-12">
		              		<?php if($dis) echo $ovt_detail_reason;
		              		else { ?>
		              		<textarea rows="3" style="width:100%;" name="ovt_detail_reason" required <?php echo $dis;?>><?php echo $ovt_detail_reason;?></textarea>
		              		<?php }?>
		              	</div>
		              </div>
		            </div>
		            
		            <?php if($flag || $ovt_status=="Approve" || $ovt_status=="Reject") { ?>
		            <div class="form-group">
		              <label class="form-label-long"><b>Feedback by Supervisor</b></label>
		              <div class="row">
		              	<div class="col-md-12">
		              		<?php if($ovt_status=="Waiting") {?>
		              		<textarea rows="3" style="width:100%;" name="ovt_feedback" required><?php echo $ovt_feedback;?></textarea>
		              		<?php } else echo $ovt_feedback;?>
		              	</div>
		              </div>
		            </div>
		            <div class="form-group">
		              <label class="form-label"><b>Status</b></label>
		              <div class="row">
		              	<div class="col-md-12">
		              		<?php
		              		if($ovt_status=="Waiting") echo form_dropdown("ovt_status", $opt_status, $ovt_status, "");
		              		else echo $ovt_status;
		              		?>
		              	</div>
		              </div>
		            </div>
		          	<?php }?>
		          </div>
		        </div>
		      </div>
		    </div>
		  </div>
		</div>
		
		<div class="col-md-12">
		<?php if($ovt_status=="" || $ovt_status=="Waiting") {?>
			<div class="clearfix_button pull-right">
				<button type="submit" class="btn btn-success">Submit</button>
			</div>
			<?php }
			if($ovt_status) {?>
			<div class="clearfix_button pull-right">
				<button type="submit" class="btn btn-back">Back</button>&nbsp;&nbsp;&nbsp;
			</div>
		</div>
		<?php } ?>
		</div>
		
		</form>
	
	</div>
</div>

<script>
var table;
$(document).ready(function() {
  $('.tgl_limit').datepicker({
		format: 'yyyy-mm-dd', 
		autoclose: true,
		todayHighlight: true,
		startDate: "-<?php echo GetConfigDirect('limit_day_ovt');?>d",
		endDate: '0d'
	});
});
</script>
<script src="<?php echo assets_url('modules/js/explode.js')?>"></script>
<script src="<?php echo assets_url('modules/js/attendance_form.js')?>"></script>