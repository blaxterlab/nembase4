<?php

###################
#ben 21/07/08
#hardcoded to include nemdb3_header/body files, changed to nembase4_header/body (lines 14, 17 and 116)
#changed display section to match new blast dbs (uniref 100, wormpep - lines 80-100)
#changed loads of it, see original if things go wrong
#ben 08/06/09
#added class=\"tablephp\" to make table look nicer
#ben 17/06/09
#added offset and change values to split results into pages of 50 clusters
#ben 08/07/09
#added species name and clade to top of results page
##################

print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
print "<html><!-- InstanceBegin codeOutsideHTMLIsLocked=\"false\" -->\n";
print "<head>\n";
print "<!-- InstanceBeginEditable name=\"doctitle\" -->\n";
print "<title> www.nematodes.org - the Home of Nematode and Neglected Genomics in the Blaxter Lab </title>\n";
print "<!-- InstanceEndEditable -->\n";
include('/var/www/html/includes/nembase4_header.ssi');
print "</head>\n";
print "<body>\n";
include('/var/www/html/includes/nembase4_body_upper.ssi');

$organism=$_GET["organism"];
$offset=$_GET["offset"];

#define variables used with the database
#usually this goes in an include file
$PG_HOST="localhost";
$PG_PORT=5432;
$PG_DATABASE="nemdb4";
$PG_USER="webuser";
$PG_PASS="";
$PI=pi();

$dbconn=pg_connect( "dbname=$PG_DATABASE host=$PG_HOST port=$PG_PORT user=$PG_USER password=$PG_PASS" );
if ( ! $dbconn ) {
    echo "Error connecting to the database !<br> " ;
    printf("%s", pg_errormessage( $dbconn ) );
    exit(); 
}

$href="http://xyala.cap.ed.ac.uk/nemdb3/cluster.php?cluster=";
$width1="width=\"15%\"";
$width2="width=\"60%\"";
$width3="width=\"5%\"";

// ############################################################
// #################### Get All Clusters ######################
// ############################################################

#get number of clusters
$sql_com_num="select distinct(clus_id) from cluster where clus_id~'$organism'";
$dbres_num = pg_exec($dbconn, $sql_com_num );
if ( ! $dbres_num ) {
    echo "Error : " + pg_errormessage( $dbconn );
    exit(); 
}
$rowmax_num=pg_NumRows($dbres_num);

$change=50;
$new_offset = $offset + $change;
$old_offset = $offset - $change;

$sql_com="select distinct clus_id,num_ests from cluster where clus_id~'$organism' order by num_ests desc limit $change offset $offset";

$dbres = pg_exec($dbconn, $sql_com );
if ( ! $dbres ) {
    echo "Error : " + pg_errormessage( $dbconn );
    exit(); 
}

//and interpret the results
$row=0;
$rowmax=pg_NumRows($dbres);

if ($offset != 0){
	$prev_link = "<a href=\"search_results.php?organism=$organism&offset=$old_offset\">previous $change</a>";
}else{
	$prev_link = "";
}
if ($new_offset < $rowmax_num){
	$next_link = "<a href=\"search_results.php?organism=$organism&offset=$new_offset\">next $change</a>";
}else{
	$next_link = "";
	$new_offset = $rowmax_num;
}

// and now print the results table

$sqlcom0="select species,clade from species where spec_id = '$organism'";
$dbres0 = pg_exec($dbconn, $sqlcom0 );
$do0 = pg_Fetch_Object($dbres0, 0);
$species=$do0->species;
$clade=$do0->clade;

print("<table class=\"tablephp1\" > ");
print("<tr><td colspan=7 align=center class=\"mainTitle\">There are $rowmax_num clusters for $species (clade $clade)</td></tr>\n");
print("<tr><td width=\"15%\">$prev_link</td><td align=center class=\"centermainTitle\">Currently viewing clusters $offset - $new_offset</td><td align=right width=\"15%\">$next_link</td></tr>");
print("</table>");
// output layout changed
print("<table class=\"tablephp1\" > ");
print("<TR class=\"mainBig\"><TD>Cluster ID</TD>");
print("<TD>ESTs</TD><TD><table width=\"100%\"><tr>");
print("<td $width1>Blast Database</TD>");
print("<TD $width1>ID</TD>");
print("<TD $width2>Top Blast Hit Description</TD>");
print("<TD $width3>Score</TD>");
print("<TD $width3>E-value</TD></TR></table></td></tr>\n");

while($row<$rowmax) {
 $s='';
 $do_clusters=pg_Fetch_Object($dbres, $row);
 $abundance=$do_clusters->num_ests;
 $clus=$do_clusters->clus_id;
 $s="<tr class=\"tablephp21\"><td valign=middle><a href=\"cluster.php?cluster=$clus\">$clus</a>&nbsp;&nbsp;</td>";
 #$s.="<td></td>";//$s. shortened

 
 $sql_com1="select description,score,prog,db,eval,id from blast where clus_id='$clus' order by score desc";
 $dbres1 = pg_exec($dbconn, $sql_com1 );
 if ( ! $dbres1 ) { 
  echo "Error : " + pg_errormessage( $dbconn ); 
  exit(); 
 }
 //layout changed
 $blastmax=pg_NumRows($dbres1);
 $blu="<td valign=middle>$abundance</td><td class=\"tablephp21\"><table width=\"100%\"><tr><td $width1>BlastX v Uniref100</td><td $width1>";
 $blw="</td></tr><td $width1>BlastX v wormpep</td><td $width1>";
 $bluflag=$blwflag=$blnflag=0;
 for($blast_ind=0;$blast_ind<$blastmax;$blast_ind++)  {
 	$do_blast = pg_Fetch_Object($dbres1, $blast_ind);
 	$prog=$do_blast->prog;
 	$db=$do_blast->db;
	$id=$do_blast->id;
 	$desc=$do_blast->description;
 	$score=$do_blast->score;
	$eval=$do_blast->eval;
 	if($score==1) { $score=''; }
 	if($prog=='blastx' && $db=='uniref100' && $bluflag<1 && $blastmax>0) { $blu.="$id</td><td $width2>$desc</td><td $width3>$score</td><td $width3>$eval"; $bluflag++; }
 	if($prog=='blastx' && $db=='wormpep' && $blwflag<1 && $blastmax>0) { $blw.="$id</td><td $width2>$desc</td><td $width3>$score</td><td $width3>$eval"; $blwflag++; }
	}
 //check to see if any clusters have no BLAST hits for either uniref or womrpep
 if ($bluflag==0){$blu.="N/A</td><td $width2>No BLAST Hits!</td><td $width3>N/A</td><td $width3>N/A";}
 if ($blwflag==0){$blw.="N/A</td><td $width2>No BLAST Hits!</td><td $width3>N/A</td><td $width3>N/A";} 
 $s.=$blu.$blw."</td></tr></table></td></tr>\n";
 #$s.="</table>";
 printf ("%s",$s);
 $row++;
 }
print("<table class=\"tablephp1\" > ");
print("<tr><td width=\"15%\">$prev_link</td><td align=center class=\"mainTitle\">Currently viewing clusters $offset - $new_offset</td><td align=right width=\"15%\">$next_link</td></tr>");
print("</table>"); 
printf("</table>");



//close the database
pg_close( $dbconn );

print "<!-- InstanceEndEditable -->\n";
include('/var/www/html/includes/nembase4_body_lower.ssi');
print "</body>\n";
print "<!-- InstanceEnd --></html>\n";

?>
