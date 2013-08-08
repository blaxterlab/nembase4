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

<?php	
######################################
#ben 28/07/08
#changed spid to clus_id in line 176 as spid no longer exists
#05/08/08
#updated change above to include other options that use this page, eg. ec,gotcha,kegg,domain
#### Get the species list #### section sets species value dependent on value of $_POST["varin"]
#05/08/08
#changed EC number search parameter to include wild card on end and output EC number 
#else {$txt="and a8r_blastec.ec_id~'^".$_POST["ectxt"]."' ";}
#06/08/08
#changed whole of gotcha section and all links to it
#including be_more_specific() function as it used non-existant tables gotcha and go!!!
#also use e-value cutoff instead of confidence as no longer present
#also rewritten output layout
#21/08/08
#added if ($orgs!=""){ to species list section to remove foreach errors
#18/09/06
#$output = 0 as $_POST['out'] from annotaion.shtml no longer exists
######################################


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
print "<div class=\"mainTitle\">Annotation Search Results</div>";

$annotations=$_POST["anno"]; #read the annotations array
#$output=$_POST['out']; #get the output type 
$output = 0;
#print_r($orgs);#*****************************


############################################################
################ open the database #########################
############################################################
$dbconn=pg_connect( "dbname=nemdb4 host=localhost port=5432 user=webuser password=ben" );
if ( ! $dbconn ) {
    echo "Error connecting to the database !<br> " ;
    printf("%s", pg_errormessage( $dbconn ) );
    exit(); 
}

$id=time();
$href="http://xyala.cap.ed.ac.uk/nembase4/cluster.php?cluster=";

