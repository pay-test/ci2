<div class="col-md-2">
  <label>Division</label>
  <?php 
	  $opt_divisi = GetOptDivision();
	  echo form_dropdown("s_div", $opt_divisi, "", "class='span2_2' onChange='get_section(this.value);'");
  ?>
</div>