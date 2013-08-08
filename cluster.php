<?php

#######
#ben 21/07/08
#removed pept_id from $sqlcom at line 100 as no longer in cluster table
#29/07/08
#get_blasts function was calling old blastdb names, needs to match ones in current version, therefore changed lines 15-152
#11/08/08
#removed dbest blast results 
#19/08/08
#removed reference to psort (add in again when completed)
#removed reference to gotcha_slim table and replace with a8r_blastgo
#######

print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
print "<html>\n";
print "<head>\n";
print "<title> www.nematodes.org - NEMBASE4 </title>\n";
include('/var/www/html/includes/nembase4_header.ssi');
print "</head>\n";
print "<body>\n";
include('/var/www/html/includes/nembase4_body_upper.ssi');

print("<form>\n");

$cluster = $_REQUEST['cluster']; 
$protein = preg_replace ('/(\w\w)C/', '\1P',$cluster);

#define variables used with the database
#usually this goes in an include file
$PG_HOST="localhost";
$PG_PORT=5432;
$PG_DATABASE="nemdb4";
$PG_DATABASE2="species_db4";
$PG_USER="webuser";
$PG_PASS="";


if($cluster)  {
  $cluster = strtoupper ($cluster);
  if (preg_match("/^(\w\w\w\d\d\d\d\d)/","$cluster", $match)) { $cluster=$match[0];}

#let's open the database
  $dbconn=pg_connect( "dbname=$PG_DATABASE host=$PG_HOST port=$PG_PORT user=$PG_USER password=$PG_PASS" );
  if ( ! $dbconn ) {
    echo "Error connecting to the database !<br> " ;
    printf("%s", pg_errormessage( $dbconn ) );
    exit(); 
  }

#same for species database
  $dbconn2=pg_connect( "dbname=$PG_DATABASE2 host=$PG_HOST port=$PG_PORT user=$PG_USER password=$PG_PASS" );
  if ( ! $dbconn2 ) {
    echo "Error connecting to the database !<br> " ;
    printf("%s", pg_errormessage( $dbconn2 ) );
    exit(); 
  }
  if (preg_match("/^(\w\w\w)/","$cluster", $match)) { $species_id=$match[0];}



#get info from species_db
  $info_sql= "select * from info where spec_id='$species_id';";
  $info_query = pg_exec($dbconn2, $info_sql);
  $do_info = pg_Fetch_Object($info_query, 0);
  $spec_name=$do_info->name;
  $short_des=$do_info->short_des;
  if ($short_des == NULL) {$short_des = "description not available"; }

  $stats_sql= "select * from stats where spec_id='$species_id';";
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
  
  $ref = "species/$species_id" . ".html";
  $picture = "species/$species_id" . ".gif";
  if (file_exists ($picture)) {}
  else {$picture = "species/$species_id" . ".jpg";}

  printf ("<center>\n\t<table class=\"tablephp1\">\n\t\t<tr>");#backgroud table start
  printf ("<td><table class=\"tablephp1\">"); #header table start
  printf ("\n\t\t\t<td><IMG SRC=\"$picture\" HEIGHT=100 WIDTH=100></td>");
  printf ("\n\t\t\t<td><div class=\"mainTitle\"><center><i>$spec_name</i></center></div>");
  printf ("<div class=\"mainHeading\"><center>$short_des</center></div><center><a href=\"species_info.php?species=$species_id\">(details)</a></center></td>");
  printf ("\n\t\t\t<td><IMG SRC=\"$picture\" align=right HEIGHT=100 WIDTH=100></td>\n\t\t</tr>");
  printf ("\n\t</table>"); #header table end

#Determine location of relevant files
  $scf_text="http://xyala.cap.ed.ac.uk/cgi-bin/TView.cgi?DATAFILE=";
  $scf_text.="seq_tables%2F$org_dir%2F";
# old srs text, changed by MB to go to EMBL
#  $srs_text="http://srs6.ebi.ac.uk/srs6bin/cgi-bin/wgetz?-e+[";
#  $srs_text.="{embl%20patent_dna%20imgtligm%20imgthla%20ipdkir%20emblcon%20emblconexp%20refseq%20emblidacc}";
#  $srs_text.="-acc:";
  $srs_text="http://www.ebi.ac.uk/Tools/dbfetch/dbfetch?db=embl&format=fasta&style=raw&id=";
  
#Obtain the cluster info 
  $row_lib=0;
  $row_type=0;
  $sqlcom="select * from cluster where clus_id='$cluster' and retired=0 group by contig,clus_id,num_ests,consensus,retired order by contig;";
  $sqlcom1="select distinct library,count(library) from est where clus_id='$cluster' group by library order by count desc;";
  $dblib = pg_exec($dbconn, $sqlcom1);

  $stage='';
  $libtxt="";
  $libtxt1="";
  $row_maxlib=pg_NumRows($dblib);
  $flag=0;
  $flag1=0;

  while ($row_lib<$row_maxlib)  {
    $do_lib = pg_Fetch_Object($dblib, $row_lib);
    $txt=$do_lib->library;
    $txt1=$do_lib->count;
    if($txt>0)  { 
      $libtxt.="<tr><td><a href=lib.php?txt=$txt>$txt"; 
      $sqlcom3="select stage from lib where lib_id='$txt';";
      $dbstage = pg_exec($dbconn, $sqlcom3);
      if(pg_NumRows($dbstage)>0) {  
        $do_stage=pg_Fetch_Object($dbstage, 0);
        $txt=$do_stage->stage; 
      }
      if($txt=='') { $txt="unk"; }
      $libtxt.="</a></td><td>$txt</td><td>$txt1</td></tr>\n";
    } 
    $row_lib++;
  }

#  print ("122<br>$dbconn<br>$sqlcom<br>"); #***************
  $dbres = pg_exec($dbconn, $sqlcom );
  if ( ! $dbres ) {
    echo "Error : " + pg_errormessage( $dbconn );
    exit();
  }

# and interpret the results
  $row=0;
  $rowmax=pg_NumRows($dbres);
  $do = pg_Fetch_Object($dbres, 0);

  $unifile="/nembase4/nembase4_data/$org_dir/blast/uniref100/$cluster";
  #print "$unifile\n";
  $unifile.="_1.out";
  #$dbestfile="/seq_tables/$org_dir/est/$cluster";
  #$dbestfile.=".out";
  $wpfile="/nembase4/nembase4_data/$org_dir/blast/wormpep/$cluster";
  $wpfile.="_1.out";

  $blxtxt=get_blasts("uniref100","blastx",$dbconn,$cluster);
  #$dbstxt=get_blasts("est","blastn",$dbconn,$cluster);
  $wptxt=get_blasts("wormpep","blastx",$dbconn,$cluster);
  printf ("<tr><td>\n");
  printf("<table class=\"tablephp2\">\n"); #clus_id table start
  printf("<tr><td><div class=\"mainTitle\">$cluster cluster details</div></td></tr>\n");
  printf("\n</table></td></tr>\n");#clus_id table end

  $lib_row_span=$row_lib*1.7;
  $download_height=round((7-$lib_row_span)*17);
  if ($lib_row_span<7) {$lib_row_span=7;}
  elseif ($lib_row_span>10) {$lib_row_span=10;}
  printf("<tr><td>\n<table class=\"tablephp22\">\n<tr>"); #lib/blast table start
  printf("<td valign=top>\n<table>"); #library table start
  printf("\n<tr><th colspan=3>Library information</th></tr>\n");
  printf("<tr><td>ID</td><td>Stage</td><td>Number</td></tr>\n");
  printf("$libtxt<tr><td height=\"$download_height\" valign=\"bottom\" colspan=3><input type=\"button\" value=\"Download all %s seqs\" onClick=\"window.location='/downloads/download.php?db=$PG_DATABASE&cluster=$cluster&contig=0'\">\n",$do->num_ests);
  printf("</td></tr>\n</table>\n</td><td class=\"tablephp12\" width=2></td>\n");#library table end
  printf("<td valign=\"top\">\n<table><tr><th colspan=6>Blast information</th></tr>\n");#blast table start

  printf("<tr>\n");
  p_blast($unifile,$blxtxt,"window","blastx vrs uniprot");
  printf("<td></td>\n");
  #p_blast($dbestfile,$dbstxt,"window1","blastn vrs dbest");
  #printf("<td></td>\n");
  p_blast($wpfile,$wptxt,"window2","blastx vrs wormpep");
  printf("</tr>\n");

  $init_txt=preg_replace ("/\\\\n/","\n",$blxtxt);

  print("<tr><td colspan=6><textarea name=\"blsout\" value=\"$blxtxt\" rows=$lib_row_span cols=71");
  print(" wrap=off>$init_txt</textarea></td></tr></table></td></tr>");#blast table end
  print("</table></td></tr>");#lib/blast table end
  
####annotations table start##########################################################################
  print("\n<tr><td><table class=\"tablephp2\"><tr><td colspan=3>Associated Annotations</td><td align=right><a href=\"#\" onClick=\"window.open('keys/nemdb_Key_annotation_details.txt','window', 'width=470,height=500,resizable=yes,scrollbars=yes,menubar=yes')\">key</a></td></tr>"); 
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
   	  $anno_to_print.="<tr><td>$anno_source</td><td>$anno_id</td><td>$anno_desc</td><td>$anno_score</td></tr>\n";
        $anno_row++;
      }
    }
  }
  if ($anno_flag==1) {
	  print("\n<tr><td>Source</td><td>ID</td><td>Description</td><td>Score</td></tr>");
	  print("$anno_to_print");
	  print("<tr><td colspan=4>* annotated on predicted protein sequence</td></tr></table></td></tr>");
	}
	else {print ("<tr><td colspan=4>None</td></tr></table></td></tr>");}
