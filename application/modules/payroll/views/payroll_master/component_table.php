
  
  <?php $i = 1;foreach($component as $c):?>
    <tr>
      <td><?php echo $c->component?></td>
      <input type="hidden" name="component_id[]" value="<?php echo $c->component_id?>">
      <td><?php echo $c->code?></td>
      <td><input type="text" id="val_<?php echo $i?>" name="value[]" class="form-control" value="<?php echo $c->value?>"/></td>
    </tr>
  <?php $i++;endforeach;?>