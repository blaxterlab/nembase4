<?php
//this script collects domain information
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

$genus=array("I","III","IV","V","Trichinellida","Ascarididomorpha","Spiruromorpha","Strongyloidoidea","Tylenchomorpha","Strongylomorpha","Meloidogyne");
$sp=0;
foreach ($genus as $gen) {
	if (sizeof($_POST[$gen])>0) {$sp=1; last;}
}
if ($sp==0 && $_REQUEST["sp"]!="ALL") {print "<div class=\"mainBig\">You must select a species to search<br>Please use the back button on your browser to return to the annotation search page.</div>";}
elseif (!$_REQUEST["domain"]) {print "<div class=\"mainBig\">You must select a domain ID with which to search<br>Please use the back button on your browser to return to the annotation search page.</div>";}
else {

#get posted variables used with the database
$dom_id = $_REQUEST['domain']; 

#define database connection
$PG_HOST="localhost";
$PG_PORT=5432;
$PG_DATABASE="nemdb4";
$PG_USER="webuser";
$PG_PASS="";

####connect to dbs
$dbconn=pg_connect( "dbname=$PG_DATABASE host=$PG_HOST port=$PG_PORT user=$PG_USER password=$PG_PASS" );
if ( ! $dbconn ) {echo "Error connecting to the database !<br> " ;printf("%s", pg_errormessage($dbconn));exit();}

	#### Get the species list ####
	$species="";
	if ($_REQUEST["sp"]!="ALL") {
		foreach ($genus as $gen) {
			$orgs=$_POST[$gen];   #read the genus array 
			foreach ($orgs as $org) { #for each species
#				if (preg_match('/(\w\w)P/',"$org")) {
#					$org=preg_replace('/(\w\w)C/', '\1P',$org);
					$species.=" or spid='".$org."' ";
#				}
			}
		}
	$species = preg_replace('/or/','and (',$species,1);
	$species.=") ";
	}

#work out what kind of domain and thus what table to search
if($dom_id)  {
  $spid;
  $pept_id;
  $count=0;
  #NemDom domains
  if (preg_match("/^NDn/","$dom_id")) { 
  	  $sql_com= "select spid,pept_id,domid from domain_coords where pept_id in (select pept_id from domain_coords where domid='$dom_id' $species order by pept_id) order by pept_id,domid;";
#	  print "$sql_com first ND<br>";
     $query = pg_exec($dbconn, $sql_com);
     while ($row = pg_fetch_row($query)) {
   #    preg_match("/^(\w\w)/",$row[0],$sp);
   #    $sp[1].="C";
        if ($row[2]!=$dom_id && !preg_match("/$row[1]/",$associates[$row[1]])) {$associates[$row[1]].=$row[2]." ";}
        if ($row[2]==$dom_id){
          if ($pept_id[$row[1]]) {$pept_id[$row[1]]=$pept_id[$row[1]]+1;}else{$pept_id[$row[1]]=1;}
          if ($spid[$row[0]]) {$spid[$row[0]]=$spid[$row[0]]+1;}else{$spid[$row[0]]=1;}
          $count++;
        }
     }   
     $sql_com= "select distinct on (pept_id,dom_id) pept_id,dom_id from interpro where pept_id in (select pept_id from domain_coords where domid='$dom_id' $species order by pept_id) order by pept_id,dom_id;";
#	  print "$sql_com<br>";
     $query = pg_exec($dbconn, $sql_com);
     while ($row = pg_fetch_row($query)) {if($associates[$row[0]]){$associates[$row[0]].=$row[1]." ";}else{$associates[$row[0]]=$row[1]." ";}}   
     $sql_com= "select ldesc,urlref from domain_annot where domid='$dom_id';";
     $query = pg_exec($dbconn, $sql_com);
     $row = pg_fetch_row($query);
     $description="";
     if ($row[0]) {$description="<a href=\"$row[1]\">$row[0]</a>";}
  }
  #IPR numbers
  elseif (preg_match("/^IPR/","$dom_id")) { 
  	$sql_com= "select pept_id,interpro.dom_id,ipr_id from interpro,interpro_key where interpro.dom_id=interpro_key.dom_id and pept_id in (select pept_id from  interpro,interpro_key where interpro.dom_id=interpro_key.dom_id and ipr_id='$dom_id' $species order by pept_id) order by pept_id,interpro.dom_id;";
#	print "$sql_com<br>";
   $query = pg_exec($dbconn, $sql_com);
   while ($row = pg_fetch_row($query)) {
      preg_match("/^(\w\w)/",$row[0],$sp);
      $sp[1].="C";
      if ($row[2]!=$dom_id && !preg_match("/$row[1]/",$associates[$row[0]])) {$associates[$row[0]].=$row[1]." ";}
       if ($row[2]==$dom_id){
      	if ($pept_id[$row[0]]) {$pept_id[$row[0]]=$pept_id[$row[0]]+1;}else{$pept_id[$row[0]]=1;}
      	if ($spid[$sp[1]]) {$spid[$sp[1]]=$spid[$sp[1]]+1;}else{$spid[$sp[1]]=1;}
         $count++;
      }
   }   
  	$sql_com= "select distinct on (pept_id,domid) pept_id,domid from domain_coords where pept_id in (select pept_id from interpro,interpro_key where interpro.dom_id=interpro_key.dom_id and ipr_id='$dom_id' $species order by pept_id) order by pept_id,domid;";
#	print "$sql_com<br>";
   $query = pg_exec($dbconn, $sql_com);
   while ($row = pg_fetch_row($query)) {$associates[$row[0]].=$row[1]." ";}   
   $sql_com= "select description from interpro_key where ipr_id='$dom_id';";
   $query = pg_exec($dbconn, $sql_com);
   $row = pg_fetch_row($query);
   $description="";
   if ($row[0]) {$description="<a href=\"http://www.ebi.ac.uk/interpro/IEntry?ac=$dom_id\">$row[0]</a>";}
  }
  #Interpro domain id
  else{
    $sql_com= "select pept_id,dom_id from interpro where pept_id in (select pept_id from interpro where dom_id='$dom_id' $species) order by pept_id,dom_id;";
#	print "$sql_com<br>";
    $query = pg_exec($dbconn, $sql_com);
    while ($row = pg_fetch_row($query)) {
       preg_match("/^(\w\w)/",$row[0],$sp);
       $sp[1].="C";
       if ($row[1]!=$dom_id && !preg_match("/$row[1]/",$associates[$row[0]])) {$associates[$row[0]].=$row[1]." ";}
       if ($row[1]==$dom_id){
       	if ($pept_id[$row[0]]) {$pept_id[$row[0]]=$pept_id[$row[0]]+1;}else{$pept_id[$row[0]]=1;}
       	if ($spid[$sp[1]]) {$spid[$sp[1]]=$spid[$sp[1]]+1;}else{$spid[$sp[1]]=1;}
         $count++;
       }
   }  
   $sql_com= "select description,ipr_id from interpro_key where dom_id='$dom_id';";
#	print "$sql_com<br>";
   $query = pg_exec($dbconn, $sql_com);
   $row = pg_fetch_row($query);
   $description="";
   if ($row[0]) {$description="<a href=\"http://www.ebi.ac.uk/interpro/IEntry?ac=$row[1]\">$row[0]</a>";}
 }
}

#$handle = fopen ("stats.php", "r");
#if ($handle) {
#	while (!feof($handle)) {  #while there are lines in the file
#		$buffer = fgets($handle, 4096);	#read one line at a time
#		if (preg_match("/'(\w+ \w+)' => '(\w\w\w)'/",$buffer,$match)) {$species_ids{$match[2]}=$match[1];} 
#	} 
#}
print "<div class=\"mainTitle\">Domain Search Results for $dom_id<br><br></div>\n";
if ($count==0) {
	if($species==""){print "<div class=\"mainMed\">This domain is not represented in Nembase</div><br>\n";exit();}
	else {print "<div class=\"mainMed\">This domain is not represented in the selected species</div><br>\n";exit();}
}
if ($description!="") {print "<div class=\"mainMed\">Description: $description<br>";}
else {print "<div class=\"mainMed\">";}
if($species==""){print "This domain is represented in Nembase $count times</div><br>\n";}
else {print "This domain is represented in the selected species $count times</div><br>\n";}
print "<br><table class=\"tablephp1\"><tr><td colspan=2>species</td><td>Protein and associated domains</tr>\n";
$sp_names=sp_names(); #sub function get hash of spec_id=>full name
foreach ($spid as $key=>$element) {
	$ext="";	
	if (file_exists("/var/www/html/nembase4/species/$key.jpg")) {$ext="$key.jpg";}
	else if (file_exists("/var/www/html/nembase4/species/$key.gif")) {$ext="$key.gif";}
	else {$ext="not_available.jpg";}
	$copy="copy";if ($element>1) {$copy="copies";}
	print "<tr class=\"tablephp21\"><td width=60><a href=\"species/$key.shtml\"><img src=\"/nembase4/species/$ext\" width=60 height=60></a></td><td><table><tr><td class=\"$key\" width=50 height=4></td></tr></table><a href=\"species/$key.shtml\">";
	print $sp_names[$key]."</a><br>has $element $copy</td><td><table>";
	foreach ($pept_id as $key2=>$element2) {
      preg_match("/^(\w\w)/",$key2,$sp);$sp[1].="C";
		if ($sp[1] == $key) {
			print "<tr><td><a href=\"protein.php?protein=$key2\">$key2</a>";
			if ($element2>1) {
				print "<br>$element2 copies";
			}
			print "</td><td>$associates[$key2]</td></tr>\n";
		}
	}
	print "</table></td></tr>\n";
}
}
print "</table>\n";
include('/var/www/html/includes/nembase4_body_lower.ssi');
print "</body>\n";
print "</html>\n";
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
		$row++;
	}
	return $sp_names;
}
###########################################################################################################
?>
