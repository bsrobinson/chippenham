<?php
  include_once("fest.php");
  A_Check('Steward');

  dostaffhead("List Venues");
  global $Surfaces,$YEAR,$Venue_Status;

  $yn = array('','Y');
  include_once("ProgLib.php");
  include_once("DanceLib.php");
  $venues = ((isset($_REQUEST['ALL'])) ? Get_Venues(1) : Get_AVenues(1));
  $VYear = Gen_Get_Cond('VenueYear',"Year=$YEAR");
  if ($VYear) foreach($VYear as $VY) {
    $Spid = $VY['SponsoredBy'];
    if ($Spid > 0) {
      $Spon = Gen_Get('Trade',$Spid,'Tid');
      $venues[$VY['VenueId']]['SponsoredBy'] = "<a href=Trade?id=$Spid&T=S>" . $Spon['SN'] . "</a>";
    } else if ($Spid == 0 ){
      $venues[$VY['VenueId']]['SponsoredBy'] = "";
    } else {
      $venues[$VY['VenueId']]['SponsoredBy'] = "<a href=SponSort?T=V&i=" . $VY['VenueId'] . ">Many</a>";
    }
    $venues[$VY['VenueId']]['QRCount'] = ($VYear['QRCount'] ?? 0);
  }

  if (!isset($_REQUEST['ALL'])) echo "<h2>Click <a href=VenueList?ALL>All</a> to see not in use Venues</h2>";
  $edit = Access('Staff','Venues');
  $coln = 0;
  echo "<div class=Scrolltable><table id=indextable border>\n";
  echo "<thead><tr>";

  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'N')>Venue Id</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Short Name</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Full Name</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Virt</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Sponsored</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'N')>QR Count</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Status</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'N')>Dance Order</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'N')>Music Order</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'N')>Other Order</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Dance Setup Overlap</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Dance</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Music</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Comedy</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Child</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Craft</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Other</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Surface 1</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Surface 2</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'N')>Map Imp</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Prog</a>\n";
  echo "</thead><tbody>";

  if ($venues) {
    foreach ($venues as $Ven) {
      $i = $Ven['VenueId'];
      echo "<tr><td>$i<td>";
        if ($edit) echo "<a href=AddVenue?v=$i>";
        echo $Ven['ShortName'];
        if ($edit) echo "</a>";

      echo "<td>";
        if ($edit) echo "<a href=AddVenue?v=$i>";
        echo $Ven['SN'];
        if ($edit) echo "</a>";

      echo "<td>" . ($Ven['IsVirtual']?'Y':'');
      echo "<td>" . ($Ven['SponsoredBy']?$Ven['SponsoredBy']:"");

      echo "<td>" . ($Ven['QRCount']??0);
      echo "<td>" . $Venue_Status[$Ven['Status']];
      echo "<td>" . $Ven['DanceImportance'];
      echo "<td>" . $Ven['MusicImportance'];
      echo "<td>" . $Ven['OtherImportance'];
      echo "<td>" . $yn[$Ven['SetupOverlap']];
      echo "<td>" . $yn[$Ven['Dance']];
      echo "<td>" . $yn[$Ven['Music']];
      echo "<td>" . $yn[$Ven['Comedy']];
      echo "<td>" . $yn[$Ven['Child']];
      echo "<td>" . $yn[$Ven['Craft']];
      echo "<td>" . $yn[$Ven['Other']] ."\n";
      echo "<td>" . $Surfaces[$Ven['SurfaceType1']];
      echo "<td>" . $Surfaces[$Ven['SurfaceType2']] . "\n";
      echo "<td>" . $Ven['MapImp'];
      echo "<td><a href=VenueShow?Y=$YEAR&v=$i>Prog</a>";
    }
  }
  echo "</tbody></table></div>\n";

  if (Access('Committee','Venues')) {
    echo "<h2><a href=AddVenue>Add Venue</a></h2>";
  }
  dotail();
?>
