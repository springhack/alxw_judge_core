<?php /**
        Author: SpringHack - springhack@live.cn
        Last modified: 2016-05-09 13:54:06
        Filename: classes/AJC_Problem.php
        Description: Created by SpringHack using vim automatically.
**/ ?>
<?php

	//define AJC root 
	define('AJC_ROOT', '/home/AJC');

	require_once(dirname(__FILE__)."/DataPoster.php");

	class AJC_Problem {

		private $data_dir;
		private $pro_info;

		public function AJC_Problem($id = 1000)
		{

			//May need a config file
			$this->data_dir = AJC_ROOT.'/data/';
			
			$this->pro_info = array(
				'title' => file_get_contents($this->data_dir.$id.'/title'),	
				'time' => file_get_contents($this->data_dir.$id.'/time'),	
				'memory' => file_get_contents($this->data_dir.$id.'/memory'),
				//If we called this file as submissions, it won't be read, guess some key file that linux can't read...Orz...	
				'submissions' => file_get_contents($this->data_dir.$id.'/submission'),	
				'accepted' => file_get_contents($this->data_dir.$id.'/accepted'),	
				'description' => file_get_contents($this->data_dir.$id.'/description'),	
				'input' => file_get_contents($this->data_dir.$id.'/input'),
				'output' => file_get_contents($this->data_dir.$id.'/output'),	
				'sample_input' => file_get_contents($this->data_dir.$id.'/sample_input'),	
				'sample_output' => file_get_contents($this->data_dir.$id.'/sample_output'),	
				'hint' => file_get_contents($this->data_dir.$id.'/hint'),	
				'source' => file_get_contents($this->data_dir.$id.'/source')	
			);
		}

		public function getInfo()
		{
			return $this->pro_info;
		}

		public function submitCode($oj = "AJC", $id = "1000", $lang = "0", $code = "", $cid = '0')
		{
			$this->pro_submit = new DataPoster($oj, $id, $lang, $code, $cid);
		}

	}
?>
