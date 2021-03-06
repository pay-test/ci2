<!-- BEGIN PAGE CONTAINER-->
      <div class="page-content">
        <div class="content">
          <ul class="breadcrumb">
			      <li>
			        <p>YOU ARE HERE</p>
			      </li>
			      <li>Dashboard</li>
			      <li>Overtime</li>
			    </ul>
			    
			    <?php
			    if($person_id > 1 && $flagz==0) {?>
			    <div class="row-fluid">
			      <div class="span12">
			      	<div class="col-md-12">
            		<div class="tab-content">
              		<div class="tab-pane active" id="content" style="padding-bottom:0px;">
              			<!--<div id="coba" class="grid simple transparent">
											<div class="grid-title">
												<h4>Chart of <span class="semi-bold">Attendance</span></h4>
											</div>
										</div>-->
										<div id="block_grafik">
	              			<div>
	              				<div id="grafik_ot" style="height:355px;overflow:hidden;"></div>
	              				<div style="margin:auto;width:<?php echo $width_legend;?>%">
									    		<?php echo $legend;?>
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
  $('#grafik_ot').highcharts({
	  chart: {type: 'column', height: '400', marginRight: '25'},
    title: {text: 'Chart of Overtime Rasio', style: { "color": "#000", "fontSize": "14px" }},
    xAxis: {
      categories: [<?php echo $nik;?>],
      labels: {
      		enabled : true,
      		rotation : -45,
      }
    },
    yAxis: {
    	max: 55,
      min: 0,
      title: {
          text: 'Percen (%)'
      },
      stackLabels: {
          enabled: true,
          style: {
              fontWeight: 'bold',
              color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
          }
      },
      plotLines: [{
          color: 'green',
          dashStyle: 'dash',
          width: 1,
          value: <?php echo $avg_rasio;?>,
          label: {
              style: {color: 'green'},
              text: '',
              align: 'left',
              x: -30
          }
      },{
          color: 'red',
          dashStyle: 'dash',
          width: 1,
          value: <?php echo $limit_ot;?>,
          label: {
              style: {color: 'red'},
              text: '',
              align: 'left',
              x: -30
          }
      }]
    },
    tooltip: {
    	useHTML: true,
      headerFormat: '<b>{point.key}</b><table>',
      pointFormat: '{point.info}',
      footerFormat: '</table>',
      /*positioner: function () {
          return { x: 200, y: 50 };
      },*/
    },
    plotOptions: {
      column: {
        dataLabels: {
          enabled: true,
          color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'black',
          style: {
              textShadow: '0 0 3px white, 0 0 3px white'
          }
        }
      }
    },series: [{
        name: '',
        data: [<?php echo $rasio;?>],
        point: {
          events: {
            click: function () {
            	var nik = this.id_emp;
            	$("#block_grafik").load("<?php echo site_url('dashboard/list_data/"+nik+"');?>");
            }
          }
      	}
    }],
	});

});
</script>