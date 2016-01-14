
  <?php $i = 1;foreach($component as $c): $val = 0;?>

  <?php foreach ($component_value as $cv) {
  	if ($cv['component_id'] == $c->component_id) {
  		$master_comp_id = $cv['id'];
  		$val = $cv['value'];
  	}
  } ?>
    <tr>
      <td><?php echo $c->component?></td>
      <input type="hidden" name="master_component_id[]" value="<?php echo $master_comp_id ?>">
      <input type="hidden" name="component_id[]" value="<?php echo $c->component_id?>">
      <td><?php echo $c->code?></td>
      <td><input type="text" name="value[]" class="form-control" value="<?php echo $val ?>"/></td>
    </tr>
  <?php $i++;endforeach;?>