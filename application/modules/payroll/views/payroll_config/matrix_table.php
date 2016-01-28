<table class="table table-hover table-bordered" id="tbl">
  <thead>
    <tr>
      <th style="width:20%">Job Class</th>
      <th style="width:20%">Job level</th>
      <th style="width:15%">Job Value</th>
      <th style="width:15%">Value</th>
      <th style="width:15%">Value Min</th>
      <th style="width:15%">Value Max</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($matrix->result() as $m):?>
      <tr id="<?php echo $m->id?>" class="edit_tr">
        <td><?php echo $m->job_class?></td>
        <td><?php echo $m->job_level?></td>
        <td><?php echo $m->job_value?></td>
        <td align="right" id="<?php echo $m->id?>" class="edit_value"><a href="javascript:void(0);"><span class="text" id="value<?php echo $m->id?>"><?php echo number_format($m->value, 2)?></span></a><input type="text" style="display:none" value="<?php echo number_format($m->value, 2)?>" id="text<?php echo $m->id?>" class="editbox money text-right form-control"></td>
        <td align="right" id="<?php echo $m->id?>" class="edit_value_min"><a href="javascript:void(0);"><span class="text_min" id="value_min<?php echo $m->id?>"><?php echo number_format($m->value_min, 2)?></span></a><input type="text" style="display:none" value="<?php echo number_format($m->value_min, 2)?>" id="text_min<?php echo $m->id?>" class="editbox_min money text-right form-control"></td>
        <td align="right" id="<?php echo $m->id?>" class="edit_value_max"><a href="javascript:void(0);"><span class="text_max" id="value_max<?php echo $m->id?>"><?php echo number_format($m->value_max, 2)?></span></a><input type="text" style="display:none" value="<?php echo number_format($m->value_max, 2)?>" id="text_max<?php echo $m->id?>" class="editbox_max money text-right form-control"></td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>
<script type="text/javascript" src="<?=assets_url('assets/plugins/jquery-maskmoney/jquery.maskMoney.js')?>"></script>
<script type="text/javascript">
$(document).ready(function() {
  var sess_id = $('#session_select option:selected').val()
  var id = $('#section_select option:selected').val()
  $(".edit_value").click(function(){
      var ID=$(this).attr('id');

      $("#value"+ID).hide();
      $("#text"+ID).show();
      $('.money').maskMoney({allowZero:true});
      $("#text"+ID).focus();
    }).change(function(){
      var ID=$(this).attr('id');
      var first=$("#text"+ID).val();
      var dataString = 'id='+ ID +'&value='+first;
      var img = "<?php echo assets_url('assets/img/loading.gif')?>"
      $("#text"+ID).html('<img src="'+img+'" />'); // Loading image
      
        $.ajax({
          type: "POST",
          url: "payroll_config/edit",
          data: dataString,
          cache: false,
          success: function(html){
            $("#value"+ID).html(first);
          }
        });
  });

    $(".edit_value_min").click(function(){
      var ID=$(this).attr('id');

      $("#value_min"+ID).hide();
      $("#text_min"+ID).show();
      $("#text_min"+ID).focus();;
      $('.money').maskMoney({allowZero:true});
    }).change(function(){
      var ID=$(this).attr('id');
      var first=$("#text_min"+ID).val();
      var dataString = 'id='+ ID +'&value_min='+first;
      var img = "<?php echo assets_url('assets/img/loading.gif')?>"
      $("#text_min"+ID).html('<img src="'+img+'" />'); // Loading image
      
        $.ajax({
          type: "POST",
          url: "payroll_config/edit/_min",
          data: dataString,
          cache: false,
          success: function(html){
            $("#value_min"+ID).html(first);
          }
        });
  });

    $(".edit_value_max").click(function(){
      var ID=$(this).attr('id');

      $("#value_max"+ID).hide();
      $("#text_max"+ID).show();
      $("#text_max"+ID).focus();;
      $('.money').maskMoney({allowZero:true});
    }).change(function(){
      var ID=$(this).attr('id');
      var first=$("#text_max"+ID).val();
      var dataString = 'id='+ ID +'&value_max='+first;
      var img = "<?php echo assets_url('assets/img/loading.gif')?>"
      $("#text_max"+ID).html('<img src="'+img+'" />');
        $.ajax({
          type: "POST",
          url: "payroll_config/edit/_max",
          data: dataString,
          cache: false,
          success: function(html){
            $("#value_max"+ID).html(first);
          }
        });
  });

    // Edit input box click action
    $(".editbox").add(".editbox_min").add(".editbox_max").mouseup(function()
    {
      return false
    });

    $(".editbox").keypress(function(event){
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13'){
          $(".editbox").hide();
          $(".text").show();  
          $(".edit_value").change();
      }
    });

    $(".editbox_min").keypress(function(event){
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13'){
          $(".editbox_min").hide();
          $(".text_min").show();  
          $(".edit_value_min").change();
      }
    });

    $(".editbox_max").keypress(function(event){
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13'){
          $(".editbox_max").hide();
          $(".text_max").show();  
          $(".edit_value_max").change();
      }
    });

  // Outside click action
  $(document).mouseup(function(){
  $(".editbox").hide();
  $(".text").show();
  $(".editbox_min").hide();
  $(".text_min").show();
  $(".editbox_max").hide();
  $(".text_max").show();
  });

  $(document).keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){
        $(".editbox").hide();
        $(".text").show();  
        $(".editbox_min").hide();
        $(".text_min").show();

  $(".editbox_max").hide();
  $(".text_max").show();
    }
  });
});
</script>