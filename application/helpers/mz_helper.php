<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('print_mz')){	
	function print_mz($data)
	{
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		die();
	}
}

if (!function_exists('lastq')){	
	function lastq()
	{
		$CI =& get_instance();
		die($CI->db->last_query());
	}
}

if (!function_exists('permissionactionz')){
	function permissionactionz()
	{
		$CI =& get_instance();
		$grup = $CI->session->userdata("webmaster_grup");
		if($grup == 4 || $grup==1) return 0;
		else return 1;
	}
}

if (!function_exists('cekIpad')){
	function cekIpad()
	{
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/ipad/i',$user_agent)) return TRUE;
		else return FALSE;
	}
}
	
function cekAccessMenu($ref_menu)
{
	$CI =& get_instance();
	$CI->db->where("filez",$ref_menu);
	$query = $CI->db->get("kg_menu_admin");
	//die($this->db->last_query());
	return $query;
}

function cekLogin($username,$userpass)
{
	$CI =& get_instance();
	$CI->db->where("username",$username);
	$CI->db->where("userpass",$userpass);
	$query=$CI->db->get("kg_admin");
	return $query;
}

function cekLoginEmployee($username,$userpass)
{
	$CI =& get_instance();
	if(!preg_match("/-/", $username)) $username = substr($username,0,1)."-".substr($username,1,4)."-".substr($username,5,3);
	$CI->db->where("nik",$username);
	$CI->db->where("userpass",$userpass);
	$query=$CI->db->get("employee");
	return $query;
}

if (!function_exists('json_encode'))
{
    function json_encode($a=false)
    {
        if (is_null($a)) return 'null';
        if ($a === false) return 'false';
        if ($a === true) return 'true';
        if (is_scalar($a))
        {
            if (is_float($a))
            {
                // Always use "." for floats.
                return floatval(str_replace(",", ".", strval($a)));
            }

            if (is_string($a))
            {
                static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
                return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
            }
            else
            return $a;
        }
        $isList = true;
        for ($i = 0, reset($a); $i < count($a); $i++, next($a))
        {
            if (key($a) !== $i)
            {
                $isList = false;
                break;
            }
        }
        $result = array();
        if ($isList)
        {
            foreach ($a as $v) $result[] = json_encode($v);
            return '[' . join(',', $result) . ']';
        }
        else
        {
            foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
            return '{' . join(',', $result) . '}';
        }
    }
}

if (!function_exists('GetUserID')){
	function GetUserID()
	{
		$CI =& get_instance();
		return $CI->session->userdata("webmaster_id");
	}
}

if (!function_exists('GetHeaderFooter')){	
	function GetHeaderFooter($flag_sidebar=NULL)
	{
		$CI =& get_instance();
		
		if($CI->session->userdata('webmaster_id'))
		{
			$data['dis_login'] = "display:'';";
			$data['nama_user'] = $CI->session->userdata('admin');
		}
		else
		{
			$data['dis_login'] = "display:none;";
			$data['nama_user'] = "";
		}
		
		$data['header'] = 'header';
		$data['menu'] = 'menu';
		//$data['sidebar'] = 'sidebar';
		$data['footer'] = 'footer';
		$data['breadcrumb'] = Breadcrumb();
		
		$data['spic']=$data['sd']=$data['dv']=$data['bln']=$data['thn']=$data['tp']="";
		return $data;
	}
}

if (!function_exists('cek_akses')){
	function cek_akses($db, $id_menu, $webmaster_grup)
	{
		$CI =& get_instance();
		$CI->db->where("id_admin_grup", $webmaster_grup);
		$CI->db->where("id_menu_admin", $id_menu);
		$q = $CI->db->get("admin_auth");
		if($q->num_rows() > 0) return true;
		else return false;
	}
}

if (!function_exists('Breadcrumb')){
	function Breadcrumb()
	{
		$CI =& get_instance();
		$breadcrumb = "";//Home
		$flag=1;
		$id_menu = $id_menu_temp = GetValue("id","kg_menu_admin", array("filez"=> "where/".$CI->uri->segment(1)));
		if($id_menu)
		{
			while($flag)
			{
				$CI->db->where("id", $id_menu);
				$q = $CI->db->get("kg_menu_admin");
				foreach($q->result_array() as $r)
				{
					if($id_menu_temp == $id_menu) $breadcrumb = "<li>".$r['title']."</li>".$breadcrumb;
					else if($id_menu == 3) $breadcrumb = "<li><a href='".site_url($r['filez'].'/dashboard/'.$CI->uri->segment(3))."'><b>".$r['title']."</b></a></li>".$breadcrumb;
					else $breadcrumb = "<li><a href='".site_url($r['filez'])."'><b>".$r['title']."</b></a></li>".$breadcrumb;
					$id_menu=$r['id_parents'];
					if($r['id_parents'] == 0) $flag=0;
				}
			}
		}
		
		return "<li class='first'><a href='".site_url('home')."'>Home</a></li>".$breadcrumb;
		
		return $data['breadcrumb'];
	}
}

if (!function_exists('GetValue')){
	function GetValue($field,$table,$filter=array(),$order=NULL)
	{
		$CI =& get_instance();
		$CI->db->select($field);
		foreach($filter as $key=> $value)
		{
			$exp = explode("/",$value);
			if(isset($exp[1]))
			{
				if($exp[0] == "where") $CI->db->where($key, $exp[1]);
				else if($exp[0] == "like") $CI->db->like($key, $exp[1]);
				else if($exp[0] == "like_after") $CI->db->like($key, $exp[1], 'after');
				else if($exp[0] == "like_before") $CI->db->like($key, $exp[1], 'before');
				else if($exp[0] == "not_like") $CI->db->not_like($key, $exp[1]);
				else if($exp[0] == "not_like_after") $CI->db->not_like($key, $exp[1], 'after');
				else if($exp[0] == "not_like_before") $CI->db->not_like($key, $exp[1], 'before');
				else if($exp[0] == "wherebetween"){
					$xx=explode(',',$exp[1]);
				 $CI->db->where($key.' >=',$xx[0]);
				 $CI->db->where($key.' <=',$xx[1]);
				}
				else if($exp[0] == "order")
				{
					$key = str_replace("=","",$key);
					$CI->db->order_by($key, $exp[1]);
				}
				else if($key == "limit") $CI->db->limit($exp[1], $exp[0]);
			}
			
			if($exp[0] == "group") $CI->db->group_by($key);
		}
		
		if($order) $CI->db->order_by($order);
		$q = $CI->db->get($table);
		foreach($q->result_array() as $r)
		{
			return $r[$field];
		}
		return 0;
	}
}

