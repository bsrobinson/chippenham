<?php
  include_once("int/fest.php");

  include_once("int/ContactLib.php");
  global $ContCatState, $ContCatColours,$PLANYEAR, $db;
  
  dohead("Contact Us",[],1);

  echo "<div class=tablecont><table class='FullWidth InfoTable'>";
  echo "<tr><th colspan=4>Main Contacts for $PLANYEAR Festival";
  
  $Teams=Get_ContactCats(1);
  
  foreach ($Teams as $t) {
    if ($t['RelOrder']<0) continue;
    echo "<tr><td>" . $t['SN'] . "<td style='font-weight:bold; color:" . $ContCatColours[$t['OpenState']] . "'>" . $ContCatState[$t['OpenState']];
    if ($t['OpenState']) {
      echo "<td><a href='mailto:" . $t['Email'] . '@' . Feature('HostURL') . "'>"  . $t['Email'] . '@' . Feature('HostURL') . "</a>";
    } else {
      echo "<td>";
    }
    echo "<td>" . $t['Description'];
  }
  echo "</table></div><p>";

  $ans = $db->query("SELECT * FROM FestUsers WHERE Contacts!=0 ORDER BY RelOrder DESC");
  $url = Feature('HostURL');
  $url = preg_replace('/\./','. ',$url);

  while ($user = $ans->fetch_assoc()) {
    echo "<div class=smallfloatleft><div class=mini>\n";
    if ($user['WMFFemail']) echo "<a href=mailto:" . $user['WMFFemail'] . '@' . Feature('HostURL') . ">";
    if (feature('ShowContactPhotos')) {
      echo "<img alt='Chippenham Folk Festival' style='float:left; max-width:200px;max-height:200px;margin-right:5px' src='";
      echo ($user['Image']?$user['Image']:"/images/WimborneFolkLogo.png") . "'>";
    }
    if ($user['Contacts'] == 1) echo "<h2 class=minittl>" . $user['SN'] . "</h2>";
    echo "<span class=minitxt>" . $user['Roll'] . "</span>";
    if ($user['WMFFemail']) echo "<br>" . $user['WMFFemail'] . "@ $url</a>";
    echo "</div></div>\n";
  }

/*
<!-- <h2 class="subtitle">Contact Us during the Festival Weekend</h2>
<p>If you need to get in touch with the festival committee during the festival weekend, please find a steward, ask at our Information Points on The Square or at The Allendale Centre 

<p style="color:#CC0000; font-weight:bold;">In the event of an emergency, please find a steward or security or go to our first aid points (The Square or Willow Walk) or if life threatening, dial 999.</p> */

  echo "<br clear=all><p><br clear=all><p><br clear=all>";

//  echo "<br clear=all><p><br clear=all><p><br clear=all>";
//    echo "<br clear=all><p><br clear=all><p><br clear=all>";
      echo "Please note that we may not be able to respond to urgent emails and bear in mind that due to the high number of enquiries we get throughout the year, " .
       "we may not be able to respond to every email.<p>";

  dotail();

