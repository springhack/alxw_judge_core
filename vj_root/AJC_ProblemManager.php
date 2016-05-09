<?php /**
        Author: SpringHack - springhack@live.cn
        Last modified: 2016-05-09 13:53:54
        Filename: AJC_ProblemManager.php
        Description: Created by SpringHack using vim automatically.
**/ ?>
<?php

	require_once("api.php");

	if (!$app->user->isLogin())
		die('<center><a href=\'admin/status.php?action=login&url=../index.php\'>Please login or register first!</a></center>');
	if ($app->user->getPower() != 0)
		die('<center><a href=\'admin/status.php?action=login&url=../index.php\'>Please login or register first!</a></center>');

	require_once('classes/AJC_Problem.php');

	function getList()
	{
		$ret = array();
		$handle = opendir(AJC_ROOT.'/data');
		while ($file = readdir($handle))
		{
			if ($file != '.' && $file != '..')
				$ret[] = $file;
		}
		closedir($handle);
		return $ret;
	}

	function getInfo($id)
	{
		return (new AJC_Problem($id))->getInfo();
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>AJC题目管理</title>
	</head>
	<body>
		<center>
			<?php
				$sstart = isset($_GET['page'])?(intval($_GET['page'])-1)*10:0;
			?>
			<script language="javascript" src="Widget/pageSwitcher/pageSwitcher.js"></script>
		</center>
	</body>
</html>
