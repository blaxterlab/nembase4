<?php 
include("include/session.php");
if($session->logged_in){
    echo "<p align=right><a href=\"process.php\">Logout</a></p>";
}else{ 
	header("Location: main.php"); 
}
?>

<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
	
	<meta name="keywords" content="earthworms, blast, Lumbricus, pollution">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta name="GENERATOR" content="Mozilla/4.7 [en] (X11; I; Linux 2.2.13-0.9 alpha) [Netscape]">
	<title>Earthworm Genome</title>
	<!--link rel="shortcut icon" href="../favicon.ico" -->
	<script type="text/javascript" language="JavaScript" src="java/genome_menu2.js"></script>
	<script type="text/javascript" language="JavaScript" src="java/sliding_worm_banner_head.js"></script>

	
</head>
<script type="text/javascript" language="JavaScript" src="java/sliding_worm_banner_body.js"></script><br><br>

<? virtual ("includes/genome_body_upper.ssi") ?>
<? virtual ("includes/genome_header.ssi") ?>

<body onload="start()">

<center>
  <h1><i>Lumbricus rubellus</i> Genome Project<br>Metabolic Pathways</h1>
</center>

<form id="form" method="post" action="http://lumbricus.bio.ed.ac.uk/cgi-bin/pathways.cgi">
	<table width=100%>
		<tr><td align = center>Please enter an evalue for the cut-off of EC number inclusion, e.g. 1e-20</td></tr>
		<tr><td align = center>(Default = 1e-05)</td></tr>
		<tr><td align = center><input type="text" name="eval"></td></tr>
		<tr><td align = center><input TYPE="submit" VALUE="Search">&nbsp;&nbsp;</td></tr>
	</table>
</form>

<body>
<? virtual ("includes/body_lower.ssi") ?>

<center>
Comments and suggestions to: <a href="mailto:ben.elsworth@ed.ac.uk">ben.elsworth@ed.ac.uk </a>
</center>

</body>
</html>
