<?php /**
        Author: SpringHack - springhack@live.cn
        Last modified: 2016-05-09 20:54:45
        Filename: AJC_Problem.php
        Description: Created by SpringHack using vim automatically.
**/ ?>
<?php

	//define AJC root 
	define('AJC_ROOT', '/home/AJC');

	require_once(dirname(__FILE__)."/DataPoster.php");

	class AJC_Problem {

		private $data_dir;
		private $pro_info;
		private $db;
		private $id;

		public function AJC_Problem($id = 1000)
		{

			//May need a config file
			$this->data_dir = AJC_ROOT.'/data/';

			$this->id = $id;
			
		}

		public function getInfo()
		{
			$this->db = new MySQL();
			$this->pro_info = $this->db->from('AJC_Problem')->where('`id`=\''.$this->id.'\'')->select()->fetch_one();
			foreach ($this->pro_info as $k => $v)
				if (is_numeric($k))
					unset($this->pro_info[$k]);
			unset($this->pro_info['id']);
			return $this->pro_info;
		}

		public function submitCode($oj = "AJC", $id = "1000", $lang = "0", $code = "", $cid = '0')
		{
			$this->pro_submit = new DataPoster($oj, $id, $lang, $code, $cid);
		}

	}
?>
