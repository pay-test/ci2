
  
  <?php $i = 1;foreach($component as $c):?>
    <tr>
      <td><?php echo $c->component?></td>
      <input type="hidden" name="component_id[]" value="<?php echo $c->component_id?>">
      <td><?php echo $c->code?></td>
      <td><input type="text" name="value[]" class="form-control" value=""/></td>
    </tr>
  <?php endforeach;?>