if (!function_exists('GetAll')){
	function GetAll($tbl,$filter=array(),$filter_where_in=array())
	{
		$CI =& get_instance();
		foreach($filter as $key=> $value)
		{
			// Multiple Like
			if(is_array($value))
			{
				$key = str_replace(" =","",$key);
				$like="";
				$v=0;
				foreach($value as $r=> $s)
				{
					$v++;
					$exp = explode("/",$s);
					if(isset($exp[1]))
					{
						if($exp[0] == "like")
						{
							if($key == "tanggal" || $key == "tahun")
							{
								$key = "tanggal";
								if(strlen($exp[1]) == 4)
								{
									if($v == 1) $like .= $key." LIKE '%".$exp[1]."-%' ";
									else $like .= " OR ".$key." LIKE '%".$exp[1]."-%' ";
								}
								else 
								{
									if($v == 1) $like .= $key." LIKE '%-".$exp[1]."-%' ";
									else $like .= " OR ".$key." LIKE '%-".$exp[1]."-%' ";
								}
							}
							else
							{
								if($v == 1) $like .= $key." LIKE '%".$exp[1]."%' ";
								else $like .= " OR ".$key." LIKE '%".$exp[1]."%' ";
							}
						}
					}
				}
				if($like) $CI->db->where("id > 0 AND ($like)");
				$exp[0]=$exp[1]="";
			}
			else $exp = explode("/",$value);
			
			if(isset($exp[1]))
			{
				if($exp[0] == "where") $CI->db->where($key, $exp[1]);
				else if($exp[0] == "like") $CI->db->like($key, $exp[1]);
				else if($exp[0] == "like_after") $CI->db->like($key, $exp[1], 'after');
				else if($exp[0] == "like_before") $CI->db->like($key, $exp[1], 'before');
				else if($exp[0] == "not_like") $CI->db->not_like($key, $exp[1]);
				else if($exp[0] == "not_like_after") $CI->db->not_like($key, $exp[1], 'after');
				else if($exp[0] == "not_like_before") $CI->db->not_like($key, $exp[1], 'before');
				else if($exp[0] == "wherebetween"){
					$xx=explode(',',$exp[1]);
				 $CI->db->where($key.' >=',$xx[0]);
				 $CI->db->where($key.' <=',$xx[1]);
				}
				else if($exp[0] == "order")
				{
					$key = str_replace("=","",$key);
					$CI->db->order_by($key, $exp[1]);
				}
				else if($key == "limit") $CI->db->limit($exp[1], $exp[0]);
			}
			else if($exp[0] == "where") $CI->db->where($key);
			
			if($exp[0] == "group") $CI->db->group_by($key);
		}
		
		foreach($filter_where_in as $key=> $value)
		{
			$CI->db->where_in($key, $value);
		}
		
		$q = $CI->db->get($tbl);
		//die($CI->db->last_query());
		
		return $q;
	}
}

if (!function_exists('GetAllSelect')){
	function GetAllSelect($tbl,$select,$filter=array(),$filter_where_in=array())
	{
		$CI =& get_instance();
		$CI->db->select($select);
		foreach($filter as $key=> $value)
		{
			// Multiple Like
			if(is_array($value))
			{
				$key = str_replace(" =","",$key);
				$like="";
				$v=0;
				foreach($value as $r=> $s)
				{
					$v++;
					$exp = explode("/",$s);
					if(isset($exp[1]))
					{
						if($exp[0] == "like")
						{
							if($key == "tanggal" || $key == "tahun")
							{
								$key = "tanggal";
								if(strlen($exp[1]) == 4)
								{
									if($v == 1) $like .= $key." LIKE '%".$exp[1]."-%' ";
									else $like .= " OR ".$key." LIKE '%".$exp[1]."-%' ";
								}
								else 
								{
									if($v == 1) $like .= $key." LIKE '%-".$exp[1]."-%' ";
									else $like .= " OR ".$key." LIKE '%-".$exp[1]."-%' ";
								}
							}
							else
							{
								if($v == 1) $like .= $key." LIKE '%".$exp[1]."%' ";
								else $like .= " OR ".$key." LIKE '%".$exp[1]."%' ";
							}
						}
					}
				}
				if($like) $CI->db->where("id > 0 AND ($like)");
				$exp[0]=$exp[1]="";
			}
			else $exp = explode("/",$value);
			
			if(isset($exp[1]))
			{
				if($exp[0] == "where") $CI->db->where($key, $exp[1]);
				else if($exp[0] == "like") $CI->db->like($key, $exp[1]);
				else if($exp[0] == "like_after") $CI->db->like($key, $exp[1], 'after');
				else if($exp[0] == "like_before") $CI->db->like($key, $exp[1], 'before');
				else if($exp[0] == "not_like") $CI->db->not_like($key, $exp[1]);
				else if($exp[0] == "not_like_after") $CI->db->not_like($key, $exp[1], 'after');
				else if($exp[0] == "not_like_before") $CI->db->not_like($key, $exp[1], 'before');
				else if($exp[0] == "order")
				{
					$key = str_replace("=","",$key);
					$CI->db->order_by($key, $exp[1]);
				}
				else if($key == "limit") $CI->db->limit($exp[1], $exp[0]);
			}
			else if($exp[0] == "where") $CI->db->where($key);
			
			if($exp[0] == "group") $CI->db->group_by($key);
		}
		
		foreach($filter_where_in as $key=> $value)
		{
			$CI->db->where_in($key, $value);
		}
		
		$q = $CI->db->get($tbl);
		//die($CI->db->last_query());
		
		return $q;
	}
}

