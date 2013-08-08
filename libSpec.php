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
<div class="mainTitle">Identification of NEMBASE4 clusters by library-specific expression levels</div><br>
<div class="mainMed"><center>(1) select the species to search; (2) then select the numbers of ESTs per library</center></div><p>
<table class="tablephp1">
	<tr>
		<td>
			<table class="tablephp2">
				<tr><td align="center">
					<? include ("species_db_queries.php"); ?>
				</td></tr>
				<tr><td><br></td></tr>
					<form method="post" action="../nembase4/result_library.php">
						<? include ("library_list.php"); ?>
						<table>
							<tr>
								<td><input TYPE="submit" VALUE="Search"></td>
								<td><input TYPE="reset" VALUE="Reset"></td>
							</tr>
						<table>
					</form>
			</table>
		</td>
	</tr>
</table>

<!-- InstanceEndEditable -->
<? include ("nembase4_body_lower.ssi"); ?>
</body>
<!-- InstanceEnd -->
</html>
