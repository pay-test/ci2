<table class="table table-hover table-bordered">
  <thead>
    <tr>
      <th style="width:50%" class="text-center">Job Class</th>
      <th style="width:25%" class="text-center">JAM</th>
      <th style="width:25%" class="text-center">JM</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($com->result() as $m):?>
      <tr id="<?php echo $m->id?>" class="edit_tr">
        <td><?php echo $m->job_class?></td>
        <td align="right" id="<?php echo $m->id?>" class="edit_var"><a href="javascript:void(0);"><span class="text_var" id="var<?php echo $m->id?>"><?php echo $m->var?></span></a><input type="text" style="display:none" value="<?php echo $m->var?>" id="text_var<?php echo $m->id?>" class="editbox_var text-right"></td>
        <td align="right" id="<?php echo $m->id?>" class="edit_fix"><a href="javascript:void(0);"><span class="text_fix" id="fix<?php echo $m->id?>"><?php echo $m->fix?></span></a><input type="text" style="display:none" value="<?php echo $m->fix?>" id="text_fix<?php echo $m->id?>" class="editbox_fix text-right"></td>
      </tr>
    <?php endforeach;?>
  </tbody>
</table>

<script type="text/javascript">
$(document).ready(function() {
    $(".edit_var").click(function(){
      var ID=$(this).attr('id');

      $("#var"+ID).hide();
      $("#text_var"+ID).show();
      $("#text_var"+ID).focus();;
      
    }).change(function(){
      var ID=$(this).attr('id');
      var first=$("#text_var"+ID).val();
      var dataString = 'id='+ ID +'&value='+first;
      var img = "<?php echo assets_url('assets/img/loading.gif')?>"
      $("#text_var"+ID).html('<img src="'+img+'" />'); // Loading image
      
        $.ajax({
          type: "POST",
          url: "payroll_config/edit_com/var",
          data: dataString,
          cache: false,
          success: function(html){
            $("#var"+ID).html(first);
          }
        });
  });

    $(".edit_fix").click(function(){
      var ID=$(this).attr('id');

      $("#fix"+ID).hide();
      $("#text_fix"+ID).show();
      $("#text_fix"+ID).focus();;
      
    }).change(function(){
      var ID=$(this).attr('id');
      var first=$("#text_fix"+ID).val();
      var dataString = 'id='+ ID +'&value='+first;
      var img = "<?php echo assets_url('assets/img/loading.gif')?>"
      $("#text_fix"+ID).html('<img src="'+img+'" />');
        $.ajax({
          type: "POST",
          url: "payroll_config/edit_com/fix",
          data: dataString,
          cache: false,
          success: function(html){
            $("#fix"+ID).html(first);
          }
        });
  });

    // Edit input box click action
    $(".editbox_var").add(".editbox_fix").mouseup(function()
    {
      return false
    });

    $(".editbox_var").keypress(function(event){
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13'){
          $(".editbox_var").hide();
          $(".text_var").show();  
          $(".edit_var").change();
      }
    });

    $(".editbox_fix").keypress(function(event){
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13'){
          $(".editbox_fix").hide();
          $(".text_fix").show();  
          $(".edit_fix").change();
      }
    });

  // Outside click action
  $(document).mouseup(function(){
  $(".editbox_var").hide();
  $(".text_var").show();
  $(".editbox_fix").hide();
  $(".text_fix").show();
  });

  $(document).keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){ 
        $(".editbox_var").hide();
        $(".text_var").show();
        $(".editbox_fix").hide();
        $(".text_fix").show();
    }
  });
});
</script>