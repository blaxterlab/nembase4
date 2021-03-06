<?
print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
print "<html>\n";
print "<head>\n";
print "<title> www.nematodes.org - nembase4 </title>\n";
include('/var/www/html/includes/nembase4_header.ssi');
print "</head>\n";
print "<body>\n";
include('/var/www/html/includes/nembase4_body_upper.ssi');
?>
<form ACTION="/cgi-bin/nembase4_blast.cgi" METHOD=POST NAME="QFormat" target="_blank">

<div align="center"><b><font color="BB0033" size=+4>Nematode Blast Server</font></b><br>
  <center>
  <img SRC="species/Litomosoides_male.jpg" height=151 width=341>
  <center>
</div>
<p align="center">&nbsp;&nbsp;&nbsp; <b>Choose database to search:</b>
  <select name = "DBS" size=3 onChange="redirect(this.options.selectedIndex)">
<option>Individual Nematode Species (Nucleotide)
<option>Groups of Nematodes (Nucleotide)
<!--option>C.elegans Genome (Nucleotide)-->
<!--option>EST Consensi Databases (Nucleotide)-->
<option>Protein Databases
</select>
<p align="center"><a href="http://xyala.cap.ed.ac.uk/docs/blast_program.html#program">Program
</a><select name = "PROGRAM">
<option>blastn</option>
<option>tblastn</option>
<option>tblastx</option>
</select>
<a href="http://xyala.cap.ed.ac.uk/docs/blast_program.html#db">Database</a>
<select name = "DATALIB">
<option value="AAC">Angiostrongylus cantonensis&nbsp;</option>
<option value="ABC">Ancylostoma braziliense&nbsp;</option>
<option value="ACC">Ancylostoma caninum&nbsp;</option>
<option value="AIC">Anisakis simplex&nbsp;</option>
<option value="ALC">Ascaris lumbricoides&nbsp;</option>
<option value="ASC">Ascaris suum&nbsp;</option>
<option value="AYC">Ancylostoma ceylanicum&nbsp;</option>
<option value="BMC">Brugia malayi&nbsp;</option>
<option value="BPC">Brugia pahangi&nbsp;</option>
<option value="BUC">Bursaphelenchus mucronatus&nbsp;</option>
<option value="BXC">Bursaphelenchus xylophilus&nbsp;</option>
<option value="CBC">Caenorhabditis brenneri&nbsp;</option>
<option value="CGC">Caenorhabditis briggsae&nbsp;</option>
<option value="CJC">Caenorhabditis japonica&nbsp;</option>
<option value="CRC">Caenorhabditis remanei&nbsp;</option>
<option value="CSC">Caenorhabditis sp. 5 AC-2008&nbsp;</option>
<option value="DAC">Ditylenchus africanus&nbsp;</option>
<option value="DIC">Dirofilaria immitis&nbsp;</option>
<option value="DVC">Dictyocaulus viviparus&nbsp;</option>
<option value="GMC">Globodera mexicana&nbsp;</option>
<option value="GPC">Globodera pallida&nbsp;</option>
<option value="GRC">Globodera rostochiensis&nbsp;</option>
<option value="HBC">Heterorhabditis bacteriophora&nbsp;</option>
<option value="HCC">Haemonchus contortus&nbsp;</option>
<option value="HGC">Heterodera glycines&nbsp;</option>
<option value="HSC">Heterodera schachtii&nbsp;</option>
<option value="LLC">Loa loa&nbsp;</option>
<option value="LSC">Litomosoides sigmodontis&nbsp;</option>
<option value="MAC">Meloidogyne arenaria&nbsp;</option>
<option value="MCC">Meloidogyne chitwoodi&nbsp;</option>
<option value="MHC">Meloidogyne hapla&nbsp;</option>
<option value="MIC">Meloidogyne incognita&nbsp;</option>
<option value="MJC">Meloidogyne javanica&nbsp;</option>
<option value="MPC">Meloidogyne paranaensis&nbsp;</option>
<option value="NAC">Necator americanus&nbsp;</option>
<option value="NBC">Nippostrongylus brasiliensis&nbsp;</option>
<option value="OCC">Onchocerca ochengi&nbsp;</option>
<option value="ODC">Oesophagostomum dentatum&nbsp;</option>
<option value="OFC">Onchocerca flexuosa&nbsp;</option>
<option value="OOC">Ostertagia ostertagi&nbsp;</option>
<option value="OVC">Onchocerca volvulus&nbsp;</option>
<option value="PAC">Parelaphostrongylus tenuis&nbsp;</option>
<option value="PEC">Pratylenchus penetrans&nbsp;</option>
<option value="PPC">Pristionchus pacificus&nbsp;</option>
<option value="PSC">Panagrolaimus superbus&nbsp;</option>
<option value="PTC">Parastrongyloides trichosuri&nbsp;</option>
<option value="PVC">Pratylenchus vulnus&nbsp;</option>
<option value="PTC">Parastrongyloides trichosuri&nbsp;</option>
<option value="RSC">Radopholus similis&nbsp;</option>
<option value="SCC">Steinernema carpocapsae&nbsp;</option>
<option value="SFC">Steinernema feltiae&nbsp;</option>
<option value="SRC">Strongyloides ratti&nbsp;</option>
<option value="SSC">Strongyloides stercoralis&nbsp;</option>
<option value="TCC">Toxocara canis&nbsp;</option>
<option value="TDC">Teladorsagia circumcincta&nbsp;</option>
<option value="TIC">Trichostrongylus vitrinus&nbsp;</option>
<option value="TLC">Toxascaris leonina&nbsp;</option>
<option value="TMC">Trichuris muris&nbsp;</option>
<option value="TSC">Trichinella spiralis&nbsp;</option>
<option value="TVC">Trichuris vulpis&nbsp;</option>
<option value="WBC">Wuchereria bancrofti&nbsp;</option>
<option value="XIC">Xiphinema index&nbsp;</option>
<option value="ZPC">Zeldia punctata&nbsp;</option>

