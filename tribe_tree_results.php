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
<div class="mainTitle">Identification of NEMBASE4 tribes specific to node or species</div><br>
<div class="mainMed"><center>Results for node or species of interest</center></div><p>
<table class="tablephp1">
	<tr>
		<td>
			<table class="tablephp2">
				<tr><td align="center">
						
			<?php
			$node = $_GET['node'];
			#$inf = $_POST['inf'];
			//open the database
			$PG_HOST="localhost";
			$PG_PORT=5432;
			$PG_DATABASE2="nemdb4";
			$PG_USER="webuser";
			$PG_PASS="";
			$dbconn=pg_connect( "dbname=$PG_DATABASE2 host=$PG_HOST port=$PG_PORT user=$PG_USER password=$PG_PASS" );
			if ( ! $dbconn ) {
				echo "Error connecting to the database !<br> " ;
			printf("%s", pg_errormessage( $dbconn ) );
			exit(); 
			}
			
			#catch species with no protein predictions			
			if ($node == "BPC" || $node == "GMC"){
				print "<tr><td>Unfortunately $node has no protein predictions and therefore no tribes</td></tr>";
				}
			#catch nodes
			elseif ($node < 46){
				$arr = array(11,15,20,25,30,35,40,45,50);
				print "<h3>Tribe results for node $node</h3>";
				$table .= "<table border = 1 CELLPADDING=3 CELLSPACING=3 ALIGN=center>";
				$csv_hdr = "Inflation,Total number tribes,Nematode specific, Non nematode specific";
				$table .= "<tr><td><b>Inflation*</b></td><td><b>Total number tribes</b></td><td><b>Nematode specific</b></td><td><b>Non nematode specific</b></td></tr>";	
				foreach ($arr as &$value) {		
					$value10=$value/10;
					$sqlcom1 = "select tribes,non_nematode from node_stats where node = $node and inf=$value;";
					#print "$sqlcom1";
					$dbres1 = pg_exec($dbconn, $sqlcom1 );
					if ( ! $dbres1 ) {echo "Error : " + pg_errormessage( $dbconn ); exit();} 
					$rowmax1=pg_NumRows($dbres1);
					$do = pg_Fetch_Object($dbres1, 0);
					$count_nem = $do->non_nematode;	
					$tribes = $do->tribes;		
					$nem_specific = $tribes - $count_nem;
					$csv_output .= $value10.", ".$tribes.", ".$nem_specific.", ".$count_nem."\n";
					if ($nem_specific > 0){
						$nem_specific = "<a href=\"tribe_tree_tribes.php?inf=$value&node=$node&flag=0&nem=$nem_specific\">$nem_specific</a>";
					}if ($count_nem > 0){
						$count_nem = "<a href=\"tribe_tree_tribes.php?inf=$value&node=$node&flag=1&nem=$count_nem\">$count_nem</a>";
					}
						
					$table .= "<tr><td align=center>$value10</td><td align=center>$tribes</td><td align=center>$nem_specific</td><td align=center>$count_nem</td></tr>";					
				}$table .= "</table>";
			#catch good species
			}elseif ($node > 45){
				$sqlcom0="select species.spec_id,species.species,species,species.clade from tribe_node,species where species.spec_id = tribe_node.description and tribe_node.node = $node";
				#print "$sqlcom0";
				$dbres0 = pg_exec($dbconn, $sqlcom0 );
				$do0 = pg_Fetch_Object($dbres0, 0);
				$species=$do0->species;
				$clade=$do0->clade;
				$inf10 = $inf*10;
				print "<h3>$species (clade $clade)</h3>";
				#get info for each inflation value
				$arr = array(11,15,20,25,30,35,40,45,50);
				$table .= "<table border = 1 CELLPADDING=3 CELLSPACING=3 ALIGN=center>";
				$csv_hdr = "Inflation,Total number tribes,Singletons, Species Specific Tribes";
				$table .= "<tr><td><b>Inflation*</b></td><td><b>Total number tribes</b></td><td><b>Singletons</b></td><td><b>Species Specific Tribes</b></td></tr>";
				foreach ($arr as &$value) {			
					$sqlcom1 = "select tribes,singleton,species_specific from node_stats where node = $node and inf=$value;";
					#print "$sqlcom1";
					$dbres1 = pg_exec($dbconn, $sqlcom1 );
					$do1 = pg_Fetch_Object($dbres1, 0);
					$tribes=$do1->tribes;
					$single=$do1->singleton;
					$specific=$do1->species_specific;
					$value10=$value/10;
					$csv_output .= $value10.", ".$tribes.", ".$single.", ".$specific."\n";
					if ($single > 0){
						$single = "<a href=\"tribe_tree_tribes.php?inf=$value&node=$node&value=single\">$single</a>";
					}if ($specific > 0){
						$specific = "<a href=\"tribe_tree_tribes.php?inf=$value&node=$node&value=specific\">$specific</a>";
					}
					$table .= "<tr><td align=center>$value10</td><td align = center>$tribes</td><td align = center>$single</td>";
					$table .= "<td align = center>$specific</td></tr>";
					
				}
				$table .= "</table>";
			}				
			?>		
			<h5>*Parameter used when generating tribes using MCL. Ranges here from from 1.1 (produces fewer cluster but with more members) to 5.0 (produces more clusters with fewer members).</h5>
			<center>
				<form name="export" action="csv_export.php" method="post">
				<input type="submit" value="Export table to CSV">
				<input type="hidden" value="<? echo $csv_hdr; ?>" name="csv_hdr">
				<input type="hidden" value="<? echo $csv_output; ?>" name="csv_output">
				<input type="hidden" value="<? echo "tribe_tree1_results"; ?>" name="filename">
				</form>
			</center>
			<? print $table; ?>
			</td></tr>
			
			</table>
		</td>
	</tr>
	
</table>



<!-- InstanceEndEditable -->
<? include ("nembase4_body_lower.ssi"); ?>
</body>
<!-- InstanceEnd -->
</html>
