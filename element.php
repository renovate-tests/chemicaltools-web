<!DOCTYPE html>
<html lang="zh-cn">
  <head>
   <meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>元素查询 -- 化学e+</title>
<?php include 'head.php';?>
  </head>
  <body>
<?php include 'header.php';
?>
    <section class="main-content">
	<? require 'load.php';
	include 'title.php';?>
		<h2>元素查询</h2>
		<form method='post' action='element.php'>
		<table><tr><table><tr><td><input type="text" name="input"/></td><td><input type="submit" value="查询"></td></tr></table></tr>
<?php
use \LeanCloud\Query;
use \LeanCloud\User;
if($_POST['input']!=""||$_GET['input']!=""){
	if($_POST['input']!="")$input=$_POST['input'];else $input=$_GET['input'];
	$nameQuery = new Query("Element");
	$nameQuery->equalTo("ElementName", $input);
	$AbbrQuery = new Query("Element");
	$AbbrQuery->equalTo("ElementAbbr", $input);
	$NumberQuery= new Query("Element");
	$NumberQuery->equalTo("ElementNumber", (int)$input);
	$IUPACQuery= new Query("Element");
	$IUPACQuery->equalTo("ElementIUPACname", $input);
	$query = Query::orQuery($nameQuery, $AbbrQuery,$NumberQuery,$IUPACQuery);
	if($query->count()>0){
		$todo = $query->first();
		$name=$todo->get("ElementName");
		$Abbr=$todo->get("ElementAbbr");
		$IUPACname = $todo->get("ElementIUPACname");
		$ElementNumber=$todo->get("ElementNumber");
		$ElementMass=$todo->get("ElementMass");
		$ElementOrigin=$todo->get("ElementOrigin");
		$output="元素名称：".$name."\n元素符号：".$Abbr."\nIUPAC名：".$IUPACname."\n原子序数：".$ElementNumber.
		"\n相对原子质量：".$ElementMass."\n元素名称含义：".$ElementOrigin;
		$outputHtml=$output."\n<a href='https://en.wikipedia.org/wiki/".$IUPACname."'>访问维基百科</a>";
		echo "<tr><table><tr><td><img src='img/element_".$ElementNumber.".png'></td></tr><tr><td>".nl2br($outputHtml)."</td></tr></table></tr>";
		if ($currentUser != null) {
			$currentUser->set("historyElementOutput", $output);
			$currentUser->set("historyElementOutputHtml", $outputHtml);
			$currentUser->set("historyElementNumber", (string)$ElementNumber);
			$currentUser->set("historyElement", $input);
			$currentUser->save();
		}
	}else{
		echo "<p>输入有误！</p>";
	}
}else{
	if ($currentUser != null) {
		$ElementNumber=$currentUser->get("historyElementNumber");
		$outputHtml=$currentUser->get("historyElementOutputHtml");
		echo "<tr><table><tr><td><img src='img/element_".$ElementNumber.".png'></td></tr><tr><td>".nl2br($outputHtml)."</td></tr></table></tr>";
	}
}
?></table></form>
<?php include 'foot.php';?>
    </section>
  </body>
</html>