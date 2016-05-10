<?php /**
        Author: SpringHack - springhack@live.cn
        Last modified: 2016-05-10 22:31:58
        Filename: AJC_FPS_Import.php
        Description: Created by SpringHack using vim automatically.
**/ ?>
<?php

require_once('api.php');

if (!$app->user->isLogin())
	die('<center><a href=\'admin/status.php?action=login&url=../index.php\'>Please login or register first!</a></center>');
if ($app->user->getPower() != 0)
	die('<center><a href=\'admin/status.php?action=login&url=../index.php\'>Please login or register first!</a></center>');

if (isset($_FILES['fps']))
{

	if ($_FILES['fps']['error'] != UPLOAD_ERR_OK)
		die('<h2>Import error !</h2>');

	function mkdata($pid, $file, $node)
	{
		@mkdir('/home/AJC/data/'.$pid);
		file_put_contents('/home/AJC/data/'.$pid.'/'.$file, $node);
	}

	function image_save_file($filepath ,$base64_encoded_img)
	{
    	$fp=fopen($filepath ,"wb");
    	fwrite($fp,base64_decode($base64_encoded_img));
    	fclose($fp);
	}

    function getValue($Node, $TagName) {
        
        return $Node->$TagName;
    }

    function getAttribute($Node, $TagName,$attribute) {
        return $Node->children()->$TagName->attributes()->$attribute;
    }

    function hasProblem($title){
        return true;
        
    }
    
    $tempfile = $_FILES['fps']['tmp_name'];

    $xmlDoc=simplexml_load_file($tempfile, 'SimpleXMLElement', LIBXML_PARSEHUGE);
    $searchNodes = $xmlDoc->xpath ( "/fps/item" );

	$db = new MySQL();

	$num = $db->from("AJC_Problem")
	          ->select("max(cast(id as signed))")
			  ->fetch_one();

	$pid = intval($num['max(cast(id as signed))']) + 1;
	if ($pid < 1000)
		$pid = 1000;
	$count = 0;

    foreach($searchNodes as $searchNode) {
        
        $title =$searchNode->title;

        $time_limit = $searchNode->time_limit;
        $unit=getAttribute($searchNode,'time_limit','unit');
        if($unit=='ms') $time_limit/=1000;

        
        $memory_limit = getValue ( $searchNode, 'memory_limit' );
        $unit=getAttribute($searchNode,'memory_limit','unit');
        if($unit=='kb') $memory_limit/=1024;

        
        $description = getValue ( $searchNode, 'description' );
        $input = getValue ( $searchNode, 'input' );
        $output = getValue ( $searchNode, 'output' );
        $sample_input = getValue ( $searchNode, 'sample_input' );
        $sample_output = getValue ( $searchNode, 'sample_output' );
        $hint = getValue ( $searchNode, 'hint' );
        $source = getValue ( $searchNode, 'source' );
        $solutions = $searchNode->children()->solution;
        if(hasProblem($title))
		{

			$t_data = array(
				'id' => $pid,
				'title' => (string)$title,
				'time' => (string)$time_limit,
				'memory' => (string)$memory_limit,
				'submissions' => '0',
				'accepted' => '0',
				'description' => (string)$description,
				'input' => (string)$input,
				'output' => (string)$output,
				'sample_input' => (string)$sample_input,
				'sample_output' => (string)$sample_output,
				'hint' => (string)$hint,
				'source' => (string)$source,
				'time_s' => time() + ($count++)
			);


            $testinputs=$searchNode->children()->test_input;
            $testno=1;
            foreach($testinputs as $testNode){
                mkdata($pid,"data".$testno++.".in",$testNode);
            }

            $testinputs=$searchNode->children()->test_output;
            $testno=1;
            foreach($testinputs as $testNode){
                mkdata($pid,"data".$testno++.".out",$testNode);
            }

            $images = ($searchNode->children()->img);
            $did = array();
            $testno = 0;
            foreach($images as $img){
                $src=getValue($img,"src");
                if(!in_array($src,$did)){
                    $base64=getValue($img,"base64");
                    $ext=pathinfo($src);
                    $ext=strtolower($ext['extension']);
                    if(!stristr(",jpeg,jpg,png,gif,bmp",$ext)){
                        $ext="bad";
                        exit(1);
                    }
                    $testno++;
					
					$base64 = 'data:image/'.$ext.';base64,'.$base64;

					$t_data['description'] = str_replace($src, $base64, $t_data['description']);
					$t_data['input'] = str_replace($src, $base64, $t_data['input']);
					$t_data['output'] = str_replace($src, $base64, $t_data['output']);
                    
                    //image_save_file($newpath,$base64);
                    array_push($did,$src);
                }
                
            }


			//Import to dbms
			foreach ($t_data as $k => $v)
				$t_data[$k] = addslashes($v);
			$db->value($t_data)->insert('AJC_Problem');

			echo '<h2>Problem "'.$title.'" import to system ok !</h2><br />';

			/**
            foreach($solutions as $solution) {
                $language =$solution->attributes()->language;
                submitSolution($pid,$solution,$language);
                
            }
			**/
        }
		$pid++;
  	} 
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>导入题目</title>
    </head>
    <body>
        <center>
        <form method='post' enctype='multipart/form-data'>
            <input type='file' name='fps' /><input type='submit' name='submit' value='Import' />
        </form>
		</center>
	</body>
</html>
