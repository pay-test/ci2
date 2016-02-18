<!-- BEGIN PAGE CONTAINER-->
<div class="page-content">
    <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
    <div id="portlet-config" class="modal hide">
      <div class="modal-header">
        <button data-dismiss="modal" class="close" type="button"></button>
        <h3>Widget Settings</h3>
      </div>
      <div class="modal-body"> Widget settings form goes here </div>
    </div>
    <div class="clearfix"></div>
    <div class="content">
      <ul class="breadcrumb">
        <li>
          <p>YOU ARE HERE</p>
        </li>
        <li><a href="#" class="active"><?php echo 'Payroll'; ?></a> </li>
        <li><a href="#" class="active"><?php echo 'Config'; ?></a> </li>
      </ul>
        <div class="tabbable">
            <ul id="myTab2" class="nav nav-tabs nav-justified">
                <li class="active">
                    <a href="#tab-matrix" data-toggle="tab">
                        Job Value Matrix
                    </a>
                </li>

                <li>
                    <a href="#tab-com" data-toggle="tab">
                        Compensation MIX
                    </a>
                </li>

                <li>
                    <a href="#tab-jm" data-toggle="tab">
                        JM Parameter
                    </a>
                </li>

                <li>
                    <a href="#tab-divider" data-toggle="tab">
                        Payroll Divider
                    </a>
                </li>

                <li>
                    <a href="#tab-rate" data-toggle="tab">
                        Converter
                    </a>
                </li>
                <li>
                    <a href="#tab-cola" data-toggle="tab">
                       COLA
                    </a>
                </li>
            </ul>
            <div class="tab-content" style="height:550px;overflow:auto;">
                <!--Tab Matrix-->
                <div class="tab-pane fade in active" id="tab-matrix">
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="matrix">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label class="label-form">Select Session</label>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="select2" id="session_select" style="width:100%">
                                                <option>-- Select Session --</option>
                                                <?php foreach($session->result() as $s):
                                                $selected = ($s->id == sessNow()) ? "selected='selected'" : '';
                                                ?>
                                                    <option value="<?php echo $s->id?>" <?php echo $selected?>><?php echo $s->description?></option>
                                                <?php endforeach;?>
                                            </select>
                                            <input type="hidden" value="" id="sess">
                                        </div>
                                    </div>
                                    <br/>
                                    <hr/>
                                    <div id="table_matrix">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB Compensation -->
                <div class="tab-pane fade" id="tab-com">
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="com">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label class="label-form">Select Session</label>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="select2" id="session_select_com" style="width:100%">
                                                <option>-- Select Session --</option>
                                                <?php foreach($session->result() as $s):
                                                    $selected = ($s->id == sessNow()) ? "selected='selected'" : '';
                                                ?>
                                                    <option value="<?php echo $s->id?>" <?php echo $selected?>><?php echo $s->description?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                    </div>
                                    <hr/>
                                    <div id="table_com">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--Tab JM Parameter-->
                <div class="tab-pane fade" id="tab-jm">
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="jm">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label class="label-form">Select Session</label>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="select2" id="session_select_jm" style="width:100%">
                                                <option>-- Select Session --</option>
                                                <?php foreach($session->result() as $s):
                                                    $selected = ($s->id == sessNow()) ? "selected='selected'" : '';
                                                ?>
                                                    <option value="<?php echo $s->id?>" <?php echo $selected?>><?php echo $s->description?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                    </div>
                                    <hr/>
                                    <div id="table_jm">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--Tab Payroll Divider-->
                <div class="tab-pane fade in active" id="tab-divider">
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="divider">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label class="label-form">Select Session</label>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="select2" id="session_select_divider" style="width:100%">
                                                <option>-- Select Session --</option>
                                                <?php foreach($session->result() as $s):
                                                    $selected = ($s->id == sessNow()) ? "selected='selected'" : '';
                                                ?>
                                                    <option value="<?php echo $s->id?>" <?php echo $selected?>><?php echo $s->description?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="label-form">Value</label>
                                        </div>
                                        <div class="col-md-1">
                                            <label style="font-weight:700" class="form-label" id="divider-value"></label>
                                            <input type="hidden" class="form-control" id="divider-id">
                                            <input type="text" style="display:none" class="form-control" id="divider-value-text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB Exchange Rate -->
                <div class="tab-pane fade" id="tab-rate">
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="rate">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label class="label-form">Select Session</label>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="select2" id="session_select_rate" style="width:100%">
                                                <option>-- Select Session --</option>
                                                <?php foreach($session->result() as $s):
                                                    $selected = ($s->id == sessNow()) ? "selected='selected'" : '';
                                                ?>
                                                    <option value="<?php echo $s->id?>" <?php echo $selected?>><?php echo $s->description?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="label-form">Value</label>
                                        </div>
                                        <div class="col-md-2">
                                            <label style="font-weight:700" class="form-label" id="rate-value"></label>
                                            <input type="hidden" class="form-control" id="rate-id">
                                            <input type="text" style="display:none" class="form-control money" id="rate-value-text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--Tab COLA-->
                <div class="tab-pane fade" id="tab-cola">
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="cola">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label class="label-form">Select Session</label>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="select2" id="session_select_cola" style="width:100%">
                                                <option>-- Select Session --</option>
                                                <?php foreach($session->result() as $s):
                                                    $selected = ($s->id == sessNow()) ? "selected='selected'" : '';
                                                ?>
                                                    <option value="<?php echo $s->id?>" <?php echo $selected?>><?php echo $s->description?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="label-form">Value</label>
                                        </div>
                                        <div class="col-md-2">
                                            <label style="font-weight:700" class="form-label" id="cola-value"></label>
                                            <input type="hidden" class="form-control" id="cola-id">
                                            <input type="text" style="display:none" class="form-control money" id="cola-value-text">
                                        </div>
                                

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END CONTAINER -->

