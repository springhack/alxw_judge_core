<?php /**
        Author: SpringHack - springhack@live.cn
        Last modified: 2016-05-11 15:12:49
        Filename: AJC_Insert.php
        Description: Created by SpringHack using vim automatically.
**/ ?>
<?php
	require_once("api.php");
	require_once("classes/AJC_Problem.php");
	if (!$app->user->isLogin())
		die('<center><a href=\'admin/status.php?action=login&url=../index.php\'>Please login or register first!</a></center>');
	if ($app->user->getPower() != 0)
		die('<center><a href=\'admin/status.php?action=login&url=../index.php\'>Please login or register first!</a></center>');
	if (!isset($_GET['id']))
		redirect('admin/error.php');
	$db = new MySQL();
	if (isset($_POST['submit']))
	{
		unset($_POST['submit']);
		unset($_POST['test_in']);
		unset($_POST['test_out']);
		$_POST['id'] = $_GET['id'];
		$_POST['submissions'] = '0';
		$_POST['accepted'] = '0';
		$flag = false;
		if ($_POST['id'] == 'new')
		{
			$flag = true;
			$num = $db->from("AJC_Problem")
				->select("max(cast(id as signed))")
				->fetch_one();
			$_POST['id'] = intval($num['max(cast(id as signed))']) + 1;
			if (intval($_POST['id']) < 1000)
				$_POST['id'] = 1000;
			$_POST['time_s'] = time();
		}
		if (!get_magic_quotes_gpc())
			foreach ($_POST as $k => $v)
				$_POST[$k] = addslashes($v);
		if ($flag)
			$db->value($_POST)
				->insert('AJC_Problem');
		else
			$db->set($_POST)
				->where('`id`=\''.$_POST['id'].'\'')
				->update('AJC_Problem');
		@mkdir(AJC_ROOT.'/data/'.intval($_POST['id']));
		@chmod(AJC_ROOT.'/data/'.intval($_POST['id']), 0777);
		if (isset($_FILES['test_in']) && $_FILES['test_in']['error'] == 0)
		{
			@move_uploaded_file($_FILES['test_in']['tmp_name'], AJC_ROOT.'/data/'.intval($_POST['id']).'/data1.in');
			@chmod(AJC_ROOT.'/data/'.intval($_POST['id']).'/data1.in', 0777);
		}
		if (isset($_FILES['test_out']) && $_FILES['test_out']['error'] == 0)
		{
			@move_uploaded_file($_FILES['test_out']['tmp_name'], AJC_ROOT.'/data/'.intval($_POST['id']).'/data1.out');
			@chmod(AJC_ROOT.'/data/'.intval($_POST['id']).'/data1.out', 0777);
		}
	}
	if (isset($_GET['id']) && is_numeric($_GET['id']))
		$info = $db->from('AJC_Problem')->where('`id`=\''.intval($_GET['id']).'\'')->select()->fetch_one();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>编辑题目</title>
		<script language='javascript' src='ckeditor/ckeditor.js'></script>
		<style>
			td {
				padding: 2px;
				padding-left: 10px;
				padding-right: 10px;
				border: 1px solid #AAA;
			}
		</style>
	</head>
	<body>
		<center>
			<form method='post' enctype='multipart/form-data'>
			<table>
				<tr>
					<td>
						Submit
					</td>
					<td>
						<input type='submit' name='submit' value='提交' />
					</td>
				</tr>
				<tr>
					<td>
						Title
					</td>
					<td>
						<input type='text' name='title' size='100' <?php if (isset($info)) echo 'value=\''.$info['title'].'\''; ?> />
					</td>
				</tr>
				<tr>
					<td>
						time
					</td>
					<td>
						<input type='text' name='time' value='<?php if (isset($info)) echo $info['time']; else echo 1000; ?>' />MS
					</td>
				</tr>
				<tr>
					<td>
						memory
					</td>
					<td>
						<input type='text' name='memory' value='<?php if (isset($info)) echo $info['memory']; else echo 65536; ?>' />KB
					</td>
				</tr>
				<tr>
					<td>
						description
					</td>
					<td>
						<textarea id='t_1' name='description' rows='10' cols='100'><?php if (isset($info)) echo $info['description']; ?></textarea>
					</td>
				</tr>
				<tr>
					<td>
						input
					</td>
					<td>
						<textarea id='t_2' name='input' rows='10' cols='100'><?php if (isset($info)) echo $info['input']; ?></textarea>
					</td>
				</tr>
				<tr>
					<td>
						output
					</td>
					<td>
						<textarea id='t_3' name='output' rows='10' cols='100'><?php if (isset($info)) echo $info['output']; ?></textarea>
					</td>
				</tr>
				<tr>
					<td>
						sample_input
					</td>
					<td>
						<textarea name='sample_input' rows='10' cols='100'><?php if (isset($info)) echo $info['sample_input']; ?></textarea>
					</td>
				</tr>
				<tr>
					<td>
						sample_input
					</td>
					<td>
						<textarea name='sample_output' rows='10' cols='100'><?php if (isset($info)) echo $info['sample_output']; ?></textarea>
					</td>
				</tr>
				<tr>
					<td>
						test.in:<input type=file name='test.in' />
					</td>
					<td>
						test.out:<input type=file name='test.out' />
					</td>
				</tr>
				<tr>
					<td>
						hint
					</td>
					<td>
						<input type='text' name='hint' value='<?php if (isset($info)) echo $info['hint']; else echo 'none'; ?>' />
					</td>
				</tr>
				<tr>
					<td>
						source
					</td>
					<td>
						<input type='text' name='source' value='<?php if (isset($info)) echo $info['source']; else echo 'SpringHack' ?>' />
					</td>
				</tr>
			</table>
			</form>
		</center>
		<script language='javascript'>
			(function (window, undefined) {
			 
				for (var i=1;i<=3;++i)
					CKEDITOR.replace('t_' + i);
			 
			})(window);
		</script>
	</body>
</html>
