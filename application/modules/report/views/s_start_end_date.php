<?php
$period = GetPeriod(date("M Y"));
$start_date=substr($period,0,10);
$end_date=substr($period,11,10);
?>
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