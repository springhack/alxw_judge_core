<?php /**
        Author: SpringHack - springhack@live.cn
        Last modified: 2016-04-29 20:15:40
        Filename: AJC_Record.php
        Description: Created by SpringHack using vim automatically.
**/ ?>
<?php
	
	class AJC_Record {
		
		private $db = NULL;
		private $res = "";
		private $id = "";
		private $rid;
		
		//Common construct
		public function AJC_Record($id)
		{
			$this->id = $id;
			$this->rid = $id;
		}

		
		//For view
		public function getInfo()
		{
			$this->db = new MySQL();
			$this->res = $this->db->from("Record")->where("`id` = '".$this->id."'")->select()->fetch_one();
			return $this->res;
		}

		
	}
	
?>