####annotations table end###########################################################################
  
  $prot_short=$protein;
  while ($row<$rowmax)  { #for each contig
    $do = pg_Fetch_Object($dbres, $row);
    $contig=$do->contig;
    $seq=$do->consensus;
    $protein=$prot_short."_".$contig; 

    printf("\n<tr><td><table class=\"tablephp2\"><tr><td>"); #contig table start
    printf("\n<table class=\"tablephp2\"><tr>\n<td class=\"mainBig\">$cluster - contig : %s</td>\n", $contig);#contig header table start
    
    $con_seq=$seq;
    $sqlcom="select * from est where clus_id='$cluster' and contig='$contig' order by library,est_id;";
    $dbres_est = pg_exec($dbconn, $sqlcom );
    $row_est=0;
    $row_estmax[$contig]=pg_NumRows($dbres_est);
    $seq_length=strlen($seq);
 
    $sqlcom="select * from p4e_ind where pept_id='$protein';";
    $dbres_prot = pg_exec($dbconn, $sqlcom );
    $prots=pg_NumRows($dbres_prot);
    $prot_link="";
    if ($prots>0) {$prot_link="<a href=\"/nembase4/protein.php?protein=$protein\">Predicted Protein Translation</a>\n";}
    else {$prot_link="&nbsp\n";}
    printf("<td class=\"mainBig\" align=center>Length of Contig : %d</td>\n", $seq_length);
    print("<td class=\"mainBig\" align=right>$prot_link</td></tr>\n");
    printf("</table></td></tr><tr><td><hr></td></tr>\n");#contig header table end
  

    printf("<tr><td><table>");#contig alignment table start
    $length[$contig]=$seq_length;
    $singletons[$contig]=0;
    $clustered[$contig]=0;
    $imageH = $row_estmax[$contig]*10+50;
    
    $library='';
    while ($row_est<$row_estmax[$contig]) {
      $do_est = pg_Fetch_Object($dbres_est, $row_est);
      if($library != $do_est->library)  { 
        if($library!='') { printf("\n</td></tr>"); }
        printf("\n<tr><td>");
        $stage="unk";
        if($do_est->library)  {
          $library = $do_est->library;	  
          $sqlcom3="select stage from lib where lib_id='$library';";
          $dbres_lib = pg_exec($dbconn, $sqlcom3);
          if(pg_NumRows($dbres_est)>0)    {
            if($do_lib = pg_Fetch_Object($dbres_lib, 0)) {
              $stage=$do_lib->stage;
            }
            if($stage=='') { $stage="unk"; }
          }
        }
        printf("$library - $stage:</td><td>");
      }
      $e="$do_est->est_id";


      if($do_est->a_end > 1)  {
        $est_name[$contig][$clustered[$contig]]=$e;
 #changed for new embl links
 #       $ln_est_txt[$contig][$clustered[$contig]]=$srs_text.$e."]";
        $ln_est_txt[$contig][$clustered[$contig]]=$srs_text.$e;
        $end[$contig][$clustered[$contig]]=$do_est->a_end;
        if($end[$contig][$clustered[$contig]] > $length[$contig]) { $length[$contig]=$end[$contig][$clustered[$contig]]; }
        $start[$contig][$clustered[$contig]]=$do_est->a_start;
        $clustered[$contig]++;
      }
      if($do_est->a_end < 2)  {
        $sqlcom1="select sequence from est_seq where est_id='$e';";
        $dbres_sing = pg_exec($dbconn, $sqlcom1 );
        $do_est_sing = pg_Fetch_Object($dbres_sing, 0);
        $seq=$do_est_sing->sequence;
        $sing_length=strlen($seq);
        $sing_st[$contig][$singletons[$contig]]=$sing_length;
        $sing_name[$contig][$singletons[$contig]]=$e;
#changed for new embl links
#        $ln_sing_txt[$contig][$singletons[$contig]]=$srs_text.$e."]";
        $ln_sing_txt[$contig][$singletons[$contig]]=$srs_text.$e;
        $singletons[$contig]++;
      }
      $row_est++;
      if($row_est==$row_estmax) { $e.='<br>'; }
      else { $e.=' / '; }
      printf("\n<a href=%s%s]>",$srs_text,$do_est->est_id);
      $col_index=$do_est->type;
#      $col_est=$col[$col_index];
      $col_est=$col[0];
      printf("$e</a>");
    }
    print("\n</td></tr>\n");

    print("<tr><td align=center><MAP NAME=map$contig>");#EST alignment imagemap start
    $scale=580/$length[$contig];
    $xh=10+$length[$contig]*$scale;
    print("<AREA SHAPE=rect COORDS=\"10,20,$xh,30\"");
    printf(" href=\"/cgi-bin/n4_align.cgi?CLUSTER=$cluster&CONTIG=$contig&ORGANISM=$org_dir\"");
    print(" target=\"_blank\">\n");


    for($m1=0;$m1<$clustered[$contig];$m1++) {
      $xl=10+($start[$contig][$m1]*$scale);
      $yl=($m1*10)+38;
      $xh=10+$end[$contig][$m1]*$scale;
      $yh=($m1*10)+45;
      $name=$est_name[$contig][$m1];
      print("<AREA SHAPE=rect COORDS=\"$xl,$yl,$xh,$yh\"");
      printf(" href=%s\n", $ln_est_txt[$contig][$m1]);
      print(" target=\"_blank\">\n");

 # See if we have a trace for this EST  - $e
 # 1. Locate file in directory sequences
 # 2. Open and read file and look for clone name
 # 3. See if the trace exists from its clone name
      $specname=preg_replace('/ /', '_',$spec_name);
		if(file_exists("/var/www/html/nembase4/nembase4_data/$org_dir/sequences/$name"))  {
        $fp = fopen ("/var/www/html/nembase4/nembase4_data/$org_dir/sequences/$name", "r");
        $line = fgets($fp, 4096); 
        if(preg_match("/(\w\w_\w{2,5}_\d\d\w\d\d)/",$line,$match))   {
          preg_match("/\w\w_(\w{2,5})_\d\d\w\d\d/",$match[0],$trace_dir); 
          if(file_exists("/var/www/html/seq_tables/$org_dir/$trace_dir[1]/scf/$match[0].gz"))    {
            print("<AREA SHAPE=rect COORDS=\"614,$yl,622,$yh\"");
            print(" href=http://xyala.cap.ed.ac.uk/cgi-bin/TView.cgi?DATAFILE=");
            print("seq_tables%2F$org_dir%2F$trace_dir[1]%2Fscf%2F$match[0]&TITLE=$name&SPECIES=$specname&CLUSTER=$cluster");
            print(" target=\"_blank\">\n");
          }
        }
        else if(preg_match("/(Nb_ad.+)_M13/",$line,$match))   {
          preg_match("/Nb_(ad\d)/",$match[0],$trace_dir);  
          if(file_exists("/var/www/html/nembase4/nembase4_data/$org_dir/$trace_dir[1]/scf/$match[1].gz"))    {
            print("<AREA SHAPE=rect COORDS=\"614,$yl,622,$yh\""); 
            print(" href=http://xyala.cap.ed.ac.uk/cgi-bin/TView.cgi?DATAFILE=");
            print("seq_tables%2F$org_dir%2F$trace_dir[1]%2Fscf%2F$match[1]&TITLE=$name&SPECIES=$specname&CLUSTER=$cluster");
            print(" target=\"_blank\">\n");
          }
        }
        else if(preg_match("/(\w\w\d\d\w\d\d\.y\d)/",$line,$match))   {
          if(file_exists("/var/www/html/nembase4/nembase4_data/$org_dir/washu/$match[1].gz")) {
            print("<AREA SHAPE=rect COORDS=\"614,$yl,622,$yh\"");
            print(" href=http://xyala.cap.ed.ac.uk/cgi-bin/TView.cgi?DATAFILE=");
            print("seq_tables%2F$org_dir%2Fwashu%2F$match[1]&TITLE=$name&SPECIES=$specname&CLUSTER=$cluster");
            print(" target=\"_blank\">\n");
          }
        }
        fclose($fp);
      }
    }

#same again singletons

    $m1++;
    for($m2=0;$m2<$singletons[$contig];$m2++) {
      $xl=10+$scale;
      $yl=(($m2+$m1)*10)+38;
      $xh=10+$sing_st[$contig][$m2]*$scale;
      $yh=(($m2+$m1)*10)+45;
      $name=$sing_name[$contig][$m2];
      print("<AREA SHAPE=rect COORDS=\"$xl,$yl,$xh,$yh\"");
      printf(" href=%s\n",$ln_sing_txt[$contig][$m2]);
      print(" target=\"_blank\">\n");

      if(file_exists("/var/www/html/nembase4/nembase4_data/$org_dir/sequences/$name"))  {
        $fp = fopen ("/var/www/html/nembase4/nembase4_data/$org_dir/sequences/$name", "r");
        $line = fgets($fp, 4096);
        if(preg_match("/(\w\w_\w{2,5}_\d\d\w\d\d)/",$line,$match))  {
          preg_match("/\w\w_(\w{2,5})_\d\d\w\d\d/",$match[0],$trace_dir); 
          if(file_exists("/var/www/html/seq_tables/$org_dir/$trace_dir[1]/scf/$match[1].gz"))    {
            print("<AREA SHAPE=rect COORDS=\"614,$yl,622,$yh\"");
            print(" href=http://xyala.cap.ed.ac.uk/cgi-bin/TView.cgi?DATAFILE=");
            print("seq_tables%2F$org_dir%2F$trace_dir[1]%2Fscf%2F$match[1]&TITLE=$name");
            print(" target=\"_blank\">\n");
          }
        }
        else if(preg_match("/(Nb_ad.+)_M13/",$line,$match))  {
          preg_match("/Nb_(ad\d)/",$match[0],$trace_dir); 
          if(file_exists("/var/www/html/seq_tables/$org_dir/$trace_dir[1]/scf/$match[1].gz"))   {
            print("<AREA SHAPE=rect COORDS=\"614,$yl,622,$yh\"");
            print(" href=http://xyala.cap.ed.ac.uk/cgi-bin/TView.cgi?DATAFILE=");
            print("seq_tables%2F$org_dir%2F$trace_dir[1]%2Fscf%2F$match[1]&TITLE=$name");
            print(" target=\"_blank\">\n");
          }
        }
        else if(preg_match("/(k\w\d\d\w\d\d\.y\d)/",$line,$match))  {
          if(file_exists("/var/www/html/seq_tables/$org_dir/washu/scf/$match[1].gz"))   {
            print("<AREA SHAPE=rect COORDS=\"614,$yl,622,$yh\"");
            print(" href=http://xyala.cap.ed.ac.uk/cgi-bin/TView.cgi?DATAFILE=");
            print("seq_tables%2F$org_dir%2Fwashu%2Fscf%2F$match[1]&TITLE=$name");
            print(" target=\"_blank\">\n");
          }
        }
        fclose($fp);
      }
    }
    print("</MAP></td></tr>"); #EST alignment imagemap end
    print ("<tr><td colspan=3><IMG border=0 USEMAP=\"#map$contig\" SRC=\"clus_img.php?cluster=$cluster&contig=$contig&length=$seq_length&org_dir=$org_dir\"></img>");
    printf("<br><input type=\"button\" value=\"Download sequences associated with this contig\" onClick=\"window.location='/downloads/download.php?db=$PG_DATABASE&cluster=$cluster&contig=$contig'\"></td><td valign=top align=right><a href=\"#\" onClick=\"window.open('keys/nemdb_Key_contig_map.jpg','window', 'width=630,height=280')\">key</a></td></tr>\n");
    printf("<tr><td colspan=3>Sequence : </td></tr>\n<tr><td colspan=3>\n<textarea rows=6 cols=80 wrap=virtual face=monospace>"); #consensus sequence start

    $n1=0;
    $chars = preg_split('//', $con_seq);
	#while ($letter=each($chars)) {
 	foreach ($chars as $letter) {
 		print_r($letter);
 		if($n1!=0 && ($n1 % 80) == 0) {printf("\n");}
  		$n1++;
	}
#	if(($n1 % 80) != 0) { printf("\n"); }
   
    printf("\n</textarea></td></tr>\n");#consensus sequence end
    printf("<tr><td><input type=\"button\" value=\"Blast\" onClick=\"window.open('blast.php?seq=$con_seq','window4')\"></td>\n");
    printf("<td> our in-house dbs with this sequence</td></tr></table></td></tr>\n\n");#contig alignment table end   
    printf("</table></td></tr>\n\n");#contig table end
 $row++;
  }

