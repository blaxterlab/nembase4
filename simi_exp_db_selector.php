<?php
	// Find which organisms are available for phyloview, define variables used with the database
	//usually this goes in an include file
	$PG_HOST="localhost";
	$PG_PORT=5432;
	$PG_DATABASE="nemdb3";
	$PG_USER="webuser";
	$PG_PASS="";

	//open the database
	$dbconn=pg_connect( "dbname=$PG_DATABASE host=$PG_HOST port=$PG_PORT user=$PG_USER password=$PG_PASS" );
	if ( ! $dbconn ) {
	    echo "Error connecting to the database !<br> " ;
	    printf("%s", pg_errormessage( $dbconn ) );
	    exit(); 
	}

	$sqlcom="select * from sim_dblist;";
	$dbres = pg_exec($dbconn, $sqlcom );
	if ( ! $dbres ) {
	    echo "Error : " + pg_errormessage( $dbconn );
	    exit(); 
	}
	$row=0;
	$rowmax=pg_NumRows($dbres);
	$s='';
	while($row<$rowmax) {
		$do_venndbs=pg_Fetch_Object($dbres, $row);
		$vdb=$do_venndbs->dbid;
		$vdbname=$do_venndbs->dbname;
		$s.="<option value=\"$vdb\">$vdbname</option>\n";
		$row++;
	}
	print "<select size=1 name=\"EX1\" onChange=\"setValue(this)\">$s</select>....Node1<br>";
	print "<select size=1 name=\"EX2\" onChange=\"resubmit(this)\">$s</select>....Node2<br>";
	print "<select size=1 name=\"EX3\">$s</select>....Node3<br>";
?>