if (!function_exists('GetQuery')){
	function GetQuery($field,$table,$where='',$order='',$group='')
	{
		$CI =& get_instance();
		$where = !empty($where) ? "WHERE ".$where : "";
		$order = !empty($order) ? "ORDER BY ".$order : "";
		$group = !empty($group) ? "GROUP BY ".$group : "";		
		
		$q = $CI->db->query("SELECT $field FROM $table $where $order $group");
		
		return $q;
	}
}

if (!function_exists('GetJoin')){
	function GetJoin($tbl,$tbl_join,$condition,$type,$select,$filter=array(),$filter_where_in=array())
	{
		$CI =& get_instance();
		$CI->db->select($select);
		foreach($filter as $key=> $value)
		{
			// Multiple Like
			if(is_array($value))
			{
				if($key == "group") $CI->db->group_by($value);
				$exp[0]=$exp[1]="";
			}
			else $exp = explode("/",$value);
			if(isset($exp[1]))
			{
				if($exp[0] == "where") $CI->db->where($key, $exp[1]);
				else if($exp[0] == "like") $CI->db->like($key, $exp[1]);
				else if($exp[0] == "order") $CI->db->order_by($key, $exp[1]);
				else if($key == "limit") $CI->db->limit($exp[1], $exp[0]);
			}
			
			if($exp[0] == "group") $CI->db->group_by($key);
		}
		
		foreach($filter_where_in as $key=> $value)
		{
			$CI->db->where_in($key, $value);
		}
		
		$CI->db->join($tbl_join, $condition, $type);
		$q = $CI->db->get($tbl);
		//die($CI->db->last_query());
		
		return $q;
	}
}

if (!function_exists('GetSum')){
	function GetSum($table,$field,$filter=array(),$get="")
	{
		$CI =& get_instance();
		$CI->db->select("SUM($field) as total");
		foreach($filter as $key=> $value)
		{
			$exp = explode("/",$value);
			if(isset($exp[1]))
			{
				if($exp[0] == "where") $CI->db->where($key, $exp[1]);
				else if($exp[0] == "like") $CI->db->like($key, $exp[1]);
				else if($exp[0] == "order") $CI->db->order_by($key, $exp[1]);
				else if($key == "limit") $CI->db->limit($exp[1], $exp[0]);
			}
			
			if($exp[0] == "group") $CI->db->group_by($key);
		}
		
		$q = $CI->db->get($table);
		
		if($get == "value")
		{
			$val = 0;
			//die($CI->db->last_query());
			foreach($q->result_array() as $r) $val=$r['total'];
			return $val;
		}
		else return $q;
	}
}

if (!function_exists('GetCount')){
	function GetCount($table,$field,$filter=array(),$filter_where_in=array(),$get="")
	{
		$CI =& get_instance();
		$CI->db->select("$field as label, COUNT($field) as total");
		foreach($filter as $key=> $value)
		{
			$exp = explode("/",$value);
			if(isset($exp[1]))
			{
				if($exp[0] == "where") $CI->db->where($key, $exp[1]);
				else if($exp[0] == "like") $CI->db->like($key, $exp[1]);
				else if($exp[0] == "order") $CI->db->order_by($key, $exp[1]);
				else if($key == "limit") $CI->db->limit($exp[1], $exp[0]);
			}
			
			if($exp[0] == "group") $CI->db->group_by($key);
		}
		
		foreach($filter_where_in as $key=> $value)
		{
			$CI->db->where_in($key, $value);
		}
		
		$q = $CI->db->get($table);
		if($get == "value")
		{
			$val = 0;
			//die($CI->db->last_query());
			foreach($q->result_array() as $r) $val=$r['total'];
			return $val;
		}
		else return $q;
	}
}

if (!function_exists('GetColumns')){	
	function GetColumns($tbl)
	{
		$CI =& get_instance();
		//if(substr($tbl,0,3) != "kg_") $tbl = "kg_".$tbl;
		$query = $CI->db->query("SHOW COLUMNS FROM ".$tbl);
		return $query->result_array();
	}
}
	
if (!function_exists('GetUrlDate')){	
	function GetUrlDate($date)
	{
		$exp1 = explode(" ", $date);
		$exp = explode("-",$exp1[0]);
		return $exp[2]."/".$exp[1]."/".$exp[0];
	}
}

if (!function_exists('ExplodeNameFile')){
	function ExplodeNameFile($source)
	{
		$ext = strrchr($source, '.');
		$name = ($ext === FALSE) ? $source : substr($source, 0, -strlen($ext));

		return array('ext' => $ext, 'name' => $name);
	}
}

if (!function_exists('GetThumb')){	
	function GetThumb($image, $path="_thumb")
	{
		$exp = ExplodeNameFile($image);
		return $exp['name'].$path.$exp['ext'];
	}
}

if (!function_exists('ResizeImage')){	
	function ResizeImage($up_file,$w,$h)
	{
		//Resize
		$CI =& get_instance();
		$config['image_library'] = 'gd2';
		$config['source_image'] = $up_file;
		$config['dest_image'] = "./".$CI->config->item('path_upload')."/";
		$config['create_thumb'] = TRUE;
		$config['maintain_ratio'] = TRUE; //Width=Height
		$config['height'] = $h;
		$config['width'] = $w;
		
		$CI->load->library('image_lib', $config);
		if($CI->image_lib->resize()) return 1;
		else return 0; 
	}
}

