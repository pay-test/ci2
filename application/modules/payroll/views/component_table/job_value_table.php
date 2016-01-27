<?php if($component_job_value->num_rows() > 0):
foreach($component_job_value->result() as $row):
?>
	<tr>
		<td><?php echo $row->job_value?></td>
		<td>
			<div class="checkbox check-success">
	        	<input type="checkbox" id="checkbox_comp<?php echo $i; ?>" value="<?php echo $pcomp['id']; ?>" name="p_component[]" class="inc">
	            <label for="checkbox_comp<?php echo $i; ?>"></label>
	        </div>
		</td>
		<td><input class="form-control" type="text" /></td>
	</tr>
	<?php endforeach;else:
	foreach($job_value->result() as $j):
	?>
		<tr>
			<td><?php echo $j->title?></td>
			<td>
				<div class="checkbox check-success">
	        		<input type="checkbox" id="checkbox_comp" value="" name="p_component[]" class="inc">
	            	<label for="checkbox_comp"></label>
	        	</div>
			</td>
			<td>
				<input class="form-control" type="text" />
			</td>
		</tr>
		<?php endif;?>