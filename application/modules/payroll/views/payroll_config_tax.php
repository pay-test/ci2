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
        <div class="row">
            <div class="span12">
                <div class="grid simple ">
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
            </div>
        </div>
        <br/>

        <div class="tabbable">
            <ul id="myTab2" class="nav nav-tabs nav-justified">
                <li class="active">
                    <a href="#tab-component" data-toggle="tab">
                        Tax Component
                    </a>
                </li>

                <li>
                    <a href="#tab-ptkp" data-toggle="tab">
                        PTKP
                    </a>
                </li>

                <li>
                    <a href="#tab-progressive" data-toggle="tab">
                        Progressive Tax
                    </a>
                </li>

                <li>
                    <a href="#tab-method" data-toggle="tab">
                        Method
                    </a>
                </li>

                <li>
                    <a href="#tab-umk" data-toggle="tab">
                        UMK
                    </a>
                </li>

                <li>
                    <a href="#tab-rate" data-toggle="tab">
                        Tax Rate
                    </a>
                </li>
            </ul>

            <div class="tab-content" style="height:550px;overflow:auto;">

                <!--Tab COPONENT-->
                <div class="tab-pane fade in active" id="tab-component">
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="component">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB PTKP -->
                <div class="tab-pane fade" id="tab-ptkp">
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="ptkp">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--Tab Progressive-->
                <div class="tab-pane fade" id="tab-progressive">
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="progressive">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--Tab Method-->
                <div class="tab-pane fade" id="tab-method">
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="method">
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--Tab Payroll Divider-->
                <div class="tab-pane fade" id="tab-umk">
                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="umk">
                                    <div class="col-md-2">
                                        <label class="label-form">UMK Value</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label style="font-weight:700" class="form-label" id="value"></label>
                                        <input type="hidden" class="form-control" id="id">
                                        <input type="text" style="display:none" class="form-control money" id="value-text">
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
                                    <div class="col-md-2">
                                        <label class="label-form">Rate Value</label>
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
        </div>
    </div>
</div>
<!-- END CONTAINER -->




<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form" class="form-horizontal">
                    <input id="group_id" type="hidden" value="" name="id"/> 
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-4">Name</label>
                            <div class="col-md-8">
                                <input name="title" placeholder="Name" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">Column</label>
                            <div class="col-md-8">
                                <input name="column" placeholder="Code" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->


<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form_ptkp" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form-ptkp" class="form-horizontal">
                    <input id="group_id" type="hidden" value="" name="id"/> 
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-4">Name</label>
                            <div class="col-md-8">
                                <input name="title" placeholder="Name" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">Value</label>
                            <div class="col-md-8">
                                <input name="value" placeholder="Code" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->




<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form_progressive" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form-progressive" class="form-horizontal">
                    <input id="group_id" type="hidden" value="" name="id"/> 
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-4">Min Value</label>
                            <div class="col-md-8">
                                <input name="value_min" placeholder="Min Value" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-4">Max Value</label>
                            <div class="col-md-8">
                                <input name="value_max" placeholder="Code" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                         <div class="form-group">
                            <label class="control-label col-md-4">Tax (%)</label>
                            <div class="col-md-8">
                                <input name="percentage" placeholder="Code" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->



<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form_method" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <form action="#" id="form_method" class="form-horizontal">
                    <input id="group_id" type="hidden" value="" name="id"/> 
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-4">Name</label>
                            <div class="col-md-8">
                                <input name="title" placeholder="Name" class="form-control" type="text">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="form-group">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->
