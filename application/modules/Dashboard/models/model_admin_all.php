<?php
//die($this->db->last_query());
class model_admin_all extends CI_Model {
	function __construct()
	{
		$this->db = $this->load->database("default",TRUE);
		parent::__construct();
	}
	
	function LogActivities($users_id,$tbl,$tbl_id,$logs,$title_menu,$title_record,$filename,$act)
	{
		$date = date("Y-m-d H:i:s");
		$message  = "<tr>";
		$message .= "<td><a href='url/".$filename."'>".$title_menu."</a></td>";
		$message .= "<td>id_admin has ".$act." <a href='url/".$filename."/edit/".$tbl_id."'>".$title_record."</a></td>";
		/*$message .= "<td>".$title_menu."</td>";
		$message .= "<td>id_admin has ".$act." <b>".$title_record."</b></td>";*/
		$message .= "<td>".$date."</td>";
		$message .= "</tr>";
		
		$data = array("id_admin"=> $users_id,
									"tabel"=> $tbl,
									"tabel_id"=> $tbl_id,
									"logs"=> $logs,
									"message"=> $message,
									"create_date"=> $date
									);
		
		$this->db->insert("admin_log", $data);
	}
	
	function GetAll($tbl,$filter=array())
	{
		foreach($filter as $key=> $value)
		{
			$exp = explode("/",$value);
			if($exp[1])
			{
				if($exp[0] == "where") $this->db->where($key, $exp[1]);
				else if($exp[0] == "like") $this->db->like($key, $exp[1]);
				else if($exp[0] == "order") $this->db->order_by($key, $exp[1]);
			}
		}
		
		$query = $this->db->get($tbl);
		
		return $query;
	}
	
	function GetList($tbl,$filter=array(),$start_limit=0,$limit)
	{
		foreach($filter as $key=> $value)
		{
			$exp = explode("/",$value);
			if($exp[1])
			{
				if($exp[0] == "where") $this->db->where($key, $exp[1]);
				else if($exp[0] == "like") $this->db->like($key, $exp[1]);
				else if($exp[0] == "order") $this->db->order_by($key, $exp[1]);
			}
		}
		$this->db->limit($limit,$start_limit);
		$query = $this->db->get($tbl);
		return $query->result_array();
	}
	
	function GetById($tbl,$id,$id_lang=0)
	{
		$GetAllConfig = $this->model_admin_all->GetAllConfig($tbl);
		if($GetAllConfig->num_rows() > 0)
		{
			foreach($GetAllConfig->result_array() as $r)
			{
				$GetMultiLang = $r['is_multi_lang'];
			}
		}
		else $GetMultiLang="";
		
		$this->db->where("id", $id);
		if($id_lang || $GetMultiLang) $this->db->where("id_lang", $this->session->userdata("ses_id_lang"));
		$query = $this->db->get($tbl);
		
		return $query;
	}
	
	function GetListTable()
	{
		$query = $this->db->query("SHOW TABLES WHERE TABLES_in_".$this->db->database." LIKE '%".$this->db->dbprefix."%'");
		return $query->result_array();
	}
	
	function GetColumns($tbl)
	{
		if(substr($tbl,0,3) != "kg_") $tbl = "kg_".$tbl;
		$query = $this->db->query("SHOW COLUMNS FROM ".$tbl);
		return $query->result_array();
	}
	
	function GetTypeDataByField($tbl,$field)
	{
		if(substr($tbl,0,3) != "kg_") $tbl = "kg_".$tbl;
		$query = $this->db->query("SHOW COLUMNS FROM ".$tbl." WHERE Field='".$field."'");
		foreach($query->result_array() as $r)
		{
			return $r['Type'];
		}
		return false;
	}
	
	function GetDashboard()
	{
		$this->db->select("menu_admin.id,title,filez,is_active");
		$this->db->from("menu_admin");
		$this->db->where("is_active","Active");
		$this->db->join("dashboard","dashboard.id_menu_admin=menu_admin.id");
		$this->db->order_by("dashboard.sort");
		$query = $this->db->get();
		return $query->result_array();
	}
	
	function GetSetting()
	{
		//if($multi_lang) $this->db->where("id_lang", $this->session->userdata("ses_id_lang"));
		$this->db->select("menu_admin.id,title,is_active");
		$this->db->from("menu_admin");
		$this->db->where("filez !=","#");
		$this->db->not_like("filez","contents");
		$this->db->join("dashboard","dashboard.id_menu_admin=menu_admin.id","left");
		$this->db->group_by("menu_admin.id");
		$this->db->order_by("dashboard.sort");
		$query = $this->db->get();
		return $query->result_array();
	}
	
	function GetSettingByIdRefMenuAdmin($id)
	{
		$this->db->where("id_menu_admin", $id);
		$query = $this->db->get("dashboard");
		return $query;
	}
	
	function GetActivities($tbl,$limit)
	{
		$this->db->join("admin","admin.id=".$tbl.".id_admin");
		$this->db->limit($limit);
		$this->db->order_by($tbl.".create_date","desc");
		$query = $this->db->get($tbl);
		return $query->result_array();
	}
	
	function GetConfig($tbl)
	{
		$this->db->where("tabel", $tbl);
		$query = $this->db->get("config");
		foreach($query->result_array() as $r)
		{
			return unserialize($r['config']);
		}
		return array();
	}
	
	function GetAllConfig($tbl)
	{
		$this->db->where("tabel", $tbl);
		$query = $this->db->get("config");
		return $query;
	}
}
?>