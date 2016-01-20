<div class="grid simple transparent">
	<div class="grid-title">
		<h4>Edit <span class="semi-bold">Attendance</span></h4></a>
	</div>
</div>

<form id="form_edit_att" action="attendance/update" method="post" enctype="multipart/form-data">
<!--<table class="table table-striped">-->
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
              <label class="form-label">Name</label>
              <?php 
              echo form_hidden("id", $val['id']);
              echo form_hidden("id_employee", $val['id_employee']);
              echo GetValue("person_nm", "hris_persons", array("person_id"=>"where/".$val['id_employee']));?>
            </div>
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
              <label class="form-label">Absensi</label>
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
            <div class="form-group">
          		<div class="row">
            	<div class="col-md-12">
                	<label class="form-label">Scan In</label>
                <div class="input-group transparent clockpicker col-md-5">
                  <input type="text" class="form-control" placeholder="Pick a time" name="scan_masuk" value="<?php echo $val['scan_masuk'];?>">
                  <span class="input-group-addon ">
                   <i class="fa fa-clock-o"></i>
                  </span>
                </div>
                	<label class="form-label">Scan Out</label>
                <div class="input-group transparent clockpicker col-md-5">
                  <input type="text" class="form-control" placeholder="Pick a time" name="scan_pulang" value="<?php echo $val['scan_pulang'];?>">
                  <span class="input-group-addon ">
                   <i class="fa fa-clock-o"></i>
                  </span>
                </div>
            	</div>
            </div>
            </div>
            <!--<div class="form-group">
          		<div class="row">
            	<div class="col-md-12">
                	<label class="form-label">Pulang Cepat</label>
                <div class="input-group transparent clockpicker col-md-5">
                  <input type="text" class="form-control" placeholder="Pick a time">
                  <span class="input-group-addon ">
                   <i class="fa fa-clock-o"></i>
                  </span>
                </div>
            	</div>
            </div>-->
            </div>
          </div>
        </div>
      </div>
  </div>

  <div class="col-md-6">
    <div class="grid simple">
      <div class="grid-title no-border">
        <h4><span class="semi-bold">Overtime</span></h4>
      </div>
      <div class="grid-body no-border">
        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="form-group" style="display:none;">
              <label class="form-label">OT Incidental</label>
              <?php
              $nm_f = "ot_incidental";
              echo form_dropdown($nm_f, $opt_lembur, isset($val[$nm_f]) ? $val[$nm_f] : 0, "id='".$nm_f."' class='span2 ot_inc'");
              ?>
            </div>
            <div class="form-group">
              <label class="form-label">Hour SUM</label>
              <div class="controls">
                <input name="acc_ot_incidental" id="acc_ot_incidental" value="<?php echo $val['acc_ot_incidental'];?>" class="form-control">
              </div>
            </div>
            <!--<div class="form-group">
              <label class="form-label">OT Kelebihan Jam</label>
              <select class="select2" style="width:100%">
                      <option value="0">-- Pilih OT Kelebihan Jam --</option>
                      <option value="0">Tes</option>
                      <option value="0">Tes 2</option>
                  </select>
            </div>
            <div class="form-group">
              <label class="form-label">Acc OT Kelebihan Jam</label>
              <div class="controls">
                <input type="text" placeholder="0" class="form-control">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Tunjangan Hari Kerja</label>
              <select class="select2" style="width:100%">
                      <option value="0">-- Pilih Tunjangan Hari Kerja --</option>
                      <option value="0">Tes</option>
                      <option value="0">Tes 2</option>
                  </select>
            </div>
            <div class="form-group">
              <label class="form-label">Acc Tunjangan Hari Kerja</label>
              <div class="controls">
                <input type="text" placeholder="0" class="form-control">
              </div>
            </div>-->
            <div class="form-group">
              <label class="form-label">Reason</label>
              <select name="alasan_lembur" class="select2" style="width:100%">
                <option value="0">-- Select Reason --</option>
                <option value="General Work" <?php if($val['ovt_reason']=="General Work") echo "selected";?>>General Work</option>
                <option value="SDM/PSDM/TSDM" <?php if($val['ovt_reason']=="SDM/PSDM/TSDM") echo "selected";?>>SDM/PSDM/TSDM</option>
                <option value="Mengganti orang cuti" <?php if($val['ovt_reason']=="Mengganti orang cuti") echo "selected";?>>Mengganti orang cuti</option>
                <option value="OT Auto" <?php if($val['ovt_reason']=="OT Auto") echo "selected";?>>OT Auto</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Description</label>
              <textarea name="ovt_detail_reason" class="form-control"><?php echo $val['ovt_detail_reason'];?></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="clearfix_button pull-right">
		<button type="submit" class="btn btn-success btn-submit-att">&nbsp;Submit</button>
		<button type="submit" class="btn btn-cancel-att">&nbsp;Cancel</button>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>
	</div>
</div>
</form>

<script src="<?php echo assets_url('modules/js/attendance.js')?>"></script>