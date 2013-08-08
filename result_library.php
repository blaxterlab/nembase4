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

#######
#ben 21/07/08
#new lib_count table doesnt store libraries as lib_XXX just XXX, therefore line 56 has been changed.
#ben 31/07/08
#changed lib table library column names back to include lib_ at the start as psql calls didnt like it
#changed the actual table construction in PartiGene_db.pl
#updated blast display to match blastdb names and include id
#######

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

# coming from libSpec.php : 

$href="/nembase4/cluster.php?cluster="; ### change when moved
####$href="http://xyala.cap.ed.ac.uk/nembase4/cluster.php?cluster=";

print "<div class=\"mainTitle\">Library Specific Clusters</div><br>\n";
#### a few checks 

$libids = $_POST['lib_stuff'];
$libnums = $_POST['lib_numbers'];
$size = count($libids);

for ($i=0; $i<$size; $i++) {
	if (preg_match('/^\d+$/',$libnums[$i])) {	#there is no > or <
		$libnums[$i]="=".$libnums[$i];
	}
	if (preg_match('/\d+/',$libnums[$i])) {$library_query.=" and lib_$libids[$i]".$libnums[$i];} # if there's a value
}	
$library_query = preg_replace('/and /',' where ',$library_query,1);									#replace the first 'and' with 'where'
#print $library_query;

$sql_com = "select clus_id,total_ests from lib_count $library_query group by clus_id,total_ests order by total_ests desc;";
#print $sql_com;
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
if ($rowmax>0) {
	$table="<table class=\"tablephp1\">\n\t<TR>\n\t\t<TD WIDTH=10% >Cluster ID</TD>\n\t\t<TD WIDTH=5%>Abundance</TD>\n\t\t<TD WIDTH=60%>Descriptions and scores of matching query";
	$csv_hdr = "Cluster ID,Abundance,BLAST,DB,Descriptions,Score";
	while($row<$rowmax) {
	  $do_clusters=pg_Fetch_Object($dbres, $row);
	  $abundance=$do_clusters->total_ests;
	  $clus=$do_clusters->clus_id;
	  $table.="</TD>\n\t</tr>\n\t<tr>\n\t\t<td class=\"tablephp21\"><a href=\"cluster.php?cluster=$clus\">$clus</a></td>\n\t\t<td class=\"tablephp21\">$abundance</td>\n\t\t<td>\n\t\t\t<table class=\"tablephp12\">";
	  $sql_com1="select description,id,score,prog,db from blast where clus_id='$clus' order by db,score,description";
	  $dbres1 = pg_exec($dbconn, $sql_com1 );
	  if ( ! $dbres1 ) {
	     echo "Error : " + pg_errormessage( $dbconn );
	     exit(); 
	  }
	
	  $blastmax=pg_NumRows($dbres1);
	  $blx="<tr><td class=\"tablephp21\" valign=\"top\">BlastX v uniref100</td><td class=\"tablephp21\" width=\"100%\">";
	  $blxw="<tr><td class=\"tablephp21\"valign=\"top\">BlastX v wormpep</td><td class=\"tablephp21\">";
	  #$bld="<tr><td class=\"tablephp21\" valign=\"top\">BlastN v dbest</td><td class=\"tablephp21\">";
	  $blxflag=$blxwflag=$bldflag=0;
	  $blxdesc=$blxwdesc=$blddesc="";
	  for($blast_ind=0;$blast_ind<$blastmax;$blast_ind++)	 {
	    $do_blast = pg_Fetch_Object($dbres1, $blast_ind);
	    $prog=$do_blast->prog;
	    $db=$do_blast->db;
	    $desc=$do_blast->description;
	    $score=$do_blast->score;
	    $id=$do_blast->id;
	    $desc=preg_replace('/,/', ';',$desc);
	    if($score==1) {
	    	$score=''; 
	    }if($prog=='blastx' && $db=='uniref100' && $blxflag<2 && $desc!=$blxdesc){ 
	    	$csv_output.=$clus.", ".$abundance.", ".$prog.", ".$db.", ".$desc.", ".$score."\n";
	    	$blx.="<b>$hitid</b> $id $desc $score <br>"; $blxflag++;$blxdesc=$desc;
	    }else if($prog=='blastx' && $db=='wormpep' && $blxwflag<2 && $desc!=$blxwdesc) {
	    	$blxw.="<b>$hitid</b>$id $desc $score<br>"; $blxwflag++; $blxwdesc=$desc;
	    	$csv_output.=$clus.", ".$abundance.", ".$prog.", ".$db.", ".$desc.", ".$score."\n";
	    }
	    #else if($prog=='blastn' && $db=='est' && $bldflag<2 && $desc!=$blddesc)  {$bld.="<b>$hitid</b> $desc 	$score<br>"; $bldflag++; $blddesc=$desc;}
	  }
	  
	  $table.=$blx."</td></tr>".$blxw."</td></tr>".$bld."</td></tr></table>";
	  

	  $row++;
	}
		#set the csv output
	?>
	<center>
		<form name="export" action="csv_export.php" method="post">
		<input type="submit" value="Export table to CSV">
		<input type="hidden" value="<? echo $csv_hdr; ?>" name="csv_hdr">
		<input type="hidden" value="<? echo $csv_output; ?>" name="csv_output">
		<input type="hidden" value="Library" name="filename">
		</form>
	</center>
	<?
	print($table."\n\t\t</table>");
}

pg_close( $dbconn );

?>

<!-- InstanceEndEditable -->
<? include ("nembase4_body_lower.ssi"); ?>
</body>
<!-- InstanceEnd -->
</html>
