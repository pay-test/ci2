<div class="grid simple transparent">
	<div class="grid-title">
		<h4>Edit of<span class="semi-bold"> Config</span></h4></a>
	</div>
</div>

<form id="form_edit_config" action="config/update" method="post" enctype="multipart/form-data">
<div class="row">
  <div class="col-md-6">
    <div class="grid simple">
    	<div class="grid-title no-border">
        <h4><span class="semi-bold">Attendance</span></h4>
      </div>
      <div class="grid-body no-border">
        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
          	<div class="form-group">
							<label class="form-label">Active From</label>
							<?php
								$nm_f = "att_active_from";
								echo form_input($nm_f, $config[$nm_f], "id='".$nm_f."' class='span5 tgl'");
							?>
						</div>
						
						<div class="form-group">
							<label class="form-label">Start Period</label>
							<?php
								$nm_f = "att_start_period";
								echo form_input($nm_f, $config[$nm_f], "id='".$nm_f."' class='span5'")." 1 to 31";
							?>
						</div>
						
						<div class="form-group">
							<label class="form-label">End Period</label>
							<?php
								$nm_f = "att_end_period";
								echo form_input($nm_f, $config[$nm_f], "id='".$nm_f."' class='span5'")." 1 to 31";
							?>
						</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
  	<div class="grid simple">
  	</div>
  </div>
</div>
<div class="clearfix_button pull-right">
	<button type="submit" class="btn btn-success btn-submit">&nbsp;Submit</button>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><br><br>
</div>
</form>

<script src="<?php echo assets_url('modules/js/config.js')?>"></script>
<link href="<?php echo assets_url('modules/css/custom.css')?>" rel="stylesheet" type="text/css" />