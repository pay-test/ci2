$(document).ready(function() {
    $(".select2").select2();
    $(".money").maskMoney({allowZero:true});
    $("#session_select").change(function(){
        var id = $(this).val();
        if(id != 0){
           $('#table_matrix').load('payroll_config/get_table_matrix/'+id);
           $("#sess").val(id);
        }
    })
    .change();

    $("#session_select_com").change(function(){
        var id = $(this).val();
        if(id != 0){
           $('#table_com').load('payroll_config/get_table_com/'+id);
        }
    })
    .change();

    $("#session_select_jm").change(function(){
        var id = $(this).val();
        if(id != 0){
           $('#table_jm').load('payroll_config/get_table_jm/'+id);
        }
    })
    .change();

    //JAM
    $("#session_select_jam").change(function(){
        var id = $(this).val();
        if(id != 0){
           $.ajax({
                type: 'POST',
                url: 'payroll_config/get_jam',
                data: {id : id},
                dataType: "JSON",
                success: function(data) {
                    $('#jam-value').html('<a href="javascript:void(0)"><u>'+data.jam.value+'</u></a>');
                    $('#jam-id').val(data.jam.id);
                    $('#jam-value-text').val(data.jam.value);
                    $('#jam-std-value').html('<a href="javascript:void(0)"><u>'+data.std.value+'</u></a>');
                    $('#jam-std-id').val(data.std.id);
                    $('#jam-std-value-text').val(data.std.value);
                }
            });
        }
    })
    .change();

    //DIVIDER
    $("#session_select_divider").change(function(){
        var id = $(this).val();
        if(id != 0){
           $.ajax({
                type: 'POST',
                url: 'payroll_config/get_divider',
                data: {id : id},
                dataType: "JSON",
                success: function(data) {
                    $('#divider-value').html('<a href="javascript:void(0)"><u>'+data.value+'</u></a>');
                    $('#divider-id').val(data.id);
                    $('#divider-value-text').val(data.value);
                }
            });
        }
    })
    .change();

    $("#session_select_rate").change(function(){
        var id = $(this).val();
        if(id != 0){
           $.ajax({
                type: 'POST',
                url: 'payroll_config/get_rate',
                data: {id : id},
                dataType: "JSON",
                success: function(data) {
                    var value = addCommas(data.value);
                    $('#rate-value').html('<a href="javascript:void(0)"><u>'+value+'</u></a>');
                    $('#rate-id').val(data.id);
                    $('#rate-value-text').val(data.value);
                }
            });
        }
    })
    .change();

    $("#session_select_cola").change(function(){
        var id = $(this).val(),
        session_id = $('#session_select_cola option:selected').val();
        if(id != 0){
           $.ajax({
                type: 'POST',
                url: 'payroll_config/get_cola',
                data: {id : id, session_id:session_id},
                dataType: "JSON",
                success: function(data) {
                    var value = addCommas(data.value);
                    $('#cola-value').html('<a href="javascript:void(0)"><u>'+value+'</u></a>');
                    $('#cola-id').val(data.id);
                    $('#cola-value-text').val(data.value);
                }
            });
        }
    })
    .change();

    //EDIT RATE
    $("#rate-value").click(function(){
      $("#rate-value").hide();
      $("#rate-value-text").show();
      $("#rate-value-text").focus();
    });

    $("#rate-value-text").add(".editbox_min").mouseup(function()
    {
      return false
    });

    $("#rate-value-text").keypress(function(event){
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13'){
        var ID=$("#rate-id").val();
        var value=$("#rate-value-text").val();
          var dataString = 'id='+ ID +'&value='+value;
          var img = "<?php echo assets_url('assets/img/loading.gif')?>"
          $("#rate-value-text").html('<img src="'+img+'" />'); // Loading image
          
            $.ajax({
              type: "POST",
              url: "payroll_config/edit_tax_rate/",
              data: dataString,
              cache: false,
              success: function(html){
                $("#rate-value").html('<a href="javascript:void(0)"><u>'+value+'</u></a>');
              }
            });
              $("#rate-value-text").hide();
              $("#rate-value").show();  
              $("#rate-value").change();
          }
        });

    //EDIT COLA
    $("#cola-value").click(function(){
      $("#cola-value").hide();
      $("#cola-value-text").show();
      $("#cola-value-text").focus();
    });

    $("#cola-value-text").add(".editbox_min").mouseup(function()
    {
      return false
    });

    $("#cola-value-text").keypress(function(event){
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13'){
        var ID=$("#cola-id").val(),
        value=$("#cola-value-text").val(),
        session_id = $('#session_select_cola option:selected').val();
          var dataString = 'id='+ ID +'&value='+value + '&session_id=' + session_id;
          var img = "<?php echo assets_url('assets/img/loading.gif')?>"
          $("#cola-value-text").html('<img src="'+img+'" />'); // Loading image
          
            $.ajax({
              type: "POST",
              url: "payroll_config/edit_cola/",
              data: dataString,
              cache: false,
              success: function(html){
                $("#cola-value").html('<a href="javascript:void(0)"><u>'+value+'</u></a>');
              }
            });
              $("#cola-value-text").hide();
              $("#cola-value").show();  
              $("#cola-value").change();
          }
        });

     //EDIT divider
    $("#divider-value").click(function(){
      $("#divider-value").hide();
      $("#divider-value-text").show();
      $("#divider-value-text").focus();
    });

    $("#divider-value-text").add(".editbox_min").mouseup(function()
    {
      return false
    });

    $("#divider-value-text").keypress(function(event){
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13'){
        var ID=$("#divider-id").val();
        var value=$("#divider-value-text").val();
          var dataString = 'id='+ ID +'&value='+value;
          var img = "<?php echo assets_url('assets/img/loading.gif')?>"
          $("#divider-value-text").html('<img src="'+img+'" />'); // Loading image
          
            $.ajax({
              type: "POST",
              url: "payroll_config/edit_divider/",
              data: dataString,
              cache: false,
              success: function(html){
                $("#divider-value").html('<a href="javascript:void(0)"><u>'+value+'</u></a>');
              }
            });
              $("#divider-value-text").hide();
              $("#divider-value").show();  
              $("#divider-value").change();
          }
        });

    //EDIT JAM
    $("#jam-value").click(function(){
      $("#jam-value").hide();
      $("#jam-value-text").show();
      $("#jam-value-text").focus();
    });

    $("#jam-value-text").add(".editbox_min").mouseup(function()
    {
      return false
    });

    $("#jam-std-value").click(function(){
      $("#jam-std-value").hide();
      $("#jam-std-value-text").show();
      $("#jam-std-value-text").focus();
    });

    $("#jam-std-value-text").add(".editbox_min").mouseup(function()
    {
      return false
    });

    $("#jam-value-text").keypress(function(event){
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13'){
        var ID=$("#jam-id").val(),
            session_id = $('#session_select_jam option:selected').val();
            value=$("#jam-value-text").val();
          var dataString = 'id='+ ID +'&value='+value+'&session_id='+session_id;
          var img = "<?php echo assets_url('assets/img/loading.gif')?>"
          $("#jam-value-text").html('<img src="'+img+'" />'); // Loading image
          
            $.ajax({
              type: "POST",
              url: "payroll_config/edit_jam/",
              data: dataString,
              cache: false,
              success: function(html){
                $("#jam-value").html('<a href="javascript:void(0)"><u>'+value+'</u></a>');
              }
            });
              $("#jam-value-text").hide();
              $("#jam-value").show();  
              $("#jam-value").change();
          }
        });

    $("#jam-std-value-text").keypress(function(event){
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13'){
        var ID=$("#jam-std-id").val(),
            session_id = $('#session_select_jam option:selected').val();
            value=$("#jam-std-value-text").val();
          var dataString = 'id='+ ID +'&value='+value+'&session_id='+session_id;
          var img = "<?php echo assets_url('assets/img/loading.gif')?>"
          $("#jam-std-value-text").html('<img src="'+img+'" />'); // Loading image
          
            $.ajax({
              type: "POST",
              url: "payroll_config/edit_jam_std/",
              data: dataString,
              cache: false,
              success: function(html){
                $("#jam-std-value").html('<a href="javascript:void(0)"><u>'+value+'</u></a>');
              }
            });
              $("#jam-std-value-text").hide();
              $("#jam-std-value").show();  
              $("#jam-std-value").change();
          }
        });


  // Outside click action
  $(document).mouseup(function(){
    $(".td-val").show();
    $(".text-val").hide();
    $(".td-min").show();
    $(".text-val-min").hide();
    $(".td-max").show();
    $(".text-val-max").hide();
    $("#rate-value-text").hide();
    $("#rate-value").show();
    $("#jam-value-text").hide();
    $("#jam-value").show();
    $("#jam-std-value-text").hide();
    $("#jam-std-value").show();
    $("#divider-value-text").hide();
    $("#divider-value").show();
    $("#cola-value-text").hide();
    $("#cola-value").show();
  });

  $(document).keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){ 
        $("#rate-value-text").hide();
        $("#rate-value").show();
        $("#rate-value-text").hide();
        $("#rate-value").show();
        $("#divider-value-text").hide();
        $("#divider-value").show();
        $("#cola-value-text").hide();
        $("#cola-value").show();
    }
  });
});

function addCommas(nStr)
    {
      nStr += '';
      x = nStr.split('.');
      x1 = x[0];
      x2 = x.length > 1 ? '.' + x[1] : '';
      var rgx = /(\d+)(\d{3})/;
      while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
      }
      return x1 + x2;
    }
  //TAB MATRIX
