<?php
//this script builds the protein description page
//nembase3 September 2007
//Ann Hedley, University of Edinburgh

#############
#ben 13_11_10
#if proteins are too short they are not included so have added code to catch for this in tribes section
#ben 26_10_10
#genome proteins now point to wormbase and not to cluster.php
#ben 19_08_08
#removed reference to psort (add in again when completed)
#removed reference to gotcha_slim table and replace with a8r_blastgo
#21/08/08
#hashed out psort, domain stuff, reciprocal blasts and tribe stuff 
#03/03/09
#hashed out or removed p4e protein info associated with p4e_hsp and p4e_loc tables
#as the info was not provided with latest data from James
#23/03/09
#added reciprocal section again
#############

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
$protein = $_GET['protein']; 

#define database connection
$PG_HOST="localhost";
$PG_PORT=5432;
$PG_DATABASE="nemdb4";
$PG_DATABASE2="species_db4";
$PG_USER="webuser";
$PG_PASS="";


#work out species_id, cluster, contig
if($protein)  {
  $protein = strtoupper ($protein);
  if (preg_match("/^(\w\w)/","$protein", $match)) { $species_id= "$match[0]" . "C" ;}
  if (preg_match("/(\d\d\d\d\d)/","$protein", $match)) { $cluster = "$species_id" . "$match[0]";}
  $contig=1;  
  if (preg_match("/_(\d+)$/","$protein", $match)) { 
    $contig =  "$match[0]";
    $contig = preg_replace("/_/", "", $contig);
  }  

####connect to species_db & get info
  $dbconn2=pg_connect( "dbname=$PG_DATABASE2 host=$PG_HOST port=$PG_PORT user=$PG_USER password=$PG_PASS" );
  if ( ! $dbconn2 ) {
    echo "Error connecting to the database !<br> " ;
    printf("%s", pg_errormessage( $dbconn2 ) );
    exit(); 
  }
	$info_sql= "select name,short_des from info where spec_id='$species_id';";

#  $info_sql= "select * from info where spec_id='$species_id';";
  $info_query = pg_exec($dbconn2, $info_sql);
  $do_info = pg_Fetch_Object($info_query, 0);
  $spec_name=$do_info->name;
  $short_des=$do_info->short_des;
  if ($short_des == NULL) {$short_des = "description not available"; }

/*  $stats_sql= "select * from stats where spec_id='$species_id';";
  $stats_query=pg_exec($dbconn2, $stats_sql);
  $do_stats= pg_Fetch_Object($stats_query, 0);
  $nr_seq = $do_stats->num_seq;
  $nr_clus = $do_stats->num_clus;
  $nr_lib = $do_stats->num_lib;
  $update = $do_stats->last_update;
  $org_dir = $do_stats->directory;
  $file = $do_stats->file;
  
  $org_sql= "select seq_cent,email,e_name from org where spec_id='$species_id'";
  $org = pg_query($dbconn2, "$org_sql");

  	$sqlcom_sp="select spec_id,name from info;";
	$dbres_sp = pg_exec($dbconn2, $sqlcom_sp );
	if ( ! $dbres_sp ) {echo "Error : " + pg_errormessage( $dbconn_sp ); exit();} 
	$row=0;
	$rowmax=pg_NumRows($dbres_sp);
	while ($row<$rowmax)  {
		$do = pg_Fetch_Object($dbres_sp, $row);
		$sp_names[$do->spec_id]=$do->name;
		$row++;
	}
 */
# if contig exists there is a unique hit
  if ($contig) { 
    $dbconn=pg_connect( "dbname=$PG_DATABASE host=$PG_HOST port=$PG_PORT user=$PG_USER password=$PG_PASS" );
    if ( ! $dbconn ) {
      echo "Error connecting to the database !<br> " ;
      printf("%s", pg_errormessage( $dbconn ) );
      exit(); 
    }
    $picture = "species/$species_id" . ".jpg";
    if (file_exists ($picture)) {}
    else {$picture = "species/$species_id" . ".gif";}

####get protein info and consensus seq    
    $sqlp4e="select * from p4e_ind where pept_id='$protein';";
#	 print ("sqlp4e 134 $sqlp4e<br>\n");##################################
    $dbres = pg_exec($dbconn, $sqlp4e );  #query the p4e tables
    if ( ! $dbres )  {echo "Error : " + pg_errormessage( $dbconn );exit();}
    $prot_rows=0;
    $prot_rows_max=pg_NumRows($dbres);

    $sqlcons="select consensus from cluster where clus_id='$cluster' and contig='$contig';";
#	 print ("$sqlcons<br>\n");####################################
    $dbres1 = pg_exec($dbconn, $sqlcons );  
    if ( ! $dbres1 )  {echo "Error : " + pg_errormessage( $dbconn );exit();}
    $cons_rows=0;
    $cons_rows_max=pg_NumRows($dbres1);
    if   ($cons_rows_max > 0) {
      $do = pg_Fetch_Object($dbres1, $cons_rows);  
      $dnaseq=$do->consensus;
      $dnalength=strlen($dnaseq);
    }

/*    $sqlpsort="select location,probability from psort where pept_id='$protein' order by probability desc;";
#	 print ("$sqlpsort<br>\n");####################################
    $dbresps = pg_exec($dbconn, $sqlpsort );  #query the psort data
    if ( ! $dbresps )  {echo "Error : " + pg_errormessage( $dbconn );exit();}
    $ps_string="";
    $psort = pg_fetch_row($dbresps);
    if ($psort[0]) {$ps_string.="$psort[0] ($psort[1])";}
    else {$ps_string.="no prediction";}
*/
	 
#nn_smean_yn=>siganl nn_ymax_yn=>cleavage site nn_ymax_pos=>cleavage position
    $sqlsigp="select nn_smean_yn,nn_ymax_yn,nn_ymax_pos from signalp where pept_id='$protein';";
#	 print ("$sqlsigp<br>\n");####################################
    $dbressp = pg_exec($dbconn, $sqlsigp );  #query the sport data
    if ( ! $dbressp )  {echo "Error : " + pg_errormessage( $dbconn );exit();}
    $sp_string="";
    $sig = pg_fetch_row($dbressp);
    if ($sig[0]=="f"|"t"){
    	if ($sig[0]=="f") {$sp_string.="No signal predicted";}
    	else if ($sig[1]=="f") {$sp_string.="Anchor signal predicted";$sig_type="A";}
    	else {$pos=$sig[2]-1; $sp_string.="Secretory signal predicted with cleavage site between residues $pos - $sig[2]";$sig_type="S";}
   }else{$sp_string.="No signal information avaialble";}
   # $sp_string.="</td>";



#print("<p>if $prot_rows_max is greater than $prot_rows should get a table</p>");#******************************************

# header table
    printf ("<center>\n\t<table class=\"tablephp1\">\n\t\t<tr>");
    printf     ("\n\t\t\t<td><IMG SRC=\"$picture\" HEIGHT=100 WIDTH=100></td>");
    printf     ("\n\t\t\t<td><div class=\"centermainTitle\"><i>$spec_name</i></div>");
    printf     ("<div class=\"mainHeading\"><center>$short_des</center></div><center><a href=\"species_info.php?species=$species_id\">(details)</a></center></td>");
    printf     ("\n\t\t\t<td><IMG SRC=\"$picture\" align=right HEIGHT=100 WIDTH=100></td>\n\t\t</tr>");
    printf ("\n\t</table>");
###end of header table start of info table#######

##catch the genomic proteins and create a link for the wormbase ones
if(preg_match("/^BMG.*/",$protein)){
    printf ("\n\t<table class=\"tablephp1\">\n\t\t<tr>");
    if ($prot_rows_max==0) {die("\n\t\t<td><table class=\"tablephp2\"><tr>\n\t\t\t<td><div class=\"mainBig\">There is no protein prediction for $protein in the database<div></td>\n\t\t</tr>\n\t\t<tr>\n\t\t\t<td></td>\n\t\t</tr></table></td>\n\t</tr>");}
    printf ("\n\t\t<td><table class=\"tablephp2\"><tr>\n\t\t\t<td><div class=\"mainTitle\">Protein prediction - $protein<div></td>\n\t\t</tr>\n\t\t<tr>\n\t\t\t<td></td>\n\t\t</tr></table></td>\n\t</tr>");	


}elseif (preg_match("/^\D{2}G.*/",$protein)){
    $sqlwp="select wormpep from p4e_ind where pept_id = '$protein';";
    $dbres=pg_exec($dbconn,$sqlwp);  
    $do = pg_Fetch_Object($dbres, 0);
    $wormpep_id = $do->wormpep;
    printf ("\n\t<table class=\"tablephp1\">\n\t\t<tr>");
    if ($prot_rows_max==0) {die("\n\t\t<td><table class=\"tablephp2\"><tr>\n\t\t\t<td><div class=\"mainBig\">There is no protein prediction for $protein in the database<div></td>\n\t\t</tr>\n\t\t<tr>\n\t\t\t<td align=right>Wormbase - <a href=\"http://www.wormbase.org/db/seq/protein?name=$wormpep_id;class=Protein\">$wormpep_id</a></td>\n\t\t</tr></table></td>\n\t</tr>");}
    printf ("\n\t\t<td><table class=\"tablephp2\"><tr>\n\t\t\t<td><div class=\"mainTitle\">Protein prediction - $protein<div></td>\n\t\t</tr>\n\t\t<tr>\n\t\t\t<td align=right>Wormbase - <a href=\"http://www.wormbase.org/db/seq/protein?name=$wormpep_id;class=Protein\">$wormpep_id</a></td>\n\t\t</tr></table></td>\n\t</tr>");


}else{
    printf ("\n\t<table class=\"tablephp1\">\n\t\t<tr>");
    if ($prot_rows_max==0) {die("\n\t\t<td><table class=\"tablephp2\"><tr>\n\t\t\t<td><div class=\"mainBig\">There is no protein prediction for $protein in the database<div></td>\n\t\t</tr>\n\t\t<tr>\n\t\t\t<td align=right><a href=\"cluster.php?cluster=$cluster\">Cluster Details</a></td>\n\t\t</tr></table></td>\n\t</tr>");}
    printf ("\n\t\t<td><table class=\"tablephp2\"><tr>\n\t\t\t<td><div class=\"mainTitle\">Protein prediction - $protein<div></td>\n\t\t</tr>\n\t\t<tr>\n\t\t\t<td align=right><a href=\"cluster.php?cluster=$cluster\">Cluster Details</a></td>\n\t\t</tr></table></td>\n\t</tr>");
}
#### if there is no protein    #################################
    #if ($prot_rows_max==0) {die("this protein doesn't exist in the database</td></tr></table>");}


#### else there is a protein so fetch other p4e data ##################################
    print("<tr><td><table class=\"tablephp2\" >");
    #print("<tr>\n\t\t\t\t\t\n<td width=\"300\">Predicted localisation (probability)</td>\n\t\t\t\t\t<td>$ps_string</td>\n\t\t\t\t</tr>\n");
    print("<tr><td>Signal peptide prediction:&nbsp;&nbsp;&nbsp;&nbsp;$sp_string</td></tr>");

#	 print ("$sqlp4e<br>\n");####################################
      $dbres = pg_exec($dbconn, $sqlp4e );  #query the p4e tables
      $do = pg_Fetch_Object($dbres, 0);  
      #$p_start=$do->xtn_s;
      #$p_end=$do->xtn_e;
      #$conf_start=$do->conf_s;
      #$conf_end=$do->conf_e;
      #$frame=$do->frame_s;
      $method=$do->method;
    print("<tr><td>Translation method:&nbsp;&nbsp;&nbsp;&nbsp;prot4EST - $method</td></tr>");

      ####prot4est reports conf_start and conf_end values but leaves p_start and p_end values null for 
      ####decoder and estscan, for these p_ needs set to conf and conf needs set to 0 so you can draw the 
      ####whole protein using the p_ values and then put the confident bar on top if there are conf values
      #if ($p_start=='' && ($method == "estscan" || $method == "decoder")) {$p_start=$conf_start; $conf_start=0;}
      #elseif ($p_start=='') {$p_start=$conf_start;}	
      #if ($p_end=='' && ($method == "estscan" || $method == "decoder")) {$p_end=$conf_end; $conf_end=0;}  
      #elseif ($p_end=='') {$p_end=$conf_end; }

      $protseq=$do->seq;
      #if ($frame>0) {$dir="F";$direction="forward";}
      #else {$dir="R";$direction="reverse";}
    #print("\n\t\t\t\t\n<tr>\n\t\t\t\t\t<td>Direction</td>\n\t\t\t\t\t<td>$direction</td>\n\t\t\t\t</tr>\n\t\t\t</table>\n\t\t</td>\n\t</tr>\n\n");
      #$tr_length=$p_end-$p_start;
      #$pcluster=$dir;
      #$plength=$tr_length/3;
      #$plength=round($plength);
      #$pro = preg_split('//', $protseq, -1, PREG_SPLIT_NO_EMPTY);
      #for($i=0;$i<26;$i++) { $aa[$i]=0; }
      #for($i=1;$i< strlen($protseq)+1;$i++) {
      #        $idx=ord($pro[$i-1]);
      #        $idx-=65;
      #        $aa[$idx]++;
      #}
####annotations table start##########################################################################
  print("<tr><td><table class=\"tablephp2\" ><tr><td colspan=3 class=\"mainBig\">Associated Annotations</td><td align=right><a href=\"#\" onClick=\"window.open('keys/nemdb_Key_annotation_details.txt','window', 'width=470,height=500,resizable=yes,scrollbars=yes,menubar=yes')\">key</a></td></tr>"); 
  $anno_flag=0;
  $anno_to_print="";
  #get the top uniprot hit from the search already done
  if (preg_match("/^Contig_\d+\s+(\w+)\s+\(\w+\)\s+(.+?)\s+([-e0-9]{0,5}\d+)/",$blxtxt,$anno_unipr)) {
    $anno_to_print.="<tr><td>BLAST v Uniprot</td><td>$anno_unipr[1]</td><td>$anno_unipr[2]</td><td>$anno_unipr[3]</td></tr>";
    $anno_flag=1;
  }
  $annocom[0]="select distinct on (ec_id) 'Enzyme Commission' as source,ec_id as id,descr as desc,bestev as score from a8r_blastec where pept_id~'$protein' order by ec_id,bestev;";
  #$annocom[1]="select distinct on (go_term) 'GOtcha (slim)' as source,go_term as id,description as desc,confidence as score from gotcha_slim where pept_id~'$protein' order by go_term, confidence desc;";
  $annocom[1]="select distinct on (go_term) 'GO' as source,go_term as id,descr as desc,bestev as score from a8r_blastgo where pept_id~'$protein' order by go_term, bestev desc;";
  $annocom[2]="select distinct on (ko_id) 'KEGG' as source,ko_id as id,descr as desc,bestev as score from a8r_blastkegg where pept_id~'$protein' order by ko_id, bestev;";
  $annocom[3]="select distinct on (dom_id) database as source,dom_id||' ('||ipr_id||')' as id,description as desc,score from interpro natural join interpro_key where pept_id~'$protein' and ipr_id!='NULL' order by dom_id,score;";
  for ($i=0; $i<4; $i++) { #for each annotation search
#    print "<br>$annocom[$i]\n";
    $annores = pg_exec($dbconn, $annocom[$i]);
    if (!$annores) {echo "Error : " + pg_errormessage( $dbconn );exit();}
    $anno_row=0;
    $anno_rowmax=pg_NumRows($annores);
#    print "  $anno_rowmax\n";
	 if ($anno_rowmax>0) {
	   $anno_flag=1;
	   while ($anno_row<$anno_rowmax)  { #while there are annotation search results
   	  $anno_do = pg_Fetch_Object($annores, $anno_row);
   	  $anno_source=$anno_do->source;
   	  $anno_id=$anno_do->id;
   	  $anno_desc=$anno_do->desc;
   	  $anno_score=$anno_do->score;
		  $anno_source.="*";
   	  $anno_to_print.="<tr><td>$anno_source</td><td>$anno_id</td><td>$anno_desc</td><td align=center>$anno_score</td></tr>\n";
        $anno_row++;
      }
    }
  }
  if ($anno_flag==1) {
	  print("\n<tr><td>Source</td><td>ID</td><td>Description</td><td align=center>Score</td></tr>");
	  print("$anno_to_print");
	  print("<tr><td colspan=4>* annotated on predicted protein sequence</td></tr></table></td></tr>");
	}
	else {print ("<tr><td colspan=4>None</td></tr></table></td></tr>");}
####annotations table end###########################################################################

####print out protein sequence##################################

	####set the table up####
      print("\n\t<tr><td><table class=\"tablephp2\">");
      print("<tr>\n\t\t<td>\n\t\t\t<div class=\"mainBig\">Translated Sequence</div></td><td align=right><a href=\"#\" onClick=\"window.open('keys/nemdb_Key_protein_seq.jpg','window', 'width=800,height=375')\">key</a></td></tr>");

      print("\n\t\t\t<tr><td><font face=courier>");

      ####print the scale bar along the top of the translation####
      print("\n\t\t\t<N1>");
      for($idx=1;$idx<81;$idx++) {
      	      if($idx % 10 == 0) { print "$idx"; }
      	      elseif($idx < 10)  { print "."; }
      	      elseif($idx % 10 >= strlen($idx)) { print "."; }
      }
      print("</N1>");

      printf ("\n\t\t\t<br>\n\t\t\t");
      $n1=0;
      $chars = preg_split('//', $protseq);
      
      ####print the protein sequence####
      while ($letter=each($chars)) {							      #while the are amino acids in the sequence
        
        if ($conf_start>0 && $n1 >= (($conf_start-$p_start)/3) && $n1 <= ((($conf_end-$p_start)/3)+1)) {$fg="C";}else {$fg="N";}#set the confident / not confident forground colour  
	   
        $bg="1"; #default background
        if ($n1%10==0)  {$bg="0";}#set 10th residue background
        if ($sig[0]=="t" && $n1<$sig[2]) {$bg=$sig_type;} #set signal background

        print("<$fg$bg>$letter[value]</$fg$bg>");	#print the aa and formatting
	     if(($n1 % 80) == 0 && $n1>0) { print("<br>\n\t\t\t"); }	#if it's the 80th aa start a new row
        $n1++;  #next amino acid
      }
		
      if(($n1 % 80) != 0) { printf("<br>"); }				      #if the sequence didn't end at the 80th residue start a new row
      print("\n\t\t\t</font>\n\t\t</td>\n\t</tr>\n\t<tr></tr>");	      

#### if the prot sequence is crap say so #######################
      #for($i=0;$i<26;$i++) { $paa[$i]=($aa[$i]*100)/$plength;}
      #if($plength < 20 || $paa[23] > 5){      ##if it's short or got too many X's  in the seq
      #        print("\n\t<tr>\n\t\t<td align=center>*** Warning - The following protein prediction
      #        is well dodgy ***<br>It's either too short or has too low quality (lots of X's)</td>\n\t</tr>\n");
      #}
		print("</table></td></tr>\n");
#############################################################################################

###get the reciprocal hits info###########################################################
	$recipsql="select db,hit_id,class,bit_score,e_val from reciprocals where pept_id='$protein';";
#	print "$recipsql<br>";*************************************
	$dbrecipres = pg_exec($dbconn, $recipsql);
   if ( ! $dbrecipres )  {echo "Error : " + pg_errormessage( $dbconn );exit();}
	$reciprowmax=pg_NumRows($dbrecipres);
	print("<tr><td><table class=\"tablephp2\"><tr><td>\n");
		print("<tr><td><table width=\"100%\"><tr><td width=\"100%\">Reciprocal BLAST hits</td><td align=right><a href=\"#\" onClick=\"window.open('keys/nemdb_Key_to_recips.jpg','window', 'width=395,height=436')\">key</a></td></tr></table></td></tr>");
	print("<tr><td><table class=\"tablephp21\">\n");
	if ($reciprowmax>1) {  #if there is more than just a self hit
		$recip = isSet($_REQUEST['recip']) ? $_REQUEST['recip'] : '';#how to stop 'Undefined variable' warnings
		$Recip = isSet($_REQUEST['Recip']) ? $_REQUEST['Recip'] : '';#how to stop 'Undefined variable' warnings
		$self_score = isSet($_REQUEST['self_score']) ? $_REQUEST['self_score'] : '';#how to stop 'Undefined variable' warnings
		$Recip=0;
		$dbrecipres = pg_exec($dbconn, $recipsql);
		while ($do_inf = pg_fetch_row($dbrecipres)) {
			if ($do_inf[2]=="SELF"){$self_score=$do_inf[3];}
			else {
				$recip[$Recip][0]=$do_inf[0];
				$recip[$Recip][1]=$do_inf[1];
				$recip[$Recip][2]=$do_inf[2];
				$recip[$Recip][3]=$do_inf[3];
				preg_match("/^(\w\w)/",$recip[$Recip][0],$sp);$recip[$Recip][4]=$sp[1]."C";
				$recip[$Recip][6]=$do_inf[4];
				$Recip++;
			}
		}
		#background array
		$bkgrd=array("VL","L","M","H","VH","VH"); #equation gives 0-5 where 0 eqv to 0-0.199r, 1 eqv 0.2-0.399r, 2 eqv 0.4-0.599r, 3 eqv 0.6-0.799r, 4 eqv 0.8-0.999r & 5=1(identical) 
	 	for ($Recip=0; $Recip<$reciprowmax; $Recip++) {$recip[$Recip][5]=floor(($recip[$Recip][3]/$self_score)*5);}
	 	for ($Recip=0; $Recip<$reciprowmax; $Recip++) {$recip[$Recip][3]=$bkgrd[floor(($recip[$Recip][3]/$self_score)*5)];}
####print the reciprocal hits data###########################################################
		if ($reciprowmax<20) {
			print("<tr><td></td>\n");
			for ($I=0; $I<$reciprowmax-1; $I++) {print "<td class=\"".$recip[$I][4]."\" width=50 height=4></td>";}
			print("</tr><tr><td>Taxon<br>BLAST hit</td>\n");
			for ($I=0; $I<$reciprowmax-1; $I++) {print "<td class=\"".$recip[$I][3]."\" align=center title=\"".$sp_names[$recip[$I][4]]." ".$recip[$I][1]." (e-value ".$recip[$I][6].")\" onMouseOver=\"this.status=''; return true\" onMouseOut=\"this.status='';return false\" class=\"binactive\"><a href=\"protein.php?protein=".$recip[$I][1]."\" >".$recip[$I][0]."</a><br>".$recip[$I][2]."</td>\n";}
		}
		else {	#put them on two rows
			print("<tr><td></td>\n");
			for ($I=0; $I<($reciprowmax-1)/2; $I++) {print "<td class=\"".$recip[$I][4]."\" width=50 height=4></td>";}
			print("</tr><tr><td>Taxon<br>BLAST hit</td>");
			for ($J=0; $J<($reciprowmax-1)/2; $J++) {print "<td class=\"".$recip[$J][3]."\" align=center title=\"".$sp_names[$recip[$J][4]]." ".$recip[$J][1]." (e-value ".$recip[$J][6].")\" onMouseOver=\"this.status=''; return true\" onMouseOut=\"this.status='';return false\" class=\"binactive\"><a href=\"protein.php?protein=".$recip[$J][1]."\">".$recip[$J][0]."</a><br>".$recip[$J][2]."</td>";}
			print("</tr>\n<tr height=4></tr>\n<tr><td></td>");
			for ($I=$I; $I<$reciprowmax-1; $I++) {print "<td class=\"".$recip[$I][4]."\" width=50 height=4></td>";}
			print("</tr><tr><td>Taxon<br>BLAST hit</td>");
			for ($J=$J; $J<$reciprowmax-1; $J++) {print "<td class=\"".$recip[$J][3]."\" align=center title=\"".$sp_names[$recip[$J][4]]." ".$recip[$J][1]." (e-value ".$recip[$J][6].")\" onMouseOver=\"this.status=''; return true\" onMouseOut=\"this.status='';return false\" class=\"binactive\"><a href=\"protein.php?protein=".$recip[$J][1]."\">".$recip[$J][0]."</a><br>".$recip[$J][2]."</td>";}
		}
		print("");
	}
	else {print "<tr><td colspan=$reciprowmax>There are no reciprocal BLAST hits (e-value <1e-10)</td>\n";}
	print("</tr>\n</table></td></tr>\n</table></td></tr>\n");

#################################################################################################
############## Clickable Map for Domain gif ################################################
####colour array#####
/*
$color[0] = "#9932CC";	#purple
$color[1] = "#D15FEE";	#purple
$color[2] = "#DDA0DD";	#purple
$color[3] = "#EAADEA";	#purple
$color[4] = "#DA70D6";	#purple


      ####the domain links
      $scale=580/$plength;					      #set the scale for the domain bars
      $sqlcom0="select domain_coords.domid,start,stop,evalue,sdesc,urlref,'NemDom' as database from domain_coords,domain_annot where domain_coords.domid=domain_annot.domid and pept_id='$protein' order by domid;";
      $sqlcom1="select interpro.dom_id,d_start,d_end,score,description,ipr_id,database from interpro,interpro_key where interpro.dom_id=interpro_key.dom_id and pept_id='$protein' and interpro.dom_id!='seg' and interpro.dom_id!='coil' order by dom_id;";
 #     print "0= $sqlcom0<br>1= $sqlcom1<br>";#****************************************************************
      $dbres_dom0 = pg_exec($dbconn, $sqlcom0 );
      $dbres_dom1 = pg_exec($dbconn, $sqlcom1 );
      
      $varh=70;
		$map_areas="";
		$checks="";
      $row_dom=0;
      $dom_string="";
      $i=0;
      while ($row = pg_fetch_row($dbres_dom0)) {
      	$doms[$row_dom]=$row;
      	$map_areas.="<AREA href=\"domain.php?domain=$row[0]&sp=ALL\" TARGET=\"new\" ALT=\"$row[5]\" COORDS=\"".(int)(12+($row[1]*$scale)).",".$varh.",".(int)(12+($row[2]*$scale)).",".($varh+12)."\" SHAPE=RECT title=\""."$row[0]"." "."$row[4]"." "."$row[3]"."\">\n";
         $checks.="<tr class=\"DC".$i."\" height=23><td><input TYPE=\"checkbox\" NAME=\"DOM[]\" value=\"$row[0]\">$row[6]</input></td></tr>\n";
      	$row_dom++;
      	foreach ($row as $item) {$item=preg_replace('/[:;]/',' ',$item); $dom_string=$dom_string.$item.":";}
      	$dom_string.=";";
	      $varh=$varh+25;
	      $i++;
	      if ($i==5){$i=0;}
      }
      while ($row = pg_fetch_row($dbres_dom1)) {
      	$doms[$row_dom]=$row;
      	$map_areas.="<AREA href=\"domain.php?domain=$row[0]&sp=ALL\" TARGET=\"new\" ALT=\"$row[5]\" COORDS=\"".(int)(12+($row[1]*$scale)).",".$varh.",".(int)(12+($row[2]*$scale)).",".($varh+12)."\" SHAPE=RECT title=\""."$row[0]"." "."$row[4]"." "."$row[3]"."\">\n";
      	$checks.="<tr class=\"DC".$i."\" height=23><td><input TYPE=\"checkbox\" NAME=\"DOM[]\" value=\"$row[0]\">$row[6]</input></td></tr>\n";
      	$row_dom++;
      	foreach ($row as $item) {$item=preg_replace('/[:;]/',' ',$item); $dom_string=$dom_string.$item.":";}
      	$dom_string.=";";
	      $varh=$varh+25;
	      $i++;
	      if ($i==5){$i=0;}
	    }
    #  print("$dom_string<br>");
    #  print_r($doms);
      printf("\n\t<tr>\n\t\t<td>\n\t\t\t<table class=\"tablephp21\">\n\t\t\t\t<tr>\n\t\t\t\t\t<td><div class=\"mainBig\">Domain Map :</div></td><td align=right><a href=\"#\" onClick=\"window.open('keys/nemdb_Key_domain_map.jpg','window', 'width=740,height=416')\">key</a></td>\n\t\t\t\t</tr>\n\t\t\t\t<tr>\n\t\t\t\t\t<td align=right><MAP NAME=map$dir>$map_areas");
		
      ####the 6 frame translated EST link
      $scale=580/$dnalength;					      #set the scale for the translated protion of dna bar
      $xs=10+($p_start*$scale);
      $ys=25;
      $xe=10+($scale*$p_end);
      $ye=27;
      print("\n\t\t\t\t\t\t<AREA SHAPE=rect COORDS=\"$xs,$ys,$xe,$ye\"");
      print("href=\"translate.php?cluster=$cluster&contig=$contig&dir=$dir\" target=\"new\">");
    #  printf("href=\"translate.php?cluster=$cluster&contig=$contig&dir=$dir\"");
    #  print(" target=\"_blank\">\n");

      print("</MAP>");
		#$_SESSION['doms'] = $doms;  this sould allow you to pass the array to img.php, works as an include but not in an IMG tag thus the string 	
		#include('prot_img.php');
      print("\n\t\t\t\t\t\t<IMG border=0 USEMAP=\"#map$dir\"SRC=\"prot_img.php?protein=$protein&dnalength=$dnalength&plength=$plength&tr_length=$tr_length&p_start=$p_start&p_end=$p_end&conf_start=$conf_start&conf_end=$conf_end&dir=$dir&height=$varh&dom_string=$dom_string\"></img>\n\n<br><br>\n\n");
		print("\n\t\t\t\t\t</td>\n\t\t\t\t\t<td width=100% align=left><form id=\"domform\" method=\"post\" action=\"pro_dom_search.php\">\n\t\t\t\t\t\t\t<table>\n\t\t\t\t\t\t\t\t<tr height=45></tr>$checks\n\t\t\t\t\t\t</table>\n\t\t\t\t\t</TD>\n\t\t\t\t</tr>\n\t\t\t\t<tr>\n\t\t\t\t\t<td align=\"right\">Search Nembase for proteins with selected domains</td>\n\t\t\t\t\t<td><input TYPE=\"submit\" VALUE=\"Search\"></td>\n\t\t\t\t</tr>\n\t\t\t</table>\n\t\t\t\t\t\t</form></td>\n\t\t\t\t</tr>");


      $id=time();
*/
  }
##############################################################################
####get tribe info ###########################################################
	$inflation=array("1.1","1.5","2","2.5","3","3.5","4","4.5","5");
	$tribe1sql="select spid,inf11,inf15,inf20,inf25,inf30,inf35,inf40,inf45,inf50 from tribe where pept_id='$protein';";
#	print "$tribe1sql<br>";#************************************
	$dbtriberes = pg_exec($dbconn, $tribe1sql);
  if ( ! $dbtriberes )  {echo "Error : " + pg_errormessage( $dbconn );exit();}
	$tribe_rowmax=pg_NumRows($dbtriberes);
  	if ($tribe_rowmax > 0){
		$tribe=pg_fetch_row($dbtriberes, 0);
		for ($Inf=0; $Inf<9; $Inf++) {
			$sqltribesp="select num_pepts,num_sp from tribe_info where tribe=".$tribe[$Inf+1]." and inflation=".$inflation[$Inf].";";
			#print "$Inf - $sqltribesp<br>";#************************************
			$dbtriberes2 = pg_exec($dbconn, $sqltribesp);
			$tmp=pg_fetch_row($dbtriberes2);
			$inf[$Inf][0]=$tmp[0];
			$inf[$Inf][1]=$tmp[1];
		}
####print the tribe data###########################################################
			print("<tr><td><table class=\"tablephp2\">\n");
			print("<tr><td class=\"mainBig\" colspan=9>Tribes</td></tr>\n");
			$head=array("Inflation-Tribe","Members","Species");
			$K=0;	
			foreach ($head as $header) {
				print("<tr><td>$header</td>");
				if ($K==0) {for ($I=0; $I<9; $I++) {
					print "<td align=right><a href=\"tribe.php?inf=".$inflation[$I]."&tribe=".$tribe[$I+1]."\">".$inflation[$I]." - ".$tribe[$I+1]."</a></td>";}
					$K=1;
				}
				else {
					for ($I=0; $I<9; $I++) {print "<td align=right>".$inf[$I][$K-1]."</td>";}
					$K++;
				}
				print("</tr>\n");
			}
			print("</table></td></tr>\n");
	}else{
		print("<tr><td><table class=\"tablephp2\">\n");
		print("<tr><td class=\"mainBig\" colspan=9>Tribes</td></tr>\n");
		print("<tr><td colspan=9>This protein does not belong to any tribes</td></tr>\n");
	}
###########################################################################
print("\n\t\t\t\t</table>");
}

pg_close( $dbconn2 );




###########################################################################################################
function get_browser_info() {
## GET BROWSER VERSION (NS/IE Check)
  $isiepos = strpos(getenv('HTTP_USER_AGENT'),"MSIE");
  $isie = ( $isiepos>0 ? substr(getenv('HTTP_USER_AGENT'),$isiepos+5,3) : 0 );
  list($isnsver,$d) = explode(" ",getenv('HTTP_USER_AGENT'));
  $agent=getenv('HTTP_USER_AGENT');

  $isnsver = ( substr($isnsver,0,8)=="Mozilla/" ? substr( $isnsver,8 ) : 0 );
  $isns = ( $isnsver>4.0 ? $isnsver : 0 );

  $brwserflg=0;
	if( $isie>=5 || $isns>=5 )
	 { $brwserflg=1; }
  return $brwserflg;
}
###########################################################################################################

include('/var/www/html/includes/nembase4_body_lower.ssi');
print "</body>\n";
print "</html>\n";

?>
