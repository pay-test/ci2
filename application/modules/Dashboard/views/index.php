<!-- BEGIN PAGE CONTAINER-->
      <div class="page-content">
        <div class="content">
          <ul class="breadcrumb">
			      <li>
			        <p>YOU ARE HERE</p>
			      </li>
			      <li>Dashboard</li>
			      <li>Attendance</li>
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
	              			<div id="grafik_att" style="margin-bottom:15px;"></div>
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
  $('#grafik_att').highcharts({
	  chart: {type: 'column', height: '350', marginRight: '25'},
    title: {text: 'Chart of Attendance', style: { "color": "#000", "fontSize": "14px" }},
    xAxis: {
      categories: [<?php echo $nik;?>],
      labels: {
      		enabled : true,
      		rotation : -45,
      },
    },
    yAxis: {
    	max: 30,
      min: 0,
      title: {
          text: 'Attendance (days)'
      },
      stackLabels: {
          enabled: false,
          style: {
              fontWeight: 'bold',
              color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
          }
      }
    },
    tooltip: {
        shared: true,
        useHTML: true,
        headerFormat: '<small>{point.key}</small><table>',
        pointFormat: '<tr><td style="color: {series.color}">{series.name}: </td>' +
            '<td style="text-align: right"><b>{point.y}</b></td></tr>',
        footerFormat: '</table>',
    },
    plotOptions: {
      column: {
        stacking: 'normal',
        dataLabels: {
          enabled: true,
          color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
          style: {
              textShadow: '0 0 3px black, 0 0 3px black'
          }
        }
      }
    },series: [{
        name: 'ATTEND',
        data: [<?php echo $hadir;?>],
        color: '#00BB27',
        point: {
          events: {
            click: function () {
            	var nik = this.id_emp;
            	$("#block_grafik").load("<?php echo site_url('dashboard/list_data/"+nik+"');?>");
            }
          }
      	}
    }, {
        name: 'OFF',
        data: [<?php echo $off;?>],
        color: '#000000',
        point: {
          events: {
            click: function () {
            	var nik = this.id_emp;
            	$("#block_grafik").load("<?php echo site_url('dashboard/list_data/"+nik+"');?>");
            }
          }
      	}
    }, {
        name: 'ALPA',
        data: [<?php echo $alpa;?>],
        color: '#fd0000',
        point: {
          events: {
            click: function () {
            	var nik = this.id_emp;
            	$("#block_grafik").load("<?php echo site_url('dashboard/list_data/"+nik+"');?>");
            }
          }
      	}
    }]
	});
});
</script>