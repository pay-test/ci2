<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*************************************
  * Created : Dec 2011
  * Creator : Mazhters Irwan
  * Email   : irwansyah@komunigrafik.com
  * CMS ver : CI ver.2.0
*************************************/	

class config extends CI_Controller {
	
	var $filename = "config";
	var $tabel = "config";
	var $id_primary = "id";
	var $title = "Data Config";
	
	function __construct()
	{
		parent::__construct();
		$this->lang->load('config');
	}
	
	function index()
	{
    $data['title'] = $this->title;
    permission();
		$this->_render_page('index', $data);
	}
	
	function _render_page($view, $data=null, $render=false)
  {
      // $this->viewdata = (empty($data)) ? $this->data: $data;
      // $view_html = $this->load->view($view, $this->viewdata, $render);
      // if (!$render) return $view_html;
      $data = (empty($data)) ? $this->data : $data;
      if ( ! $render)
      {
          $this->load->library('template');

              if(in_array($view, array('index', 'config_edit')))
              {
                  $this->template->set_layout('default');

                  $this->template->add_css('assets/plugins/bootstrap-select2/select2.css');
                  $this->template->add_css('assets/plugins/bootstrap-datepicker/css/datepicker.css');
                  $this->template->add_css('assets/plugins/boostrap-clockpicker/bootstrap-clockpicker.min.css');

                  $this->template->add_js('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js');
                  $this->template->add_js('assets/plugins/boostrap-clockpicker/bootstrap-clockpicker.min.js');
                  $this->template->add_js('modules/js/'.$this->filename.'.js');
              }

          if ( ! empty($data['title']))
          {
              $this->template->set_title($data['title']);
          }

          $this->template->load_view($view, $data);
      }
      else
      {
          return $this->load->view($view, $data, TRUE);
      }
  }
    
	function detail()
	{
		//Set Global
		permission();
		$data = GetHeaderFooter();
		$data['path_file'] = $this->filename;
		$data['main_content'] = $data['path_file'].'_form';
		$data['filename'] = $this->filename;
		$data['title'] = $this->title;
		//End Global
		
		$config = array();
		$q = GetAll("kg_".$this->tabel);
		foreach($q->result_array() as $r) {
			$config[$r['title']] = $r['value'];
		}
		$data['config'] = $config;
		
		$this->load->view('config_form',$data);
	}
	
	function update()
	{
		$webmaster_id = permission();
		$data=array();
		$post = $this->input->post();
		foreach($post as $key=> $val) {
			$data[$key] = $val;
			$this->db->where("title", $key);
			$this->db->update("kg_".$this->tabel, array("value"=> $val));
		}
		$data['create_date'] = date("Y-m-d H:i:s");
		$this->db->insert("kg_".$this->tabel."_temp", $data);
		//print_mz($data);
	}
}
?>