#close the database
pg_close( $dbconn );
pg_close( $dbconn2 );

}  #for each contig loop end


function get_blasts($blsdb,$blsprog,&$dbconn,$cluster)  {
  $sqlcom="select id,description,score,contig from blast where clus_id='$cluster' and 
  prog='$blsprog' and db='$blsdb' order by score;";
  $dbres_blast = pg_exec($dbconn, $sqlcom );
  $row_blastmax=pg_NumRows($dbres_blast);
  $row_blast=0;
  $row_blastmax=pg_NumRows($dbres_blast);
  $blstxt='';
  while ($row_blast<$row_blastmax) {
    $do_blast = pg_Fetch_Object($dbres_blast, $row_blast);
    $id = $do_blast->id;
    $desc=$do_blast->description;  
    $blcont=$do_blast->contig;
    if($blcont < 10) { $blcont="0".$blcont; }
    $score=' ';
    $score.=$do_blast->score;
    if($score==1) { $score=''; }
    $txt= "$id " . "$desc";
    preg_match("/.{1,54}/",$txt,$newtxt);
    $blstxt.="Contig_". "$blcont ";
    $blstxt.=$newtxt[0];
    $blstxt.=$score;
    $blstxt.='\n';
    $row_blast++;
  }
  $blstxt= ereg_replace ("'", "", $blstxt);
  $blstxt= ereg_replace ("%", "", $blstxt);
  return $blstxt;
}

