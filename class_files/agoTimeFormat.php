<?php
//classs for converting time ago
 class convertToAgo
{
	function convert_datetime($str)
	{
		list($date,$time)=explode(' ',$str);
		list($year,$month,$day)=explode('-',$date);
		list($hour,$minute,$second)=explode(':',$time);		
		$timestamp=mktime($hour,$minute,$second,$month,$day,$year);
		return $timestamp;
	}
	
	function makeAgo($timestamp)
	{
	$difference=time()-$timestamp;
	$periods=array("sec","min","hour","day","week","month","year","decade");
	$length=array("60","60","24","7","4.35","12","10");
	for($j=0; $difference >=$length[$j] ;$j++)
	$difference/=$length[$j];
	$difference=round($difference);
	if($difference!=1) $periods[$j].="s";
	$text="$difference $periods[$j] ago"; 
	return $text;
	}
		
}
?>