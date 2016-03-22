$(document).ready(function() {
    //$(".select2").select2();
    $(".money").maskMoney({allowZero:true});

    $("#session_select").change(function(){
        var id = $(this).val();
        if(id != 0){
          $('#component').load('payroll_config_tax/get_tax_component/');
          $('#ptkp').load('payroll_config_tax/get_ptkp/');
          $('#progressive').load('payroll_config_tax/get_progressive/');
          $('#method').load('payroll_config_tax/get_method/');
          getRate(id);
          getUmk(id);
        }
    })
    .change();

    function getUmk(id)
    {
      if(id != 0){
         $.ajax({
              type: 'POST',
              url: 'payroll_config_tax/get_umk',
              data: {id : id},
              dataType: "JSON",
              success: function(data) {
                //alert(data.value);
                  var value = addCommas(data.value);
                  $('#value').html('<a href="javascript:void(0)"><u>'+value+'</u></a>');
                  $('#id').val(data.id);
                  $('#value-text').val(data.value);
              }
          });
      }
    }

    function getRate(id)
    {
      if(id != 0){
         $.ajax({
              type: 'POST',
              url: 'payroll_config_tax/get_tax_rate',
              data: {id : id},
              dataType: "JSON",
              success: function(data) {
                //alert(data.value);
                  var value = addCommas(data.value);
                  $('#rate-value').html('<a href="javascript:void(0)"><u>'+value+'</u></a>');
                  $('#rate-id').val(data.id);
                  $('#rate-value-text').val(data.value);
              }
          });
      }
    }

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
        var session_id = $('#session_select option:selected').val();
        //alert(session_id);
        var ID=$("#rate-id").val();
        var value=$("#rate-value-text").val();
           var dataString = 'id='+ ID +'&value='+value +'&session_id='+session_id;
          var img = "<?php echo assets_url('assets/img/loading.gif')?>"
          $("#rate-value-text").html('<img src="'+img+'" />'); // Loading image
          
            $.ajax({
              type: "POST",
              url: "payroll_config_tax/edit_tax_rate/",
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




//UMK
$("#value").click(function(){
      $("#value").hide();
      $("#value-text").show();
      $("#value-text").focus();
    });

    $("#value-text").add(".editbox").mouseup(function()
    {
      return false
    });

    $("#value-text").keypress(function(event){
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13'){
        var session_id = $('#session_select option:selected').val();
        var ID=$("#id").val();
        var value=$("#value-text").val();
          var dataString = 'id='+ ID +'&value='+value +'&session_id='+session_id;
          var img = "<?php echo assets_url('assets/img/loading.gif')?>"
          $("#value-text").html('<img src="'+img+'" />'); // Loading image
          
            $.ajax({
              type: "POST",
              url: "payroll_umk/edit/",
              data: dataString,
              cache: false,
              success: function(html){
                $("#value").html('<a href="javascript:void(0)"><u>'+value+'</u></a>');
              }
            });
              $("#value-text").hide();
              $("#value").show();  
              $("#value").change();
          }
        });


  // Outside click action
  $(document).mouseup(function(){
    $("#value-text").hide();
    $("#value").show();
  });

  $(document).keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){ 
        $("#value-text").hide();
        $("#value").show();
    }
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
