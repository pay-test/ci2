<ul class="nav nav-tabs nav-justified" role="tablist">
    <li class="active">
      <a href="#tab1hellowWorld" role="tab" data-toggle="tab">Management</a>
    </li>
    <li>
      <a href="#tab1FollowUs" role="tab" data-toggle="tab">Non Management</a>
    </li>
</ul>
  <div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
  <div class="tab-content" style="height:600px;overflow:auto;">
    <div class="tab-pane active" id="tab1hellowWorld">
      <div class="row column-seperation">
        <div class="col-md-12">
          <table class="table table-hover table-bordered" id="tbl">
            <thead>
              <tr>
                <th style="width:20%">Job Class</th>
                <th style="width:15%">Job Value</th>
                <th style="width:15%">Value</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($pos->result() as $m):?>
                <tr>
                  <th rowspan="3"><?php echo $m->job_class_nm?></th>
                  <?php foreach($val->result() as $v){?>
                  <td><?php echo $v->title?></td>
                  <?php $value = getValue('value', 'payroll_job_value_matrix', array('session_id'=>'where/'.$session_id,'job_class_id'=>'where/'.$m->job_class_id, 'job_value_id'=>'where/'.$v->id));
                    $valuex = (!empty($value)) ? $value : '0';
                  ?>
                  <td align="right" ><a href="javascript:void(0);" onclick="updateVal('<?php echo $m->job_class_id?>','<?php echo $v->id?>')"><span id="td<?php echo $m->job_class_id?><?php echo $v->id?>" class="td-val"><?php echo number_format($valuex, 0)?></span></a><input type="text" style="display:none" value="<?php echo number_format($valuex, 0)?>" id="text<?php echo $m->job_class_id?><?php echo $v->id?>" class="text-val text-right form-control" onchange="changeVal()"></td>
                </tr>
                <?php } ?>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="tab-pane" id="tab1FollowUs">
      <div class="row">
        <div class="col-md-12">
          <table class="table table-hover table-bordered" id="tbl">
            <thead>
              <tr>
                <th style="width:20%">Job Class</th>
                <th style="width:15%">Job Value</th>
                <th style="width:15%">Value Min</th>
                <th style="width:15%">Value Max</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($pos_non->result() as $m):?>
                <tr>
                  <th rowspan="3"><?php echo $m->job_class_nm?><br/>(Grade <?php echo $m->gradeval_top ?>)</th>
                  <?php foreach($val->result() as $v){?>
                  <td><?php echo $v->title?></td>
                  <?php $value_min = getValue('value_min', 'payroll_job_value_matrix', array('session_id'=>'where/'.$session_id,'job_class_id'=>'where/'.$m->job_class_id, 'job_value_id'=>'where/'.$v->id));
                    $value_minx = (!empty($value_min)) ? $value_min : '0';

                    $value_max = getValue('value_max', 'payroll_job_value_matrix', array('session_id'=>'where/'.$session_id,'job_class_id'=>'where/'.$m->job_class_id, 'job_value_id'=>'where/'.$v->id));
                    $value_maxx = (!empty($value_max)) ? $value_max : '0';
                  ?>
                  <td align="right"><a href="javascript:void(0);" onclick="updateValMin('<?php echo $m->job_class_id?>','<?php echo $v->id?>')"><span class="td-min" id="td_min<?php echo $m->job_class_id?><?php echo $v->id?>"><?php echo number_format($value_minx, 0)?></span></a><input type="text" style="display:none" value="<?php echo number_format($value_minx, 0)?>" id="text_min<?php echo $m->job_class_id?><?php echo $v->id?>" class="text-val-min text-right form-control" onchange="changeValMin()"></td>
                  <td align="right"><a href="javascript:void(0);" onclick="updateValMax('<?php echo $m->job_class_id?>','<?php echo $v->id?>')"><span class="td-max" id="td_max<?php echo $m->job_class_id?><?php echo $v->id?>"><?php echo number_format($value_maxx,0)?></span></a><input type="text" style="display:none" value="<?php echo number_format($value_maxx,0)?>" id="text_max<?php echo $m->job_class_id?><?php echo $v->id?>" class="text-val-max text-right form-control" onchange="changeValMax()"></td>
                </tr>
                <?php } ?>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="<?php echo assets_url('assets/plugins/jquery-maskmoney/jquery.maskMoney.js')?>"></script>
<script type="text/javascript">
  var sess = $('#sess').val();
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
          url: "payroll_config/edit_matrix/"+sess+"/"+a+"/"+b,
          data: dataString,
          cache: false,
          success: function(html){
            $("#text"+ID).hide();
            $("#td"+ID).html(addCommas(first));
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
          url: "payroll_config/edit_matrix/"+"/"+sess+"/"+a+"/"+b+"/_min",
          data: dataString,
          cache: false,
          success: function(html){
            $("#text_min"+ID).hide();
            $("#td_min"+ID).html(addCommas(first));
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
          url: "payroll_config/edit_matrix/"+sess+"/"+a+"/"+b+"/_max",
          data: dataString,
          cache: false,
          success: function(html){
            $("#text_max"+ID).hide();
            $("#td_max"+ID).html(addCommas(first));
            $("#td_max"+ID).show();
          }
        });
    }
  }
</script>
