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
			$inf = $_GET['inf'];
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
			$value = $_GET['value'];
			#get singleton tribes
			if ($value == 'single'){			
				$sqlcom="select distinct(tribe) from node2tribe where inf = $inf and node = $node and singleton > 0;";	
				$dbres = pg_exec($dbconn, $sqlcom );
				if ( ! $dbres ) {echo "Error : " + pg_errormessage( $dbconn ); exit();} 
				$row=0;
				$inf10 = $inf/10;
				$rowmax=pg_NumRows($dbres);
				print "<br><table><tr>";
				print "<td align=center><h3>Node $node returns $rowmax singleton tribes for inflation $inf</h3></td></tr><tr><td>";			
				while ($row<$rowmax)  {
					$do = pg_Fetch_Object($dbres, $row);
					$tribe=$do->tribe;
					print "<a href=\"tribe.php?inf=$inf10&tribe=$tribe\">$tribe</a> ";
					$row++;
					}
				print "</td></tr></table>";
			#get species specific tribes
			}elseif ($value == 'specific'){
				$sqlcom="select distinct(tribe) from node2tribe where inf = $inf and node = $node and species_specific>0;";
				$dbres = pg_exec($dbconn, $sqlcom );
				if ( ! $dbres ) {echo "Error : " + pg_errormessage( $dbconn ); exit();} 
				$row=0;
				$inf10 = $inf/10;
				$rowmax=pg_NumRows($dbres);
				print "<br><table><tr>";
				print "<td align=center><h3>Node $node returns $rowmax species specific tribes for inflation $inf10</h3></td></tr><tr><td>";
				while ($row<$rowmax)  {
					$do = pg_Fetch_Object($dbres, $row);
					$tribe=$do->tribe;
					print "<a href=\"tribe.php?inf=$inf10&tribe=$tribe\">$tribe</a> ";
					$row++;
					}
				print "</td></tr></table>";
			#get all tribes
			}else{
				$flag = $_GET['flag'];#get flag
				#$cut = $_GET['cut']; #get evalue cutoff
				$cut = '1e-05'; #get evalue cutoff
				$num_nem = $_GET['nem']; #get number of non-nematode hits from tribe_tree_results.php
				$order = $_GET['order'];
				if ($order == 'members'){
					$order = 'members desc';
				}elseif ($order == 'non_nem'){
					$order = 'non_nematode desc';
				}elseif ($order == 'eval'){
					$order = 'eval';
				}elseif ($order == 'top_hit'){
					$order = 'top_hit';
				}else{
					$order = 'tribe';
				}
					
				if ($flag == 0){
					$sqlcom="select * from node2tribe where inf = $inf and node = $node and non_nematode < 1 order by members desc;";
					#print $sqlcom;
				}elseif ($flag == 1){
					#$sqlcom = "select node2tribe.tribe, node2tribe.members, node2tribe.non_nematode, tribe.eval, tribe.top_hit from node2tribe, tribe where node = $node and inf=$inf and node2tribe.tribe=tribe.inf$inf and node2tribe.non_nematode>0 and eval<$cut group by tribe.eval,node2tribe.tribe,node2tribe.members,node2tribe.non_nematode,tribe.top_hit order by node2tribe.tribe,tribe.eval;";
					$sqlcom = "select node2tribe.tribe, node2tribe.members, node2tribe.non_nematode, tribe.eval, tribe.top_hit from node2tribe, tribe where node = $node and inf=$inf and node2tribe.tribe=tribe.inf$inf and node2tribe.non_nematode>0 and eval<$cut group by tribe.eval,node2tribe.tribe,node2tribe.members,node2tribe.non_nematode,tribe.top_hit order by $order;";
					#print $sqlcom;
				}
				$dbres = pg_exec($dbconn, $sqlcom );
				if ( ! $dbres ) {echo "Error : " + pg_errormessage( $dbconn ); exit();} 
				$row=0;
				$inf10 = $inf/10;
				$rowmax=pg_NumRows($dbres);
				if ($flag == 0){
					$table .= "<table><tr><td align=center><h3>Node $node returns $num_nem tribes for inflation $inf10 which are nematode specific</h3></td></tr></table>";
					$table .= "<table border=1><tr><td>Tribe</td><td>Number of members</td></tr>";
					$csv_hdr = "Tribe, Number of Members";
				}else{
					print "<table><tr><td align=center><h3>Node $node returns $num_nem tribes for inflation $inf10 which have members which are non nematode specific</h3></td></tr></table>";
					print "<table border=1><tr><td><a href=\"tribe_tree_tribes.php?inf=$inf&node=$node&flag=$flag&nem=$num_nem&order=tribe\">Tribe</a></td><td><a href=\"tribe_tree_tribes.php?inf=$inf&node=$node&flag=$flag&nem=$num_nem&order=members\">Number of members</a></td><td><a href=\"tribe_tree_tribes.php?inf=$inf&node=$node&flag=$flag&nem=$num_nem&order=non_nem\">Members which hit non nematode</a></td><td><a href=\"tribe_tree_tribes.php?inf=$inf&node=$node&flag=$flag&nem=$num_nem&order=eval\">Minimum evalue</a></td><td><a href=\"tribe_tree_tribes.php?inf=$inf&node=$node&flag=$flag&nem=$num_nem&order=top_hit\">Top hit</a></td></td>";
					$csv_hdr = "Tribe, Number of members, Members which hit non nematode, Minimum evalue, Top hit";
				}	
				$tribe_array=array();
				while ($row<$rowmax)  {
					$do = pg_Fetch_Object($dbres, $row);
					$tribe=$do->tribe;
					$pep=$do->members;
					$sum=$do->non_nematode;
					$eval=$do->eval;
					$id=$do->id;
					$hit=$do->top_hit;	
					#create an array of tribe IDs and check if one has already been printed
					if (!in_array($tribe, $tribe_array)) {			
						$tribe_array[]=$tribe;
						if ($tribe != $old_tribe){ #check for same tribe
							if ($flag == 0){ 
								$table .= "<tr><td align=center><a href=\"tribe.php?inf=$inf10&tribe=$tribe\">$tribe</a></td><td align=center>$pep</td></tr>";
								$csv_output .= $tribe.", ".$pep."\n";
							}
							if ($flag == 1){ 
								$table .= "<tr><td align=center><a href=\"tribe.php?inf=$inf10&tribe=$tribe\">$tribe</a></td><td align=center>$pep</td><td align=center>$sum</td><td align=center>$eval</td><td><a href = \"http://www.ncbi.nlm.nih.gov/protein/$hit\">$hit</a></td></tr>";
								$csv_output .= $tribe.", ".$pep.", ".$sum.", ".$eval.", ".$hit."\n";
							}					
							$old_tribe=$tribe;
						}
					}
					$row++;
				}	
				?>
				<center>
				<form name="export" action="csv_export.php" method="post">
				<input type="submit" value="Export table to CSV">
				<input type="hidden" value="<? echo $csv_hdr; ?>" name="csv_hdr">
				<input type="hidden" value="<? echo $csv_output; ?>" name="csv_output">
				<input type="hidden" value="<? echo "tribe_tree2_results"; ?>" name="filename">
				</form>
				</center>
				<?
				print "$table</td></tr></table>";
			}			
			?>			
			<td align="center">			
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
