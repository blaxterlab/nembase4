<?php
//this script collects EST overlap information for a cluster_contig
//and then draws an image of the cluster_consensus structure
//nembase3 September 2007
//Ann Hedley and John Parkinson, University of Edinburgh
//19/01/10
//edit by Ben Elsworth for nembase4
//script now calls nembase4 data from /var/www/html/nembase4/nembase4_data/


$cluster = $_GET['cluster'];
$contig = $_GET['contig'];
$length = $_GET['length'];
$org_dir = $_GET['org_dir'];

session_start();
header("content-type: image/png");
$PG_HOST="localhost";
$PG_PORT=5432;
$PG_DATABASE="nemdb4";
$PG_USER="webuser";
$PG_PASS="";

//let's open the database

$dbconn1=pg_connect( "dbname=$PG_DATABASE host=$PG_HOST port=$PG_PORT user=$PG_USER password=$PG_PASS" );
$sqlcom="select * from est where clus_id='$cluster' and contig='$contig' group
by library,est_id,clus_id,contig,a_start,a_end,q_start,q_end,type;";
$dbres_est = pg_exec($dbconn1, $sqlcom );
$row_est=0;
$clustered=0;
$singletons=0;
$row_estmax=pg_NumRows($dbres_est);
while ($row_est<$row_estmax)
 {
 $do_est = pg_Fetch_Object($dbres_est, $row_est);

 if($do_est->a_end > 1)
  {
  $est_name[$clustered]=$do_est->est_id;
  $end[$clustered]=$do_est->a_end;
  if($end[$clustered] > $length) { $length=$end[$clustered]; }
  $start[$clustered]=$do_est->a_start;
  $qend[$clustered]=$do_est->q_end;
  if($qend[$clustered] > $end[$clustered]) 
  { $qend[$clustered]=$end[$clustered]; }
  $qstart[$clustered]=$do_est->q_start;
  if($qstart[$clustered] < $start[$clustered]) 
  { $qstart[$clustered]=$start[$clustered]; }
  $clustered++;
  }
 else
  {
  $name=$do_est->est_id;
  $sqlcom1="select sequence from est_seq where est_id='$name';";
  $dbres_sing = pg_exec($dbconn1, $sqlcom1 );
  $do_est_sing = pg_Fetch_Object($dbres_sing, 0);
  $seq=$do_est_sing->sequence;
  $length_sing=strlen($seq);
  $sing_st[$singletons]=$length_sing;
  $sing_name[$singletons]=$name;
  $singletons++;
  }
 $row_est++;
 }

if ($row_estmax>400) {$row_estmax=400;}
//set the graph width and height
$imageW = 650;
$imageH = $row_estmax*10+50;

$scale=580/$length;

//draw the border
$dotD = 4;
$image = ImageCreate($imageW,$imageH);
$lcolor = ImageColorAllocate($image,0,0,0);
$acolor = ImageColorAllocate($image,150,10,150);
$qcolor = ImageColorAllocate($image,150,150,10);
$dcolor = ImageColorAllocate($image,255,0,0);
$bcolor = ImageColorAllocate($image,255,255,255);
$gcolor = ImageColorAllocate($image,255,125,0);
$ncolor = ImageColorAllocate($image,100,150,255);
ImageFilledRectangle($image,0,0,$imageW,$imageH,$bcolor);

if($length > 20)   { $bar=10; }
if($length > 100)  { $bar=50; }
if($length > 500)  { $bar=100; }
if($length > 1000) { $bar=200; }
if($length > 2500) { $bar=500; }

for($i=0;$i<$length;$i+=$bar)
 {
 ImageFilledRectangle($image,($scale*$i)+10,15,($scale*$i)+12,20,$lcolor);
 $c_string=$i;
 ImageString($image,0,($scale*$i)+5,5,$c_string,$lcolor);
 }

