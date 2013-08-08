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

$evalue=$_POST["eval"]; #get the evalue from pathways.shtml to pass to the cgi script
$id_list=('');
if ($_POST) {
	#$kv = array();
	foreach ($_POST as $key) {
		if (is_array($key)){
			foreach ($key as $value){
				if (preg_match('/[A-Z]{3}/',$value)){
					$value_new = preg_replace('/C$/','P',$value);
					$id_list.=$value_new.':';
				}
			}
		
		}
	}
}
virtual('/cgi-bin/nembase4_pathways.cgi?eval='.$evalue.'&id_list='.$id_list.'');

include('/var/www/html/includes/nembase4_body_lower.ssi');
?>


</body>
<!-- InstanceEnd -->
</html>



