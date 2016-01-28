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
                    <a href="#myTab2_example3" data-toggle="tab">
                        Compensation MIX
                    </a>
                </li>

                <li>
                    <a href="#myTab2_example3" data-toggle="tab">
                        JM Parameter
                    </a>
                </li>

                <li>
                    <a href="#myTab2_example4" data-toggle="tab">
                        Payroll Divider
                    </a>
                </li>

                <li>
                    <a href="#myTab2_example5" data-toggle="tab">
                        Exchange Rate
                    </a>
                </li>
                <li>
                    <a href="#myTab2_example6" data-toggle="tab">
                       COLA
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <!--Tab Matrix-->
                <div class="tab-pane fade in active" id="tab-matrix">
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="matrix">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="label-form">Select Session</label>
                                        </div>
                                        <div class="col-md-6">
                                            <select class="select2" id="session_select" style="width:100%">
                                                <option>-- Select Session --</option>

                                                <?php foreach($session->result() as $s):?>
                                                    <option value="<?php echo $s->id?>"><?php echo $s->description?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="label-form">Select Section</label>
                                        </div>
                                        <div class="col-md-6">
                                            <select class="select2" id="section_select" style="width:100%">
                                                <option>-- Select Section --</option>
                                                <?php foreach($org->result() as $o):?>
                                                    <option value="<?php echo $o->org_id?>"><?php echo $o->org_nm?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                    </div>
                                    <hr/>
                                    <div id="table_matrix">

                                    </div>

                                    <div class="row row-matrix">
                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB Compensation -->
                <div class="tab-pane fade" id="myTab2_example2">
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                            </div>
                        </div>
                    </div>
                </div>

                <!--Tab JM Parameter-->
                <div class="tab-pane fade" id="myTab2_example3">
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                            </div>
                        </div>
                    </div>
                </div>

                <!--Tab Payroll Divider-->
                <div class="tab-pane fade in active" id="myTab2_example4">
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB Exchange Rate -->
                <div class="tab-pane fade" id="myTab2_example5">
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                            </div>
                        </div>
                    </div>
                </div>

                <!--Tab COLA-->
                <div class="tab-pane fade" id="myTab2_example6">
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END CONTAINER -->
