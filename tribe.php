<?php
//this script seareches the tribe dataset in nembase3
//nembase3 September 2007
//Ann Hedley, University of Edinburgh

session_start();
print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
print "<html>\n";
print "<head>\n";
print "<title> www.nematodes.org - NEMBASE4 </title>\n";
include('/var/www/html/includes/nembase4_header.ssi');
print "</head>\n";
print "<body>\n";
include('/var/www/html/includes/nembase4_body_upper.ssi');

#get posted variables used with the database
$inf = "inf".$_REQUEST['inf']*10; 
$tribe = $_REQUEST['tribe'];
 
#define database connection
$PG_HOST="localhost";
$PG_PORT=5432;
$PG_DATABASE="nemdb4";
$PG_USER="webuser";
$PG_PASS="";

####connect to dbs
$dbconn=pg_connect( "dbname=$PG_DATABASE host=$PG_HOST port=$PG_PORT user=$PG_USER password=$PG_PASS" );
if ( ! $dbconn ) {echo "Error connecting to the database !<br> " ;printf("%s", pg_errormessage($dbconn));exit();}

#work out what kind of domain and thus what table to search
if($tribe)  {
	$count=0;
	$spid;
	$pept_id;
	$sql_com= "select spid,pept_id from tribe where $inf=$tribe order by pept_id;";
#	print "$sql_com<br>";#**************
	print "<div class=\"mainTitle\">Search Results for Tribe $inf-$tribe<br><br></div>\n";
   $query = pg_exec($dbconn, $sql_com);
	while ($row = pg_fetch_row($query)) {
		if ($pept_id[$row[0]]) {$pept_id[$row[0]].=$row[1].",";}
		else {$pept_id[$row[0]]=$row[1].",";}
		$count++;
	}
#	print "$sql_com<br>";
	$members="members"; if ($count==1) {$members="member";}
	if ($count==0) {print "<div class=\"mainMed\">This tribe is not represented in Nembase</div><br>\n";exit();}
	else {print "<div class=\"mainMed\" align=center>Tribe $inf-$tribe has $count $members</div><br>\n";}
	print "<br><table class=\"tablephp1\"><tr><td colspan=2>Species</td><td>Proteins that are members of tribe $inf-$tribe</tr>\n";
	$sp_names=sp_names(); #sub function get hash of spec_id=>full name
	foreach ($sp_names as $key=>$element) {
		if ($pept_id[$key]) {
			$ext="";	
			if (file_exists("/var/www/html/nembase4/species/$key.jpg")) {$ext="$key.jpg";}
			else if (file_exists("/var/www/html/nembase4/species/$key.gif")) {$ext="$key.gif";}
			else {$ext="not_available.jpg";}
			print "<tr class=\"tablephp21\"><td width=60><a href=\"species/$key.shtml\"><img src=\"/nembase4/species/$ext\" width=60 height=60></a></td><td><a href=\"species_info.php?species=$key\">";
			print $sp_names[$key]."</a><br><table><tr><td class=\"$key\" width=20 height=4></td></tr></table></td><td>";
			$pep = preg_split('/,/', $pept_id[$key], -1, PREG_SPLIT_NO_EMPTY);
			foreach ($pep as $pept) {print "<a href=\"protein.php?protein=$pept\">$pept</a> ";}
			print "</td></tr>\n";
		}
	}
}
print "</table>\n";
include('/var/www/html/includes/nembase4_body_lower.ssi');
print "</body>\n";
print "</html>\n";
#close the database
pg_close( $dbconn );
###########################################################################################################
function	sp_names() {
	// Get species id
	#define database connection
	$PG_HOST="localhost";
	$PG_PORT=5432;
	$PG_DATABASE="species_db4";
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
#		print $do->spec_id." => ".$sp_names[$do->spec_id]."<br>";
		$row++;
	}
	return $sp_names;
}
###########################################################################################################
?>
