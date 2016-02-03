$(document).ready(function() {
    $(".select2").select2();
    $(".money").maskMoney({allowZero:true});
    $("#session_select").change(function(){
        var id = $(this).val();
        if(id != 0){
           $('#table_matrix').load('payroll_config/get_table_matrix/'+id);
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

    $("#session_select_divider").change(function(){
        var id = $(this).val();
        if(id != 0){
           $.ajax({
                type: 'POST',
                url: 'payroll_config/get_divider',
                data: {id : id},
                dataType: "JSON",
                success: function(data) {
                    $('#divider-value').text(data.value);
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
                    $('#rate-value').text(value);
                    $('#rate-id').val(data.id);
                    $('#rate-value-text').val(data.value);
                }
            });
        }
    })
    .change();

    $("#session_select_cola").change(function(){
        var id = $(this).val();
        if(id != 0){
           $.ajax({
                type: 'POST',
                url: 'payroll_config/get_cola',
                data: {id : id},
                dataType: "JSON",
                success: function(data) {
                    var value = addCommas(data.value);
                    $('#cola-value').text(value);
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
              url: "payroll_config/edit_rate/",
              data: dataString,
              cache: false,
              success: function(html){
                $("#rate-value").html(value);
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
        var ID=$("#cola-id").val();
        var value=$("#cola-value-text").val();
          var dataString = 'id='+ ID +'&value='+value;
          var img = "<?php echo assets_url('assets/img/loading.gif')?>"
          $("#cola-value-text").html('<img src="'+img+'" />'); // Loading image
          
            $.ajax({
              type: "POST",
              url: "payroll_config/edit_cola/",
              data: dataString,
              cache: false,
              success: function(html){
                $("#cola-value").html(value);
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
                $("#divider-value").html(value);
              }
            });
              $("#divider-value-text").hide();
              $("#divider-value").show();  
              $("#divider-value").change();
          }
        });


  // Outside click action
  $(document).mouseup(function(){
    $("#rate-value-text").hide();
    $("#rate-value").show();
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

  window.updateVal = function(a,b){
      var ID = a+b;
      $("#td"+ID).hide();
      $("#text"+ID).show();
      $("#text"+ID).focus();
      $(".money").maskMoney({allowZero:true});
        window.changeVal = function(){
        var first=$("#text"+ID).val();
        var dataString = 'value='+first;
        $.ajax({
          type: "POST",
          url: "payroll_config/edit_matrix/"+a+"/"+b,
          data: dataString,
          cache: false,
          success: function(html){
            $("#text"+ID).hide();
            $("#td"+ID).html(first);
            $("#td"+ID).show();
          }
        });
    }
  }

  window.updateValMin = function(a,b){
      var ID = a+b;
      $("#td_min"+ID).hide();
      $("#text_min"+ID).show();
      $("#text_min"+ID).focus();
      $(".money").maskMoney({allowZero:true});
        window.changeValMin = function(){
        var first=$("#text_min"+ID).val();
        var dataString = 'value='+first;
        $.ajax({
          type: "POST",
          url: "payroll_config/edit_matrix/"+a+"/"+b+"/_min",
          data: dataString,
          cache: false,
          success: function(html){
            $("#text_min"+ID).hide();
            $("#td_min"+ID).html(first);
            $("#td_min"+ID).show();
          }
        });
    }
  }

  window.updateValMax = function(a,b){
      var ID = a+b;
      $("#td_max"+ID).hide();
      $("#text_max"+ID).show();
      $("#text_max"+ID).focus();
      $(".money").maskMoney({allowZero:true});
        window.changeValMax = function(){
        var first=$("#text_max"+ID).val();
        var dataString = 'value='+first;
        $.ajax({
          type: "POST",
          url: "payroll_config/edit_matrix/"+a+"/"+b+"/_max",
          data: dataString,
          cache: false,
          success: function(html){
            $("#text_max"+ID).hide();
            $("#td_max"+ID).html(first);
            $("#td_max"+ID).show();
          }
        });
    }
  }