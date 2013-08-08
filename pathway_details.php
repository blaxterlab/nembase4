<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title> www.nematodes.org - nembase4 </title>

<?
include('/var/www/html/includes/nembase4_header.ssi');
?>

</head>
<body>
<!-- InstanceEndEditable -->

<?
include('/var/www/html/includes/nembase4_body_upper.ssi');

$path_id=$_GET["pathid"]; #get the evalue from pathways.shtml to pass to the cgi script
$enz=$_GET["enz"];
$eval=$_GET["eval"];
$sp=$_GET["sp"];
virtual('/cgi-bin/map.cgi?pathid='.$path_id.'&enz='.$enz.'&eval='.$eval.'&sp='.$sp.'');

include('/var/www/html/includes/nembase4_body_lower.ssi');
?>


</body>
<!-- InstanceEnd -->
</html>



