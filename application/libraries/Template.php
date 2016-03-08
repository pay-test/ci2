<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Template Library
 * Handle masterview and views within masterview
 */

class Template {

    private $_ci;

    protected $brand_name = 'MCCI';
    protected $title_separator = ' - ';
    protected $ga_id = FALSE; // UA-XXXXX-X

    protected $layout = 'default';

    protected $title = FALSE;
    protected $description = FALSE;

    protected $metadata = array();

    protected $js = array();
    protected $css = array();

    function __construct()
    {
        $this->_ci =& get_instance();
    }

    /**
     * Set page layout view (1 column, 2 column...)
     *
     * @access  public
     * @param   string  $layout
     * @return  void
     */
    public function set_layout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Set page title
     *
     * @access  public
     * @param   string  $title
     * @return  void
     */
    public function set_title($title)
    {
        $this->title = $title;
    }

    /**
     * Set page description
     *
     * @access  public
     * @param   string  $description
     * @return  void
     */
    public function set_description($description)
    {
        $this->description = $description;
    }

    /**
     * Add metadata
     *
     * @access  public
     * @param   string  $name
     * @param   string  $content
     * @return  void
     */
    public function add_metadata($name, $content)
    {
        $name = htmlspecialchars(strip_tags($name));
        $content = htmlspecialchars(strip_tags($content));

        $this->metadata[$name] = $content;
    }

    /**
     * Add js file path
     *
     * @access  public
     * @param   string  $js
     * @return  void
     */
    public function add_js($js)
    {
        $this->js[$js] = $js;
    }

    /**
     * Add css file path
     *
     * @access  public
     * @param   string  $css
     * @return  void
     */
    public function add_css($css)
    {
        $this->css[$css] = $css;
    }

