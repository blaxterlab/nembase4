<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<!-- InstanceBegin template="/Templates/bang.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<!-- InstanceBeginEditable name="doctitle" -->
<title> www.nematodes.org - NEMBASE3 </title>
<!-- InstanceEndEditable -->
<? include ("nembase3_header.ssi"); ?>
</head>
<body>
<? include ("nembase3_body_upper.ssi"); ?>
<!-- InstanceBeginEditable name="BodyEditRegion" -->

<div class="mainTitle">Search Results for proteins with the following domains</div><br>
<div class="mainBig"><center>
<?php
$doms=$_POST["DOM"]; #read the annotations array
$i=0;
foreach ($doms as $dom) {$dom_ids[$dom]=1;}	#read them into a hash to remove duplicates
foreach ($dom_ids as $key=>$element) {
	print "$key &nbsp&nbsp&nbsp ";
	$i++;
	if (($i % 4) == 0) {print "<br>\n";}
}
print "</div></center><br>";

############################################################
################ open the database #########################
############################################################
$dbconn=pg_connect( "dbname=nemdb3 host=localhost port=5432 user=webuser password=" );
if ( ! $dbconn ) {
    echo "Error connecting to the database !<br> " ;
    printf("%s", pg_errormessage( $dbconn ) );
    exit(); 
}

$href="http://xyala.cap.ed.ac.uk/nembase3/protein.php?protein";

############################################################
############# set up the basic query #######################
############################################################
$sql_com="select distinct on (spid,pept_id) pept_id,spid from ";
$nemdom=$intdom=$sql_com_tail="";
$i=0;
foreach ($doms as $dom) {	
	if($i>0){ 
		$sql_com.=" and pept_id in (select pept_id from ";
		$sql_com_tail.=")";
			}
	if (preg_match('/^ND/',$dom)) {$sql_com.="domain_coords where domid='$dom' ";}
	else {$sql_com.="interpro where dom_id='$dom' ";}
	$i++;
}
$sql_com.=$sql_com_tail." order by spid,pept_id;";
#print "$sql_com<br>";######################
############################################################
#########Table of seq ids output ###########################
############################################################
####execute the query
$dbres = pg_exec($dbconn, $sql_com );
if ( ! $dbres ) {
	echo "Error : " + pg_errormessage( $dbconn );
	exit(); 
}
#### and now print the results table
$row=0;
$rowmax=pg_NumRows($dbres);
if ($rowmax==0) {print "<div class=\"mainMed\">There are no clusters matching your search</div><br>\n";}
else {
	print "<div class=\"mainMed\">There are $rowmax clusters matching your search</div><br>\n";
	print "<table class=\"tablephp1\"><TR><td colspan=2>Species</td><TD>Proteins containing these domain(s)</TD></tr>\n";
	$sp_names=sp_names(); #sub function get hash of spec_id=>full name
	$prev_spid="";
	while($row<$rowmax) {
		$do_prot=pg_fetch_row($dbres, $row);
		$prot=$do_prot[0];
		$key=$do_prot[1];
		if ($key!=$prev_spid) {
			$ext="";	
			if (file_exists("/var/www/html/nembase3/species/$key.jpg")) {$ext="$key.jpg";}
			else if (file_exists("/var/www/html/nembase3/species/$key.gif")) {$ext="$key.gif";}
			else {$ext="not_available.jpg";}
			$prev_spid=$key;
			if ($row>0) {print "</td></tr>\n";}
			print "<tr><td class=\"tablephp21\" width=60><a href=\"species/$key.shtml\"><img src=\"/nembase3/species/$ext\" width=60 height=60></a></td><td class=\"tablephp21\"><table><tr><td class=\"$key\" width=50 height=4></td></tr></table>".$sp_names[$key]."</td><td class=\"tablephp21\">";
		}
		print "<a href=\"$href=$prot\">$prot</a> \n";
		$row++;
	}
	print "</td></tr></table>\n";
}
###########################################################################################################
function	sp_names() {
	// Get species id
	#define database connection
	$PG_HOST="localhost";
	$PG_PORT=5432;
	$PG_DATABASE="species_db";
	$PG_USER="webuser";
	$PG_PASS="";
	$dbconn_sp=pg_connect( "dbname=$PG_DATABASE host=$PG_HOST port=$PG_PORT user=$PG_USER password=$PG_PASS" );
	if ( ! $dbconn_sp ) {echo "Error connecting to the database !<br> " ;printf("%s", pg_errormessage($dbconn_sp));exit();}
	$sqlcom_sp="select spec_id,name from info;";
	$dbres_sp = pg_exec($dbconn_sp, $sqlcom_sp );
	if ( ! $dbres_sp ) {echo "Error : " + pg_errormessage( $dbconn_sp ); exit();} 
	$row=0;
	$rowmax=pg_NumRows($dbres_sp);
	while ($row<$rowmax)  {
		$do = pg_Fetch_Object($dbres_sp, $row);
		$sp_names[$do->spec_id]=$do->name;
		$row++;
	}
	return $sp_names;
}
###########################################################################################################?>

<!-- InstanceEndEditable -->
<? include ("nembase3_body_lower.ssi"); ?>
</body>
<!-- InstanceEnd -->
</html>