############################################################
############# set up the basic query #######################
############################################################

	$link_page="protein.php";$link_id="protein";	#set links to a protein page and change for blast searches
	if (!$_POST["varin"]){			#no search parameters selected just give all clusters and a blastx result
		$lead="select clus_id,description,score"; 
		$table=" from blast";
		$conditions.=" where prog='blastx' and (hit=1 or description='No Significant Hit') ";
		$order="order by clus_id,score";
		$link_page="cluster.php";$link_id="cluster";					#set links to a cluster page
	}
	else if ($_POST["varin"]==1) {		#blast search
		$lead="select blast.clus_id,blast.db,blast.id,blast.description,blast.eval,blast.score";
		$table=" from blast";
		$prog=""; $txt=""; $score="";
		#if (sizeof($_POST["prog"])==1) {$progs=$_POST["prog"]; $prog="and blast.prog='".$progs[0]."' ";}	#if one blast type selected
		if ($_POST["blstxt"]) {$txt="and blast.description~*'".$_POST["blstxt"]."' ";$targett=$_POST["blstxt"];}		#if search text
		if ($_POST["evaltxt"]) {			#if min E-val selected
			$eval=$_POST["evaltxt"];
			if (!preg_match("/^\d/","$eval")) {$eval="1".$eval;} #if e-# make 1e-#
			$score="and blast.eval<$eval ";	
		}
		if ($_POST["scoretxt"]) {			#if min score selected
			$scoretxt=$_POST["scoretxt"];
			$score="and blast.score>$scoretxt ";	
		}
		$conditions.=$txt.$score;
		$conditions = preg_replace('/and /',' where ',$conditions,1);
		if ($_POST["out"]==0) {
			if ($_POST["order"]==0) {		#order by abundance
				$lead.=",cluster.num_ests ";
				$table.=",cluster";
				$conditions.=" and blast.clus_id=cluster.clus_id and cluster.retired=0 ";
				if (!$_POST["blstxt"]&&!$POST["evaltxt"]&&!$POST["scoretxt"]){$conditions = preg_replace('/and /',' where ',$conditions,1);}  ###catches instance with no parameters set
				if ($_POST["hits"]==1) {$conditions.="and hit=1 ";	}	#top hit only
				$order="order by cluster.num_ests desc,blast.clus_id,blast.eval";
			}else{
				$order="order by blast.eval,blast.score desc";
				if ($_POST["hits"]==1) {$conditions.=" and hit=1 ";	}	#top hit
				if (!$_POST["blstxt"]&&!$POST["evaltxt"]&&!$POST["scoretxt"]){$conditions = preg_replace('/and /',' where ',$conditions,1);}  ###catches instance with no parameters set
			}
		}
		$link_page="cluster.php";$link_id="cluster";					#set links to a cluster page
	}

	else if ($_POST["varin"]==2) {		#gotcha search
		$lead="select pept_id,go_term,descr,bestev ";
		$table="from a8r_blastgo ";
		$txt=""; 
		$score="";
		if (!$_POST["term"]) {
			if ($_POST["gotxt"]) {							#if search text
				if ($_POST["go"]==0 && !preg_match('/^GO:\d{7}/', $_POST["gotxt"])) {		#if search description
					#check if there's more than 6 in the go table
					$sql_check="select descr,go_term from a8r_blastgo where descr~*'".$_POST["gotxt"]."';";
					#print "$sql_check<br>";
					$dbres = pg_exec($dbconn, $sql_check );
					if ( ! $dbres ) {echo "Error : " + pg_errormessage( $dbconn );exit();}
					$count=pg_NumRows($dbres);
					if ($count>6) {be_more_specific($dbres,$count,$genus);}
					else {$txt.="where descr~*'".$_POST["gotxt"]."' ";}
				}			
				else {$txt.="where go_term~*'".$_POST["gotxt"]."' ";}	#if search id
				$targett=$_POST["gotxt"];
			}
		}
		else {	#a go search with specific descriptions (therefore go_terms) selected
			$terms=$_POST['term'];
			sizeof($_POST["org"]);
			$txt.="where (";
			foreach ($terms as $term) {$txt.=" or go_term~*'$term' ";}
			$txt.=") ";
			$txt = preg_replace('/or /','',$txt,1);
		}
		if ($_POST["conf"]) {
			$scoretxt=$_POST["conf"];	#if min E-val selected
			if (!preg_match("/^\d/","$scoretxt")) {$scoretxt="1".$scoretxt;} #if e-# make 1e-#
			$score="and bestev<$scoretxt ";
			print "score = $score\n";
			}
		$conditions.=$txt.$score;		
		#$conditions = preg_replace('/and /','where ',$conditions,1);
		$order="order by bestev";
	}

	else if ($_POST["varin"]==3) {		#kegg search
		$lead="select pept_id,path,ko_id,descr,bestev ";
		$table="from a8r_blastkegg ";
		$txt="";
		if ($_POST["kegg"]==0) {$txt="and a8r_blastkegg.descr~*'".$_POST["keggtxt"]."' ";}					#if search description
		else if ($_POST["kegg"]==1) {$txt="and a8r_blastkegg.path='".$_POST["keggtxt"]."' ";}				#if search pathway
		else {$txt="and a8r_blastkegg.ko_id='".$_POST["keggtxt"]."' ";}											#if search id
		$targett=$_POST["keggtxt"];
		$conditions.=$txt;		
		$conditions = preg_replace('/and /','where ',$conditions,1);
		$order="order by bestev";
	}
		
	else if ($_POST["varin"]==4) {		#EC search
		$lead="select pept_id,ec_id,descr,bestev ";
		$table="from a8r_blastec ";
		$txt="";
		if ($_POST["ec"]==0) {$txt="and a8r_blastec.descr~'".$_POST["ectxt"]."' ";}			#if search description
		else {												#if ec description
			if (preg_match('/\d\.\d\.\d+\.\d+/',$_POST["ectxt"])){					#if whole ec number entered
				$txt="and a8r_blastec.ec_id='".$_POST["ectxt"]."'";}				#if incomplete use wildcard
			else{$txt="and a8r_blastec.ec_id~'^".$_POST["ectxt"]."' ";}
			}	
		$targett=$_POST["ectxt"];
		$conditions.=$txt;		
		$conditions = preg_replace('/and /','where ',$conditions,1);
		$order="order by ec_id,bestev";
	}
		
	else if ($_POST["varin"]==5) {		#domain search
		$lead="select interpro.pept_id,interpro_key.short_desc,interpro.score ";
		$table="from interpro,interpro_key ";
		$txt="";
		if ($_POST["ip"]==0) {$txt="and interpro_key.short_desc~*'".$_POST["iptxt"]."' ";}#if search description
		else {  #if search id
			$iptxt=$_POST["iptxt"];
			if (preg_match("/^IPR/","$iptxt")) {$txt="and interpro_key.ipr_id~*'$iptxt' ";}
			else {$txt="and interpro.dom_id='$iptxt' ";}
		}
		$targett=$_POST["iptxt"];
		$conditions.=$txt."and interpro.dom_id=interpro_key.dom_id ";		
		$conditions = preg_replace('/and /','where ',$conditions,1);
		$order="order by score";
	}

	#### Get the species list ####
	$species="";
	foreach ($genus as $gen) {#### Get the species list ####
		#print "gen = $gen"; 
		$orgs=$_POST[$gen];   #read the genus array 
		if ($orgs!=""){ 
			foreach ($orgs as $org) { #for each species
				#print " org = $org";
				if (preg_match('/(\w\w)C/',"$org")) { 				
					$orgsliced = substr($org, 0, 2);
				if ($_POST["varin"]!=1) { 
					#$species.=" or spid~'".$orgsliced."' ";
					$species.=" or pept_id ~'".$orgsliced."' ";
				}else{
					$species.=" or blast.clus_id ~'".$orgsliced."' ";
					}				
				}
			}
		}
	}
	$species = preg_replace('/or/','and (',$species,1);
	if (!$_POST["blstxt"]&&!$_POST["evaltxt"]&&!$_POST["scoretxt"]&&!$_POST["order"]==0 && !$_POST["hits"]==1){$species = preg_replace('/and/','where',$species,1);}  ###catches instance with no parameters set
	$species.=") ";
	if ($_POST["out"]==0) {	
############################################################
#########Table of seq ids output ###########################
############################################################
		$sql_com=$lead.$table.$conditions.$species.$order;
		#print "$sql_com<br><br>"; ##**********************
#		print "lead $lead<br>table $table<br>cond $conditions<br>species $species<br>order $order<br>";#******************
#		exit();
		####execute the query
		$dbres = pg_exec($dbconn, $sql_com );
		if ( ! $dbres ) {
			echo "Error : " + pg_errormessage( $dbconn );
			exit(); 
		}
	
	#### and interpret the results
	$row=0;
	$rowmax=pg_NumRows($dbres);
	if ($rowmax==0) {print "<div class=\"mainMed$do_clusters[3]\" align=center>There are no clusters matching your search</div><br>\n";}
	else {
	#### and now print the results table
	if ($_POST["varin"] && $_POST["varin"]==1 && $_POST["order"]==0) {
		$table="<table class=\"tablephp1\"><TR><TD>Cluster ID</TD><TD>Blast DB</TD><TD>Abundance</TD><TD>Hit ID</TD><TD>Description</TD><TD>E-value</TD><TD>Score";
		$csv_hdr = "Cluster ID,Blast DB,Abundance,Hit ID,Description,E-value,Score";
	}if ($_POST["varin"] && $_POST["varin"]==1 && $_POST["order"]==1) {
		$table="<table class=\"tablephp1\"><TR><TD>Cluster ID</TD><TD>Blast DB</TD><TD>Hit ID</TD><TD>Description</TD><TD>E-value</TD><TD>Score";
		$csv_hdr="Cluster ID,Blast DB,Hit ID,Description,E-value,Score";
	}if ($_POST["varin"] && $_POST["varin"]==2) {
		$table="<table class=\"tablephp1\"><TR><TD>Pept ID</TD><TD>GO Term</TD><TD>Description</TD><TD>E-value";
		$csv_hdr="Pept ID,GO Term,Description,E-value";
	}if ($_POST["varin"] && $_POST["varin"]==3) {
		$table="<table class=\"tablephp1\"><TR><TD>Pept ID</TD><TD>Path No.</TD><TD>KEGG No.</TD><TD>Description</TD><TD>E-value";
		$csv_hdr="Pept ID,Path No.,KEGG No.,Description,E-value";
	}if ($_POST["varin"] && $_POST["varin"]==4) {
		$table="<table class=\"tablephp1\"><TR><TD>Pept ID</TD><TD>EC Number</TD><TD>Description</TD><TD>E-value";
		$csv_hdr="Pept ID,EC Number,Description,E-value";
	}if ($_POST["varin"] && $_POST["varin"]==5) {
		$table="<table class=\"tablephp1\"><TR><TD>Pept ID</TD><TD>Description</TD><TD>Score";
		$csv_hdr="Pept ID,Description,Score";
	}
	#else {$table="<table class=\"tablephp1\"><TR><TD>Cluster ID</TD><TD>Descriptions</TD><TD>Scores matching query";}

	$count=1;
	while($row<$rowmax) {
		$do_clusters=pg_fetch_row($dbres, $row);
		$clus=$do_clusters[0];
		if ($targett!="") {$do_clusters= preg_replace("/($targett)/i","<b>$1</b>", $do_clusters);}
				
		if ($row==0) {
			$prev_clus=$clus;
			$prev_info1="";$prev_info2="";$prev_info3="";$prev_info4="";$prev_info5="";
			
		}

		if ($prev_clus!=$clus) {
			if ($_POST["varin"]==1 && $_POST["order"]==0) {
				$table.="</td></tr>\n<tr><td class=\"tablephp21\"><a href=\"$link_page?$link_id=$prev_clus\">$prev_clus</a></td><td class=\"tablephp21\">$info1</td><td class=\"tablephp21\">$info6</td><td class=\"tablephp21\">$info2</td><td class=\"tablephp21\">$info3</td><td class=\"tablephp21\">$info4</td><td class=\"tablephp21\">$info5";
				$csv_output.=$prev_clus.", ".$info1_csv.", ".$info6_csv.", ".$info2_csv.", ".$info3_csv.", ".$info4_csv.", ".$info5_csv."\n";
			}if ($_POST["varin"]==1 && $_POST["order"]==1) {
				$table.="</td></tr>\n<tr><td class=\"tablephp21\"><a href=\"$link_page?$link_id=$prev_clus\">$prev_clus</a></td><td class=\"tablephp21\">$info1</td><td class=\"tablephp21\">$info2</td><td class=\"tablephp21\">$info3</td><td class=\"tablephp21\">$info4</td><td class=\"tablephp21\">$info5";
				$csv_output.=$prev_clus.", ".$info1_csv.", ".$info2_csv.", ".$info3_csv.", ".$info4_csv.", ".$info5_csv."\n";
			}if ($_POST["varin"]==2) {
				$table.="</td></tr>\n<tr><td class=\"tablephp21\"><a href=\"$link_page?$link_id=$prev_clus\">$prev_clus</a></td><td class=\"tablephp21\">$info1</td><td class=\"tablephp21\">$info2</td><td class=\"tablephp21\">$info3";
				$csv_output.=$prev_clus.", ".$info1_csv.", ".$info2_csv.", ".$info3_csv."\n";
			}if ($_POST["varin"]==3) {
				$table.="</td></tr>\n<tr><td class=\"tablephp21\"><a href=\"$link_page?$link_id=$prev_clus\">$prev_clus</a></td><td class=\"tablephp21\">$info1</td><td class=\"tablephp21\">$info2</td><td class=\"tablephp21\">$info3</td><td class=\"tablephp21\">$info4";
				$csv_output.=$prev_clus.", ".$info1_csv.", ".$info2_csv.", ".$info3_csv.", ".$info4_csv."\n";
			}
			if ($_POST["varin"]==4) {
				$table.="</td></tr>\n<tr><td class=\"tablephp21\"><a href=\"$link_page?$link_id=$prev_clus\">$prev_clus</a></td><td class=\"tablephp21\">$info1</td><td class=\"tablephp21\">$info2</td><td class=\"tablephp21\">$info3";
				$csv_output.=$prev_clus.", ".$info1_csv.", ".$info2_csv.", ".$info3_csv."\n";
			}
			if ($_POST["varin"]==5) {
				$table.="</td></tr>\n<tr><td class=\"tablephp21\"><a href=\"$link_page?$link_id=$prev_clus\">$prev_clus</a></td><td class=\"tablephp21\">$info1</td><td class=\"tablephp21\">$info2</td><td class=\"tablephp21\">$info3</td><td class=\"tablephp21\">$info4";
				$csv_output.=$prev_clus.", ".$info1_csv.", ".$info2_csv.", ".$info3_csv.", ".$info4_csv."\n";
			}
			$info1=""; $info2=""; $info3=""; $info4=""; $info5=""; $info6="";
			$info1_csv=""; $info2_csv=""; $info3_csv=""; $info4_csv=""; $info5_csv=""; $info6_csv="";								
			$count++;
		}
		
		if ($_POST["varin"]==1 && $do_clusters[3]!=$prev_info3) {
			#make the csv data separately as it doesn't need or like multiple hits
			$info1_csv=$do_clusters[1]." ";$info2_csv=$do_clusters[2]." ";$info3_csv=$do_clusters[3]." ";$info4_csv=$do_clusters[4]." ";$info5_csv=$do_clusters[5]." ";$info6_csv=$do_clusters[6]." ";
			if(!($prev_info3!="" && $info3=="No Significant Hit")) {	#only different descs
				$info1.=$do_clusters[1]." ";$info2.=$do_clusters[2]." ";$info3.=$do_clusters[3]." ";$info4.=$do_clusters[4]." ";$info5.=$do_clusters[5]." ";$info6=$do_clusters[6]." ";
				$prev_info3= $do_clusters[3];
				$info1.="<br>"; 
				$info2.="<br>";
				$info3.="<br>";
				$info4.="<br>";
				$info5.="<br>";
			}
		}else{
			$info1=$do_clusters[1]." ";$info2=$do_clusters[2]." ";$info3=$do_clusters[3]." ";$info4=$do_clusters[4]." ";$info5=$do_clusters[5]." "; $info6=$do_clusters[6]." ";
			$info1_csv=$do_clusters[1]." ";$info2_csv=$do_clusters[2]." ";$info3_csv=$do_clusters[3]." ";$info4_csv=$do_clusters[4]." ";$info5_csv=$do_clusters[5]." ";$info6_csv=$do_clusters[6]." ";
			}
		$info3_csv=preg_replace('/,/', ';',$info3_csv);
		$row++;
		$prev_clus=$clus;
	}
	#run once more to catch last entry
	#if ($targett!="") {$do_clusters= preg_replace("/($targett)/i","<b>$1</b>", $do_clusters);}
			if ($_POST["varin"]==1 && $_POST["order"]==0) {
				$table.="</td></tr>\n<tr><td class=\"tablephp21\"><a href=\"$link_page?$link_id=$prev_clus\">$prev_clus</a></td><td class=\"tablephp21\">$info1</td><td class=\"tablephp21\">$info6</td><td class=\"tablephp21\">$info2</td><td class=\"tablephp21\">$info3</td><td class=\"tablephp21\">$info4</td><td class=\"tablephp21\">$info5";
				$csv_output.=$prev_clus.", ".$info1_csv.", ".$info6_csv.", ".$info2_csv.", ".$info3_csv.", ".$info4_csv.", ".$info5_csv."\n";
			}if ($_POST["varin"]==1 && $_POST["order"]==1) {
				$table.="</td></tr>\n<tr><td class=\"tablephp21\"><a href=\"$link_page?$link_id=$prev_clus\">$prev_clus</a></td><td class=\"tablephp21\">$info1</td><td class=\"tablephp21\">$info2</td><td class=\"tablephp21\">$info3</td><td class=\"tablephp21\">$info4</td><td class=\"tablephp21\">$info5";
				$csv_output.=$prev_clus.", ".$info1_csv.", ".$info2_csv.", ".$info3_csv.", ".$info4_csv.", ".$info5_csv."\n";
			}if ($_POST["varin"]==2) {
				$table.="</td></tr>\n<tr><td class=\"tablephp21\"><a href=\"$link_page?$link_id=$prev_clus\">$prev_clus</a></td><td class=\"tablephp21\">$info1</td><td class=\"tablephp21\">$info2</td><td class=\"tablephp21\">$info3";
				$csv_output.=$prev_clus.", ".$info1_csv.", ".$info2_csv.", ".$info3_csv."\n";
			}if ($_POST["varin"]==3) {
				$table.="</td></tr>\n<tr><td class=\"tablephp21\"><a href=\"$link_page?$link_id=$prev_clus\">$prev_clus</a></td><td class=\"tablephp21\">$info1</td><td class=\"tablephp21\">$info2</td><td class=\"tablephp21\">$info3</td><td class=\"tablephp21\">$info4";
				$csv_output.=$prev_clus.", ".$info1_csv.", ".$info2_csv.", ".$info3_csv.", ".$info4_csv."\n";
			}
			if ($_POST["varin"]==4) {
				$table.="</td></tr>\n<tr><td class=\"tablephp21\"><a href=\"$link_page?$link_id=$prev_clus\">$prev_clus</a></td><td class=\"tablephp21\">$info1</td><td class=\"tablephp21\">$info2</td><td class=\"tablephp21\">$info3";
				$csv_output.=$prev_clus.", ".$info1_csv.", ".$info2_csv.", ".$info3_csv."\n";
			}
			if ($_POST["varin"]==5) {
				$table.="</td></tr>\n<tr><td class=\"tablephp21\"><a href=\"$link_page?$link_id=$prev_clus\">$prev_clus</a></td><td class=\"tablephp21\">$info1</td><td class=\"tablephp21\">$info2</td><td class=\"tablephp21\">$info3</td><td class=\"tablephp21\">$info4";
				$csv_output.=$prev_clus.", ".$info1_csv.", ".$info2_csv.", ".$info3_csv.", ".$info4_csv."\n";
			}	
			print "<div class=\"mainMed\" align=center>There are $count clusters matching your search</div><br>";
	$csv_output=preg_replace('/<br>/', '',$csv_output);		
	$csv_output=preg_replace('/<b>/', '',$csv_output);
	$csv_output=preg_replace('/<\/b>/', '',$csv_output);	
	
	#set the csv output
	?>
	<center>
		<form name="export" action="csv_export.php" method="post">
		<input type="submit" value="Export table to CSV">
		<input type="hidden" value="<? echo $csv_hdr; ?>" name="csv_hdr">
		<input type="hidden" value="<? echo $csv_output; ?>" name="csv_output">
		<input type="hidden" value="<? echo $_POST["varin"]; ?>" name="filename">
		</form>
	</center>
	<?
	print "$table";
	}
}