    /**
     * Load view
     *
     * @access  public
     * @param   string  $view
     * @param   mixed   $data
     * @param   boolean $return
     * @return  void
     */
    public function load_view($view, $data = array(), $return = FALSE)
    {
        // Not include master view on ajax request
        if ($this->_ci->input->is_ajax_request())
        {
            $this->_ci->load->view($view, $data);
            return;
        }

        // Title
        if (empty($this->title))
        {
            $title = $this->brand_name;
        }
        else
        {
            $title = $this->title . $this->title_separator . $this->brand_name;
        }

        // Description
        $description = $this->description;

        // Metadata
        $metadata = array();
        foreach ($this->metadata as $name => $content)
        {
            if (strpos($name, 'og:') === 0)
            {
                $metadata[] = '<meta property="' . $name . '" content="' . $content . '">';
            }
            else
            {
                $metadata[] = '<meta name="' . $name . '" content="' . $content . '">';
            }
        }
        $metadata = implode('', $metadata);

        // Javascript
        $js = array();
        foreach ($this->js as $js_file)
        {
            $js[] = '<script src="' . assets_url($js_file) . '"></script>';
        }
        $js = implode('', $js);

        // CSS
        $css = array();
        foreach ($this->css as $css_file)
        {
            $css[] = '<link rel="stylesheet" href="' . assets_url($css_file) . '">';
        }
         $person_id = $this->_ci->session->userdata('person_id');
        $data['person_id'] = $person_id;
                $data['person_nm'] = getValue('person_nm', 'hris_persons', array('person_id'=>'where/'.$person_id));
                if(!$data['person_nm']) $data['person_nm']="Administrator";
        $data['person_img'] = file_exists('assets/assets/img/profiles/PICTURE_'.$person_id.'.JPG') ? assets_url('assets/img/profiles/PICTURE_'.$person_id.'.JPG') : assets_url('assets/img/profiles/photo-default.png');
        
        //Inbox Overtime
        $inbox = 0;
                $list_notif = "";
        //Cek Bawahan
        $bawahan=array();
        $q = GetAll("hris_employee_job", array("upper_employee_id"=> "where/".$person_id));
        if($q->num_rows() > 0) {
                    foreach($q->result_array() as $r) {
                        $bawahan[] = $r['employee_id'];
                    }
                    
                    $q = GetAll("kg_view_overtime", array("ovt_status"=> "where/Waiting"), array("id_employee"=> $bawahan));
                    foreach($q->result_array() as $r) {
                        $inbox++;
                        $img = GetPP($r['id_employee']);
                        $list_notif .= "<a href='".site_url('attendance_form/overtime/'.$r['id'])."'><div class='notification-messages info'>
                    <div class='user-profile'>
                      <img src='".$img."' width='35' height='35'>
                    </div>
                    <div class='message-wrapper'>
                      <div class='heading'>".$r['person_nm']." - Overtime</div>
                      <div class='description'>".FormatTanggalShort($r['date_full'])."</div>
                      <!--<div class='date pull-left'>A min ago</div>-->
                    </div>
                    <div class='clearfix'></div>
                  </div></a>";
                    }
                }
                
                $q = GetAll("kg_view_overtime", array("ovt_status"=> "where/Approve", "is_read"=> "where/0", "create_user_id"=> "where/".$person_id));
                foreach($q->result_array() as $r) {
                    $inbox++;
                    $img = GetPP($r['modify_user_id']);
                    $list_notif .= "<a href='".site_url('attendance_form/overtime/'.$r['id'])."'><div class='notification-messages info'>
                  <div class='user-profile'>
                    <img src='".$img."' width='35' height='35'>
                  </div>
                  <div class='message-wrapper'>
                    <div class='heading'>".strtok(GetValue("person_nm", "hris_persons", array("person_id"=> "where/".$r['modify_user_id'])), " ")." - Approval Overtime</div>
                    <div class='description'>".FormatTanggalShort($r['date_full'])."</div>
                    <!--<div class='date pull-left'>A min ago</div>-->
                  </div>
                  <div class='clearfix'></div>
                </div></a>";
                }
                $data['inbox'] = $inbox;
                $data['list_notif'] = $list_notif;

       
        $menu = $this->_ci->uri->segment(1, 0);
        $data['active']=$data['active1']=$data['active2']=$data['active3']=$data['active4']="";
        switch ($menu) {
            case 'dashboard':
                $data['active'] = "class='active'";
                break;
            case 'employee':
                $data['active1'] = "class='active'";
                break;
            case 'attendance':
                $data['active2'] = "class='active'";
                break;
            case 'attendance_form':
                $data['active2'] = "class='active'";
                break;
            case 'config':
                $data['active3'] = "class='active'";
                break;
            case 'report':
                $data['active4'] = "class='active'";
                break;
            default:
                $$data['active1'] = "class='active'";
                break;
        }
        
        //Sub Menu
        $submenu = $this->_ci->uri->segment(2);
        $param=$menu."/".$submenu;
        $data['active2_1']=$data['active2_2']=$data['active2_3']="";
        $data['active3_1']=$data['active3_2']=$data['active3_3']="";
        $data['active4_1']=$data['active4_2']=$data['active4_3']="";
        switch ($param) {
            case 'attendance/':
                $data['active2_1'] = "class='active'";
                break;
            case 'attendance_form/overtime':
                $data['active2_2'] = "class='active'";
                break;
            case 'config/':
                $data['active3_1'] = "class='active'";
                break;
            case 'config/holiday':
                $data['active3_2'] = "class='active'";
                break;
            case 'config/overtime':
                $data['active3_3'] = "class='active'";
                break;
            default:
                $$data['active3_1'] = "class='active'";
                break;
        }

        $css = implode('', $css);
        $header = $this->_ci->load->view('header', $data, TRUE);
        $footer = $this->_ci->load->view('footer', array(), TRUE);
        $sidebar = $this->_ci->load->view('sidebar', $data, TRUE);
        $main_content = $this->_ci->load->view($view, $data, TRUE);

        $body = $this->_ci->load->view('layout/' . $this->layout, array(
            'header' => $header,
            'footer' => $footer,
            'sidebar' => $sidebar,
            'main_content' => $main_content,
        ), TRUE);

        return $this->_ci->load->view('base_view', array(
            'title' => $title,
            'description' => $description,
            'metadata' => $metadata,
            'js' => $js,
            'css' => $css,
            'body' => $body,
            'ga_id' => $this->ga_id,
        ), $return);
    }
}

/* End of file Template.php */
/* Location: ./application/libraries/Template.php */