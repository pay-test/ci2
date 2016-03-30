
<?php
foreach($city->result() as $c):
	$filter = array('session_id'=>'where/'.$sess_id, 'umk_city_id'=>'where/'.$c->id);
	$value = getValue('value', 'payroll_umk', $filter);
	$valuex = (!empty($value)) ? $value : 0;
?>
<div class="row">
	<div class="col-md-2">
	    <label class="label-form">UMK <?php echo $c->title?></label>
	</div>
	<div class="col-md-2">
	    <a href="javascript:void(0)" style="font-weight:700" class="form-label umk-text" id="value<?php echo $c->id?>" onclick="updateVal('<?php echo $c->id?>')"><u><?php echo number_format($valuex, 2) ?></u></a>
	    <input type="hidden" class="form-control" id="id" value="">
	    <input type="text" style="display:none" class="form-control money text-right umk-text-value" id="value-text<?php echo $c->id?>" value="<?php echo number_format($valuex, 2) ?>">
	</div>
</div>
<br/>
<?php endforeach;?>
<script type="text/javascript" src="<?php echo assets_url('assets/plugins/jquery-maskmoney/jquery.maskMoney.js')?>"></script>
<script type="text/javascript">
  var sess = <?php echo $sess_id?>;
  window.updateVal = function(ID){
      $("#value"+ID).hide();
      $("#value-text"+ID).show();
      $("#value-text"+ID).focus();
      $(".money").maskMoney({allowZero:true});
      $("#value-text"+ID).dblclick(function(){
      	update(ID);
	  });
      
      $(document).keypress(function(event){
	    var keycode = (event.keyCode ? event.keyCode : event.which);
	    if(keycode == '13'){ 
	        update(ID);
	    }
	  });
  }
  	function update(ID){
        var first=$("#value-text"+ID).val();
        var dataString = 'value='+first;
        $.ajax({
          type: "POST",
          url: "payroll_config_tax/edit_umk/"+sess+"/"+ID,
          data: dataString,
          cache: false,
          success: function(html){
            $("#value-text"+ID).hide();
            $("#value"+ID).html(addCommas(first));
            $("#value"+ID).show();
          }
        });
    }
 </script>
