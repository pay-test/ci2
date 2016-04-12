<div class="col-md-2">
  <label>Reguler/Shift</label>
  <?php 
	  $opt_regs = GetOptShiftReguler();
	  echo form_dropdown("s_regs", $opt_regs, "", "class='span2_2'");
  ?>
</div>