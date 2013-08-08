<?php
//this script generates results for cluster searches
//nembase3 September 2007
//Ann Hedley, University of Edinburgh
//Ben 08/06/09 conver cluster id to uppercase

$cluster=$_POST["cluster"];
$cluster = strtoupper($cluster);

#define variables used with the database
#usually this goes in an include file
$PG_HOST="localhost";
$PG_PORT=5432;
$PG_DATABASE="nemdb4";
$PG_USER="webuser";
$PG_PASS="";


$dbconn=pg_connect( "dbname=$PG_DATABASE host=$PG_HOST port=$PG_PORT user=$PG_USER password=$PG_PASS" );
if ( ! $dbconn ) {
    echo "Error connecting to the database!<br> " ;
    printf("%s", pg_errormessage( $dbconn ) );
    exit(); }


############################################################
############### Retreive a single cluster ##################
############################################################
if($cluster) {
  if(preg_match("/\w\wC\d\d\d\d\d/","$cluster")) { header ("Location: cluster.php?cluster=$cluster"); exit; }
  if(preg_match("/\w\wP\d\d\d\d\d/","$cluster")) { header ("Location: protein.php?protein=$cluster"); exit; }
  else {
    if(preg_match("/_/","$cluster"))  {
      $sqlcom="select clus_id,est.est_id from est,clone_name where est.est_id=clone_name.est_id
        and clone_name.clone_id='$cluster';";
      print "$sqlcom";
	  $clone_id=$cluster." - ";
    }
    else  {
      $cluster = strtoupper ($cluster);
      $sqlcom="select clus_id,est_id from est where est_id~'$cluster';";
      $clone_id='';
    }
    $dbres = pg_exec($dbconn, $sqlcom );
    if ( ! $dbres ) {
      echo "Error : " + pg_errormessage( $dbconn );
      exit(); 
    }

 
 //and interpret the results
    $row=0;
    $rowmax=pg_NumRows($dbres);
    if($rowmax==0) {
      pg_close( $dbconn );
      print "Sorry this sequence is not in any cluster\n";
      exit(); 
    }
    else {
      $do = pg_Fetch_Object($dbres, $row);
      $clusnum=$do->clus_id;
      $acc_id=$do->est_id;
      print "<br><hl>\n";
      print "<center><a HREF=../nembase4/cluster.php?cluster=$clusnum>";
#      print "<center><a HREF=../nembase4/\&quot;cluster.php?cluster=$clusnum\&quot;>";
      print "$clone_id $acc_id is in cluster $clusnum - click to be redirected</a>";
      exit; 
    }
  }
}
else print "ERROR: The server complains about not receiving any information to deal with";
