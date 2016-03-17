
  
  <?php $i = 1;foreach($component as $c):?>
    <tr>
      <td><?php echo $c->component?></td>
      <input type="hidden" name="component_id[]" value="<?php echo $c->component_id?>">
      <td><?php echo $c->code?></td>
      <td><input class="form-control auto text-right" data-a-sep="," data-a-dec="." type="text" value="0" name="value[]"></input></td>
    </tr>
  <?php endforeach;?>
  
  <script type="text/javascript" src="<?=assets_url('assets/plugins/jquery-maskmoney/jquery.maskMoney.js')?>"></script>
<script type="text/javascript">
  $(document).ready(function() {
    //$(".select2").select2();
    //datatables
    $('.auto').maskMoney({allowNegative:true});
});
</script>
