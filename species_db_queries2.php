<?php
// this script collects species-specific information for display as checkboxes
// this is version 2 of the script
// original for nembase3 September 2007
// also used in NEMBASE4 September 2008
// original written by Ann Hedley, University of Edinburgh
// version 2 by Ben Elsworth and Mark Blaxter, University of Edinburgh

##################
#ben 21/07/08
#couldn't connect to search_results.shtml/php to view clusters from species_info.php
#search_results.shtml/php was not in nembase3, copied over from nemdb3/probably_junk!!!
#see line 97
#20/08/08
#removed 'view all clusters' button and replaced with text link
#################

//open the database
$PG_HOST="localhost";
$PG_PORT=5432;
$PG_DATABASE2="species_db4";
$PG_USER="webuser";
$PG_PASS="";
$dbconn=pg_connect( "dbname=$PG_DATABASE2 host=$PG_HOST port=$PG_PORT user=$PG_USER password=$PG_PASS" );
if ( ! $dbconn ) {
    echo "Error connecting to the database !<br> " ;
    printf("%s", pg_errormessage( $dbconn ) );
    exit(); 
}
#$ref=$_SERVER['DOCUMENT_NAME'];print "* $ref<br><br>\n";############
if ($_SERVER['DOCUMENT_NAME'] && preg_match("/index/",$_SERVER['DOCUMENT_NAME'])) {overall_stats($dbconn,"index");}
elseif ($_SERVER['DOCUMENT_NAME'] && preg_match("/overview/",$_SERVER['DOCUMENT_NAME'])) {overall_stats($dbconn,"overview");overview($dbconn);}
elseif ($_SERVER['PHP_SELF'] && preg_match("/libSpec.php/",$_SERVER['PHP_SELF'])) {dropdown_list($dbconn);}
elseif ($_SERVER['PHP_SELF'] && preg_match("/species_info.php/",$_SERVER['PHP_SELF'])) {details_for_sp($dbconn);}
else 	{clades($dbconn);}
	