function p_blast($file,$txt,$window,$buttxt) {
## GET BROWSER VERSION (NS/IE Check)
  $isiepos = strpos(getenv('HTTP_USER_AGENT'),"MSIE");
  $isie = ( $isiepos>0 ? substr(getenv('HTTP_USER_AGENT'),$isiepos+5,3) : 0 );
  list($isnsver,$d) = explode(" ",getenv('HTTP_USER_AGENT'));
  $agent=getenv('HTTP_USER_AGENT');
#
# printf("$agent $isnsver<br>");
#
  $isnsver = ( substr($isnsver,0,8)=="Mozilla/" ? substr( $isnsver,8 ) : 0 );
  $isns = ( $isnsver>4.0 ? $isnsver : 0 );
  if( $isie>=5 || $isns>=5 || preg_match("/Opera/",$agent)) {
    printf("<td><input type=\"button\" value=\"$buttxt\" onClick=\"window.open('$file','$window', 'width=640,height=360,resizable=yes,scrollbars=yes,menubar=yes')\" onmouseover=\"document.forms[0].blsout.value='$txt';\"></td>");
  } 
  else  {
    print("<td><a href=\"$file\" target=\"_blank\" onmouseover=\"document.forms[0].blsout.value='$txt'; \"></td>");
  }
}

print"</table>";#background table end
print"</form>";
print "<!-- InstanceEndEditable -->\n";
include('/var/www/html/includes/nembase4_body_lower.ssi');
print "</body>\n";
print "<!-- InstanceEnd --></html>\n";

?>
