<!-- BEGIN PAGE CONTAINER-->
      <div class="page-content">
        <div class="content">
          <ul class="breadcrumb">
			      <li>
			        <p>YOU ARE HERE</p>
			      </li>
			      <li>Dashboard</li>
			      <li>Slide & no Slide</li>
			    </ul>
			    
			    <?php
			    if($person_id > 1 && $flagz==0) {?>
			    <div class="row-fluid">
			      <div class="span12">
			      	<div class="col-md-12">
            		<div class="tab-content">
              		<div class="tab-pane active" id="content" style="padding-bottom:0px;">
              			<div id="block_grafik">
            			   <form id="search_pie" action="<?php echo site_url('dashboard/index_slide');?>" method="post">
							        <div class="row">
							          <div class="col-sm-12" style="padding:0px;">
							          	<div class="col-md-12">
								          	<div class="col-md-1 pull-right">
						                  <div class="row">
						                    <div class="col-md-12">
						                       <button type="submit" class="btn btn-info btn-search-pie"><i class="fa fa-search"></i></button>
						                    </div>
						                  </div>
							              </div>
							              <div class="col-md-2 pull-right">
						                  <div id="datepicker_start" class="input-append date success no-padding">
						                    <input type="text" class="form-control start_att tgl span2" name="date_slide" value="<?php echo $tgl;?>" required>
						                    <span class="add-on"><span class="arrow"></span><i class="fa fa-th"></i></span> 
						                  </div>
							              </div>
						              </div>
							          </div>
							        </div>
							       </form>
	              			<div class="col-md-12">
	              				<div id="content_pie">
		              				<div class="col-md-4">
		              					<?php	if($chart_ada) {?>
		              					<div id="mz_grafik_pie"></div>
		              					<?php }?>
		              				</div>
		              				<div class="col-md-8">
		              					<div id="mz_tabel" style="margin-bottom:15px;"><?php echo $list_data_tabel;?></div>
		              				</div>
		              			</div>
	              			</div>
	              		</div>
              		</div>
            		</div>
            	</div>
	          </div>
	        </div>
			  	<?php }?>
			  </div>
			</div>
          
<script src="<?php echo assets_url('assets/plugins/jquery.js');?>"></script>
<script type="text/javascript">
$(function () {
  $('.tgl').datepicker({
      format: 'yyyy-mm-dd', 
      autoclose: true,
      todayHighlight: true
  });
  
  $('#mz_grafik_pie').highcharts({
    chart: {height: '350', marginRight: '25'},
 		title: {text: 'Chart of Attend', style: { "color": "#000", "fontSize": "14px" }},
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.y}',
            },
            showInLegend: true
        }
    },
    series: [{
      type: 'pie',
      name: 'Persentase',
      data: [<?php echo $chart_pie;?>],
     	point: {
          events: {
              /*click: function () {
              	var no_slide = this.no_slide;
              	$("#mz_tabel").load("<?php echo site_url('dashboard/list_data_no_slide/'.$tgl.'/"+no_slide+"');?>");
              }*/
          }
      }
    }]
  });
});
</script>
<style>
#mz_grafik_pie .highcharts-button{display:none;}
</style>