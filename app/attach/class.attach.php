<?php
defined('IN_TS') or die('Access Denied.');
class attach extends tsApp{

	//构造函数
	public function __construct($db){
		parent::__construct($db);
	}
	
	//获取单个附件
	function getOneAttach($attachid){
	
		$isattach = $this->db->once_num_rows("select * from ".dbprefix."attach where attachid='$attachid'");
		
		if($isattach > '0'){
			$strAttach = $this->db->once_fetch_assoc("select * from ".dbprefix."attach where attachid='$attachid'");
			
			$menu = substr($strAttach['userid'],0,1);
			
			$strAttach['attachurl'] = 'uploadfile/attach/'.$menu.'/'.$strAttach['attachurl'];
			
			$strAttach['isattach'] = '1';
			
			
		}else{
			$strAttach['isattach'] = '0';
		}
		
		return $strAttach;
		
	}
	
}