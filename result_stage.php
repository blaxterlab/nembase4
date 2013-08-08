<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<!-- InstanceBegin template="/Templates/bang.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<!-- InstanceBeginEditable name="doctitle" -->
<title> www.nematodes.org - NEMBASE4 </title>
<!-- InstanceEndEditable -->
<? include ("nembase4_header.ssi"); ?>
</head>
<body>
<? include ("nembase4_body_upper.ssi"); ?>
<!-- InstanceBeginEditable name="BodyEditRegion" -->

<div class="mainTitle">Growth Stage Specific Search Results</div>

<?php

##########
#ben 01/03/11
#added some param input checks
#ben 07/01/11
#changed sql to one command instead of two, to use blast_top table and check proportions as well as actual numbers
#ben 21/08/08
#added if ($orgs!=""){ to species list section to remove foreach errors
#ben 20/06/09
#added $check to check if at least one stage is selected as never completes without
##########

#	include("../server_variables.php");

$genus=array("All","I","III","IV","V","Trichinellida","Ascarididomorpha","Spiruromorpha","Strongyloidoidea","Tylenchomorpha","Strongylomorpha","Meloidogyne");
$sp=0;
foreach ($genus as $gen) {
	if (sizeof($_POST[$gen])>0) {
#		print "$gen=> ";print_r($_POST[$gen]);print "<br>";  #comment this in to see which species you're getting
		$sp=1; 
		last;
	}
}
if ($sp==0) {print "<div class=\"mainBig\">You must select a species to search<br>Please use the back button on your browser to return to the annotation search page.</div>";}

