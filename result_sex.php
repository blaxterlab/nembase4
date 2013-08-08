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

<div class="mainTitle">Gender Specific Search Results</div>

<?php

##########
#ben 21/08/08
#added if ($orgs!=""){ to species list section to remove foreach errors
#ben 20/06/09
#added $check to check if at least one stage is selected as never completes without
##########

$genus=array("I","III","IV","V","Trichinellida","Ascarididomorpha","Spiruromorpha","Strongyloidoidea","Tylenchomorpha","Strongylomorpha","Meloidogyne");
$sp=0;
foreach ($genus as $gen) {
	if (sizeof($_POST[$gen])>0) {$sp=1; last;}
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

# coming from sexSpec.shtml
$href="/nembase4/cluster.php?cluster=";

#### a few checks 
$list[0] = "female";
$list[1] = "male";
$list[2] = "mixed";

$check = 0;
$sex = $_POST['sex'];
for ($i=0; $i<3; $i++) {
	if (preg_match('/^\d+$/',$sex[$i],$matches)) {	#there is no > or <
		$sex[$i]="=".$sex[$i];
		$check = $check + $matches[0];
	}if (preg_match('/\d+/',$sex[$i],$matches)) {
		$sex_query.=" and $list[$i]".$sex[$i];
		$check = $check + $matches[0];
		} # if there's a value
}	

#check if there is at least one stage selected
if ($check > 0){

$sex_query = preg_replace('/and /',' where ',$sex_query,1);	#replace the first 'and' with 'where'

	#### Get the species list ####
	$species="";
	foreach ($genus as $gen) {
		$orgs=$_POST[$gen];   #read the genus array 
		if ($orgs!=""){
			foreach ($orgs as $org) { #for each species
				if (preg_match('/(\w\w)P/',"$org")) {
					if ($_POST["varin"]==1) {$org=preg_replace('/(\w\w)P/', '\1C',$org);}#if it's blast change to clus id type
					$species.=" or spid='".$org."' ";
				}
			}
		}	
	}
	$species = preg_replace('/or/','and (',$species,1);
	$species.=") ";


#$sql_com = "select * from sex_count $sex_query $species_query group by clus_id,female,male,mixed,total_ests order by total_ests desc;";
$sql_com = "select sex_count.clus_id,total_ests,description,id,score,prog,db from sex_count left outer join blast_top on (sex_count.clus_id = blast_top.clus_id) $sex_query $species_query group by sex_count.clus_id,female,male,mixed,total_ests,description,id,score,prog,db order by total_ests desc;";            

#print "$sql_com<br>*$sex_query*<br>*$species_query*<br>";

#### do sql
$dbres = pg_exec($dbconn, $sql_com );
if ( ! $dbres ) {
  echo "Error : " + pg_errormessage( $dbconn );
  exit(); 
}

#### and interpret the results
$row=0;
$rowmax=pg_NumRows($dbres);
print "There are $rowmax clusters matching your search<br><br>";

## and now print the results table
if ($rowmax>100000) {
	print "<h2><center>This search has produced $rowmax results<br>Please go <a href = \"/nembase4/sexSpec.shtml\">back</a> and refine it either by selecting more expression levels and / or less species.</center></h2>";
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
		<input type="hidden" value="Sex_stage" name="filename">
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

?>
<!-- InstanceEndEditable -->
<? include ("nembase4_body_lower.ssi"); ?>
</body>
<!-- InstanceEnd -->
</html>

