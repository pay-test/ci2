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
        <li><a href=""><?php echo $title; ?></a> </li>
        <li><a href="" class="active"><?php echo $title.' - Generate Salary'; ?></a> </li>
      </ul>
      <div class="row-fluid">
        <div class="col-md-7">
          <div class="grid simple ">
            <div class="grid-title">
              <h4><span class="semi-bold">Process Monthly Income For Regular Income</span></h4>
            </div>
            <div class="grid-body">
              <form id="form-process2" action="#">
                <div id="loading2" style="display:none"><img src="<?php echo base_url('assets/assets/img/loading.gif')?>"></div>
                <div id="form-monthly-process">
                <div class="form-body">
                  <div class="form-group">
                    <label class="control-label col-md-3">Period</label>
                    <div class="col-md-9">
                        <select class="form-control select2 period" name="period_id">
                          <option value="0">Select session..</option>
                          <?php if ($period->num_rows() > 0) {
                            foreach ($period->result_array() as $p) { 
                              $selected = ($p['year'] == date('Y') && $p['month'] == date('m')) ? "selected='selected'" : '';
                              ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo $selected?>><?php echo $p['title']; ?></option>
                            <?php }
                          } ?>
                        </select>
                        <span class="help-block"></span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-md-3">Status</label>
                    <div class="col-md-9">
                      <div id="status">
                        <div class="radio radio-success">
                          <input value="0" name="status" id="open" type="radio">
                          <label for="open">Open</label>
                          <input checked="checked" value="1" name="status" id="close" type="radio">
                          <label for="close">Close</label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <button type="submit" id="btnSave" class="btn btn-primary m-l-10">Process</button>
                  </div>
                </div>
                <hr/>
                  <div class="row">
                    <span class="text-extra-small">
                      payroll process is used to generate a monthly salary and pph for all employees based on the chosen period
                    </span>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="col-md-5">
          <div class="grid simple ">
            <div class="grid-title">
              <h4><span class="semi-bold">Generate Basic Salary Value</span></h4>
            </div>
            <div class="grid-body">
              <?php echo form_open(base_url('payroll/payroll_setup/generate_value'), array('id'=>'form-process'))?>
              <div id="loading" style="display:none"><img src="<?php echo base_url('assets/assets/img/loading.gif')?>"></div>
              <div id="form-generate"> 
                <div class="form-body">
                  <div class="form-group">
                    <div class="col-md-12 pull-left">
                        <button type="submit" id="btnSave" class="btn btn-primary">Generate</button>
                    </div>
                  </div>
                  <div style="padding-bottom: 30px"></div>
                  <hr/>
                  <div class="row">
                    <div class="form-group">
                      <span class="text-extra-small">Generate salary value is used to calculate the basic salary's of each employee, this feature is used when there is a new employee who has not calculated the basic salary of the previous period and when there is a change in the job value matrix configuration
                      </span>
                    </div>
                  </div>
                </div>
              </form>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- e.o row fluid -->
      <div class="row-fluid">
        <div class="col-md-12">
          <div class="grid simple ">
            <div class="grid-title">
              <h4><span class="semi-bold">Process Monthly Income For Iregular Income</span></h4>
            </div>
            <div class="grid-body">
              <form id="form-process3" action="#">
                <div id="loading3" style="display:none"><img src="<?php echo base_url('assets/assets/img/loading.gif')?>"></div>
                <div id="form-monthly-process">
                  <div class="form-body">
                    <div class="form-group">
                      <label class="control-label col-md-3">Period</label>
                      <div class="col-md-7">
                          <select class="form-control select2 period" name="period_iregular_id">
                            <option value="0">Select session..</option>
                            <?php if ($period->num_rows() > 0) {
                              foreach ($period->result_array() as $p) { 
                                $selected = ($p['year'] == date('Y') && $p['month'] == date('m')) ? "selected='selected'" : '';
                                ?>
                              <option value="<?php echo $p['id']; ?>" <?php echo $selected?>><?php echo $p['title']; ?></option>
                              <?php }
                            } ?>
                          </select>
                          <span class="help-block"></span>
                      </div>
                      <label class="control-label col-md-3">Choose Iregular Component</label>
                      <div class="col-md-7">
                          <select id="multi" style="width:100%" multiple>
                            <?php if ($ireg_comp->num_rows() > 0) {
                              foreach ($ireg_comp->result_array() as $p) {?>
                              <option value="<?php echo $p['id']; ?>"><?php echo $p['title']; ?></option>
                              <?php }
                            } ?>
                          </select>
                      </div>
                      <div class="col-md-2">
                          <button type="submit" id="btnSaveIregullar" class="btn btn-primary m-l-10">Process</button>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
              <br/>
              <br/>
              <br/>
            </div>
          </div>
        </div>
      </div> <!-- e.o row fluid -->
      <!--
      <div class="row-fluid">
          <div class="col-md-6"></div>
        <div class="col-md-6">
          <div class="grid simple ">
            <div class="grid-title">
              <h4><span class="semi-bold">Generate New Session</span></h4>
            </div>
            <div class="grid-body">
              <?php echo form_open(base_url('payroll/payroll_setup/generate_new_Session'), array('id'=>'form-new-session'))?>
              <div id="loading-session" style="display:none"><img src="<?php echo base_url('assets/assets/img/loading.gif')?>"></div>
              <div id="form-generate-session"> 
                <div class="form-body">
                  <div class="form-group">
                    <div class="col-md-9">
                        <span>Generate new session from session <?php echo date('Y')-1?> to session <?php echo date('Y')?></span>
                        <span class="help-block"></span>
                    </div>
                  </div>
                  <div class="form-group">
                    <button type="submit" id="" class="btn btn-primary">Generate</button>
                  </div>
                  <div style="padding-bottom: 10px"></div>
                  <hr/>
                  <div class="row">
                    <div class="form-group">
                      <span class="text-extra-small" >
                        Generate new session is used to copy the entire master data which used for calculating the salary in the previous session, to be re-used in the new session.
                      </span>
                    </div>
                  </div>
                </div>
              </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      -->
</div>
<!-- END CONTAINER -->