###########################################################################################################
###############                               Sub routines                                  ############### 
###########################################################################################################
function	dropdown_list($dbconn) {
	$sqlcom="select distinct spec_id,name from info;";
	$dbres = pg_exec($dbconn, $sqlcom );
	if ( ! $dbres ) {echo "Error : " + pg_errormessage( $dbconn ); exit();} 
	$row=0;
	$rowmax=pg_NumRows($dbres);
	print "<form method=\"post\" action=\"libSpec.php\"><select  name=\"organism\" onchange=\"this.form.submit();\">\n<option value=\"select\">Select an organism...</option>\n"; 
	while ($row<$rowmax)  {
	    $do = pg_Fetch_Object($dbres, $row);
	    $name=$do->name;
	    print "<option value=\"$name\">$name</option>\n";
	    $row++;
	}
	print("</select></form>\n\n");
}
###########################################################################################################
# checkboxes without clades - nolonger used
/*function	checkbox_list($dbconn) {
	$sqlcom="select distinct spec_id,name from info;";
	$dbres = pg_exec($dbconn, $sqlcom );
	if ( ! $dbres ) {echo "Error : " + pg_errormessage( $dbconn ); exit();} 
	$row=0;
	$rowmax=pg_NumRows($dbres);
	print "<tr><td><div class=\"mainBig\">Species to search</div></td>";
	print "<td align=left><input type=checkbox onClick=\"if(this.checked)checkAll(); else clearAll()\" checked> select all</td></tr>";
	print "<tr height=20></tr>\n";
	print "<tr><td colspan=3>\n<table width=\"100%\"><tr>\n";
	while ($row<$rowmax)  {
	    $do = pg_Fetch_Object($dbres, $row);
	    $name=$do->name;
	    $spec_id[$row]=$do->spec_id;
	    $spec_id[$row]=preg_replace('/(\w\w)C/', '\1P',$spec_id[$row]);	#make them all prot then change back to clus for the blast search.
#	    print "<td class=\"mainSmall\"><input TYPE=\"checkbox\" NAME=\"org[]\" value=\"$spec_id[$row]\"><i>$name</i></input></td>\n";
	    print "<td><input TYPE=\"checkbox\" NAME=\"org[]\" value=\"$spec_id[$row]\" checked></input></td><td class=\"mainMed\"><i>$name</i></td>\n";
	    $row++;
	    if ($row%4==0) {print "</tr>\n<tr>";}
	}
	if ($row%4!=0) {print("</tr>\n");}
	print("</table></td></tr>\n");
}*/
###########################################################################################################
function	details_for_sp($dbconn) {
	// Get species id
	$sp=$_REQUEST["species"];
	$sqlcom="select name,short_des,long_des,lifecyc,taxonomy,links,clade,num_seq,num_clus,num_lib from info natural join stats where spec_id='$sp';";
	$dbres = pg_exec($dbconn, $sqlcom );
	if ( ! $dbres ) {echo "Error : " + pg_errormessage( $dbconn ); exit();} 
	$do = pg_Fetch_Object($dbres, 0);
	$name=$do->name;
	$short=$do->short_des;
	$long=$do->long_des;
	$lifecyc=$do->lifecyc;
	$taxonomy=$do->taxonomy;
	$links=$do->links;
	$clade=$do->clade;
	$num_seq=$do->num_seq;
	$num_clus=$do->num_clus;
	$num_lib=$do->num_lib;
	if (file_exists("/var/www/html/nembase4/species/$sp.jpg")) {$image=$sp.".jpg";}else{$image=$sp.".gif";}
	print "<table cellspacing=0 cellpadding=3 border=0><tr>\n";
	print "<td><IMG SRC=\"/nembase4/species/$image\" WIDTH=150 HEIGHT=150 ALIGN=bottom></td>\n";
	print "<td width=300><I>$name</I>";
	if ($short!="") {print "<br>($short)<br><br>\n";}else{print "<br><br><br>\n";}
	#print "<A HREF=\"/nembase4/search_results.php?organism=$sp\" target=\"_top\" onMouseOver=\"hiLite('img01','but2')\" onMouseOut=\"hiLite('img01','but1')\"><IMG ALT=\"View all clusters\" NAME=\"img01\" SRC=\"/nembase4/species/viewall_nembase4.jpg\" width=150 height=40 border=0></A></td></tr></table><hr>\n";
	print "<A HREF=\"/nembase4/search_results.php?organism=$sp\" target=\"_top\" >View All Clusters</A></td></tr></table><hr>\n";
	if ($long!="") {print "<table cellspacing=0 cellpadding=3 border=0><tr><td>Description</td></tr><tr><td><I>$name</I> $long</td></tr></table><hr>\n";}
	if ($lifecyc!="") {print "<table cellspacing=0 cellpadding=3 border=0><tr><td>Lifecycle</td></tr><tr><td>$lifecyc</td></tr></table><hr>\n";}
	print "<table cellspacing=0 cellpadding=3 border=0><tr><td width=450 colspan=2>Details</td></tr><tr><td>Total number of sequences</td><td>$num_seq</td></tr><tr><td>Number of NEMBASE clusters</td><td>$num_clus</td></tr><tr><td>Number of Libraries</td><td>$num_lib</td></tr>\n";
	print "<tr><td>Phylogeny</td><td><a href=\"/nembase4/clade$clade.shtml\" target=\"_top\">Clade $clade</a></td></tr>\n";
	print "<tr valign=top><td>Links</td><td><a href=\"/nembase4/index.shtml\" target=\"_top\">NEMBASE</A><br>$links</td></tr></table>\n";
	$sqlcom="select seq_cent,e_name,email from org where spec_id='$sp';";
	$dbres = pg_exec($dbconn, $sqlcom );
	if ( ! $dbres ) {echo "Error : " + pg_errormessage( $dbconn ); exit();} 
	$row=0;
	$rowmax=pg_NumRows($dbres);
	if ($rowmax>0) {print "<hr><table><tr valign=top><td colspan=2>Contacts</td><td></td></tr>\n";}
	while ($row<$rowmax)  {
	$do = pg_Fetch_Object($dbres, $row);
		$seq_cent=$do->seq_cent;
		$e_name=$do->e_name;
		$email=$do->email;
		if ($email!="") {$mail_link="<a href=\"mailto:$email\">$e_name</a>";}
		else {$mail_link="$e_name";}
		print "<tr><td>$seq_cent</td><td>$mail_link</td></tr>\n";
		$row++;
	}
	print "</table>\n";
}
###########################################################################################################
function	overall_stats($dbconn,$page) {
   $sqlcom="select count(distinct spec_id),sum(num_seq) as seqs,sum(num_clus) as clus,sum(num_lib) as libs,max(last_update),max(anno_update) as anno from stats;";
	$dbres = pg_exec($dbconn, $sqlcom );
	if ( ! $dbres ) {echo "Error : " + pg_errormessage( $dbconn );exit();}
	$do = pg_Fetch_Object($dbres, $row);
	$count=$do->count;
	$seqs=$do->seqs;$seqs=number_format($seqs);
	$clus=$do->clus;$clus=number_format($clus);
	$libs=$do->libs;$libs=number_format($libs);
	$update=$do->max;preg_match("/^(\d\d\d\d)-(\d\d)-\d\d/","$update", $date);
	$anno_update=$do->anno;preg_match("/^(\d\d\d\d)-(\d\d)-\d\d/","$anno_update", $anno_date);
	if ($page=="index") {
		print "<tr><td>Last annotation update</td><td>$anno_date[2] / $anno_date[1]</td></tr>\n"; 
		print "<tr><td>Last sequence update</td><td>$date[2] / $date[1]</td></tr>\n"; 
		print "<tr><td>Number of Species</td><td align=\"right\">$count</td></tr>\n"; 
		print "<tr><td>Number of Libraries &nbsp&nbsp</td><td align=\"right\">$libs</td></tr>\n"; 
		print "<tr><td>Number of Clusters</td><td align=\"right\">$clus</td></tr>\n"; 
		print "<tr><td>Number of ESTs</td><td align=\"right\">$seqs</td></tr>\n"; 
	}
	else {
		print "<tr><td  class=\"mainBig\" colspan=8><br>Last data update $date[2] / $date[1] to contain $count Species, $libs libraries, $clus clusters and $seqs ESTs<br><br></td></tr><tr>\n"; 
	}
}
###########################################################################################################
function	overview($dbconn) {
	$sqlcom="select info.spec_id,name,num_seq,num_clus,num_lib,last_update from info natural join stats;";
	$dbres = pg_exec($dbconn, $sqlcom );
	if ( ! $dbres ) {
		echo "Error : " + pg_errormessage( $dbconn );
		exit(); 
	}
	$row=0;
	$rowmax=pg_NumRows($dbres);
#	print "$rowmax rows<br>";
	while ($row<$rowmax)  {
	   $do = pg_Fetch_Object($dbres, $row);
		$spec_id=$do->spec_id;
		$species=$do->name;$species = preg_replace('/ /','<br>',$species,1);
		$seqs=$do->num_seq;$seqs=number_format($seqs);
		$clus=$do->num_clus;$clus=number_format($clus);
		$libs=$do->num_lib;$libs=number_format($libs);
		$update=$do->last_update;preg_match("/^(\d\d\d\d)-(\d\d)-\d\d/","$update", $date);
		if (file_exists("/var/www/html/nembase4/species/$spec_id.jpg")) {$image=$spec_id.".jpg";}else{$image=$spec_id.".gif";}
		print "<td class=\"mainSmall\"><i><a href=\"/nembase4/species_info.php?species=$spec_id\"><img src=\"/nembase4/species/$image\" width=100 height=100></a><br>$species</i><br>updated $date[2]/$date[1]<br>$libs libraries<br>$clus clusters<br>$seqs ESTs<br><a href=\"/downloads_area/databases/nembase4/$spec_id.fsa\">download</a></td>\n";
		$row++;
		if ($row%8==0 && $row<$rowmax) {print "</tr><tr>\n";}
	}
	print("</tr><tr>\n");
}
###########################################################################################################
#this was supposed to be generic so you could update the database the the code would update the website but
#trying to make it look like a tree has made it too specific and you'll probably have to fiddle with it if
#you add clades

