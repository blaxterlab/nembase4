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
$cluster=$_GET["cluster"];
$contig=$_GET["contig"];
$dir=$_GET["dir"];
$protseq=$_GET["protseq"];

print("<center>\n<table class=\"tablephp21\">\n");
print("\t<tr>\n");
print("\t\t<td align=\"center\">\n");
print("\t\t\t\n");
print("\t\t\tSix frame translations for cluster <a href=\"http://xyala.cap.ed.ac.uk/nembase4/cluster.php?cluster=$cluster\">$cluster</a><br><br>\n");
print("\t\t\t\n");
print("\t\t</td>\n\t</tr>\n<br>\n");
#print("<div class=\"mainSmall\">");

$PG_HOST="localhost";
$PG_PORT=5432;
$PG_DATABASE="nemdb";
$PG_USER="webuser";
$PG_PASS="";

#define $protein####
if (preg_match("/^(\w\w)/","$cluster", $match)) { $protein= "$match[0]" . "P" ;}
if (preg_match("/(\d\d\d\d\d)/","$cluster", $match)) { $protein = "$protein" . "$match[0]" . "_" . "$contig";}


#connect to the database####
$dbconn=pg_connect( "dbname=$PG_DATABASE host=$PG_HOST port=$PG_PORT user=$PG_USER password=$PG_PASS" );
if ( ! $dbconn ) {
	echo "Error connecting to the database !<br> " ;
	printf("%s", pg_errormessage( $dbconn ) );
	exit(); 
}

#get id for p4eprediction
$sqlp4e="select pept_ref from p4e_ind where pept_id~'$protein';";

#print("$sqlp4e<br>");

#run the database command####
$dbres = pg_exec($dbconn, $sqlp4e );
if ( ! $dbres )  {
	echo "Error : " + pg_errormessage( $dbconn );
	exit();
}

$data = pg_fetch_row($dbres);
$pept_ref = $data[0];
 

#define the database command####
$sqlcom="select * from p4e_hsp,p4e_ind,cluster where p4e_ind.pept_ref~'$pept_ref' and cluster.clus_id~'$cluster' and cluster.contig='$contig' order by p_start;";

#print ("$sqlcom");
#run the database command####
$dbres = pg_exec($dbconn, $sqlcom );
if ( ! $dbres )  {
	echo "Error : " + pg_errormessage( $dbconn );
	exit();
}

#process the results from the database command####
$prot_rows_max=pg_NumRows($dbres);
$prot_rows=0;

while($prot_rows<$prot_rows_max) {
	$do = pg_Fetch_Object($dbres, $prot_rows);	#define 'fetch result'
	$p_start=$do->p_start;								#fetch variables for one result
	$p_end=$do->p_end;
	$pseq=$do->$seq;

	if($p_start > $p_end) { $rstart=$p_end+1; $rend=$p_start+3; $rprotseq=$pseq; }
	else { $fstart=$p_start; $fend=$p_end+1; $fprotseq=$pseq; }
	$prot_rows++;
}

#print "pstart $p_start";
#print "pend $p_end";
#print "seq $pseq";


$dnaseq=$do->consensus; 
$outseq='';
$con='';
$revcon='';

include("ntaaseq.php");
$pro = preg_split('//', $protseq, -1, PREG_SPLIT_NO_EMPTY);
$pros[0]=''; $x=0;
for($i=1;$i<strlen($protseq)+1;$i++) {
	$pros[$x].=$pro[$i-1];
	if($i%100 == 0)	{ $x++; }
}

$dseq[0]="<font face=courier color=\"#000000\">\n\t\t\t";	#3' UTR	
$rdseq[0]="<font face=courier color=\"#000000\">\n\t\t\t";	#first row reverse nt seq
$pseq[0][0]="<font face=courier color=\"#000000\">\n\t\t\t";	#first row forward translations 
$pseq[3][0]="<font face=courier color=\"#000000\">\n\t\t\t";	#first row reverse translations

$oseq=preg_split('/U/', $outseq, -1, PREG_SPLIT_NO_EMPTY);
$flag=0;
$x=0;
for($i=1;$i<strlen($dnaseq)+1;$i++) {
	if($i == $fstart) {
		$dseq[$x].="<font face=courier font color=\"#FF0000\">";			#translated nt seq 3' part row	
		$flag=1;
	}
	if($i == $fend) {
		$dseq[$x].="<font face=courier font color=\"#000000\">";			#translated nt seq 5' part row
		$flag=0;
	}
	if($i == $rstart) {
		$rdseq[$x].="<font face=courier font color=\"#0000FF\">\n\t\t\t"; 	#??
		$rflag=1; 
	}
	if($i == $rend) {
		$rdseq[$x].="<font face=courier font color=\"#000000\">\n\t\t\t";	#??
		$rflag=0;
	}
	$dseq[$x].=$con[$i-1];
	$rdseq[$x].=$revcon[$i-1];
	$pseq[0][$x].=$oseq[0][$i-1]; 
	$pseq[1][$x].=$oseq[1][$i-1]; 
	$pseq[2][$x].=$oseq[2][$i-1]; 
	$pseq[3][$x].=$oseq[3][$i-1]; 
	$pseq[4][$x].=$oseq[4][$i-1]; 
	$pseq[5][$x].=$oseq[5][$i-1]; 
	if($i%100 == 0) {
		$x++;
		if($flag==1){$dseq[$x].="<font face=courier color=\"#FF0000\">"; }#translated nt seq full rows
		else {$dseq[$x].="<font face=courier color=\"#000000\">\n\t\t\t"; } #5' UTR whole rows
		if($rflag==1) {$rdseq[$x].="<font face=courier color=\"#0000FF\">\n\t\t\t";} #??
		else {$rdseq[$x].="<font face=courier color=\"#000000\">\n\t\t\t";} #reverse nt strand
		$pseq[0][$x]="<font face=courier color=\"#000000\">\n\t\t\t"; #forward translations
		$pseq[3][$x]="<font face=courier color=\"#000000\">\n\t\t\t"; #reverse translations
   }
}
print("\t<tr>\t\t<td>\n");
for($i=0;$i<$x+1;$i++) {
	$count=($i+1)*100;
	$pseq1=$pseq[0][$i];
	$pseq2=$pseq[1][$i];
	$pseq3=$pseq[2][$i];
	$pseq4=$pseq[3][$i];
	$pseq5=$pseq[4][$i];
	$pseq6=$pseq[5][$i];
	print("\t\t\t$pseq1<br>\n\t\t\t$pseq2<br>\n\t\t\t$pseq3<br>\n\t\t\t$dseq[$i] $count<br>\n");
	print("\t\t\t$rdseq[$i]<br>\n\t\t\t$pseq4<br>\n\t\t\t$pseq5<br>\n\t\t\t$pseq6<br><br>\n\n");
}
print("\t\t</td>\t</tr><table>\n");
?>
<!-- InstanceEndEditable -->
<? include ("nembase4_body_lower.ssi"); ?>
</body>
<!-- InstanceEnd -->
</html>

