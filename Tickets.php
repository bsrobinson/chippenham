<?php
  include_once("int/fest.php");
  include_once("int/DateTime.php");

  dohead("Buy Tickets and Passes",[],1);
  global $YEARDATA,$YEAR,$DayLongList;
  set_ShowYear();
  include_once "int/ProgLib.php";

  if ($YEARDATA['TicketControl'] == 0 && $YEARDATA['CampingControl'] == 0) {
    echo "<h2>Tickets and Passes are not yet on Sale</h2>";
    dotail();
  }

  // This is a short term kludge


  echo TnC("TicketHeader");

  echo "<div class=TicketFrame style='max-width:1000px;'>";
  echo '<p><script type="text/javascript" src="https://theticketsellerslive.blob.core.windows.net/webcontent/embed/iframeResizer.min.js"></script><iframe src="https://ww2.theticketsellers.co.uk/embed/10058480" scrolling="no" style="border: 0px; width: 1px; min-width: 100%; max-width: 1100px;" onload="iFrameResize()"></iframe></p>';
  echo "</div>";

  dotail();

  // Old code for Ticketsource

  echo "<div class=biodiv>";
  echo "<img src='/images/Weekend-Wristband.jpg' class=bioimg />";

  echo "</div>";

  if ($YEARDATA['TicketControl'] == 1 && Days2Festival() < 10) {
    echo "<b>Note</b>: Online ticket sales will close at 6am on " . FestDate(0,'F') . " , after that tickets and passes can be purchased from Festival Information in the Square. " .
         "Camping may be available at the campsite gate if there is space available.<p>";
  } else if  ($YEARDATA['TicketControl'] >= 2 ) {
    echo "<b>Online Tickets have closed</b>.  Tickets may still be bought at Festival Information in the square and at the door for most events.  " .
         "Camping may be purchased at the campsite gate if space is available.<p>";
  }

  echo "Select from the options below to purchase your passes and tickets for " . Feature('FullName') . " " . substr($YEAR,0,4) . ".<p>";

  echo "The weekend Pass is access to everything (apart from events at the Tivoli and events before Friday).  The Saturday Pass gives access to everything on Saturday only.
  Event tickets are just access to that individual event.<p>";

  if ($YEARDATA['BookingFee']) echo "Please note that there is a booking fee of " . $YEARDATA['BookingFee'] . " when ordering tickets online.<p> ";
  echo "Please <a href='mailto:carers@XXX'>Contact Us</a> if you require a carer ticket.<p>";

  if ($YEARDATA['CampingCost'] && $YEARDATA['CampingControl'] == 1) {
    echo "Order your festival tickets and camping together, by selecting <strong>Continue Shopping</strong> ";
    echo "before you checkout!<p>  Camping costs <strong>&pound;" . $YEARDATA['CampingPrice1Day'] . "</strong> for the first night and <strong>&pound;" .
     ($YEARDATA['CampingPrice2Day'] - $YEARDATA['CampingPrice1Day']) . "</strong> for each additional night.  Under 8's are free.<p>";
  } else if ( $YEARDATA['CampingControl'] == 4)  {
    echo "The camping can not be booked directly through the festival this year from here.  " .
          "Please visit <a href=http://merleyhallfarm.co.uk/camping target=_blank>MerleyHallFarm.co.uk</a><p>";
  } else {
    echo "Camping is not open to book<p>";
  }

  echo "<a href=TermsAndConditions>Full Terms and Conditions</a>.<p>";

  if ($YEARDATA['CampingCost'] && $YEARDATA['CampingControl'] == 1) {
    echo "<a href=/InfoCamping><b>Camping Information and Camping Tickets.</b></a><p>";
  }

  echo "<p><div class=tablecont><table class=InfoTable>";
  echo "<tr><th colspan=5>Festival Passes</th>";
  foreach(['Weekend','Friday','Saturday','Sunday'] as $day) {
    if ($YEARDATA[$day . "PassCode"]) {
      echo "<tr><td>";
      if ($YEARDATA['TicketControl'] == 1) echo "<a href='" . $YEARDATA[$day . "PassCode"] . "' target=_blank style='font-size:18px'>";
      echo "<strong>$day Pass</strong></a><br>";
      echo "Adult (18+): <strong>";

      $str = '';
      $Cpri = $YEARDATA[$day . 'Pass'];

      if ($YEARDATA['PriceChange1']) {
        $pc = $YEARDATA['PriceChange1'];
        $Npri = $YEARDATA[$day . 'Pass1'];
        if ($Npri != $Cpri && $Npri != 0) {
          if ($pc > time()) $str .= "&pound;" . $Cpri . "</strong> until " . date('j M Y',$pc);
          $Cpri = $Npri;
        }
      }

      if ($YEARDATA['PriceChange2']) {
        $pc = $YEARDATA['PriceChange2'];
        $Npri = $YEARDATA[$day . 'Pass2'];
        if ($Npri != $Cpri && $Npri != 0) {
          if ($pc > time()) {
            if ($str) $str .= ", then ";
            $str .= "&pound;" . $Cpri . "</strong> until " . date('j M Y',$pc);
          }
          $Cpri = $Npri;
        }
      }

      if ($str) $str .= ", then ";
      $str .= "&pound;$Cpri </strong><br>";

      echo $str;
      echo "Child (5-15): <strong>" . Print_Pence($Cpri*50) . "</strong><br>";
      echo "Infant (0-4): <strong>Free</strong>";
      echo "<td style='width:70%'>" . $YEARDATA[$day . "Text"] . "<td style='text-align:center; font-size:20px'>";

      switch ($YEARDATA['TicketControl']) {
      case 0: echo "Not Yet"; break;
      case 1: echo "<a href='" . $YEARDATA[$day . "PassCode"] . "' target=_blank ><strong>Buy Now</strong></a>"; break;
      case 2: echo "Closed"; break;
      }
    }
  }
  echo "</table></div><p>";

  global $YEAR,$db,$YEARDATA;

  echo "<div class='FullWidth TicketTable'>";
  $Vens = Get_Venues(1);
  $qry = "SELECT * FROM Events WHERE Year='$YEAR' AND ((Price1!=0 AND TicketCode!='') OR SpecPriceLink!='')  AND SubEvent<=0 AND (Public=0 || Public=1) ORDER BY Day,Start";
  $Evs = $db->query($qry);

  while ($E = $Evs->fetch_assoc()) {
    $PC=0;
    if ($E['Day'] < $YEARDATA['FirstDay'] || $E['Day'] > $YEARDATA['LastDay']) {
      $PC = 1;
    } else {
      $pcf = 'PriceComplete' . ($E['Day'] >=0?$E['Day']:"_" . (-$E['Day']));
      if (!isset($YEARDATA[$pcf])) {
        $PC = 1;
      } elseif ($YEARDATA[$pcf]) {
        $PC = 1;
      }
    }
    DayTable($E['Day'],"Event Tickets",($PC?'':'(More to come)'),'','style=min-width:1000');
    $bl = "<a href=" . ($E['SpecPriceLink']? $E['SpecPriceLink'] : $E['TicketCode']) . " target=_blank>" ;
    echo "<tr><td><strong><a href=/int/EventShow?e=" . $E['EventId'] . ">" . $E['SN'] . "</a></strong><br>";
      echo Price_Show($E);
    echo "<td>" . FestDate($E['Day'],'L') . "<br>";
      if ($E['Venue']) {
        echo "At: " . Venue_Parents($Vens,$E['Venue']) . "<a href=/int/VenueShow?v=" . $E['Venue'] . ">" . VenName($Vens[$E['Venue']]) . "</a><br>";
      } else {
        echo "At: </b>Venue not yet known</b><br>";
      }
      echo "From: " . timecolon($E['Start']) . " to " . timecolon($E['End']);
    echo "<td style='width:50%'>";
      if ($E['Description']) echo $E['Description'] . "<br>";
      if ($E['BigEvent']) {
        $Others = Get_Other_Things_For($E['EventId']);
        echo Get_Other_Participants($Others,0,1,15,1,'',$E);
      } else {
        echo Get_Event_Participants($E['EventId'],0,1,15);
      }
      if ($E['ExcludePass']) {
        echo "<p><b>Note:</b> This is event excluded from the Weekend Pass ";
        if ($E['ExcludeDay'] && $YEARDATA[$DayLongList[$E['Day']] . "Pass"]!='') echo " or " . $DayLongList[$E['Day']] . " ticket\n";
      } elseif ($E['ExcludeDay'] && $YEARDATA[$DayLongList[$E['Day']] . "Pass"]!='') {
        echo "<p><b>Note:</b> This is event excluded from the " . $DayLongList[$E['Day']] . " ticket\n";
      }
    if (($YEARDATA['TicketControl'] == 1) && ($E['TicketCode'] || $E['SpecPriceLink'])) echo "<td><strong>$bl Buy Now</a></strong>\n";
  }

  if (!$Evs->num_rows) echo "No Ticketed Events are yet published.<p>";

  echo "</table></div></div></p>";

  if (($YEARDATA['CampingControl'] == 1) && $YEARDATA['CampingCost']) {
    echo "<div class=tablecont><table class='InfoTable CampTable' style='min-width:700px'>";
    echo "<tr><th colspan=7>Camping Tickets";

    $Avails = [
             'Thursday, Friday, Saturday and Sunday nights'=>['TFSS',4],
             'Thursday, Friday and Saturday nights'=>['TFSx',3],
             'Thursday and Friday nights'=>['TFxx',2],
             'Thursday night only'=>['Txxx',1],
             'Friday, Saturday and Sunday nights'=>['xFSS',3],
             'Friday and Saturday nights'=>['xFSx',2],
             'Friday night only'=>['xFxx',1],
             'Saturday and Sunday nights'=>['xxSS',2],
             'Saturday night only'=>['xxSx',1],
             'Sunday night only'=>['xxxS',1],
            ];
     $DName = ['Thursday','Friday','Saturday','Sunday'];
     foreach ($Avails as $txt=>$dat) {
       if (!$YEARDATA['CampingCode_' . $dat[0] ]) continue;
       echo "<tr><td>Camping for:";
       foreach (str_split($dat[0]) as $i=>$c) echo "<td>" . ($c == 'x'?"":$DName[$i]);
       echo "<td>" . Print_Pence($YEARDATA['CampingPrice' . $dat[1] . 'Day']*100) . "<td>";
       if (substr($YEARDATA['CampingCode_' . $dat[0] ],0,1) != '-') {
         echo "<a href='" . $YEARDATA['CampingCode_' . $dat[0] ] . "' target=_blank><b>Buy Now</b></a>";
       } else {
         echo "Closed";
       }
     }

    echo "</table></div><p>";
  }

  echo "<h2>Child Tickets</h2>";

//  echo "<p>Child ticket pricing for the festival is 0-4 Free, 5-15 Half Price, 16+ Standard ticket price.</p>";

  echo "<h2>Official Campsite</h2><p>";
  echo "<a href=/InfoCamping><b>Camping Information.</b></a><p>";

  if ($YEARDATA['CampingCost'] && $YEARDATA['CampingControl'] == 1) {
    echo "Order your festival tickets and camping together, by selecting <strong>Continue Shopping</strong> before you checkout!</p> ";
  }

//  echo "<h2>* Party In The Paddock</h2><p>";

//  echo "There will not be a Party in the Paddock this year.<p>";

  if (0) echo "If you're looking to combine a weekend of official festival events and a trip to <a href='http://partyinthepaddock.com'>Party In The Paddock</a>, " .
       "then book your tickets with us!</p>";

  if ($YEARDATA['TicketControl'] == 1) {
    echo "<h2>Official Ticket Outlets</h2>";
    echo "<p>Tickets and day/weekend passes are on sale at these outlets:</p>";

    // TODO Add a list for fest
  }

  dotail();
?>
