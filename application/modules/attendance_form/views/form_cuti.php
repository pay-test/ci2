<div class="row column-seperation">
  <div class="col-md-12">
  	<div id="coba" class="grid simple transparent">
			<div class="grid-title">
				<h4>Form <span class="semi-bold"><?php echo ucwords(lang('cuti'))?></span></h4></a>
			</div>
		</div>
		
		<form id="form_edit_att" action="<?php echo site_url('attendance_form/update_cuti');?>" method="post" enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?php echo $id;?>">
		<input type="hidden" name="id_emp" value="<?php echo $id_emp;?>">
		<!--<table class="table table-striped">-->
		<div class="row">
			<?php
			if($cuti_status=="Approve" || $cuti_status=="Reject") $dis="disabled";
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
		           	<div class="form-group">
		           		<div class="row">
			           		<div class="col-md-12">
				            	<div class="col-md-4 no-padding">
					              <label class="form-label-long"><b>Annual Balance</b></label>
					              <div class="row">
					              	<div class="col-md-12">
					                  <?php echo $max_leave_day;?>
					                  <input type="hidden" class="max_leave" value="<?php echo $max_leave_day;?>">
					                </div>
					              </div>
					            </div>
					            <div class="col-md-4 no-padding">
					            	<label class="form-label-long"><b>Duration in Day(s)</b></label>
					              <div class="row">
					              	<div class="col-md-12">
					                  <div id="durasi"><?php echo $duration;?></div>
					                  <input type="hidden" class="duration" name="duration" value="<?php echo $duration;?>">
					                </div>
					              </div>
					            </div>
					            <div class="col-md-4 no-padding">
					            	<label class="form-label-long"><b>Actual Leave</b></label>
					              <div class="row">
					              	<div class="col-md-12">
					                  <div id="durasi"><?php echo $max_leave_day-$duration;?></div>
					                </div>
					              </div>
					            </div>
					          </div>
					        </div>
		            </div>
		            		            
		           	<div class="form-group">
		              <label class="form-label"><b>Date</b></label>
		              <div class="row">
		              	<div class="col-md-4" style="padding-right:46px;">
		                  <div id="datepicker_start" class="input-append date success no-padding">
		                  	<input type="text" class="tgl_limit_cuti tgl_start form-control" value="<?php echo $tgl_start;?>" name="tgl_start" required <?php echo $dis;?>>
		                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
		                  </div>
		                </div>
		                <div class="to col-md-1" style="padding-top:8px;">to</div>
			              <div class="col-md-4" style="padding-right:46px;">
		                  <div id="datepicker_start" class="input-append date success no-padding">
		                  	<input type="text" class="tgl_limit_cuti tgl_end form-control" value="<?php echo $tgl_end;?>" name="tgl_end" required <?php echo $dis;?>>
		                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
		                  </div>
		                </div>
		                <div class="col-md-12">
		            			<label id="notif_error"></label>
		            		</div>
		              </div>
		            </div>
		            <div class="form-group">
		              <label class="form-label"><b>Kind of Leave</b></label>
		              <div class="row">
		              	<div class="col-md-12" style="padding-bottom:8px;">
		              		<?php if($dis) echo GetValue("title", "kg_ref_reason_cuti", array("id_reason_cuti"=> "where/".$cuti_reason));
		              		else echo form_dropdown("cuti_reason", $opt_reason, $cuti_reason, "required ");?>
		              	</div>
		              </div>
		            </div>
		            <div class="form-group">
		              <label class="form-label-long"><b>Phone During Leave</b></label>
		              <div class="row">
		              	<div class="col-md-7">
		                  <input type="text" class="form-control" value="<?php echo $telp_cuti;?>" name="telp_cuti" required <?php echo $dis;?>>
		                </div>
		              </div>
		              
		            </div>
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
		            <div class="form-group">
		              <label class="form-label-long"><b>Replacement Employees</b></label>
		              <div class="row">
		              	<div class="col-md-12">
		              		<?php 
		              		if($dis) {
		              			if($id_pengganti) echo GetValue("ext_id", "hris_persons", array("person_id"=> "where/".$id_pengganti))." - ".GetValue("person_nm", "hris_persons", array("person_id"=> "where/".$id_pengganti));
		              			else echo "-";
		              		}
		              		else echo form_dropdown("id_pengganti", $opt_pengganti, $id_pengganti, " ");?>
		              	</div>
		              </div>
		            </div>
		            <div class="form-group">
		              <label class="form-label"><b>Notes</b></label>
		              <div class="row">
		              	<div class="col-md-12">
		              		<?php if($dis) echo $keterangan;
		              		else { ?>
		              		<textarea rows="3" style="width:100%;" name="keterangan" required <?php echo $dis;?>><?php echo $keterangan;?></textarea>
		              		<?php }?>
		              	</div>
		              </div>
		            </div>
		            
		            <?php if($flag || $cuti_status=="Approve" || $cuti_status=="Reject") { ?>
		            <div class="form-group">
		              <label class="form-label-long"><b>Feedback by Supervisor</b></label>
		              <div class="row">
		              	<div class="col-md-12">
		              		<?php if($cuti_status=="Waiting") {?>
		              		<textarea rows="3" style="width:100%;" name="feedback" required><?php echo $feedback;?></textarea>
		              		<?php } else echo $feedback;?>
		              	</div>
		              </div>
		            </div>
		            <div class="form-group">
		              <label class="form-label"><b>Status</b></label>
		              <div class="row">
		              	<div class="col-md-12">
		              		<?php
		              		if($cuti_status=="Waiting") echo form_dropdown("cuti_status", $opt_status, $cuti_status, "");
		              		else echo $cuti_status;
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
		<?php if($cuti_status=="" || $cuti_status=="Waiting") {?>
			<div class="clearfix_button pull-right">
				<button type="submit" class="btn btn-success">Submit</button>
			</div>
			<?php }
			if($cuti_status) {?>
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
  $('.tgl_limit_cuti').datepicker({
		format: 'yyyy-mm-dd', 
		autoclose: true,
		todayHighlight: true,
		startDate: "0d",
	});
});
</script>
<script src="<?php echo assets_url('modules/js/explode.js')?>"></script>
<script src="<?php echo assets_url('modules/js/attendance_form.js')?>"></script>