
  
  <?php $i = 1;foreach($component as $c):?>
    <tr>
      <td><?php echo $c->component?></td>
      <input type="hidden" name="component_id[]" value="<?php echo $c->component_id?>">
      <td><?php echo $c->code?></td>
      <td><input class="form-control auto text-right" data-a-sep="," data-a-dec="." type="text" value="0" name="value[]"></input></td>
    </tr>
  <?php endforeach;?>
  
  <script src="<?php echo assets_url('assets/plugins/jquery/jquery-1.11.3.min.js')?>" type="text/javascript"></script>
  <script type="text/javascript" src="<?=assets_url('assets/plugins/jquery-autonumeric/autoNumeric.js')?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
    //$(".select2").select2();
    //datatables
    $('.auto').autoNumeric('init');
});
</script>
