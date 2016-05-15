<?php /**
        Author: SpringHack - springhack@live.cn
        Last modified: 2016-05-15 16:33:32
        Filename: AJC_FPS_Export.php
        Description: Created by SpringHack using vim automatically.
**/ ?>
<?php

	require_once('api.php');

	require_once('classes/AJC_Problem.php');

	if (!$app->user->isLogin())
		die('<center><a href=\'admin/status.php?action=login&url=../index.php\'>Please login or register first!</a></center>');
	if ($app->user->getPower() != 0)
		die('<center><a href=\'admin/status.php?action=login&url=../index.php\'>Please login or register first!</a></center>');
	
	if ((isset($_POST['from']) && isset($_POST['to'])) || isset($_POST['set']))
	{
		if (isset($_POST['from']) && isset($_POST['to']))
		{
			$_POST['from'] = intval($_POST['from']);
			$_POST['to'] = intval($_POST['to']);
			$list = range($_POST['from'], $_POST['to']);
		}
		if (isset($_POST['set']) && !empty($_POST['set']))
			$list = explode(',', $_POST['set']);
		header("Content-Type: application/file" );
		header("Content-Disposition: attachment;filename=\"fps-alxwvj.xml\"");

		getHeader();

		$db = new MySQL();

		foreach ($list as $id)
		{
			$res = $db->from('AJC_Problem')->where('`id`=\''.$id.'\'')->select()->fetch_one();
			if (!$res)
				continue;
			remixBody($res);
		}

		getFooter();
	}

	function getHeader()
	{
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		?>
	<fps version="1.1" url="http://code.google.com/p/freeproblemset/">
		<generator name="ALXWVJ" url="https://github.com/springhack/alxwvj"/>
<?php
	}

	function getFooter()
	{
		?>	</fps><?php
	}

	function fetchData($id, $num)
	{
		if (file_exists(AJC_ROOT.'/data/'.$id.'/data'.$num.'.in'))
			echo "<test_input><![CDATA[".file_get_contents(AJC_ROOT.'/data/'.$id.'/data'.$num.'.in')."]]></test_input>\n";
		else
			return false;
		if (file_exists(AJC_ROOT.'/data/'.$id.'/data'.$num.'.out'))
			echo "<test_output><![CDATA[".file_get_contents(AJC_ROOT.'/data/'.$id.'/data'.$num.'.out')."]]></test_output>\n";
		else
			return false;
		return true;
	}

	function remixBody($set)
	{
		?>
			<item>
			<title><![CDATA[<?php echo $set['title']; ?>]]></title>
			<time_limit unit="s"><![CDATA[<?php echo intval($set['time'])/1000; ?>]]></time_limit>
			<memory_limit unit="mb"><![CDATA[<?php echo intval($set['memory'])/1024; ?>]]></memory_limit>
			<description><![CDATA[<?php echo $set['description']; ?>]]></description>
			<input><![CDATA[<?php echo $set['input']; ?>]]></input> 
			<output><![CDATA[<?php echo $set['output']; ?>]]></output>
			<sample_input><![CDATA[<?php echo $set['sample_input']; ?>]]></sample_input>
			<sample_output><![CDATA[<?php echo $set['sample_output']; ?>]]></sample_output>
			<?php
				$num = 1;
				while (fetchData($set['id'], $num++));
			?>	
			<hint><![CDATA[<?php echo $set['hint']; ?>]]></hint>
			<source><![CDATA[<?php echo $set['source']; ?>]]></source>
			</item>
		<?php
	}

?>
<?php
	if (!isset($_POST) || empty($_POST))
	{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>导出题目</title>
    </head>
    <body>
        <center>
        <form method='post' enctype='multipart/form-data'>
            开始ID：<input type='text' name='from' /><br />
            结束ID：<input type='text' name='to' /><br />
			或枚举(例如：1001,1002)<br />
            枚举ID：<input type='text' name='set' /><br />
			<input type='submit' name='submit' value='Export' />
        </form>
		</center>
	</body>
</html>
<?php
	}
?>
