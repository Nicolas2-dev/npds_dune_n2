<?php

use App\Support\Facades\Language;


function MM_banner() 
{
   global $banners, $hlpfile;

   if (($banners) and (!$hlpfile)) {
      ob_start();
      include("banners.php");
      $MT_banner=ob_get_contents();
      ob_end_clean();
   } else {
      $MT_banner="";
   }
   return ($MT_banner);
}

function MM_msg_foot() 
{ 
   $foot1 = config('theme.footer.foot1');
   $foot2 = config('theme.footer.foot2');
   $foot3 = config('theme.footer.foot3');
   $foot4 = config('theme.footer.foot4');

   if ($foot1) $MT_foot = stripslashes($foot1)."<br />";
   if ($foot2) $MT_foot .= stripslashes($foot2)."<br />";
   if ($foot3) $MT_foot .= stripslashes($foot3)."<br />";
   if ($foot4) $MT_foot .= stripslashes($foot4);

   return $MT_foot;
}