<?php
//this script produces a marked-up protein sequence
//nembase3 September 2007
//Ann Hedley, University of Edinburgh

session_start();
#$doms = $_SESSION["doms"];	#this should get the array but it doesn't work with IMG tags hence the string
$dom_string = $_GET["dom_string"];	
	$dom=split(";",$dom_string);
	$i=0;
	foreach ($dom as $item) {$doms[$i]=split(":",$item);$i++;}
	array_pop($doms);
$protein=$_GET["protein"];
$plength=$_GET["plength"];
$dnalength=$_GET["dnalength"];
$tr_length=$_GET["tr_length"];
$conf_start=$_GET["conf_start"];
$conf_end=$_GET["conf_end"];
$dir=$_GET["dir"];
$p_start=$_GET["p_start"];
$p_end=$_GET["p_end"];
$imageH=$_GET["height"]; 
$imageW = 600;
$varh=45;
header("content-type: image/png");

####the image stuff#####################################################################################################
if ($p_end > $dnalength) { $scale=580/$p_end; }####set the scale for the image depending on whether the p_end or dnalength is longer
else {$scale=580/$dnalength;}
$image = ImageCreate($imageW+4,$imageH+4);

#set up some colours
$background_color = ImageColorAllocate($image,0xF5, 0xF5, 0xF5);	#nearly white
$border_color = ImageColorAllocate($image,0x6B, 0x6B, 0x6B);		#dk grey
$lcolor = ImageColorAllocate($image,0,0,0);				#black
$greycolor = ImageColorAllocate($image,80,80,80);		#dk grey
$dcolor = ImageColorAllocate($image,255,0,0);			#red 
$grcolor = ImageColorAllocate($image,160,160,160);		#lt grey
$qcolor = ImageColorAllocate($image,150,150,10);		#kinda gold
$bcolor = ImageColorAllocate($image,255,255,255);		#white
$ncolor = ImageColorAllocate($image,0,0,255);			#royal blue
$ucolor = ImageColorAllocate($image,660,660,990);		#royal blue
$ccolor = ImageColorAllocate($image,0,0,990);			#royal blue
$color[0] = ImageColorAllocate($image,0x99,0x32,0xCC);	#purple
$color[1] = ImageColorAllocate($image,0xD1,0x5F,0xEE);	#purple
$color[2] = ImageColorAllocate($image,0xCD,0x96,0xCD);	#purple
$color[3] = ImageColorAllocate($image,0xEE,0xAE,0xEE);	#purple
$color[4] = ImageColorAllocate($image,0xFF,0xE1,0xFF);	#purple

ImageFilledRectangle($image,0,0,$imageW+4,$imageH+4,$border_color);			####draw the dk grey
ImageFilledRectangle($image,2,2,$imageW,$imageH,$background_color);			####draw background nearly white
if($dnalength > 20)   { $bar=10; }
if($dnalength > 100)  { $bar=50; }
if($dnalength > 500)  { $bar=100; }
if($dnalength > 1000) { $bar=200; }
if($dnalength > 2500) { $bar=500; }

if($p_end > $dnalength) {	#if prot extend past end of EST draw a grey EST bar thus you get a grey length of 'EST assumed from similarity hit'
	ImageFilledRectangle($image,10,20,10+$scale*$p_end,22,$grcolor);
	for($i=0;$i<$p_end;$i+=$bar) {	#and add the ticks
		ImageFilledRectangle($image,($scale*$i)+10,15,($scale*$i)+12,20,$grcolor);
		$c_string=$i;
		Imagestring($image,0,($scale*$i)+5,5,$c_string,$lcolor);
	}
}

ImageFilledRectangle($image,10,20,10+$scale*$dnalength,22,$lcolor);	#draw the black (actual) EST bar (over the grey if it drew)
for($i=0;$i<$dnalength;$i+=$bar) {		#and do those ticks
	ImageFilledRectangle($image,($scale*$i)+10,15,($scale*$i)+12,20,$lcolor);
	$c_string=$i;
	Imagestring($image,0,($scale*$i)+5,5,$c_string,$lcolor);
}
ImageFilledRectangle($image,10+($p_start*$scale),25,10+($scale*$p_end),27,$dcolor);	#draw the thin red (translated) bar

#work out the direction and draw the blue lines
if ($dir=="F") {											#if cluster ends with an F
	ImageLine($image,10+($p_start*$scale),27,10,49,$ncolor);
	ImageLine($image,10+$scale*$p_end,27,590,49,$ncolor);
}
else {
	ImageLine($image,10+($p_start*$scale),27,590,49,$ncolor);
	ImageLine($image,10+$scale*$p_end,27,10,49,$ncolor);
}
$scale=580/$tr_length;													#recalculate scale for translated parts of image

####draw the protein bar 
ImageFilledRectangle($image,10,50,10+($scale*$tr_length),60,$ucolor);	#the entire protein as low confidence
if ($conf_start>0) {ImageFilledRectangle($image,10+($scale*($conf_start-$p_start)),50,10+($scale*($conf_end-$p_start)),60,$ccolor);}	#put confident on top

#### and put the ticks on it
$scale=580/$plength;													#recalculate scale for protein parts of image
if($plength > 20)   { $bar=10; }
if($plength > 100)  { $bar=50; }
if($plength > 500)  { $bar=100; }
if($plength > 1000) { $bar=200; }
if($plength > 2500) { $bar=500; }
for($i=0;$i<$plength;$i+=$bar) {
	ImageFilledRectangle($image,($scale*$i)+10,45,($scale*$i)+12,50,$lcolor);
	$c_string=$i;
	Imagestring($image,0,($scale*$i)+5,35,$c_string,$lcolor);
}

####draw the domain bars
$scale=580/$plength;													#recalculate scale for protein parts of image
$row_dom=-1;
#$prev_dbase="dumm!y";
$i=0;
foreach ($doms as $dom) {						      #for each domain
		$varh=$varh+25;
		$row_dom++;
		if ($row_dom==5) {$row_dom=0;}
		$legend[$i][0]=$varh;
		$legend[$i][1]=$dom[6];
		$legend[$i][2]=$color[$row_dom];
		$i++;
	ImageFilledRectangle($image,10+($dom[1]*$scale),$varh,10+($dom[2]*$scale),$varh+12,$color[$row_dom]);#draw bar
	if ($dom[4]!="NULL") {Imagestring($image,2,15+($dom[1]*$scale),$varh,$dom[4],$lcolor);}#print text
}
#ImageFilledRectangle($image,595,65,675,$imageH-5,$greycolor);#draw legend background
#foreach ($legend as $data) {
#	Imagestring($image,2,655,$data[0],"---",$data[2]);
#	Imagestring($image,2,600,$data[0],$data[1],$data[2]);
#}
// send the image
ImagePNG($image);
ImageDestroy($image);
?>