############################################################
############### Produce Simitri Output #####################
############################################################
else {
	$lead=preg_replace('/,.*/', ' ',$lead);					#you only want the id selected
	$sql_com=$lead.$table.$conditions.$species.$order;
#	print "$lead<br>$table<br>$conditions<br>$species<br>$order<br>";####
	if ($_POST["datatype"]==0) {$DB1=$_POST["DB1"]; $DB2=$_POST["DB2"]; $DB3=$_POST["DB3"];}
	else {$DB1=$_POST["EX1"]; $DB2=$_POST["EX2"]; $DB3=$_POST["EX3"];}
	$sqlcom1="select distinct clus_id,".$DB1.",".$DB2.",".$DB3." from sim_blast where clus_id in (".$sql_com.");";
#	print "$sqlcom1<br><br>";
#	print "Selected databases were ".$_POST["DB1"].", ".$_POST["DB2"]." and ".$_POST["DB3"]."<br><br>";####
#exit();

  $mapstr="<MAP NAME=map1>";
 # print("$sqlcom<br>\n");
  $venn = pg_exec($dbconn, $sqlcom1);
  if ( ! $venn ) {
     echo "Error : " + pg_errormessage( $dbconn );
     exit(); 
  }
  $rowmax=pg_NumRows($venn);
  print "\n<table class=\"tablephp1\"><tr><td class=\"tablephp21\" align=\"center\" colspan=4><br>There are $rowmax clusters matching your search<br><br>";
  $unqst=$elest=$nemst=$restst='';
  $rest_tot=$nema_tot=$elegans_tot=$unq_tot='0';
  $fp = fopen ("/tmp/blast/$id.venn", "w");
  for($i=0;$i<$rowmax;$i++) {
    $do_scores=pg_Fetch_Object($venn, $i);
# $venn_clus=$do_scores->venn_id;
    $venn_clus=$do_scores->clus_id;
    $a=$do_scores->$DB1;
    $b=$do_scores->$DB2;
    $c=$do_scores->$DB3;

    $top=max($a,$b,$c);
    if($a+$b==2 && $c>1) { 
      $restst.="<a HREF=\"cluster.php?cluster=$venn_clus\">$venn_clus</a>"; 
      $restst.="......$top\n<br>";
      $rest_tot++;
    }
    if($a+$c==2 && $b>1)  {
      $nemst.="<a HREF=\"cluster.php?cluster=$venn_clus\">$venn_clus</a>";
      $nemst.="......$top\n<br>";
      $nema_tot++;
    }
    if($c+$b==2 && $a>1)  {
      $elest.="<a HREF=\"cluster.php?cluster=$venn_clus\">$venn_clus</a>";
      $elest.="......$top\n<br>";
      $elegans_tot++;
    }
    if($a+$b+$c==3)  {
      $unqst.="<a HREF=\"cluster.php?cluster=$venn_clus\">$venn_clus</a>\n<br>";
      $unq_tot++;
    }

    $x=250;
    $y=273;
    $PI=pi();
    if($a==1) { $a=0; }
    if($b==1) { $b=0; }
    if($c==1) { $c=0; } 
    if($a+$b!=0 && $a+$c!=0 && $b+$c!=0)  {
      $A=174;
      $A/=($a+$b+$c);
      $y-=($A*$a);
      $y+=(cos($PI/3))*($A*$b);
      $x-=(sin($PI/3))*($A*$b);
      $y+=(cos($PI/3))*($A*$c);
      $x+=(sin($PI/3))*($A*$c);
      $xl=(int)$x-3;$xh=(int)$x+3;$yl=(int)$y-3;$yh=(int)$y+3;
      $mapstr.="<AREA SHAPE=rect COORDS=\"$xl,$yl,$xh,$yh\" HREF=\"cluster.php?cluster=$venn_clus\">\n";
      $colour=4;
      if($top < 80) { $colour=3; }
      if($top < 40) { $colour=2; }
      if($top < 20) { $colour=1; }
      if($top < 10) { $colour=0; }
##  $top/=;
      $top=(int)$top;
      fwrite($fp,"$venn_clus $xl $yl $top\n");
    }
  }
  fclose($fp);

  $sqlcom="select dbname from sim_dblist where dbid='$DB1';";
  $getdbname = pg_exec($dbconn, $sqlcom);
  $do_scores=pg_Fetch_Object($getdbname, 0);
  $db1=$do_scores->dbname;
  $sqlcom="select dbname from sim_dblist where dbid='$DB2';";
  $getdbname = pg_exec($dbconn, $sqlcom);
  $do_scores=pg_Fetch_Object($getdbname, 0);
  $db2=$do_scores->dbname;
  $sqlcom="select dbname from sim_dblist where dbid='$DB3';";
  $getdbname = pg_exec($dbconn, $sqlcom);
  $do_scores=pg_Fetch_Object($getdbname, 0);
  $db3=$do_scores->dbname;
#$appstr="<APPLET codebase=\"/java/phyloview\" code=\"phyloview.class\" width=620";

#print ("now here: $id.venn $db1 $db2 $db3");####


  $appstr="\n<APPLET codebase=\"http://xyala.cap.ed.ac.uk/java/simitri_nembase/\" code=\"simitri.class\" width=620";
  $appstr.=" \nheight=350><param name=\"db1\" value=\"$db1\"><param name=\"db2\"";
  $appstr.=" \nvalue=\"$db2\"><param name=\"db3\" value=\"$db3\">";
  $appstr.=" \n<param name=\"file\" value=\"$id.venn\"></APPLET>"; 

#$appstr.=" \n<param name=\"clus_link\" value=\"$href\"></APPLET>"; what's that for?

  $brwserflg=get_browser_info();

## Must replace spaces with %20's for $db1, $db2, $db3 etc

  $mapstr.="</MAP><center>\n<IMG ismap USEMAP=\"#map1\"";
  $mapstr.=" WIDTH=500 HEIGHT=450 SRC=\"pg_venn.php?id=$id&db1=$db1&db2=$db2&db3=$db3\"></img>";

  if($brwserflg==1) { print("$appstr"); }
  else { print("$mapstr"); }

  print("<br>For an explanation of the phylogenetic profile click <a href=\"phylograph.html\">here</a><br><br><br>");
#  print("<table class=\"tablephp2\><th colspan=4>");
  print("<tr><td width=100 class=\"tablephp21\">Unique Clusters ($unq_tot)</td>\n");
  print("<td width=150 class=\"tablephp21\">Clusters hitting<br>$db1 only ($elegans_tot)</td>\n");
  print("<td width=150 class=\"tablephp21\">Clusters hitting<br>$db2 only ($nema_tot)</td>\n");
  print("<td width=150 class=\"tablephp21\">Clusters hitting<br>$db3 only ($rest_tot)</td></tr>\n");
  print("<tr valign=top><td class=\"tablephp21\">$unqst</td><td class=\"tablephp21\">$elest</td><td class=\"tablephp21\">$nemst</td><td class=\"tablephp21\">$restst</td>");
  print("</tr></table>");
}
pg_close( $dbconn );
}

