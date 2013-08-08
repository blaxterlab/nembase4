<?php
//this script  performs cDNA library specific searches
//nembase3 September 2007
//Ann Hedley, University of Edinburgh

$txt=$_REQUEST["txt"];
print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
print "<html>\n";
print "<head>\n";
print "<title> www.nematodes.org - NEMBASE4 </title>\n";
include('/var/www/html/includes/nembase4_header.ssi');
print "</head>\n";
print "<body>\n";
include('/var/www/html/includes/nembase4_body_upper.ssi');

//define variables used with the database
//usually this goes in an include file
$PG_HOST="localhost";
$PG_PORT=5432;
$PG_DATABASE="nemdb4";
$PG_USER="webuser";
$PG_PASS="";


if($txt)
 {

//let's open the database
$dbconn=pg_connect( "dbname=$PG_DATABASE host=$PG_HOST port=$PG_PORT user=$PG_USER password=$PG_PASS" );
if ( ! $dbconn ) {
    echo "Error connecting to the database !<br> " ;
    printf("%s", pg_errormessage( $dbconn ) );
    exit(); }

	$sqlcom="select * from lib where lib_id=$txt;";
	#printf("$sqlcom<br>");
	$dblib = pg_exec($dbconn, $sqlcom);
	$row_maxlib=pg_NumRows($dblib);
	$do_lib = pg_Fetch_Object($dblib, 0);
	$name=$do_lib->name;     
	$organism =$do_lib->organism;  
	$strain =$do_lib->strain;    
	$sex=$do_lib->sex;        
	$stage=$do_lib->stage;      
	$tissue=$do_lib->tissue;     
	$vector =$do_lib->vector;    
	$vtype =$do_lib->vtype;     
	$rs1 =$do_lib->rs1;       
	$rs2=$do_lib->rs2;        
	$description=$do_lib->description;

	#$description=ereg_replace("'","",$description);
	#$description=preg_replace("/\'/","",$description);

	printf("<table class=\"tablephp1\"><tr><td>Library ID: $txt</td></tr>");
	printf("<tr><td><table class=\"tablephp2\">");
	printf("<tr><td colspan=3>$name</td></tr>");
	printf("<tr><td colspan=2>Species : $organism</td><td>Strain : $strain</td></tr>");
	printf("<tr><td>Sex : $sex</td><td>Stage : $stage</td><td>Tissue : $tissue</td></tr>");
	printf("<tr><td>Vector : $vector</td><td>Vector Type : $vtype</td><td>Restriction site 1 : $rs1<br>Restriction site 2 : $rs2</td></tr>");
	print("<tr><td colspan=3>Description : $description</td></tr>");
	printf("</table></td></table><br><br>");
}
printf("click your browser back button to return to search selection");

include('/var/www/html/includes/nembase4_body_lower.ssi');
print "</body>\n";
print "</html>\n";

?>