</select>
<input TYPE="checkbox" NAME="UNGAPPED_ALIGNMENT" VALUE="is_set">
Perform ungapped alignment
<br>&nbsp;
<br><FONT COLOR="#EE0000">Databases updated every Sunday</FONT>
<p align="center">The query sequence is&nbsp;<input TYPE="checkbox" NAME="FILTER" VALUE="L" CHECKED>
<a href="http://xyala.cap.ed.ac.uk/docs/blast_program.html#filt">filtered</a>
for low complexity regions by default.
<br>Enter here your input data as sequence in <a href="http://xyala.cap.ed.ac.uk/docs/blast_program.html#fasta">FASTA</a> format
<input TYPE="submit" VALUE="Search"> 
<br><textarea name="SEQUENCE" rows=6 cols=60>
<? $get_seq = $_GET['seq']; echo "$get_seq"; ?>
</textarea>
<p align="center">
<!-- <input TYPE="checkbox" NAME="DOUBLE_WINDOW" VALUE="IS_SET"  CHECKED>View
results in a separate window.
-->
<br>Alignment view
<select name = "ALIGNMENT_VIEW">
<option value=0 selected> Pairwise
<option value=1> query-anchored showing identities
<option value=2> query-anchored no identities
<option value=3> flat query-anchored, show identities
<option value=4> flat query-anchored, no identities
<option value=5> query-anchored no identities and blunt ends
<option value=6> flat query-anchored, no identities and blunt ends
<option value=7> XML Blast output
<option value=8> tabular
</select>
<br><a href="http://xyala.cap.ed.ac.uk/docs/blast_program.html#exp">Expect</a>
<select name = "EXPECT">
 <option> 0.0001 
 <option> 0.01 
 <option> 1 
 <option selected> 10 
 <option> 100 
 <option> 1000 
</select>
<a href="http://xyala.cap.ed.ac.uk/docs/blast_program.html#desc">Descriptions</a>
<select name = "DESCRIPTIONS">
<option>0
<option>10
<option selected>20
<option>50
<option>100
<option>250
<option>500
</select>
<a href="http://xyala.cap.ed.ac.uk/docs/blast_program.html#ali">Alignments</a>
<select name = "ALIGNMENTS">
<option>0
<option>10
<option selected>20
<option>50
<option>100
<option>250
<option>500
</select>
<br>

