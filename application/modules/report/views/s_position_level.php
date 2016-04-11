<div class="col-md-2">
  <label>Position Level</label>
  <?php 
	  $opt_pos = GetOptPositionLevel();
	  echo form_dropdown("s_pos", $opt_pos, "", "class='span2_2'");
  ?>
</div>