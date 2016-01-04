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
        <li><a href="<?php echo base_url('absensi/kehadiran')?>" class="active">Kehadiran</a> </li>
      </ul>
      <div class="page-title"> <i class="icon-custom-left" onclick="javascript:window.location='<?php echo site_url("dashboard");?>'"></i>
        <h3><span class="semi-bold">Kehadiran</span></h3>
      </div>
      <div class="row-fluid">
        <div class="span12">
          <div class="grid simple ">
            <!--
            <div class="grid-title">
              <h4>Table <span class="semi-bold">Styles</span></h4>
            </div>
            -->
            <div class="grid-body ">
                    
               <form id="search" action="<?php echo site_url($path_file.'/search');?>" method="post">
                      <h3>Pencarian</h3>
                      <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-3">
                                <label>Departement</label>
                                <select id="departement" style="width:100%">
                                    <option value="0">-- Pilih Departement --</option>
                                    <option value="0">Tes</option>
                                    <option value="0">Tes 2</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="row">
                                    <label>Tanggal</label>
                                    <div id="datepicker_start" class="input-append date success no-padding">
                                      <input type="text" class="form-control" name="start_cuti" required>
                                      <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <label>&nbsp;</label>
                                <label class="form-label text-center">s/d</label>
                            </div>
                            <div class="col-md-2">
                                <div class="row">
                                    <label>&nbsp;</label>
                                    <div id="datepicker_end" class="input-append date success no-padding">
                                        <input type="text" class="form-control" name="end_cuti" required>
                                        <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div class="row">
                                  <div class="col-md-12">
                                      <button type="submit" class="btn btn-info"><i class="fa fa-search"></i>&nbsp;Cari</button>
                                  </div>
                                </div>
                            </div>
                        </div>
                      </div>
                    </form>
                    <br/>

                    <h3>File Upload</h3>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-3">
                                <label>File</label>
                                <input type="file" id="file_upload" name="userfile">
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <button type="submit" name="submit" class="btn btn-primary"><i class='fa fa-upload'> Upload</i></button>
                            </div>
                        </div>
                        <div class="col-md-12">

                        <br/>
                        <legend>
                            <label><a href="http://localhost/dus/uploads/template_kehadiran.xls">Download Template</a></label>  
                        </legend>
                        </div>
                    </div>

                    <form method="post" action="<?php echo site_url($path_file.'/main/');?>" id="<?php echo $filename;?>">
  <div style="float:left;">
  	<h3><?php echo $title;?></h3>
	</div>
	<div class="clear"></div>
	<table class="table table-striped table-hover">
    <tr>
    	<input type="hidden" id="temp_id" value="">
    	<th class="box_delete">
    		<input type="checkbox" onclick="checkedAll('<?php echo $filename;?>', true)" id="primary_check" value="" name="">
    	</th>
    	<?php
    	foreach($grid as $r)
    	{
    		echo "<th>".$r."</th>";
    	}
    	?>
    	<th class='action'>Action</th>
    </tr>
    <?php
	
	$style='';
    foreach($query_list->result_array() as $r)
    {
		
		/*$getdep=GetValue("id_department", "employee", array("id"=> "where/".$r['id_employee']));
		//print_mz($dep);
		if($dep){if(in_array($getdep,$dep)){$style.='';}else{$style.='style="display:none;"'; }}*/
    	echo "<tr $style id='listz-".$r['id']."' >";
    	echo "<td class='box_delete'><input type='checkbox' class='delete' id='del".$r['id']."' value='".$r['id']."'></td>";
    	foreach($list as $s)
    	{
    		if($s=="title") {
				$r[$s] = GetValue("title", "department", array("id"=> "where/".GetValue("id_department", "employee", array("id"=> "where/".$r['id_employee']))));
			}
			elseif($s=="employee") {
				$r[$s] = GetValue("name", "employee", array("id"=> "where/".GetValue("id", "employee", array("id"=> "where/".$r['id_employee']))));
			}
			elseif($s=='bulan'){
				$r['bulan']=getBulan($r['bulan']).' '.$r['tahun'];
			}
    		echo "<td>".$r[$s]."</td>";
    	}
    	echo "<td class='action'>";
    	hak_edit1($this->session->userdata('webmaster_grup'),$filename,1,$r['id']);
    	echo "</tr>";
    }
    ?>
	</table>
	<br><br>
  <div class="clear"></div>
  <div class="pagination">
  	<ul>
      <?php echo $pagination;?>
    </ul>
  </div>
	<div id="id_temp" value=""></div>
</form>


				</div>
            </div>
        </div>
    </div>
</div>