<!--<input TYPE="checkbox" NAME="EMAIL" VALUE="IS_SET">&nbsp;&nbsp; Send
reply to the Email address:&nbsp;<input TYPE="text" NAME="PATH" VALUE="" MAXLENGTH="50">
<input TYPE="checkbox" NAME="HTML" VALUE="IS_SET">&nbsp;
In HTML format
//-->
<p align="center"><input TYPE="submit" VALUE="Search">
<input TYPE="RESET" VALUE="Reset">
<br>
You may also want to use the <a
href="http://www.ebi.ac.uk/blast2/parasites.html">parasite blast server
 </a>at the EBI
<hr>

<!--This site has been accessed <img
src="cgi-bin/Count.cgi?ft=4&frgb=990000&df=count.dat&cache=F&expires=0&dd=B&reload=T" align=absmiddle>
times-->

<script>
<!--
/*
Script to alter button settings etc
*/
var groups=document.QFormat.DBS.options.length
var group=new Array(groups)
var blast=new Array(groups)
for (i=0; i<groups; i++)
blast[i]=new Array()
blast[0][0]=blast[1][0]=new Option("blastn","blastn")
blast[0][1]=blast[1][1]=new Option("tblastn","tblastn")
blast[0][2]=blast[1][2]=new Option("tblastx","tblastx")
blast[2][0]=new Option("blastp","blastp")
blast[2][1]=new Option("blastx","blastx")
for (i=0; i<groups; i++)
	group[i]=new Array()
	
	group[0][0]=new Option("Angiostrongylus cantonensis","AAC")
	group[0][1]=new Option("Ancylostoma braziliense","ABC")
	group[0][2]=new Option("Ancylostoma caninum","ACC")
	group[0][3]=new Option("Anisakis simplex","AIC")
	group[0][4]=new Option("Ascaris lumbricoides","ALC")
	group[0][5]=new Option("Ascaris suum","ASC")
	group[0][6]=new Option("Ancylostoma ceylanicum","AYC")
	group[0][7]=new Option("Brugia malayi","BMC")
	group[0][8]=new Option("Brugia pahangi","BPC")
	group[0][9]=new Option("Bursaphelenchus mucronatus","BUC")
	group[0][10]=new Option("Bursaphelenchus xylophilus","BXC")
	group[0][11]=new Option("Caenorhabditis brenneri","CBC")
	group[0][12]=new Option("Caenorhabditis briggsae","CGC")
	group[0][13]=new Option("Caenorhabditis japonica","CJC")
	group[0][14]=new Option("Caenorhabditis remanei","CRC")
	group[0][15]=new Option("Caenorhabditis sp. 5 AC-2008","CSC")
	group[0][16]=new Option("Ditylenchus africanus","DAC")
	group[0][17]=new Option("Dirofilaria immitis","DIC")
	group[0][18]=new Option("Dictyocaulus viviparus","DVC")
	group[0][19]=new Option("Globodera mexicana","GMC")
	group[0][20]=new Option("Globodera pallida","GPC")
	group[0][21]=new Option("Globodera rostochiensis","GRC")
	group[0][22]=new Option("Heterorhabditis bacteriophora","HBC")
	group[0][23]=new Option("Haemonchus contortus","HCC")
	group[0][24]=new Option("Heterodera glycines","HGC")
	group[0][25]=new Option("Heterodera schachtii","HSC")
	group[0][26]=new Option("Loa loa","LLC")
	group[0][27]=new Option("Litomosoides sigmodontis","LSC")
	group[0][28]=new Option("Meloidogyne arenaria","MAC")
	group[0][29]=new Option("Meloidogyne chitwoodi","MCC")
	group[0][30]=new Option("Meloidogyne hapla","MHC")
	group[0][31]=new Option("Meloidogyne incognita","MIC")
	group[0][32]=new Option("Meloidogyne javanica","MJC")
	group[0][33]=new Option("Meloidogyne paranaensis","MPC")
	group[0][34]=new Option("Necator americanus","NAC")
	group[0][35]=new Option("Nippostrongylus brasiliensis","NBC")
	group[0][36]=new Option("Onchocerca ochengi","OCC")
	group[0][37]=new Option("Oesophagostomum dentatum","ODC")
	group[0][38]=new Option("Onchocerca flexuosa","OFC")
	group[0][39]=new Option("Ostertagia ostertagi","OOC")
	group[0][40]=new Option("Onchocerca volvulus","OVC")
	group[0][41]=new Option("Parelaphostrongylus tenuis","PAC")
	group[0][42]=new Option("Pratylenchus penetrans","PEC")
	group[0][43]=new Option("Pristionchus pacificus","PPC")
	group[0][44]=new Option("Panagrolaimus superbus","PSC")
	group[0][45]=new Option("Parastrongyloides trichosuri","PTC")
	group[0][46]=new Option("Pratylenchus vulnus","PVC")
	group[0][47]=new Option("Parastrongyloides trichosuri","PTC")
	group[0][48]=new Option("Radopholus similis","RSC")
	group[0][49]=new Option("Steinernema carpocapsae","SCC")
	group[0][50]=new Option("Steinernema feltiae","SFC")
	group[0][51]=new Option("Strongyloides ratti","SRC")
	group[0][52]=new Option("Strongyloides stercoralis","SSC")
	group[0][53]=new Option("Toxocara canis","TCC")
	group[0][54]=new Option("Teladorsagia circumcincta","TDC")
	group[0][55]=new Option("Trichostrongylus vitrinus","TIC")
	group[0][56]=new Option("Toxascaris leonina","TLC")
	group[0][57]=new Option("Trichuris muris","TMC")
	group[0][58]=new Option("Trichinella spiralis","TSC")
	group[0][59]=new Option("Trichuris vulpis","TVC")
	group[0][60]=new Option("Wuchereria bancrofti","WBC")
	group[0][61]=new Option("Xiphinema index","XIC")
	group[0][62]=new Option("Zeldia punctata","ZPC")
	
	group[1][0]=new Option("Clade I","Nematoda_CladeI_nuc")
	group[1][1]=new Option("Clade III","Nematoda_CladeIII_nuc")
	group[1][2]=new Option("Clade IV","Nematoda_CladeIV_nuc")
	group[1][3]=new Option("Clade V","Nematoda_CladeV_nuc")

	group[2][0]=new Option("Wormpep (C.elegans)","Caenorhabditis_elegans_pro")
	group[2][1]=new Option("All Nematode Proteins","nempep_and_genomic_29_09_09.fa")
	group[2][2]=new Option("Drosophila Proteins","Drosophila_pro")
	group[2][3]=new Option("Uniprot (formerly SwissProt/trEMBL)","uniprot_all")

	var temp=document.QFormat.DATALIB
	var temp1=document.QFormat.PROGRAM
	function redirect(x){
		if(x<0)
		x=0
		for (m=temp.options.length-1;m>0;m--)
		temp.options[m]=null
		for (m=temp1.options.length-1;m>0;m--)
		temp1.options[m]=null
		for (i=0;i<blast[x].length;i++){
			temp1.options[i]=new Option(blast[x][i].text,blast[x][i].value)
		}
		for (i=0;i<group[x].length;i++){
			temp.options[i]=new Option(group[x][i].text,group[x][i].value)
		}
		temp.options[0].selected=true
		temp1.options[0].selected=true
	}
	function go(){
		location=temp.options[temp.selectedIndex].value
	}
//-->
</script>

</form>
	    <!-- InstanceEndEditable -->

<!--#include virtual="/includes/nembase4_body_lower.ssi" -->
</body>
<?
print "<!-- InstanceEndEditable -->\n";
include('/var/www/html/includes/nembase4_body_lower.ssi');
print "</body>\n";
print "<!-- InstanceEnd --></html>\n";
?>
