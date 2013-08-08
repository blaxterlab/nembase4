<?php
//this script makes a list of cDNA libraries 
//nembase3 September 2007
//Ann Hedley, University of Edinburgh
#########
#ben 21/07/08
#num_ests no longer in lib table therefore removed this variable from sql
#ben 04/08/08
#second sql added which uses lib_id obtained from first to retieve number 
#of ESTs per cluster to fix its removal from the lib table 
#########

# Find info for libraries
$PG_HOST="localhost";
$PG_PORT=5432;
$PG_DATABASE="nemdb4";
$PG_USER="webuser";
$PG_PASS="";

if ($_REQUEST['organism']) {}
else {exit();}
$sqlcom="select lib_id,name,organism from lib where organism ~ '".$_REQUEST['organism']."' order by lib_id;";
#$sqlcom="select lib_id,name,organism,num_ests from lib  where organism ~ '".$_REQUEST['organism']."' order by num_ests desc,organism,lib_id;";
print "<tr><td class=\"mainTitle\">".$_REQUEST['organism']." Libraries<br><br></td></tr>\n";
print "<tr><td class=\"mainMed\">Limit the range of 'number of ESTs per cluster' for your library of interest. Examples: \"9\",\">2\" or \"<5\".<br>Anything else will ignore the respective library.<br><br><tr><td>\n";


####open the database
$dbconn=pg_connect( "dbname=$PG_DATABASE host=$PG_HOST port=$PG_PORT user=$PG_USER password=$PG_PASS" );
if ( ! $dbconn ) {
    echo "Error connecting to the database !<br> " ;
    printf("%s", pg_errormessage( $dbconn ) );
    exit(); 
}
$dbres = pg_exec($dbconn, $sqlcom );
if ( ! $dbres ) {
    echo "Error : " + pg_errormessage( $dbconn );
    exit(); 
}

$row=0;
$rowmax=pg_NumRows($dbres);

print "<table><tr><td align=center>limit</td><td align=center>No. ESTs</td><td></td><td>Library ID number and description</td></tr>\n";


while ($row<$rowmax)  {
    $do = pg_Fetch_Object($dbres, $row);
    $name=$do->name;
    $lib_id=$do->lib_id;
    #$num_ests=$do->num_ests;
    $row++;
	#second sql retieves number of ests for cluster and species specified using lib identifier from first sql
	#required due to removal of num_ests from lib table constuction 
	$lib = "lib_$lib_id";
    $sqlcom1="select sum($lib) as total from lib_count,species where species.species = '".$_REQUEST['organism']."' and lib_count.clus_id ~ species.spec_id";
	#print "select sum($lib) as total from lib_count,species where species.species = '".$_REQUEST['organism']."' and lib_count.clus_id ~ species.spec_id<br>";
    $dbres1 = pg_exec($dbconn, $sqlcom1);
    $row1=0;
	$do1 = pg_Fetch_Object($dbres1, $row1);
	$num_ests=$do1->total;
    print "<tr><td><input type=\"text\" size=\"4\" name=\"lib_numbers[]\" value=\"\"></td><td align=right>$num_ests&nbsp&nbsp&nbsp</td><td></td><td><a href=lib.php?txt=$lib_id>$lib_id </a>$name<BR>";
    print "<input type=\"hidden\" name=\"lib_stuff[]\" value=\"$lib_id\"></td></tr>\n";
}
print "</table></td></tr>";
?>