ImageFilledRectangle($image,10,20,10+$scale*$length,30,$dcolor);
$c_string='Contig ';
$c_string.=$contig;
ImageString($image,0,20,22,$c_string,$bcolor);
$row_est=0;
while($row_est<$clustered)
 {
 ImageFilledRectangle($image,10+($start[$row_est]*$scale),($row_est*10)+38,
 10+$end[$row_est]*$scale,($row_est*10)+45,$acolor);
 ImageFilledRectangle($image,10+($qstart[$row_est]*$scale),($row_est*10)+38,
 10+$qend[$row_est]*$scale,($row_est*10)+45,$qcolor);
 ImageString($image,0,20+($start[$row_est]*$scale),($row_est*10)+38,$est_name[$row_est],$bcolor);
 $flag=0;

if(file_exists("/var/www/html/nembase4/nembase4_data/$org_dir/sequences/$est_name[$row_est]"))
  {
  $fp = fopen ("/var/www/html/nembase4/nembase4_data/$org_dir/sequences/$est_name[$row_est]", "r");
  $line = fgets($fp, 4096);
  if(preg_match("/(\w\w_\w{2,5}_\d\d\w\d\d)/",$line,$match))
   {
   preg_match("/\w\w_(\w{2,5})_\d\d\w\d\d/",$match[0],$trace_dir); 
   if(file_exists("/var/www/html/nembase4/nembase4_data/$org_dir/$trace_dir[1]/scf/$match[0].gz"))
    {
 ImageFilledRectangle($image,614,($row_est*10)+38,622,($row_est*10)+45,$dcolor);
 $flag=1;
    }
   }
  else if(preg_match("/(Nb_ad.+)_M13/",$line,$match))
   {
   preg_match("/Nb_(ad\d)/",$match[0],$trace_dir); 
   if(file_exists("/var/www/html/nembase4/nembase4_data/$org_dir/$trace_dir[1]/scf/$match[1].gz"))
    {
 ImageFilledRectangle($image,614,($row_est*10)+38,622,($row_est*10)+45,$dcolor);
 $flag=1;
    }
   }
  else if(preg_match("/(\w\w\d\d\w\d\d\.y\d)/",$line,$match))
   {
   if(file_exists("/var/www/html/nembase4/nembase4_data/$org_dir/washu/$match[1].gz"))
    {
   ImageFilledRectangle($image,614,($row_est*10)+38,622,($row_est*10)+45,$dcolor);
   $flag=1;
    }
   }
  fclose($fp);
  }

 if($flag==0)
  {
  ImageFilledRectangle($image,628,($row_est*10)+38,636,($row_est*10)+45,$ncolor);
  } 

 $row_est++;
 }

if($singletons >0)
 {
ImageString($image,0,20+($start[$row_est]*$scale),($row_est*10)+38, 
"Singletons",$gcolor);
$row_est++;

$row_est1=0;
while($row_est1<$singletons)
 {
 ImageFilledRectangle($image,10+$scale,(($row_est+$row_est1)*10)+38,
 10+$sing_st[$row_est1]*$scale,(($row_est+$row_est1)*10)+45,$gcolor);
 
 ImageString($image,0,20+$scale,(($row_est+$row_est1)*10)+38, 
 $sing_name[$row_est1],$bcolor);

if(file_exists("/var/www/html//$org_dir/sequences/$sing_name[$row_est1]"))
  {
  $fp = fopen("/var/www/html/nembase4/nembase4_data/$org_dir/sequences/$sing_name[$row_est1]", "r");
  $line = fgets($fp, 4096);
  if(preg_match("/(\w\w_\w{2,5}_\d\d\w\d\d)/",$line,$match))
   {
   preg_match("/\w\w_(\w{2,5})_\d\d\w\d\d/",$match[0],$trace_dir); 
   if(file_exists("/var/www/html/nembase4/nembase4_data/$org_dir/$trace_dir[1]/scf/$match[0].gz"))
    {

ImageFilledRectangle($image,614,(($row_est+$row_est1)*10)+38,622,(($row_est+$row_est1)*10)+45,$dcolor);
 $flag=1;
    }
   }
  else if(preg_match("/(Nb_ad.+)_M13/",$line,$match))
   {
   preg_match("/Nb_(ad\d)/",$match[0],$trace_dir); 
   if(file_exists("/var/www/html/nembase4/nembase4_data/$org_dir/$trace_dir[1]/scf/$match[1].gz"))
    {

ImageFilledRectangle($image,614,(($row_est+$row_est1)*10)+38,622,(($row_est+$row_est1)*10)+45,$dcolor);
 $flag=1;
    }
   }
  else if(preg_match("/(w\w\d\d\w\d\d\.y\d)/",$line,$match))
   {
   if(file_exists("/var/www/html/nembase4/nembase4_data/$org_dir/washu/$match[1].gz"))
    {
 
ImageFilledRectangle($image,614,(($row_est+$row_est1)*10)+38,622,(($row_est+$row_est1)*10)+45,$dcolor);
 $flag=1;
    }
   }
  fclose($fp);
  }

 if($flag==0)
  {
 
ImageFilledRectangle($image,628,(($row_est+$row_est1)*10)+38,636,(($row_est+$row_est1)*10)+45,$ncolor);
  } 
 $row_est1++;
 }
}

ImageString($image,0,610,10,"Trace?",$lcolor); 
ImageString($image,0,608,22,"Yes",$dcolor); 
ImageString($image,0,630,22,"No",$ncolor); 

// send the image
ImagePNG($image);
ImageDestroy($image);
pg_close( $dbconn1 );
?>

