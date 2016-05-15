<?php /**
        Author: SpringHack - springhack@live.cn
        Last modified: 2016-05-16 00:11:12
        Filename: AJC_ProblemManager.php
        Description: Created by SpringHack using vim automatically.
**/ ?>
<?php
	require_once("api.php");
	if (!$app->user->isLogin())
		die('<center><a href=\'admin/status.php?action=login&url=../index.php\'>Please login or register first!</a></center>');
	if ($app->user->getPower() != 0)
		die('<center><a href=\'admin/status.php?action=login&url=../index.php\'>Please login or register first!</a></center>');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>AJC题目管理</title>
		<style>
			td {
				padding: 2px;
				padding-left: 10px;
				padding-right: 10px;
				border: 1px solid #AAA;
			}
			a {
				color: #444;
				text-decoration: none;
			}
		</style>
	</head>
	<body>
		<center>
			<?php
				$sstart = isset($_GET['page'])?(intval($_GET['page'])-1)*10:0;
				$db = new MySQL();
				if ($db->query("SHOW TABLES LIKE 'AJC_Problem'")->num_rows() != 1)
				{
					$db->struct(array(
							'id' => 'text',
							'title' => 'text',
							'time' => 'text',
							'memory' => 'text',
							'submissions' => 'text',
							'accepted' => 'text',
							'description' => 'text',
							'input' => 'text',
							'output' => 'text',
							'sample_input' => 'text',
							'sample_output' => 'text',
							'hint' => 'text',
							'source' => 'text',
							'time_s' => 'text'
						))->create("AJC_Problem");
				}
				$list = $db->from("AJC_Problem")->limit(10, $sstart)->order('desc', 'time_s')->select()->fetch_all();
				echo $db->error();
			?>
			<a href='AJC_Insert.php?id=new'>添加问题</a>
			&nbsp;&nbsp;|&nbsp;
			<a href='AJC_FPS_Import.php'>导入问题</a>
			&nbsp;&nbsp;|&nbsp;
			<a href='AJC_FPS_Export.php'>导出问题</a><br />
			<table>
				<tr>
					<td>
						ID
					</td>
					<td>
						Title
					</td>
					<td>
						Operation
					</td>
				</tr>
				<?php
					for ($i=0;$i<count($list);++$i)
						echo '<tr><td>'.$list[$i]['id'].'</td><td>'.$list[$i]['title'].'</td><td><a href="AJC_Insert.php?id='.$list[$i]['id'].'">编辑</a> | <a href="AJC_Data_Edit.php?op=home&folder='.$list[$i]['id'].'">数据</a></td></tr>';
				?>
			</table>
			<script language="javascript" src="Widget/pageSwitcher/pageSwitcher.js"></script>
		</center>
	</body>
</html>
