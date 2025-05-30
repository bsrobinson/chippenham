<?php
  include_once("fest.php");
  A_Check('Staff');

  include_once("ProgLib.php");
  include_once("DocLib.php");
  include_once("EventCheck.php");

  dostaffhead("List Events");
  global $db,$Event_Types,$Event_Types,$USERID,$Importance,$YEAR;
  global $Public_Event_Types;

  $yn = array('','Y');
  $ETNames = [];
  foreach($Event_Types as $E) $ETNames[$E['ETypeNo']] = $E['SN'];
  $res = [];

//var_dump($_REQUEST);
//var_dump($Event_Types);

  if (isset($_REQUEST['ACTION']) && Access('Staff','Events')) {
    foreach ($_REQUEST as $f=>$v) {
      if (preg_match('/E(\d*)/',$f,$res)) {
        $ev=$res[1];
        $Event = Get_Event($ev);

        switch ($_REQUEST['ACTION']) {
        case 'Delete' :
          $Event['Year'] -= 1000;
          $Event['SubEvent'] = 0;
          break;

        case 'Rename as':
          $Event['SN'] = $_REQUEST['NewName'];
          break;

        case 'Move by':
          if (($delta = $_REQUEST['Minutes'])) {
            $Event['Start'] = timeadd($Event['Start'],$delta);
            $Event['End'] = timeadd($Event['End'],$delta);
            if ($Event['SlotEnd']) $Event['SlotEnd'] = timeadd($Event['SlotEnd'],$delta);
          }
          break;

        case 'Move to':
          if (($v = $_REQUEST['v'])) $Event['Venue'] = $v;
          break;

        case 'Chown to':
          $Event['Owner'] = $_REQUEST['W'];
          break;

        case 'Public':
          $Event['Public'] = 0;
          break;

        case 'Concert':
          $Event['IsConcert'] = 1;
          break;

        case 'Set Event Type to' :
          $Event['Type'] = $_REQUEST['Type'];
          break;
        }
//        var_dump($Event);
        Put_Event($Event);
      }
    }
  }

  $Venues = Get_Real_Venues();
  if (isset($_REQUEST['V'])) {
    $se = 0;
    $V = $_REQUEST['V'];
    $Ven = Get_Venue($V);
    $SubE = " SubEvent<=0 AND Year='$YEAR' AND Venue=$V";
    echo "<h2>List Events at " . $Ven['SN'] . "</h2>";

  } else if (isset($_REQUEST['LIST'])) {
    $se = 0;
    $SubE = " Year='$YEAR' ";
    echo "<h2>List All Events</h2>";
  } else if (isset($_REQUEST['se'])) {
    $se = $_REQUEST['se'];
    $SubE = " ( SubEvent='$se' OR EventId='$se' )";
    echo "<h2>List Sub Events</h2>";
  } else {
    $se = 0;
    $SubE = " SubEvent<=0 AND Year='$YEAR'";
    echo "<h2>List Events</h2>";
  }

  $AllUsers = Get_AllUsers(0);
  $AllA = Get_AllUsers(1);
  $AllActive = array();
  foreach ($AllUsers as $id=>$name) if ($id > 10 && $AllA[$id] >= 2 && $AllA[$id] <= 6) $AllActive[$id]=$name;
  $PFComms = (isset($_REQUEST['PFComms'])?$_REQUEST['PFComms']:0); // 0 - Normal, 1 - Only Events with comments, 2 - All
  $PFX = '';
  if ($PFComms) $PFX = "&PFComms=$PFComms";

  $coln = ($PFComms?0:1);  // Start at 1 because of select all
  echo "<form method=post action=EventList>";
  echo "Click on Id/Name to edit, on Show for public page.<p>\n";
  echo "If the Stewards column has 'Stew' or 'Set' then there are more elaborate Stewarding/Setup Requirements - see the event for more detail.<p>";
  if ($se) echo fm_hidden('se',$se);
  echo "<div class=Scrolltable><table id=indextable border>\n";
  echo "<thead><tr>";

  if (!$PFComms) echo "<th><input type=checkbox name=SelectAll id=SelectAll onchange=ToolSelectAll(event)>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'N')>Event Id</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Name</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Day</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'N')>Start</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'N')>End</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Venue</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Type</a>\n";
  if (!$PFComms) echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Public</a>\n";
  if (!$PFComms) echo "<th><a href=javascript:SortTable(" . $coln++ . "," . Feature('ListPricesType','N') . ")>Price</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Stewards</a>\n";
  if ($se == 0) echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Sub Es</a>\n";
  if ($se != 0) echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>With</a>\n";
  if (!$PFComms) echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Fam</a>\n";
  if (!$PFComms) echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Spec</a>\n";
  if (!$PFComms) echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Imp</a>\n";
  if (!$PFComms) echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Owner</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Show</a>\n";
  if ($PFComms) echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Comments</a>\n";
  echo "</thead><tbody>";

  $res=$db->query("SELECT * FROM Events WHERE $SubE ORDER BY Day, Start, Type");

  if ($PFComms) {
    $Comments = [];
    $Comms = Gen_Get_Cond('EventSteward',"Year='$YEAR'");
    foreach ($Comms as $C) {
      $Eid = $C['EventId'];
      if (isset($Comments[$Eid])) { $Comments[$Eid]++; } else {$Comments[$Eid] =1; }
      $Eid = $C['SubEvent'];
      if ($Eid >0) {
        if (isset($Comments[$Eid])) { $Comments[$Eid]++; } else {$Comments[$Eid] =1; }
      }
    }
  }

  if ($res) {
    while ($evnt = $res->fetch_assoc()) {
      $i = $evnt['EventId'];
      if (($PFComms==1) && (empty($Comments[$i]))) continue;
      if (!Access('SysAdmin') && $Event_Types[$evnt['Type']]['DontList']) continue;
      echo "<tr>";
      if (!$PFComms) echo "<td><input type=checkbox name=E$i class=SelectAllAble>";
      echo "<td>$i<td>";
//      if (Access('Staff','Events') || $evnt['Owner']==$USERID || $evnt['Owner2']==$USERID)
      echo "<a href=EventAdd?e=$i$PFX>";
      if (strlen($evnt['SN']) >2) { echo $evnt['SN'] . "</a>"; } else { echo "Nameless</a>"; }
      echo "<td>" . DayList($evnt['Day']) . "<td>" . timecolon($evnt['Start']) . "<td>";
      if ($se > 0 && $evnt['SubEvent'] < 0) { echo timecolon($evnt['SlotEnd']); } else { echo timecolon($evnt['End']); }
      echo "<td>" . (isset($Venues[$evnt['Venue']]) ? $Venues[$evnt['Venue']] : "Unknown");
      echo "<td>" . ($evnt['Status'] == 1 ? "<div class=Cancel>Cancelled</div> " : "") .
            (isset($Event_Types[$evnt['Type']]['SN']) ? $Event_Types[$evnt['Type']]['SN'] : "?" );
      if (!$PFComms) echo "<td>" . $Public_Event_Types[$evnt['Public']];
      if (!$PFComms) {
        echo "<td>" ;
        if ($evnt['SeasonTicketOnly']) {
          echo (['','ST only','ST+ET only','ET only'][$evnt['SeasonTicketOnly']]??"??");
          if ($evnt['SeasonTicketOnly']==2) {
            echo " (£" . $evnt['Price1'] . ")";
          }
        } else {
          if ($evnt['SubEvent'] <= 0 || ($evnt['SpecPrice'])) {
            if ($evnt['SpecPrice']) {
              echo $evnt['SpecPrice'];
            } else {
              if ($evnt['Price1']) { echo Print_Pound($evnt['Price1']); } else echo Feature('FreeText',"Free");
              if ($evnt['Price2']) echo " /" . Print_Pound($evnt['Price2']);
              if ($evnt['DoorPrice']) echo " /" . Print_Pound($evnt['DoorPrice']);
            }
          }
        }
      }
      echo "<td>" .($evnt['NeedSteward'] ? "Y" : "" );
        if ($evnt['StewardTasks']) echo " Stew";
        if ($evnt['SetupTasks']) echo " Set";
      if ($evnt['BigEvent']) { echo "<td>BIG\n"; }
      else {
        if ($se == 0) {
          if ($evnt['SubEvent'] == 0) { echo "<td>No\n"; }
          else { echo "<td><a href=EventList?se=$i$PFX>Yes</a>\n"; }
        }
        if ($se != 0) {
          echo "<td>";
          if ($evnt['SubEvent']>0) {
            echo Get_Event_Participants($i,1,2) ;
          } else {
          }
        }
      }
      if ($se > 0 && $evnt['SubEvent'] < 0) echo " Full end: " . $evnt['End'] . " PARENT";
      if (!$PFComms) echo "<td>" . ($evnt['Family']?"Y":"");
      if (!$PFComms) echo "<td>" . ($evnt['Special']?"Y":"");
      if (!$PFComms) echo "<td>" . $Importance[$evnt['Importance']];
      if (!$PFComms) echo "<td>" . (isset($AllUsers[$evnt['Owner']]) ? $AllUsers[$evnt['Owner']] : "") ;
      echo "<td><a href=EventShow?e=$i$PFX&InsideShow=1>Show</a>\n";
      if ($PFComms) echo (empty($Comments[$i])?'<td>No':'<td><a href=ShowComments?e=$i>Yes</a>');
    }
  }
  echo "</tbody></table></div>\n";
  if (Access('Staff','Events') && !$PFComms) {
    $realvens = Get_Real_Venues();
    echo "Selected: <input type=Submit name=ACTION value=Delete " .
        " onClick=\"javascript:return confirm('are you sure you want to delete these?');\">, ";
    echo "<input type=Submit name=ACTION value='Rename as'> ";
    echo "<input type=text name=NewName>, <input type=Submit name=ACTION value='Move by'> ";
    echo "<input type=text name=Minutes size=4> Minutes, ";
    echo "<input type=Submit name=ACTION value='Move to'> " . fm_select($realvens,0,'v') . ",";
    if (Access('SysAdmin')) echo "<input type=Submit name=ACTION value='Chown to'> " . fm_select($AllActive,0,'W') . ",";
    echo "<input type=Submit name=LIST value='Show All'>\n";
    echo "<input type=Submit name=ACTION value='Public'>\n";
    echo ", <input type=Submit name=ACTION value='Set Event Type to'> " . fm_select($ETNames,0,'Type');
//    echo "<input type=Submit name=ACTION value='Concert'><br>\n";
  }
  echo "</form>\n";

  if (Access('Committee','Events')) {
    echo "<h2>";
    if (!$PFComms) echo "<a href=EventAdd>Add Event</a>";

    if ($se) echo ", <a href=EventList>List Events</a>";

    echo "</h2>";

    if (!$PFComms) {
      echo "<h2>Checking...</h2>";
      EventCheck();
    }
  }
  dotail();