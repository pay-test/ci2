<table class="table table-hover table-bordered">
  <thead>
    <tr>
      <th style="width:50%" class="text-center">Min</th>
      <th style="width:50%" class="text-center">Max</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($jm->result() as $m):?>
      <tr id="<?php echo $m->id?>" class="edit_tr">
        <td align="center" id="<?php echo $m->id?>" class="edit_min"><a href="javascript:void(0);"><span class="textz_min" id="min<?php echo $m->id?>"><u><?php echo $m->min?></u></span></a><input type="text" style="display:none" value="<?php echo $m->min?>" id="textz_min<?php echo $m->id?>" class="editbox_min text-center"></td>
        <td align="center" id="<?php echo $m->id?>" class="edit_max"><a href="javascript:void(0);"><span class="textz_max" id="max<?php echo $m->id?>"><u><?php echo $m->max?></u></span></a><input type="text" style="display:none" value="<?php echo $m->max?>" id="textz_max<?php echo $m->id?>" class="editbox_max text-center"></td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>

<script type="text/javascript">
$(document).ready(function() {
    $(".edit_max").click(function(){
      var ID=$(this).attr('id');

      $("#max"+ID).hide();
      $("#textz_max"+ID).show();
      $("#textz_max"+ID).focus();;
      
    }).change(function(){
      var ID=$(this).attr('id');
      var first=$("#textz_max"+ID).val();
      var dataString = 'id='+ ID +'&value='+first;
      var img = "<?php echo assets_url('assets/img/loading.gif')?>"
      $("#textz_max"+ID).html('<img src="'+img+'" />'); // Loading image
      
        $.ajax({
          type: "POST",
          url: "payroll_config/edit_jm/max",
          data: dataString,
          cache: false,
          success: function(html){
            $("#max"+ID).html('<u>'+first+'</u>');
          }
        });
  });

    $(".edit_min").click(function(){
      var ID=$(this).attr('id');

      $("#min"+ID).hide();
      $("#textz_min"+ID).show();
      $("#textz_min"+ID).focus();;
      
    }).change(function(){
      var ID=$(this).attr('id');
      var first=$("#textz_min"+ID).val();
      var dataString = 'id='+ ID +'&value='+first;
      var img = "<?php echo assets_url('assets/img/loading.gif')?>"
      $("#textz_min"+ID).html('<img src="'+img+'" />');
        $.ajax({
          type: "POST",
          url: "payroll_config/edit_jm/min",
          data: dataString,
          cache: false,
          success: function(html){
            $("#min"+ID).html('<u>'+first+'</u>');
          }
        });
  });

    // Edit input box click action
    $(".editbox_max").add(".editbox_min").mouseup(function()
    {
      return false
    });

    $(".editbox_max").keypress(function(event){
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13'){
          $(".editbox_max").hide();
          $(".textz_max").show();  
          $(".edit_max").change();
      }
    });

    $(".editbox_min").keypress(function(event){
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13'){
          $(".editbox_min").hide();
          $(".textz_min").show();  
          $(".edit_min").change();
      }
    });

  // Outside click action
  $(document).mouseup(function(){
  $(".editbox_max").hide();
  $(".textz_max").show();
  $(".editbox_min").hide();
  $(".textz_min").show();
  });

  $(document).keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){ 
        $(".editbox_max").hide();
        $(".textz_max").show();
        $(".editbox_min").hide();
        $(".textz_min").show();
    }
  });
});
</script>