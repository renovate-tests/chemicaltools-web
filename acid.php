<!DOCTYPE html>
<html lang="zh-cn">
  <head>
   <meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>酸碱计算 -- 化学e+</title>
<?php include 'head.php';?>
  </head>
  <body>
<?php include 'header.php';
?>
    <section class="main-content">
	<? require 'load.php';
	include 'title.php';?>
		<h2>酸碱计算</h2>
		<form method='post' action='acid.php'>
		<table><tr><td><input type="text" name="pKa" placeholder="pKa或pKb"/></td><td><select name="AorB"><option value ="acid">酸</option><option value ="base">碱</option></select></td></tr>
		<tr><td><input type="text" name="c" placeholder="分析浓度"/></td><td><input type="submit" value="计算"></td></tr></table></form>
<?php
//use \LeanCloud\Query;
use \LeanCloud\User;
if($_POST['pKa']!=""&&$_POST['c']!=""){
	$strpKa=$_POST['pKa'];
	$c=(double)$_POST['c'];
	$liquidpKa=-1.74;
	if ($currentUser != null) {
		$pKw=(double)($currentUser->get("pKw"));
	}else{
		$pKw=14;
	}
    if($_POST['AorB']=="acid")$AorB=true;else $AorB=false;
    if($AorB){
        $ABname="A";
        $ABnameall="HA";
    }else{
        $ABname="B";
        $ABnameall="BOH";
    }
	$strpKaArray=explode(" ",$strpKa);
    $valpKa=array();
    for($i=0;$i<count($strpKaArray);$i++){
        $valpKa[$i]=(double)$strpKaArray[$i];
        if ($valpKa[$i]<$liquidpKa) $valpKa[$i]=$liquidpKa;
    }
    $pH=calpH($valpKa,$c,$pKw);
    $cAB=calpHtoc($valpKa,$c,$pH);
    if(!$AorB) $pH=$pKw-$pH;
	$H=pow(10,-$pH);
    $acidOutput=$ABnameall." ,c=".$c."mol/L, ";
    for($i=0;$i<count($valpKa);$i++){
        if($AorB)$acidOutput=$acidOutput."pK<sub>a</sub>";else $acidOutput=$acidOutput."pK<sub>b</sub>";
        if(count($valpKa)>1)$acidOutput=$acidOutput."<sub>".($i+1)."</sub>";
        $acidOutput=$acidOutput."=".$strpKaArray[$i].", ";
    }
    $acidOutput=$acidOutput."\n溶液的pH为".sprintf("%.2f",$pH).".";
    $acidOutput=$acidOutput."\n"."c(H<sup>+</sup>)=".sprintf("%1$.2e",$H)."mol/L,";
    for($i=0;$i<count($cAB);$i++){
        $cABoutput="c(";
        if($AorB){
            if($i<count($cAB)-1){
                $cABoutput=$cABoutput."H";
                if(count($cAB)-$i>2) $cABoutput=$cABoutput."<sub>".(count($cAB) - $i-1)."</sub>";
            }
            $cABoutput=$cABoutput.$ABname;
            if($i>0){
                if($i>1) $cABoutput=$cABoutput."<sup>".($i)."</sup>";
                $ABoutput=$cABoutput."<sup>-</sup>";
            }
        }else{
			$cABoutput=$cABoutput.$ABname;
            if(count(cAB)-$i>2){
                $cABoutput=$cABoutput."(OH)<sub>".(count($cAB)- $i-1)."</sub>";
            }else if(count($cAB)-$i==2){
                $cABoutput=$cABoutput."OH";
            }
            if($i>0){
                if($i>1) $cABoutput=$cABoutput."<sup>".($i)."</sup>";
                $cABoutput=$cABoutput."<sup>+</sup>";
            }
        }
        $cABoutput=$cABoutput.")=";
        $acidOutput=$acidOutput."\n".$cABoutput.sprintf("%1$.2e",$cAB[$i])."mol/L,";
    }
    $acidOutput=rtrim($acidOutput,",").".";
	echo '<p>'.nl2br($acidOutput).'<p>';
	if ($currentUser != null) {
		$currentUser->set("historyAcidOutput", $acidOutput);
		$currentUser->save();
	}
}else{
	if ($currentUser != null) {
		?><p>
		<div class="history" id="historyAcid"></div>
		</p><?php
	}
}
function calpH($pKa,$c,$pKw) {
    $Ka1=pow(10,-$pKa[0]);
    $Kw=pow(10,-$pKw);
    $cH=(sqrt($Ka1*$Ka1+4*$Ka1*$c+$Kw)-$Ka1)*0.5;
    if($cH>0) return -log10($cH); else return 1024;
}
function calpHtoc($pKa,$c,$pH){
    $D=0;$E=1;
    $G=array();$Ka=array();$pHtoc=array();
    $H=pow(10,-$pH);
    $F=pow($H,count($pKa)+1);
    for($i=0;$i<count($pKa);$i++){
        $Ka[$i]=pow(10,-$pKa[$i]);
    }
    for($i=0;$i<count($pKa)+1;$i++){
        $G[$i]=$F*$E;
        $D=$D+$G[$i];
        $F=$F/$H;
        $E=$E*$Ka[$i];
    }
    for($i=0;$i<count($pKa)+1;$i++){
        $pHtoc[$i]=$c*$G[$i]/$D;
    }
    return $pHtoc;
}
?>
<?php include 'foot.php';?>
    </section>
  </body>
</html>