<div class="col-md-2">
  <label>Grade</label>
  <?php 
		$opt_grade = GetOptGrade();
		echo form_dropdown("s_grade", $opt_grade, "", "class='span2_2'");
  ?>
</div>