#NOTES 
#The "align=center"'s shouldn't be but you need to work out how to center text in a table from the CSS
#in MacIE to take them out
function	clades($dbconn) {
	$sqlcomC="select distinct clade from info order by clade;";
	$sqlcomT="select distinct clade,taxonomy from info where taxonomy!=''  order by clade,taxonomy;";
	$sqlcomS="select distinct spec_id,name,taxonomy,clade from info order by clade,taxonomy;";
	print "\n<td>\n<!-- Table of species with select buttons derived from species_db_queries.php -->\n";
	print "<table class=\"tablephpSpSelect\" border="0" cellspacing="0">\n";
	print "<tr><td colspan=6 align=left><div class=\"mainBig\">Species to search</div></td></tr>\n";
	print "<tr><td colspan=4 align=center><input type=checkbox onClick=\"if(this.checked)checkAll(); else clearAll()\" checked><br>All</td></tr>\n";
	print "<tr><td colspan=4 height=1 >\n";
	print "    <table width=100% border="0" cellspacing="0">\n";
	print "    <tr><td width=6%></td><td class=\"All\" > </td><td width=8%>\n</td></tr>\n";
	print "    </table>\n</td></tr>\n";
	$dbres = pg_exec($dbconn, $sqlcomC );if ( ! $dbres ) {echo "Error : " + pg_errormessage( $dbconn ); exit();} 
	print "<tr>";
	$row=0;$rowmax=pg_NumRows($dbres);
	while ($row<$rowmax)  { #for each clade make a check-box
		$do = pg_Fetch_Object($dbres, $row);
		$clade=$do->clade;
		$col_span=1; if($clade=="III" || $clade=="IV") {$col_span=2;}
		$row_span=1; if($clade=="I") {$row_span=3;}
		if ($clade=="I") {
			print "<td rowspan=$row_span colspan=$col_span align=center>|<br><input type=checkbox NAME=\"All[]\" value=\"cladeI\" onClick=\"if(this.checked)checkCladeI(); else clearCladeI()\" checked><br><div class=\"styleI\">CladeI</div></td>\n";
			print "<td colspan=5 align=center>|<br><input type=checkbox NAME=\"All[]\" value=\"Rhabditina\" onClick=\"if(this.checked)checkRhabditina(); else clearRhabditina()\" checked><br><div class=\"styleRhabditina\">Rhabditina</div></td></tr><tr>\n";
			print "<td colspan=5 height=2 ><table width=100%><tr><td width=17%></td><td class=\"Rhabditina\" > </td><td width=8%></td></tr></table></td></tr><tr>\n"; #bar
		}
		else {print "<td rowspan=$row_span colspan=$col_span align=center>|<br><input type=checkbox NAME=\"Rhabditina[]\" value=\"$clade\" onClick=\"if(this.checked)check$clade(); else clear$clade()\" checked><br><div class=\"style$clade\">Clade$clade</div></td>\n";}
		$row++;
	}
	print "</tr><tr>\n";
	$row=0;
	while ($row<$rowmax)  { #for each clade draw a bar
		$do = pg_Fetch_Object($dbres, $row);
		$clade=$do->clade;
		$col_span=1; if($clade=="III" || $clade=="IV") {$col_span=2;}
		print "<td colspan=$col_span height=2 ><table width=100%><tr><td width=10%></td><td class=\"$clade\" > </td><td width=10%></td></tr></table></td>\n";
		$row++;
	}
	print "</tr>\n";
	$dbres = pg_exec($dbconn, $sqlcomT );if ( ! $dbres ) {echo "Error : " + pg_errormessage( $dbconn ); exit();} 
	print "<tr>";
	$row=0;$rowmax=pg_NumRows($dbres);
	$sub_taxo_row="<td  align=center>";
	while ($row<$rowmax)  {
	   $do = pg_Fetch_Object($dbres, $row);
	   $clade=$do->clade;
	   $taxonomy=$do->taxonomy;
		if (preg_match("/^(\w+)\.(\w+)/","$taxonomy", $match)) {
			$sub_taxo_row.="|<br><input type=checkbox NAME=\"$match[1][]\" value=\"$match[2]\" onClick=\"if(this.checked)check$match[2](); else clear$match[2]()\" checked><br><div class=\"style$match[2]\">$match[2]</div>\n";
		}
		else {
			print "<td  align=center>|<br><input type=checkbox NAME=\"$clade";
			print "[]\" value=\"$taxonomy\" onClick=\"if(this.checked)check$taxonomy(); else clear$taxonomy()\" checked><br><div class=\"style$taxonomy\">$taxonomy</div></td>\n";
			if ($row>0) {$sub_taxo_row.="</td><td >\n";}
		}
		$row++;
	}
	
	print "<tr>$sub_taxo_row</td></tr>\n";
	print "</table></td></tr>\n";
	$dbres = pg_exec($dbconn, $sqlcomS);if ( ! $dbres ) {echo "Error : " + pg_errormessage( $dbconn ); exit();} 
	$row=0;
	$rowmax=pg_NumRows($dbres);
	print "<tr height=20></tr>\n";
	#now print the species list
	print "<tr><td colspan=3>\n<table width=\"100%\"><tr>\n";
	while ($row<$rowmax)  {
	   $do = pg_Fetch_Object($dbres, $row);
	   $name=$do->name;
	   $spec_id[$row]=$do->spec_id;
#	   $spec_id[$row]=preg_replace('/(\w\w)C/', '\1P',$spec_id[$row]);	#make them all prot then change back to clus for the blast search.
	   $taxo[$row]=$do->taxonomy;
		if ($taxo[$row]=="") {$taxo[$row]=$do->clade;}
		if (preg_match("/^\w+\.(\w+)/","$taxo[$row]", $match)) {$taxo[$row]=$match[1];}
		print "<td><input TYPE=\"checkbox\" NAME=\"$taxo[$row][]\" value=\"$spec_id[$row]\" checked></input></td><td class=\"mainMed\"><div class=\"style$taxo[$row]\"><i>$name</i></div></td>\n";
		$row++;
		if ($row%4==0) {print "</tr>\n<tr>";}
	}
	if ($row%4!=0) {print("</tr>\n");}
	print("</table></td></tr>\n");
}
###########################################################################################################
?>