############################################################
############# Get Info on the users Browser ################
############################################################
function get_browser_info() {
## GET BROWSER VERSION (NS/IE Check)
  $isiepos = strpos(getenv('HTTP_USER_AGENT'),"MSIE");
  $isie = ( $isiepos>0 ? substr(getenv('HTTP_USER_AGENT'),$isiepos+5,3) : 0 );
  list($isnsver,$d) = explode(" ",getenv('HTTP_USER_AGENT'));
  $agent=getenv('HTTP_USER_AGENT');
  $isnsver = ( substr($isnsver,0,8)=="Mozilla/" ? substr( $isnsver,8 ) : 0 );
  $isns = ( $isnsver>4.0 ? $isnsver : 0 );
  $brwserflg=0;
  if( $isie>=5 || $isns>=5 ) { $brwserflg=1; }
  return $brwserflg;
}


############################################################
################# GO be more specific  #####################
############################################################
function be_more_specific($dbres,$count,$genus)  {
	print "<div class=\"mainMed\">$count is too many GO terms to search efficently.  Try to be more specific by selecting only the one(s) of interest.</div><br><form id=\"varform\" method=\"post\" action=\"annotation_search.php\">\n";
	print "<input TYPE=\"hidden\" NAME=\"varin\" value=\"".$_REQUEST["varin"]."\"></input>\n";
	print "<input TYPE=\"hidden\" NAME=\"conf\" value=\"".$_REQUEST["conf"]."\"></input>\n";
	foreach ($genus as $gen) {
		$orgs=$_POST[$gen];   #read the genus array 
		foreach ($orgs as $org) { #for each species
			print "<input TYPE=\"hidden\" NAME=\"$gen"."[]\" value=\"$org\" checked></input>\n";
		}
	}
	print "<input TYPE=\"hidden\" NAME=\"out\" value=\"".$_REQUEST["out"]."\"></input>\n";
	print "<input TYPE=\"hidden\" NAME=\"order\" value=\"".$_REQUEST["order"]."\"></input>\n";
	$row=0;	
	$rowmax=pg_NumRows($dbres);
	print "<table class=\"tablephp2\"><tr><td><div class=\"mainBig\">Descriptions to search for</div></td>";
	print "<td><input TYPE=\"submit\" VALUE=\"Search\"></td></tr>";
	print "<tr><td colspan=2>\n<table width=\"100%\"><tr>\n";
	while ($row<$rowmax)  {
	    $do = pg_Fetch_Object($dbres, $row);
	    $desc=$do->descr;
	    $term_id[$row]=$do->go_term;
	    print "<tr><td><input TYPE=\"checkbox\" NAME=\"term[]\" value=\"$term_id[$row]\"></input></td><td class=\"mainMed\">$desc</td></tr>\n";
		$row++;
	}
	print("</table></td></tr></table></form>\n");
die;
}

?>

<!-- InstanceEndEditable -->
<? include ("nembase4_body_lower.ssi"); ?>
</body>
<!-- InstanceEnd --></html>
