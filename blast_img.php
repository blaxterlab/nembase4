<?php

header("content-type: image/png");
$filename=$_GET["outfile"];
$width=0;
$queryname="Query";
$index=-1;
$fp=fopen ("$filename","r");

while(!feof ($fp)) 
 {
 $line = chop(fgets($fp, 4096));
 $strand = "+";

 # Get query seq name and length

 if(preg_match("/Query=\s*(\w+)/",$line,$match)) { $queryname=$match[1]; }

 if (preg_match("/(\d*[,]{0,1}\d+)\s+letters/",$line,$match))
  {
  $width=$match[1];
  #ereg_replace (",", "", $width);
  $width = str_replace(',', '',$width);
  }

 # Get match sequence ID from the ALIGNMENTS part
 
 if(preg_match("/^>gi\|\w+\|\w+\|(\w+)/",$line,$match)) 
  { 
  if($index > -1) { $numscore[$index]=$scoreidx; }
  $index++;
  $seqid[$index]=$match[1];
  $scoreidx=-1;
  } 
 else if(preg_match("/^>(Msm_.+)\s/",$line,$match)) 
  { 
  if($index > -1) { $numscore[$index]=$scoreidx; }
  $index++;
  $seqid[$index]=$match[1];
  $scoreidx=-1;
  }
 else if(preg_match("/^>([A-Za-z0-9.]+)/",$line,$match)) 
  { 
  if($index > -1) { $numscore[$index]=$scoreidx; }
  $index++;
  $seqid[$index]=$match[1];
  $scoreidx=-1;
  }

 if(preg_match("/^Matrix:/",$line,$match)) 
  { 
  $numscore[$index]=$scoreidx; 
  $index++;
  }

 # Picks up the score of each HSP belonging to a given seqID
 # Defines $ARRAY{$seqid} from the ALIGNMENTS part

 if (preg_match("/^\s*Score\s+\=\s+(\d+)[.]{0,}\d*/",$line,$match))
  {
  $scoreidx++;
  $hspscore[$index][$scoreidx]=$match[1];
  $sflag=0;
  }	
	
# Builds a data structure for each HSP belonging to sequence SeqId 
# $seqid with HSP score $hspscore
	
  if (preg_match("/Query\:\s+(\d+)\s+.+\s+(\d+)/",$line,$match))
   {
   if($match[1] < $match[2])
    {
    $st=$match[1];
    $en=$match[2];
    }
   else
    {
    $en=$match[1];
    $st=$match[2];
    }

   if($sflag==0)
     {   $start[$index][$scoreidx]=$st; $sflag=1;  }
   if($en > $end[$index][$scoreidx]) $end[$index][$scoreidx]=$en;
   if($st < $start[$index][$scoreidx]) $start[$index][$scoreidx]=$st;

# $sd=$start[$index][$scoreidx];
# $ed=$end[$index][$scoreidx];
# print "$seqid[$index] $sd,$ed - $st,$en<br>";


   }
	
# Counts the number of sequences to display from the ALIGNMENTS
	
    }				# end while
    
    # Begins writing the image
    
    $eigth=($index*10+150);
    $bottomeigth=($index*10+80);
    $bareigth=($index*10+100);
    $image = ImageCreate(632,$eigth);
    $white = ImageColorAllocate($image,255, 255, 255);        
    $red = ImageColorAllocate($image,255, 0, 0);      
    $black = ImageColorAllocate($image,0, 0, 0);
    $blue = ImageColorAllocate($image,0,0,255);
    $green = ImageColorAllocate($image,0,205,25);
    $yellow = ImageColorAllocate($image,247,174,0);
    $grey = ImageColorAllocate($image,200,200,200);
    $violet = ImageColorAllocate($image,230,50,230);
    ImageRectangle($image,0,0,631,$eigth-1,$black);
    
    ## First, draw and scale up the query sequence
    ## Calculate the number of intervals, scale up on query seq length
    ## and draw the interval lines
    
    if ($width <= 500) {
	$bases=10;
	$display=0;
	$unit="";
	$interval=10;
    } else if ($width <= 10000) {
	$bases=100;
	$display=0;
	$unit="";
	$interval=100;
    } else {
	$bases=1000;
	$display=0;
	$unit="K";
	$interval=1;
    }
    # The number of intervals
    
    $intervals = $width/$bases;
    $modulus = ($width%$bases);
    if($intervals < 1) { $intervals=1; }
    
    # The display space (in points) corresponding to each interval
    $space = 500/$intervals;
    if ($modulus > 500) {
	$realintervals = ($intervals+1);
    } else {
	$realintervals = $intervals;
    }
    $reallength = ($space*$realintervals);
    
    ### Draw a filled rectangle (TOP AND BOTTOM) corresponding to the query seq

    ImageString($image,2,10,7,$queryname,$red);
    ImageFilledRectangle($image,110,10,$reallength+111,15,$red);
    ImageString($image,2,10,$bottomeigth-3,$queryname,$red);
    ImageFilledRectangle($image,110,$bottomeigth,$reallength+111,$bottomeigth+5,$red);
    # The starting point
    $x=110;

    # Draw the vertical bars: 
    # Write the number every 100 or 500 or 5000 bases; 
    # also paints red the corresponding mark
    
    for ($i=0;$i<=$intervals;$i++) {
	$realdisplay=$display.$unit;
	if (($i%5)==0) {
	ImageLine($image,$x+1,23,$x+1,15,$black);
	ImageLine($image,$x+1,$bottomeigth-8,$x+1,$bottomeigth,$black);
	Imagestring($image,2,$x,$bottomeigth-21,$realdisplay,$black);
        Imagestring($image,2,$x,22,$realdisplay,$black);
	} else {
	ImageLine($image,$x+1,19,$x+1,15,$black);
	ImageLine($image,$x+1,$bottomeigth-4,$x+1,$bottomeigth,$black);
	}
	$x=$x+$space;
	$display=$display+$interval;
    }
    
    ### OK, the hard stuff. Extrapolate the min - max interval
    ### for each HSP from the data structure built above
    ### We need to extract both seq ID and score for each HSP
    ### Starting vertical coordinate is 40 points
    
    $y_start=40;

    ## The picture can be used as an image map
   
   
    ## Now sort out the alignments

    
 for($i=0;$i<$index;$i++)
  {
 $y_start2=$y_start+10;

 ## For multiple alignments check the overlap
$seg=0;
$left[$seg]=$start[$i][0];
$right[$seg]=$end[$i][0];
$score[$seg]=$hspscore[$i][0];

$allleft=$left[$seg];
$allright=$right[$seg];
$seg++;

# print "$seqid[$i] $j; $numscore[$i] - $allleft ,$allright<br>";

 for($j=1;$j<$numscore[$i]+1;$j++)
  {

  if($start[$i][$j] < $allleft) { $allleft=$start[$i][$j]; }
  if($end[$i][$j] > $allright) { $allright=$end[$i][$j]; }

  $newflag=0;
  for($k=0;$k<$seg;$k++)
   {
   if($start[$i][$j] < $left[$k] && $end[$i][$j] >= $left[$k])
    { 
    $left[$k]=$start[$i][$j];
    $newflag=1;
    if($hspscore[$i][$j] > $score[$k])
     { $score[$k]=$hspscore[$i][$j]; }
    }
   if($start[$i][$j] <= $right[$k] && $end[$i][$j] > $right[$k])
    { 
    $right[$k]=$end[$i][$j];
    $newflag=1;
    if($hspscore[$i][$j] > $score[$k])
     { $score[$k]=$hspscore[$i][$j]; }
    }
   }    
   if($newflag==0)
    { 
    $left[$seg]=$start[$i][$j];
    $right[$seg]=$end[$i][$j];
    $score[$seg]=$hspscore[$i][$j];
    $seg++;
    } 
# print "$seqid[$i] $j; $numscore[$i] - $allleft .. $left[$seg],$allright .. $right[$seg]<br>";
 
 
  }

ImageString($image,2,10,$y_start-1,$seqid[$i],$black);

if($numscore[$i] >0)
 {
$allleft*=500/$width;
$allright*=500/$width;
ImageFilledRectangle($image,$allleft+110,$y_start+4,$allright+111,$y_start+9,$grey);
 }


for($l=0;$l<$seg;$l++)
 {
## Rescale left and right
$leftx=$left[$l];
$rightx=$right[$l];

$leftx*=500/$width;
$rightx*=500/$width;

## Draw a filled rectangle corresponding to the HSP; ##
 ## color corresponds to the score ##
 $hspcolor=$red;
 if($score[$l] < 200) { $hspcolor=$yellow; }
 if($score[$l] < 150) { $hspcolor=$green; }
 if($score[$l] < 100) { $hspcolor=$blue; }
 if($score[$l] < 50) { $hspcolor=$black; }
 ImageFilledRectangle($image,$leftx+110,$y_start+4,$rightx+111,$y_start+9,$hspcolor);
 }			     # end foreach score 
 $y_start=$y_start+10;
}				# END FOREACH SEQ ID


# Draw the BOTTOM reference score color bar

$interval = $reallength/5;
ImageString($image,2,140,$bareigth+20,"S<=50",$black);
ImageFilledRectangle($image,110,$bareigth,$interval+110,$bareigth+10,$black);
ImageString($image,2,$interval+120,$bareigth+20,"50<S<=100",$black);
ImageFilledRectangle($image,$interval+110,$bareigth,2*$interval+110,$bareigth+10,$blue);
ImageString($image,2,2*$interval+120,$bareigth+20,"100<S<=150",$black);
ImageFilledRectangle($image,2*$interval+110,$bareigth,3*$interval+110,$bareigth+10,$green);
ImageString($image,2,3*$interval+120,$bareigth+20,"150<S<=200",$black);
ImageFilledRectangle($image,3*$interval+110,$bareigth,4*$interval+110,$bareigth+10,$yellow);
ImageString($image,2,4*$interval+130,$bareigth+20,"S>200",$black);
ImageFilledRectangle($image,4*$interval+110,$bareigth,5*$interval+110,$bareigth+10,$red);

#fwrite($fp, "$mapstr");
fclose ($fp);

ImagePNG($image);
ImageDestroy($image);


?>

