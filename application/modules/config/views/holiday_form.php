<div class="grid simple transparent">
	<div class="grid-title">
		<h4><span class="semi-bold"> <?php echo ucwords(lang('holiday'))?></a></span></h4></a>
	</div>
</div>

<form id="form_edit_holiday" action="config/holiday_update" method="post" enctype="multipart/form-data">
<div class="row">
  <div class="col-md-12">
    <div class="grid simple">
    	<div class="grid-body no-border">
        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
          	<input type="hidden" name="id" value="<?php echo isset($val['id']) ? $val['id'] : "";?>">
          	<div class="form-group">
							<label class="form-label">Date</label>
							<?php
								$nm_f = "tanggal";
								echo form_input($nm_f, isset($val[$nm_f]) ? $val[$nm_f] : "", "id='".$nm_f."' class='span2 tgl'");
							?>
						</div>
						
						<div class="form-group">
							<label class="form-label">Description</label>
							<?php
								$nm_f = "ket";
								echo form_input($nm_f, isset($val[$nm_f]) ? $val[$nm_f] : "", "id='".$nm_f."' class='span5'");
							?>
						</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="clearfix_button pull-right">
	<button type="submit" class="btn btn-back-holiday">Back</button>&nbsp;&nbsp;&nbsp;
	<button type="submit" class="btn btn-success btn-submit-holiday">&nbsp;Submit</button>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><br><br>
</div>
</form>

<script src="<?php echo assets_url('modules/js/config.js')?>"></script>
<link href="<?php echo assets_url('modules/css/custom.css')?>" rel="stylesheet" type="text/css" />