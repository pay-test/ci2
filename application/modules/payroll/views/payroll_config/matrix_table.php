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
                  <td align="right" class="td-val"><a href="javascript:void(0);" onclick="updateVal('<?=$m->job_class_id?>','<?=$v->id?>')"><span id="td<?=$m->job_class_id?><?=$v->id?>"><?= $valuex?></span></a><input type="text" style="display:none" value="0" id="text<?=$m->job_class_id?><?=$v->id?>" class="text-val text-right form-control" onchange="changeVal()"></td>
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
                  <th rowspan="3"><?php echo $m->job_class_nm?></th>
                  <?php foreach($val->result() as $v){?>
                  <td><?php echo $v->title?></td>
                  <?php $value_min = getValue('value_min', 'payroll_job_value_matrix', array('session_id'=>'where/'.$session_id,'job_class_id'=>'where/'.$m->job_class_id, 'job_value_id'=>'where/'.$v->id));
                    $value_minx = (!empty($value_min)) ? $value_min : '0';

                    $value_max = getValue('value_max', 'payroll_job_value_matrix', array('session_id'=>'where/'.$session_id,'job_class_id'=>'where/'.$m->job_class_id, 'job_value_id'=>'where/'.$v->id));
                    $value_maxx = (!empty($value_max)) ? $value_max : '0';
                  ?>
                  <td class="td-min" align="right"><a href="javascript:void(0);" onclick="updateValMin('<?=$m->job_class_id?>','<?=$v->id?>')"><span id="td_min<?=$m->job_class_id?><?=$v->id?>"><?= $value_minx?></span></a><input type="text" style="display:none" value="0" id="text_min<?=$m->job_class_id?><?=$v->id?>" class="text-val-min text-right form-control" onchange="changeValMin()"></td>
                  <td class="td-max" align="right"><a href="javascript:void(0);" onclick="updateValMax('<?=$m->job_class_id?>','<?=$v->id?>')"><span id="td_max<?=$m->job_class_id?><?=$v->id?>"><?= $value_maxx?></span></a><input type="text" style="display:none" value="0" id="text_max<?=$m->job_class_id?><?=$v->id?>" class="text-val-max text-right form-control" onchange="changeValMax()"></td>
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
<script type="text/javascript" src="<?=assets_url('assets/plugins/jquery-maskmoney/jquery.maskMoney.js')?>"></script>
