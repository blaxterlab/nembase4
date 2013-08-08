<?php
#make hash of species name and 3 letter code for image
$species=array('Ancylostoma caninum' => 'ACC',
'Ancylostoma ceylanicum' => 'AYC',
'Ascaris lumbricoides' => 'ALC',
'Ascaris suum' => 'ASC',
'Brugia malayi' => 'BMC',
'Dirofilaria immitis' => 'DIC',
'Globodera pallida' => 'GPC',
'Globodera rostochiensis' => 'GRC',
'Heterodora glycines' => 'HGC',
'Heterodora schachtii' => 'HSC',
'Haemonchus contortus' => 'HCC',
'Litomosioides sigmodontis' => 'LSC',
'Meloidogyne arenaria' => 'MAC',
'Meloidogyne chitwoodi' => 'MCC',
'Meloidogyne hapla' => 'MHC',
'Meloidogyne incognita' => 'MIC',
'Meloidogyne javanica' => 'MJC',
'Meloidogyne paranaensis' => 'MPC',
'Necator americanus' => 'NAC',
'Nippostrongylus brasiliensis' => 'NBC',
'Onchocerca volvulus' => 'OVC',
'Ostertagia ostertagi' => 'OOC',
'Parastrongyloides trichosuri' => 'PTC',
'Pratylenchus penetrans' => 'PEC',
'Pratylenchus vulnus' => 'PVC',
'Pristionchus pacificus' => 'PPC',
'Radopholus similis' => 'PPC',
'Strongyloides ratti' => 'SRC',
'Strongyloides stercoralis' => 'SSC',
'Teladorsagia circumcincta' => 'TDC',
'Toxocara canis' => 'TCC',
'Trichinella spiralis' => 'TSC',
'Trichuris muris' => 'TMC',
'Trichuris vulpis' => 'TVC',
'Wuchereria bancrofti' => 'WBC',
'Xiphinema index' => 'XIC',
'Zeldia punctata' => 'ZPC');

#open the java text file
$handle = @fopen ("stats.txt", "r");
#if the file opened
if ($handle) {
	while (!feof($handle)) {  #while there are lines in the file
		$buffer = fgets($handle, 4096);	#read one line at a time
		if (preg_match("/^\^\^/",$buffer) or preg_match("/Current NEMBASE Statistics/",$buffer))   {} #if it's java formating do nothing
		else if (preg_match("/last update\s+:\s+(\d+\/\d+)/",$buffer,$match)) {			#if it's the end of data for that species i.e. "last update"
			$table_row.="<td class=\"tablephp21\">{$match[1]}</td></tr>";								#store last data cell info
			print("$table_row\n");																		#print the entire table row
			$table_row="";																					#empty table row for next species
		}
		else if (preg_match("/.*?:\s+(\d+)/",$buffer,$match)) {$table_row.="<td class=\"tablephp21\">{$match[1]}&nbsp&nbsp&nbsp&nbsp</td>";}	#if it's data store info
		else if (preg_match("/^(\w+\s+\w+)/",$buffer,$match)) {								#if it's species name
			$tmp=$match[1];
			if (file_exists("/var/www/html/nemdb3/species/$species[$tmp].jpg")) {$table_row.="<tr><td class=\"tablephp21\"><a href=\"/nemdb3/species/$species[$tmp].shtml\"><img SRC=\"/nemdb3/species/$species[$tmp].jpg\" WIDTH=70 HEIGHT=70></a></td><td class=\"tablephp21\">{$match[1]}</td>";}	#if there's an image for this species
			else {$table_row.="<tr>&nbsp<td class=\"tablephp21\"></td><td class=\"tablephp21\">{$match[1]}</td>";}
		}
	}
	fclose($handle);
}
?>