if (!function_exists('InputFile')){
	function InputFile($filez,$filesize=500)
	{
		$CI =& get_instance();
		$file_up = $_FILES[$filez]['name'];
		$file_up = date("YmdHis").".".str_replace("-","_",url_title($file_up));
		$myfile_up	= $_FILES[$filez]['tmp_name'];
		$ukuranfile_up = $_FILES[$filez]['size'];
		if($filez == "foto")
		$up_file = "./".$CI->config->item('path_upload')."/foto/".$file_up;
		else
		$up_file = "./".$CI->config->item('path_upload')."/".$file_up;
		
		$ext_file = strrchr($file_up, '.');
		if($ukuranfile_up < ($filesize * 1024))
		{
			if(strtolower($ext_file) == ".jpg" || strtolower($ext_file) == ".JPG" ||strtolower($ext_file) == ".jpeg" || strtolower($ext_file) == ".png")
			{
				if(copy($myfile_up, $up_file))
				{
					ResizeImage($up_file, 250, 250);
					return $file_up;
				}
			}
			//else if(strtolower($ext_file) == ".doc" || strtolower($ext_file) == ".docx" || strtolower($ext_file) == ".pdf")
			else
			{
				if(copy($myfile_up, $up_file))
				{
					return $file_up;
				}
				else return 3;
			}
			
		}
		else return 2;
	}
}

if (!function_exists('Page')){
	function Page($jum_record,$lmt,$pg,$path,$uri_segment)
	{
		$link = "";
		$config['base_url'] = $path;
		$config['total_rows'] = $jum_record;
		$config['per_page'] = $lmt;
		$config['num_links'] = 3;
		$config['cur_tag_open'] = '<li><strong>';
		$config['cur_tag_close'] = '</strong></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['uri_segment'] = $uri_segment;
		
		$CI =& get_instance();
		$CI->pagination->initialize($config);
		$link = $CI->pagination->create_links();
		return $link;
	}
}

if (!function_exists('CaptchaSecurityImages')){	
	function CaptchaSecurityImages($width='120',$height='40',$characters='6') 
	{
		$CI =& get_instance();
		$font = './assets/font/monofont.ttf';
		$code = generateCode($characters);
		/* font size will be 75% of the image height */
		$font_size = $height * 0.75;
		$image = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');
		/* set the colours */
		$background_color = imagecolorallocate($image, 255, 255, 255);
		$text_color = imagecolorallocate($image, 20, 40, 100);
		$noise_color = imagecolorallocate($image, 100, 120, 180);
		/* generate random dots in background */
		for( $i=0; $i<($width*$height)/3; $i++ ) {
			imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
		}
		/* generate random lines in background */
		for( $i=0; $i<($width*$height)/150; $i++ ) {
			imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
		}
		/* create textbox and add text */
		$textbox = imagettfbbox($font_size, 0, $font, $code) or die('Error in imagettfbbox function');
		$x = ($width - $textbox[4])/2;
		$y = ($height - $textbox[5])/2;
		imagettftext($image, $font_size, 0, $x, $y, $text_color, $font , $code) or die('Error in imagettftext function');
		
		
		/* output captcha image to browser */
		header('Content-Type: image/jpeg');
		imagejpeg($image);
		imagedestroy($image);
		$CI->session->set_userdata("security_code", $code);
	}
}

if (!function_exists('GetTanggal')){	
	function GetTanggal($tgl)
	{
		if(strlen($tgl) == 1) $tgl = "0".$tgl;
		return $tgl;
	}
}

if (!function_exists('GetBulanIndo')){	
	function GetBulanIndo($Bulan)
	{
		if($Bulan == "January")
			$Bulan = "Januari";
		else if($Bulan == "February")
			$Bulan = "Februari";
		else if($Bulan == "March")
			$Bulan = "Maret";
		else if($Bulan == "May")
			$Bulan = "Mei";
		else if($Bulan == "June")
			$Bulan = "Juni";
		else if($Bulan == "July")
			$Bulan = "Juli";
		else if($Bulan == "August")
			$Bulan = "Agustus";
		else if($Bulan == "October")
			$Bulan = "Oktober";
		else if($Bulan == "December")
			$Bulan = "Desember";

		return $Bulan;
	}
}

if (!function_exists('GetMonthIndex')){	
	function GetMonthIndex($var)
	{
		$bln = array("Jan"=> "01","Feb"=> "02","Mar"=> "03","Apr"=> "04","May"=> "05","Jun"=> "06","Jul"=> "07","Aug"=> "08","Sep"=> "09","Oct"=> "10","Nov"=> "11","Dec"=> "12");
		return $bln[$var];
	}
}