else {
#define variables used with the database
#usually this goes in an include file
$PG_HOST="localhost";
$PG_PORT=5432;
$PG_DATABASE="nemdb4";
$PG_USER="webuser";
$PG_PASS="";

$dbconn=pg_connect( "dbname=$PG_DATABASE host=$PG_HOST port=$PG_PORT user=$PG_USER password=$PG_PASS" );
if ( ! $dbconn ) {
    echo "Error connecting to the database !<br> " ;
    printf("%s", pg_errormessage( $dbconn ) );
    exit(); 
}


############################################################
############### Retreive a set of clusters #################
############################################################

# coming from searchSpec.php : eggs L1 L2 L3 L4 adult mixed unknown 

#### $href="/nembase4/cluster.php?cluster="; ### change when moved
$href="http://xyala.cap.ed.ac.uk/nembase4/cluster.php?cluster=";
#### a few checks 

 
$list[0] = "eggs";
$list[1] = "L1";
$list[2] = "L2";
$list[3] = "L3";
$list[4] = "L4";
$list[5] = "adult";
$list[6] = "mixed";
$list[7] = "unknown";

####  arange the stage part of the query
#$stage = $_POST["stage"];
#for ($i=0; $i<7; $i++) {
#	if (preg_match('/^\d+$/',$stage[$i])) {	#there is no > or <
#		$stage[$i]="=".$stage[$i];
#	}
#	if (preg_match('/\d+/',$stage[$i])) {$stage_query.=" and $list[$i]".$stage[$i];} # if there's a valuez
#}

$check = 0;
$stage = $_POST["stage"];
$type = $_POST["type"];
$input_flag="ok";

for ($i=0; $i<8; $i++) {
	#if(ctype_digit($stage[$i])){
	#	print "$stage[$i] is a digit<br>";
	#}
	if (!preg_match('/^\d+$|^<\d+$|^>\d+$/',$stage[$i],$matches)){
		$input_flag.=$stage[$i];
	}
	if (preg_match('/^\d+$/',$stage[$i],$matches)) {	#there is no > or < so add an =
		$stage[$i]="=".$stage[$i];
		$check = $check + $matches[0];
	}
	if (preg_match('/\d+/',$stage[$i],$matches)) {
		if ($type == 0){
			$stage_query.=" and $list[$i]_p".$stage[$i]; # use the proportion columns
		}else{
			$stage_query.=" and $list[$i]".$stage[$i]; # use the normal columns
		}
		$check = $check + $matches[0];
	}
}

if ($input_flag != 'ok'){
	print "<h2><center>You have entered an incorrect search term: $input_flag<br>Please use only integers preceded optionally with < or >.<br>Please go <a href = \"/nembase4/stageSpec.shtml\">back</a> and try again.</center></h2>";
}else{	
#check if there is at least one stage selected
if ($check > 0){
	
$stage_query = preg_replace('/and /',' where ',$stage_query,1);	#replace the first 'and' with 'where'

	#### Get the species list ####
	$species="";
	foreach ($genus as $gen) {
		$orgs=$_POST[$gen];   #read the genus array 
		if ($orgs!=""){ 
			foreach ($orgs as $org) { #for each species
				if (preg_match('/(\w\w)C/',"$org")) {
					$species.=" or spid='".$org."' ";
				}
			}
		}
	}
	if (!$stage_query) {$species = preg_replace('/or/',' where (',$species,1);}
	else {$species = preg_replace('/or/','and (',$species,1);}
	$species.=") ";

#print "<br>*$stage_query*<br>*$species*<br>";
$sql_com = "select stage_count.clus_id,stage_count.total_ests,blast_top.db,blast_top.score,blast_top.description,blast_top.id,blast_top.prog,blast_top.db from stage_count left outer join blast_top  on (stage_count.clus_id = blast_top.clus_id) $stage_query $species  order by total_ests desc,score desc;";
#print "$sql_com <br>";

#### do sql
$dbres = pg_exec($dbconn, $sql_com );
if ( ! $dbres ) {
  echo "Error : " + pg_errormessage( $dbconn );
  exit(); 
}

#### and interpret the results
$row=0;
$rowmax=pg_NumRows($dbres);
print "There are $rowmax clusters matching your query<br><br>";

## and now print the results table
if ($rowmax>100000) {
	print "<h2><center>This search has produced $rowmax results<br>Please go <a href = \"/nembase4/stageSpec.shtml\">back</a> and refine it either by selecting more expression levels and / or less species.</center></h2>";
}elseif ($rowmax > 0){
	$table="<table class=\"tablephp1\">\n\t<TR>\n\t\t<TD WIDTH=10% >Cluster ID</TD>\n\t\t<TD WIDTH=5%>Abundance</TD>\n\t\t<TD WIDTH=60%>Descriptions and scores of matching query";
	$csv_hdr="Cluster ID,Abundance,BLAST DB, BLAST hit ID, Description, Score";
	while($row<$rowmax) {
		$do_clusters=pg_Fetch_Object($dbres, $row);
		$abundance=$do_clusters->total_ests;
		$clus=$do_clusters->clus_id;
		if ($clus != $old_clus){
			if ($row > 0){
				$table.="</table>";
			}
			$table.="</TD>\n\t</tr>\n\t<tr>\n\t\t<td class=\"tablephp21\"><a href=\"cluster.php?cluster=$clus\">$clus</a></td>\n\t\t<td class=\"tablephp21\">$abundance</td>\n\t\t<td>\n\t\t\t<table class=\"tablephp12\">";
		}
		$prog=$do_clusters->prog;
		$db=$do_clusters->db;
		$hitid=$do_clusters->id;
		$desc=$do_clusters->description;
		$score=$do_clusters->score;
		$desc=preg_replace('/,/', ';',$desc);
		if ($prog == NULL){
			$prog = "No BLAST hits";
		}
		if($prog=='blastx' && $db=='uniref100' && $clus != $old_uni) {
			$blx="<tr><td class=\"tablephp21\" valign=\"top\" width=\"10%\">BlastX v uniref</td><td class=\"tablephp21\" width=\"90%\">";
			$blx.="<b>$hitid</b> $desc $score</tr>";
			$csv_output .= $clus.", ".$abundance.", BlastX v Uniref, ".$hitid.", ".$desc.", ".$score."\n";			
			$table.=$blx."</td></tr>";
			$old_uni=$clus;

		}else if($prog=='blastx' && $db=='wormpep' && $clus != $old_worm) {
			$blxw="<tr><td class=\"tablephp21\"valign=\"top\" width=\"10%\">BlastX v wormpep</td><td class=\"tablephp21\" >";
			$blxw.="<b>$hitid</b> $desc $score</tr>";
			$csv_output .= $clus.", ".$abundance.", BlastX v wormpep, ".$hitid.", ".$desc.", ".$score."\n";						
			$table.=$blxw."</td></tr>";
			$old_worm=$clus;
		}else{
			$table.=" <td>No BLAST hits</td></tr>";
			$csv_output .= $clus.", ".$abundance.", No BLAST hits\n";			
		}
		$old_clus=$clus;
		$row++;
	}
	?>
	<center>
		<form name="export" action="csv_export.php" method="post">
		<input type="submit" value="Export table to CSV">
		<input type="hidden" value="<? echo $csv_hdr; ?>" name="csv_hdr">
		<input type="hidden" value="<? echo $csv_output; ?>" name="csv_output">
		<input type="hidden" value="Lifecycle_stage" name="filename">
		</form>
	</center>
	<?
	print($table."\n\t\t</table></table>"); #close the first table
}

pg_close( $dbconn );

}else{
	print "<div class=\"mainBig\">You must select at least one stage to search<br>Please use the back button on your browser to return to the annotation search page.</div>";
}#close stage check
}#close species check
}#close input params check
?>

<!-- InstanceEndEditable -->
<? include ("nembase4_body_lower.ssi"); ?>
</body>
<!-- InstanceEnd --></html>

