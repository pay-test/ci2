
  <?php $i = 1;foreach($component as $c): $val = 0;?>

  <?php foreach ($component_value as $cv) {//print_mz($component_value);
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
      <td><input class="form-control auto text-right" data-a-sep="," type="text" value="<?php echo number_format($val, 2)?>" name="value[]"></td>
    </tr>
  <?php $i++;endforeach;?>
<!--
  <script src="<?php echo assets_url('assets/plugins/jquery/jquery-1.11.3.min.js')?>" type="text/javascript"></script>
-->
  <script type="text/javascript" src="<?=assets_url('assets/plugins/jquery-maskmoney/jquery.maskMoney.js')?>"></script>
<script type="text/javascript">
  $(document).ready(function() {
    //$(".select2").select2();
    //datatables
    $('.auto').maskMoney();
});
</script>