if (!function_exists('GetMonth')){	
	function GetMonth($id)
	{
		$bln = array("","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
		//$bln = array("","Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Dec");
		return $bln[intval($id)];
	}
}

if (!function_exists('GetMonthFull')){	
	function GetMonthFull($id)
	{
		$id=intval($id);
		$bln = array("","January","February","March","April","May","June","July","August","September","October","November","December");
		//$bln = array("","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
		return $bln[$id];
	}
}

if (!function_exists('GetMonthShort')){	
	function GetMonthShort($val)
	{
		$bln = array("Januari"=> "Jan","Februari"=>"Feb","Maret"=>"Mar","April"=>"Apr","Mei"=>"May","Juni"=>"Jun","Juli"=>"Jul","Agustus"=>"Aug","September"=>"Sep","Oktober"=>"Oct","November"=>"Nov","Desember"=>"Dec");
		return $bln[$val];
	}
}

if (!function_exists('GetOptDate')){	
	function GetOptDate()
	{
		$opt[''] = "- Tanggal -";
		for($i=1;$i<=31;$i++)
		{
			if(strlen($i) == 1) $j = "0".$i;
			else $j=$i;
			$opt[$j] = $j;
		}
		return $opt;
	}
}

if (!function_exists('GetOptMonth')){	
	function GetOptMonth()
	{
		$opt[''] = "- Bulan -";
		$bln = array("01"=> "Januari","02"=> "Februari","03"=> "Maret","04"=>"April","05"=>"Mei","06"=>"Juni",
		"07"=>"Juli","08"=>"Agustus","09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember");
		//$bln = array("","Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Dec");
		foreach($bln as $r=> $val)
		{
			$opt[$r] = $val;
		}
		
		return $opt;
	}
}

if (!function_exists('GetOptMonthFull')){	
	function GetOptMonthFull()
	{
		$opt[''] = "- Bulan -";
		$bln = array("Januari"=> "Januari","Februari"=> "Februari","Maret"=> "Maret","April"=>"April","Mei"=>"Mei","Juni"=>"Juni",
		"Juli"=>"Juli","Agustus"=>"Agustus","September"=>"September","Oktober"=>"Oktober","November"=>"November","Desember"=>"Desember");
		//$bln = array("","Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Dec");
		foreach($bln as $r=> $val)
		{
			$opt[$r] = $val;
		}
		
		return $opt;
	}
}

if (!function_exists('GetOptYear')){	
	function GetOptYear()
	{
		if(date("m") == "10") $year = date("Y") + 1;
		else $year = date("Y");
		$opt[''] = "- Tahun -";
		for($i=$year;$i >=2011;$i--)
		{
			$opt[$i] = $i;
		}
		return $opt;
	}
}

if (!function_exists('GetOptYeari')){	
	function GetOptYeari()
	{
		$opt[''] = "- Tahun -";
		for($i=date("Y");$i >=2006;$i--)
		{
			$opt[$i] = $i;
		}
		return $opt;
	}
}

if (!function_exists('GetOptRencanaTanggal')){	
	function GetOptRencanaTanggal()
	{
		if(date("m") == "10") $year = date("Y") + 1;
		else $year = date("Y");
		$opt[''] = "- Rencana Tanggal -";
		for($i=date("Y");$i <=$year;$i++)
		{
			for($j=1;$j<=12;$j++)
			{
				$opt[GetMonthFull($j)." ".$i] = GetMonthFull($j)." ".$i;
			}
		}
		return $opt;
	}
}

/* OPTIONS DROPDOWN */
if (!function_exists('GetOptAll')){
	function GetOptAll($tabel,$judul=NULL)
	{
		if($tabel == "pendidikan") $filter = array("urut"=> "order/asc");
		else $filter = array();
		$q = GetAll($tabel, $filter);
		if($judul) $opt[''] = $judul;
		foreach($q->result_array() as $r)
		{
			$opt[$r['id']] = $r['title'];
		}
		
		return $opt;
	}
}

if (!function_exists('GetOptPublish')){	
	function GetOptPublish()
	{
		$opt = array("Publish"=> "Publish", "NotPublish"=> "NotPublish");
		
		return $opt;
	}
}

if (!function_exists('GetOptShiftReguler')){	
	function GetOptShiftReguler()
	{
		$opt = array(""=> "- Reguler/Shift -", "shift"=> "Shift", "reguler"=> "Reguler");
		
		return $opt;
	}
}

if (!function_exists('GetOptAgama')){
	function GetOptAgama()
	{
		$q = array('Moslem', 'Christian', 'Hindu', 'Budha', 'Catholic');
		$opt[''] = "- Agama -";
		foreach($q as $r)
		{
			$opt[$r] = $r;
		}
		
		return $opt;
	}
}

if (!function_exists('GetOptStatusForm')){
	function GetOptStatusForm()
	{
		$opt = array("Waiting"=> "Waiting", "Approve"=> "Approve", "Reject"=> "Reject");
		
		return $opt;
	}
}

if (!function_exists('GetOptBlood')){
	function GetOptBlood()
	{
		$q = array('A', 'B', 'AB', 'O');
		$opt[''] = "- Gol. Darah -";
		foreach($q as $r)
		{
			$opt[$r] = $r;
		}
		
		return $opt;
	}
}

if (!function_exists('GetOptGrade')){
	function GetOptGrade()
	{
		$q = GetAll("hris_job_class", array("gradeval_bottom"=> "order/asc", "gradeval_top"=> "group"));
		$opt[''] = "- Grade -";
		foreach($q->result_array() as $r)
		{
			$opt[$r['gradeval_bottom']] = $r['gradeval_bottom'];
		}
		
		return $opt;
	}
}

if (!function_exists('GetOptPositionLevel')){
	function GetOptPositionLevel()
	{
		$q = GetAll("hris_job_class", array("status_cd"=> "where/normal", "job_class_nm"=> "order/asc"));
		$opt[''] = "- Position Level -";
		foreach($q->result_array() as $r)
		{
			$opt[$r['job_class_id']] = $r['job_class_nm'];
		}
		
		return $opt;
	}
}

if (!function_exists('GetOptReason')){	
	function GetOptReason()
	{
		$q = GetAll("kg_ref_reason");
		$opt[''] = "- Reason -";
		foreach($q->result_array() as $r)
		{
			$opt[$r['id_reason']] = $r['title'];
		}
		
		return $opt;
	}
}

if (!function_exists('GetOptPICExit')){	
	function GetOptPICExit()
	{
		$filter = array("name"=> "order/asc", "id_admin_grup !="=> "where/1", "is_active"=> "where/InActive");
		$q = GetAll("admin", $filter);
		$opt[''] = "- Karyawan -";
		foreach($q->result_array() as $r)
		{
			$opt[$r['id']] = $r['name'];
		}
		
		return $opt;
	}
}

if (!function_exists('GetOptPosition')){	
	function GetOptPosition()
	{
		$q = GetAll("position", array("urut"=> "order/asc"));
		$opt[''] = "- Position-";
		foreach($q->result_array() as $r)
		{
			$opt[$r['id']] = $r['title'];
		}
		
		return $opt;
	}
}

if (!function_exists('GetOptDivision')){	
	function GetOptDivision()
	{
		$CI =& get_instance();
		$q = GetAll("hris_orgs", array("org_class_id"=> "where/3", "status_cd"=> "where/normal", "org_nm"=> "order/asc"));
		$opt[''] = "- Division -";
		foreach($q->result_array() as $r)
		{
			$opt[$r['org_id']] = $r['org_nm'];
		}
		
		return $opt;
	}
}

if (!function_exists('GetOptKedeputianSub')){	
	function GetOptKedeputianSub($id_deputi=NULL)
	{
		$CI =& get_instance();
		$filter = array();
		if($id_deputi) $filter['id_kedeputian'] = "where/".$id_deputi;
		$q = GetAll("kedeputian_sub", $filter);
		$opt[''] = "- Sub Divisi -";
		if($CI->uri->segment(1) == "wp") $opt[-1] = "-";
		foreach($q->result_array() as $r)
		{
			$opt[$r['id']] = $r['title'];
		}
		
		return $opt;
	}
}

if (!function_exists('GetOptMenu')){	
	function GetOptMenu()
	{
		$q = GetAll("kg_menu_admin", array("title"=> "order/asc"));
		$opt[''] = "- Parents Menu -";
		foreach($q->result_array() as $r)
		{
			$opt[$r['id']] = $r['title'];
		}
		
		return $opt;
	}
}

if (!function_exists('GetOptGrup')){	
	function GetOptGrup()
	{
		$q = GetAll("admin_grup");
		$opt[''] = "- Grup Admin -";
		foreach($q->result_array() as $r)
		{
			$opt[$r['id']] = $r['title'];
		}
		
		return $opt;
	}
}

if (!function_exists('GetOptJenisCuti')){	
	function GetOptJenisCuti()
	{
		$q = GetAll("jenis_cuti");
		$opt[''] = "- Jenis Cuti -";
		foreach($q->result_array() as $r)
		{
			$opt[$r['id']] = $r['title'];
		}
		
		return $opt;
	}
}

if (!function_exists('GetOptJenisPertemuan')){	
	function GetOptJenisPertemuan($not_int=NULL, $judul=NULL)
	{
		$CI =& get_instance();
		if(count($not_int) > 0) $CI->db->where_not_in("id",$not_int);
		$q = $CI->db->get("jenis_pertemuan");
		$opt = array();
		if(!$judul) $opt[''] = "- Jenis Pertemuan -";
		else $opt[''] = $judul;
		foreach($q->result_array() as $r)
		{
			$opt[$r['id']] = $r['title'];
		}
		
		return $opt;
	}
}

if (!function_exists('DelImage')){	
	function DelImage()
	{
		$CI =& get_instance();
		$webmaster_id = $this->auth();
		$mz_function = new mz_function();
		$id = $CI->input->post('del_id_img');
		$table = $CI->input->post('del_table');
		$field = $CI->input->post('del_field');
		
		$GetFile = GetValue($field,$table, array("id"=> "where/".$id));
		$GetThumb = GetThumb($GetFile);
		if(file_exists("./".$CI->config->item('path_upload')."/".$GetFile)) unlink("./".$CI->config->item('path_upload')."/".$GetFile);
		if(file_exists("./".$CI->config->item('path_upload')."/".$GetThumb)) unlink("./".$CI->config->item('path_upload')."/".$GetThumb);
		
		$data[$field] = "";
		$this->db->where("id", $id);
		$this->db->update($table, $data);
	}
}

if (!function_exists('FormatTanggal')){
	function FormatTanggal($tgl)
	{
		$exp = explode("-", $tgl);
		$tgl = substr($exp[2],0,2)." ".GetMonthFull(intval($exp[1]))." ".$exp[0];
		return $tgl;
	}
}

if (!function_exists('FormatTanggalShort')){
	function FormatTanggalShort($tgl)
	{
		$exp = explode("-", $tgl);
		//$tgl = $exp[2]." ".GetMonth(intval($exp[1]))." ".substr($exp[0],2,2);
		$tgl = substr($exp[2],0,2)." ".GetMonth(intval($exp[1]))." ".$exp[0];
		return $tgl;
	}
}

if (!function_exists('Rupiah')){
	function Rupiah($rp)
	{
		if($rp && $rp!="-") return "Rp ".number_format($rp,0,",",".").",-";
		else return 0;
	}
}

if (!function_exists('Decimal')){
	function Decimal($rp,$koma=2)
	{
		$rp = str_replace(" ","",$rp);
		if($rp && $rp!="-") return number_format($rp,$koma);
		else return 0;
	}
}

if (!function_exists('Number')){
	function Number($rp)
	{
		$rp = str_replace(" ","",$rp);
		if($rp && $rp!="-") return number_format($rp,0,",",".");
		else return 0;
	}
}

if (!function_exists('KomaToTitik')){
	function KomaToTitik($rp)
	{
		return str_replace(",","",$rp);
	}
}

if (!function_exists('GetFilename')){
	function GetFilename($val)
	{
		if($val) return substr($val,15);
		else return "";
	}
}

if (!function_exists('GetTanggalIndo')){
	function GetTanggalIndo($val, $time=NULL)
	{
		$dt = strtotime($val);
		$dt = date("d", $dt)." ".GetBulanIndo(date("F", $dt))." ".date("Y", $dt);
		if($time) $dt .= "&nbsp;&nbsp;".substr($val,11,8);
		return $dt;
	}
}


if (!function_exists('GetLamaKerja')){
	function GetLamaKerja($dt)
	{
		$hr = date("d") - substr($dt,8,2);
		$bln = date("m") - substr($dt,5,2);
		$thn = date("Y") - substr($dt,0,4);
		
		if($hr < 0)
		{
			$hr += 30;
			$bln -=1;
		}
		
		if($bln < 0)
		{
			$bln += 12;
			$thn -=1;
		}
		
		$tahun = $thn > 0 ? $thn." tahun " : "";		
		$bulan = $bln > 0 ? $bln." bulan " : "";
		$hari = $hr > 0 ? $hr." hari " : "";
		
		$lama_kerja = $tahun.$bulan.$hari;
		return $lama_kerja;
	}
}

if (!function_exists('to_excel')){
	function to_excel($query, $filename='xlsoutput')
	{
		$headers = '';
	  header("Content-type: application/x-msdownload");
	  header("Content-Disposition: attachment; filename=$filename.xls");
	  echo "$headers\n$query";
	}
}

if (!function_exists('to_doc')){
	function to_doc($query, $filename='docoutput')
	{
		header("Content-type: application/msword");
	  header("Content-Disposition: attachment; filename=$filename.doc");
	  echo "$query";
	}
}

if (!function_exists('GetKehadiranTahunan')){
	function GetKehadiranTahunan($thn)
	{
		$hadir = GetAll("kehadirandetil", array("jh"=> "where/1", "tahun"=> "where/".$thn))->num_rows();
		$absen = GetAll("kehadirandetil", array("jh"=> "where/0", "tahun"=> "where/".$thn))->num_rows();
		if(!$absen) $absen=1;
		$persen = Decimal(($hadir / ($hadir + $absen)) * 100,2)." %";
		return $persen;
	}
}

if (!function_exists('GetOptAtasan')){
	function GetOptAtasan()
	{
		$CI =& get_instance();
		$id_user = $CI->session->userdata("webmaster_id");
		$opt[''] = "- Atasan -";
		$atasan = GetValue("id_atasan","admin",array("id"=> "where/".$id_user));
		if(strlen($atasan) > 2)
		{
			if(is_array(unserialize($atasan)))
			{
				foreach(unserialize($atasan) as $s)
				{
					if($s > 0)
					{
						$nama = GetValue("name","admin", array("id"=> "where/".$s));
						$opt[$s] = $nama;
					}
				}
			}
		}
		else
		{
			$nama = GetValue("name","admin", array("id"=> "where/".$atasan));
			$opt[0] = $nama;
		}
		
		return $opt;
	}
}

if (!function_exists('GetHRDIntendent')){
	function GetHRDIntendent()
	{
		return GetValue('name','employee',array("id_department"=> "where/7", "id_position"=> "where/3"));		
	}
}

if (!function_exists('GetHRDCoordinator')){
	function GetHRDCoordinator()
	{
		return GetValue('name','employee',array("id_department"=> "where/7", "id_position"=> "where/2"));		
	}
}

if (!function_exists('Overtime')){
	function Overtime($id, $tgl, $jam, $flag="", $cek_off="")
	{
		$CI =& get_instance();
		if($jam > 0)
		{
			if($flag=="ot")
			{
				if($cek_off == "off")
				{
					if($jam <= 8) $overtime=$jam*2;
					else
					{
						//19 dari 8x2 + 1x3
						$overtime=19+(($jam-9)*4);
					}
					//$overtime = ($jam * 2);
				}
				else $overtime = ($jam * 2) - 0.5;
				$data_upd = array("acc_ot_incidental"=> $overtime);
			}
			else if($flag=="lebih")
			{
				if($jam <= 8) $overtime=$jam*2;
				else
				{
					//19 dari 8x2 + 1x3
					$overtime=19+(($jam-9)*4);
				}
				$data_upd = array("acc_allow_shift"=> $overtime);
			}
			else {
				$overtime = ($jam * 2) - 0.5;
				$data_upd = array("acc_ot_cont_allow"=> $overtime);
			}
		}
		else {
			$overtime=0;
			if($flag=="ot") $data_upd = array("acc_ot_incidental"=> $overtime);
			else if($flag=="lebih") $data_upd = array("acc_allow_shift"=> $overtime);
			else $data_upd = array("acc_ot_cont_allow"=> $overtime);
		}
		//$CI->db->where("id", $id);
		//$CI->db->update("kehadirandetil", $data_upd);
		return $overtime;
	}
}

if (!function_exists('GetOfficeHours')){
	function GetOfficeHours($param="weekly")
	{
		if($param=="monthly") return 173;
		else return 48;
	}
}

if (!function_exists('GetConfig')){
	function GetConfig($param=NULL, $grup=NULL)
	{
		return GetValue($param, "kg_config_temp", array($grup."_active_from <="=> "where/".date("Y-m-d"), "id"=> "order/desc", "limit"=> "0/1"));
	}
}

if (!function_exists('GetConfigDirect')){
	function GetConfigDirect($param=NULL)
	{
		return GetValue("value", "kg_config", array("title"=> "where/".$param));
	}
}

if (!function_exists('GetJumHari')){
	function GetJumHari($bln, $thn)
	{
		$jml_hari = date("t", mktime(0, 0, 0, $bln, 1, $thn));
		
		return $jml_hari;
	}
}

if (!function_exists('GetPeriod')){
	function GetPeriod($param)
	{
		$exp = explode(" ", $param);
		$bln = GetMonthIndex($exp[0]);
		$tgl_start = GetValue("value", "kg_config", array("title"=> "where/att_start_period"));
		$tgl_start = sprintf("%02d", $tgl_start);
		$tgl_end = GetValue("value", "kg_config", array("title"=> "where/att_end_period"));
		if($tgl_start >= $tgl_end) {
			$start = date("Y-m-d", mktime(0, 0, 0, $bln-1, $tgl_start, $exp[1]));
			$end = $exp[1]."-".$bln."-".$tgl_end;
		} else {
			$start = $exp[1]."-".$bln."-".$tgl_start;
			$end = $exp[1]."-".$bln."-".$tgl_end;
		}
		
		return $start."~".$end;
	}
}

if (!function_exists('GetPeriodFull')){
	function GetPeriodFull($param)
	{
		$exp = explode("~", $param);
		
		return FormatTanggalShort($exp[0])." s/d ".FormatTanggalShort($exp[1]);
	}
}

if (!function_exists('GetDayName')){
	function GetDayName($param)
	{
		$val = date("D", strtotime($param));
		
		return $val;
	}
}

if (!function_exists('GetHoliday')){
	function GetHoliday($tahun=NULL)
	{
		if(!$tahun) $tahun=date("Y");
		$holiday = array();
		$q = GetAll("kg_config_holiday", array("YEAR(tanggal)"=> "where/".$tahun));
		foreach($q->result_array() as $r) {
			//$holiday[$r['tanggal']] = $r['ket'];
			$holiday[] = $r['tanggal'];
			$holiday[$r['tanggal']] = $r['ket'];
		}
		
		return $holiday;
	}
}

if (!function_exists('CekLemburAuto')){
	function CekLemburAuto($id_emp, $param)
	{
		$temp=array();
		$exp = explode("-", $param);
		for($i=1;$i<=2;$i++) {
			$new_dt = date("Y-m-j", mktime(0, 0, 0, $exp[1], $exp[2]-$i, $exp[0]));
			$new_exp = explode("-", $new_dt);
			$temp[$i] = GetValue("tgl_".$new_exp[2], "kg_jadwal_shift", array("id_employee"=> "where/".$id_emp, "bulan"=> "where/".$new_exp[1], "tahun"=> "where/".$new_exp[0]));
		}
		
		if($temp[1] != 3 || $temp[2] != 3) return 0;
		else return 1;
		
	}
}

if (!function_exists('GetHourSum')){
	function GetHourSum($masuk, $keluar)
	{
		$hour = floor((strtotime($keluar) - strtotime($masuk)) / 3600);
		if($hour < 0)
		{
			$hour = 24 + $hour;
			$minute = ceil((((strtotime($keluar) + 86400) - strtotime($masuk)) - (3600 * $hour) ) / 60);
		} else $minute = ceil(((strtotime($keluar) - strtotime($masuk)) - (3600 * $hour) ) / 60);
		
		if($minute < 30) return $hour;
		else return $hour.".5";
	}
}

if (!function_exists('CekGroup')){
	function CekGroup($val)
	{
		if($val=="N.A.") return "REG";
		else return $val;
	}
}

if (!function_exists('GetDivision')){
	function GetDivision($val)
	{
		$id_div = GetValue("parent_id", "hris_orgs", array("org_id"=> "where/".$val));
		if($id_div) return GetValue("org_nm", "hris_orgs", array("org_id"=> "where/".$id_div));
		else return "-";
	}
}

if (!function_exists('GetAccOvt')){
	function GetAccOvt($val, $jadwal=1, $config=array())
	{
		if(strtolower($jadwal)=="off" || $jadwal==33) $field = "jam_libur_";
		else $field = "jam_";

		if(count($config) <= 0) {
			$config=array();
			$q = GetAll("kg_config");
	  	foreach($q->result_array() as $r) {
	  		if(preg_match('/jam_/', $r['title'])) $config[$r['title']] = $r['value'];
	  	}
		}
		
		$acc=0;
		for($i=1;$i<=$val;$i++) {
			if(!isset($config[$field.$i])) $config[$field.$i] = 3;
			$acc += $config[$field.$i];
		}
		if(!isset($config[$field.$i])) $config[$field.$i] = 3;
		$acc += $config[$field.$i] * ($val - ($i - 1));
		
		return $acc;
	}
}

if (!function_exists('GetGapok')){
	function GetGapok($id_emp, $date=NULL)
	{
		$tahun = substr($date,0,4);
		$gapok = GetValue("value", "kg_view_master_com", array("payroll_component_id"=> "where/60", "employee_id"=> "where/".$id_emp));
		if(!$gapok) $gapok=3000000;
		return $gapok;
	}
}

//Housing Allowance
if (!function_exists('GetHA')){
	function GetHA($id_emp, $date=NULL)
	{
		$tahun = substr($date,0,4);
		$ha = GetValue("value", "kg_view_master_com", array("payroll_component_id"=> "where/66", "employee_id"=> "where/".$id_emp));
		if(!$ha) $ha=1000000;
		return $ha;
	}
}

if(!function_exists('GetPP')){
	function GetPP($val)
	{
		if(file_exists('assets/assets/img/profiles/PICTURE_'.$val.'.JPG')) return assets_url('assets/img/profiles/PICTURE_'.$val.'.JPG');
		else return assets_url('assets/img/profiles/photo-default.png');
	}
}

if (!function_exists('CekBawahan')){
	function CekBawahan($id_emp)
	{
		$bawahan=array();
		$q = GetAll("hris_employee_job", array("upper_employee_id"=> "where/".$id_emp));
  	if($q->num_rows() > 0) {
			foreach($q->result_array() as $r) {
				$bawahan[] = $r['employee_id'];
			}
		}
		return $bawahan;
	}
}

if (!function_exists('GetOTRasio')){
	function GetOTRasio($id_emp, $date)
	{
		$exp = explode("-", $date);
		$att_start_period = GetConfigDirect('att_start_period');
		if($exp[2] < $att_start_period) $awal = date("Y-m", mktime(0, 0, 0, $exp[1]-1, $exp[2], $exp[0])).$att_start_period;
		else $awal = $exp[0]."-".$exp[1]."-".$att_start_period;

		$acc=0;
    $qq = GetAll("kg_view_overtime", array("id_employee"=> "where/".$id_emp, "date_full >="=> "where/".$awal, "date_full <="=> "where/".$date));
    if($qq->num_rows() > 0) {
	    foreach($qq->result_array() as $ss) {
	    	if($ss['job_level'] != "nonmanagement") {
	    		if($ss['ovt_hour_sum'] >= 2) $acc += $ss['ovt_hour_sum'];
	    	} else $acc += $ss['ovt_hour_cal'];
	    }
			
			if($ss['job_level'] != "nonmanagement") {
	    	$upah = $acc * GetConfigDirect('rest_time');
	    } else $upah = $acc * ( GetGapok($id_emp, $exp[0]) + GetHA($id_emp, $exp[0]) ) / GetConfigDirect('total_hour_ovt');
	  }
    $ot_rasio = $upah / (GetGapok($id_emp, $exp[0]) + GetHA($id_emp, $exp[0]) + $upah) * 100;
    return Decimal($ot_rasio)."%";
  }
}
?>