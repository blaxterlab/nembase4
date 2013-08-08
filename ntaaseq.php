<?php
//this script translates DNA in both directions
//nembase3 September 2007
//Ann Hedley, University of Edinburgh


$codonlist = array (
'TTT','TTC','TTA','TTG','TCT','TCC','TCA','TCG','TAT','TAC','TAA','TAG',
'TGT','TGC','TGA','TGG','CTT','CTC','CTA','CTG','CCT','CCC','CCA','CCG',
'CAT','CAC','CAA','CAG','CGT','CGC','CGA','CGG','ATT','ATC','ATA','ATG',
'ACT','ACC','ACA','ACG','AAT','AAC','AAA','AAG','AGT','AGC','AGA','AGG',
'GTT','GTC','GTA','GTG','GCT','GCC','GCA','GCG','GAT','GAC','GAA','GAG',
'GGT','GGC','GGA','GGG','NNN',
);


$Genetic_Codes = array
( 'F','F','L','L','S','S','S','S','Y','Y','$','$','C','C','$','W','L','L','L',
'L','P','P','P','P','H','H','Q','Q','R','R','R','R','I','I','I','M','T','T','T',
'T','N','N','K','K','S','S','R','R','V','V','V','V','A','A','A','A','D','D',
'E','E','G','G','G','G', );

# Do forward frame translation first
# Split into groups of 3

$l=strlen($dnaseq);
$con = preg_split('//', $dnaseq, -1, PREG_SPLIT_NO_EMPTY);

for($i=0;$i<$l;$i++)
 {
 if(strcasecmp($con[$i],'A')==0) { $revcon[$i]='T'; }
 elseif(strcasecmp($con[$i],'C')==0) { $revcon[$i]='G'; }
 elseif(strcasecmp($con[$i],'G')==0) { $revcon[$i]='C'; }
 elseif(strcasecmp($con[$i],'T')==0) { $revcon[$i]='A'; }
 elseif(strcasecmp($con[$i],'N')==0) { $revcon[$i]='N'; }
 elseif(strcasecmp($con[$i],'X')==0) { $revcon[$i]='X'; }
 elseif(strcasecmp($con[$i],' ')==0) { $revcon[$i]=' '; }
 }
    
$x=0;
for($i=1;$i<$l+1;$i++)
 {
 $seq[0][$x].=$con[$i-1];
 $seq[1][$x].=$con[$i];
 $seq[2][$x].=$con[$i+1];
 $seq[3][$x].=$revcon[$i-1];
 $seq[4][$x].=$revcon[$i];
 $seq[5][$x].=$revcon[$i+1];
 if($i%3 ==0) { $x++; }
 }

for($i=0;$i<$x;$i++)
 {
 $seq[3][$i]=strrev($seq[3][$i]);
 $seq[4][$i]=strrev($seq[4][$i]);
 $seq[5][$i]=strrev($seq[5][$i]);
 } 
 
 
$spacer="..";
$oseq[1]=".";
$oseq[2]="..";
$oseq[3]="..";
$oseq[4]="...";
$oseq[5]="....";
for($i=0;$i<$x;$i++)
 {
 for($k=0;$k<6;$k++)
  {
  $flag=0;
  for($j=0;$j<64;$j++)
   {
   if($codonlist[$j]==$seq[$k][$i])
    { $flag=1; $oseq[$k].=$Genetic_Codes[$j].$spacer; last; }
   }
  if($flag==0)
    { $oseq[$k].="X".$spacer; }
  }
 }

for($k=0;$k<6;$k++)
 { $outseq.=$oseq[$k].'U'; }

?>
