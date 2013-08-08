<?php

#######
#ben 06/01/11
#created this to show library info for each species
#linked to from species_info.php
#######

print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
print "<html>\n";
print "<head>\n";
print "<title> www.nematodes.org - nembase4 </title>\n";
include('/var/www/html/includes/nembase4_header.ssi');
print "</head>\n";
print "<body>\n";
include('/var/www/html/includes/nembase4_body_upper.ssi');

print("<form>\n");

$cluster = $_REQUEST['sp']; 

#define variables used with the database
#usually this goes in an include file
$PG_HOST="localhost";
$PG_PORT=5432;
$PG_DATABASE="nemdb4";
$PG_DATABASE2="species_db4";
$PG_USER="webuser";
$PG_PASS="";

#let's open the database
  $dbconn=pg_connect( "dbname=$PG_DATABASE host=$PG_HOST port=$PG_PORT user=$PG_USER password=$PG_PASS" );
  if ( ! $dbconn ) {
    echo "Error connecting to the database !<br> " ;
    printf("%s", pg_errormessage( $dbconn ) );
    exit(); 
  }  

  print("<table class=\"tablephp2\" border=1>\n"); 
  print("<tr><td><b>ID</b></td><td><b>Name</b></td><td><b>Sex</b></td><td><b>Stage</b></td><td><b>Description</b></td></tr>");

  
#get info from lib
  $info_sql= "select distinct on (est.library) est.library,name,sex,stage,lib.description from est,lib where clus_id ~ '^$cluster' and est.library = lib.lib_id;";
  #print "$info_sql<br>";
  $dblib = pg_exec($dbconn, $info_sql);
  $row=0;
  $rowmax=pg_NumRows($dblib);
  while ($row<$rowmax)  {
    $do_lib = pg_Fetch_Object($dblib, $row);
    $id=$do_lib->library;
    $name=$do_lib->name;
    $sex=$do_lib->sex;
    $stage=$do_lib->stage;
    $desc=$do_lib->description;
    if (!preg_match("/./",$stage)){$stage = 'n/a';}
    if (!preg_match("/./",$name)){$name = 'n/a';}
    if (!preg_match("/./",$sex)){$sex = 'n/a';}
    if (!preg_match("/./",$desc)){$desc = 'n/a';}
    print "<tr><td>$id</td><td>$name</td><td>$sex</td><td>$stage</td><td>$desc</td></tr>";
    $row++;
  }
  
print("</table>");  
print "<!-- InstanceEndEditable -->\n";
include('/var/www/html/includes/nembase4_body_lower.ssi');
print "</body>\n";
print "<!-- InstanceEnd --></html>\n";

?>
