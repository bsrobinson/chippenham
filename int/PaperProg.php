<?php
  include_once("fest.php");

//  A_Check('Staff');

  include_once("ProgLib.php");
  include_once("DispLib.php");
  include_once("DanceLib.php");
  include_once("MusicLib.php");
  dominimalhead("Performer Print Pages", ["cache/FestStyle.css",'css/PrintPage.css']);

  global $db,$Coming_Type,$YEAR,$PLANYEAR,$Book_State,$EType_States;
  $Set = 0;
  $Order = "EffectiveImportance DESC, s.RelOrder DESC, s.SN";
  if (isset($_REQUEST['ALPHA'])) $Order = "s.SN";
  if (isset($_REQUEST['Set'])) $Set = $_REQUEST['Set'];

  $PairsPerPage = Feature('PerformerPairsExtra','7');
  $PageLimits = explode(',',$PairsPerPage);
  $Page = 1;
  $PairLimit = ($PageLimits[0] ?? 7) +0;
  $now = time();
  $Perf_Cats = [
   'Music'=>"SELECT s.*, y.*, IF(s.DiffImportance=1,s.MusicImportance,s.Importance) AS EffectiveImportance FROM Sides AS s, SideYear AS y " .
                      "WHERE s.SideId=y.SideId AND y.year='$YEAR' AND y.YearState>=" . $Book_State['Booking'] .
                      " AND s.IsAnAct=1 AND y.ReleaseDate<$now AND s.NotPerformer=0 ORDER BY $Order",
   'Dance Displays'=>"SELECT s.*, y.*, IF(s.DiffImportance=1,s.DanceImportance,s.Importance) AS EffectiveImportance " .
            "FROM Sides AS s, SideYear AS y WHERE s.SideId=y.SideId AND y.year='$YEAR' AND y.Coming=" . $Coming_Type['Y'] .
            " AND s.IsASide=1 AND y.ReleaseDate<$now AND s.NotPerformer=0 ORDER BY $Order",
   'Ceilidhs and Folk Dance'=> "SELECT s.*, y.*, IF(s.DiffImportance=1,s.OtherImportance,s.Importance) AS EffectiveImportance  FROM Sides AS s, SideYear AS y " .
            "WHERE s.SideId=y.SideId AND y.year='$YEAR' AND y.YearState>=" . $Book_State['Booking'] .
            " AND s.IsCeilidh=1 AND y.ReleaseDate<$now AND s.NotPerformer=0 ORDER BY $Order",
   'Family and Community' => "SELECT s.*, y.*, IF(s.DiffImportance=1,s.FamilyImportance,s.Importance) AS EffectiveImportance  FROM Sides AS s, SideYear AS y " .
            "WHERE s.SideId=y.SideId AND y.year='$YEAR' AND y.YearState>=" . $Book_State['Booking'] .
            " AND s.IsFamily=1 AND y.ReleaseDate<$now AND s.NotPerformer=0 ORDER BY $Order",
    /*
   'Other Performers' => "SELECT s.*, y.*, IF(s.DiffImportance=1,s.OtherImportance,s.Importance) AS EffectiveImportance  FROM Sides AS s, SideYear AS y " .
            "WHERE s.SideId=y.SideId AND y.year='$YEAR' AND y.YearState>=" . $Book_State['Booking'] .
            " AND s.IsOther=1 AND y.ReleaseDate<$now AND s.NotPerformer=0 ORDER BY $Order",
   /*
   'Youth' => "SELECT s.*, y.*, IF(s.DiffImportance=1,s.YouthImportance,s.Importance) AS EffectiveImportance  FROM Sides AS s, SideYear AS y " .
      "WHERE s.SideId=y.SideId AND y.year='$YEAR' AND y.YearState>=" . $Book_State['Booking'] .
      " AND s.IsYouth=1 AND y.ReleaseDate<$now AND s.NotPerformer=0 ORDER BY $Order",*/
  ];

  $Displayed = [];
  $SetNum = 1;
  $PairCount = $PairPageC = 0;
  echo "<script>document.getElementsByTagName('body')[0].style.background = 'none';</script><div class=PaperP>";
  foreach ($Perf_Cats as $Title=>$fetch) {
    if ($Set && ($Set != $SetNum++)) {
      $Slist = [];
      $perfQ = $db->query($fetch);
      if ($perfQ) while($side = $perfQ->fetch_assoc()) $Slist[] = $side;

      foreach ($Slist as $perf) {
        if ($perf['NotPerformer'] ) continue;
        if (isset($Displayed[$perf['SideId']])) continue;
        if (empty($perf['Description']) && Feature('OmitEmptyDescriptions')) continue;
        $Displayed[$perf['SideId']] = 1;
      }
      continue;
    }
    echo "<div style='text-align:center;font-size:24;font-weight:bold;margin:10;'>$Title</div>";
    $Slist = [];
    $perfQ = $db->query($fetch);
    if ($perfQ) while($side = $perfQ->fetch_assoc()) $Slist[] = $side;
//var_dump("Type top",$PairLimit, $PairPageC, $Page);
    $Pair = 0;
    if ($PairPageC >= $PairLimit) {
      echo "<table class='PerfT pagebreak' width=100% border>";
      $PairPageC = 0;
      $PairLimit = ($PageLimits[$Page++] ?? 7)+0;
    } else {
      echo "<table class=PerfT width=100% border>";
    }
    foreach ($Slist as $perf) {
      if ($perf['NotPerformer'] ) continue;
      if (isset($Displayed[$perf['SideId']])) continue;
      if (empty($perf['Description']) && Feature('OmitEmptyDescriptions')) continue;
      $Displayed[$perf['SideId']] = 1;
      $Imp = $perf['EffectiveImportance'];
      if ($Pair == 0) {
        if ($PairPageC >= $PairLimit) {
          echo "</table><table class='PerfT pagebreak' width=100% border>";
          $PairPageC = 0;
          $PairLimit = ($PageLimits[$Page++] ?? 7)+0;
        }
        $PairPageC++;
        echo "<tr>";
//var_dump("PAIR",$Pair, $PairLimit, $PairPageC, $Page);

        }


//      if ($Pair == 0) echo "<div class=PPair>";
      $Photo = $perf['Photo'];
      if (!$Photo) $Photo = '/images/icons/user2.png';
      echo "<td class=Pic$Pair rowspan=2><img src=$Photo class=PL$Imp>";
      if ($Pair == 1) echo "<tr>";
      echo "<td class=Desc$Pair ><span class=PName$Imp>" . $perf['SN'] . "</span> <span class=PDesc$Imp>" . $perf['Description'] . "</span>";

//      echo "<div class=PPPicP$Pair><img src=$Photo class=PPPic$Imp></div>";
//      echo "<div class=PPDescP$Pair><div class=PPName$Imp>" . $perf['SN'] . "</div><div PPDesc$Imp>" . $perf['Description'] . "</div></div>";
//      if ($Pair == 1) echo "</div><br>";
      $Pair = ($Pair+1)%2;
    }
    echo "</table>";
//    if ($Pair == 1) echo "</div>";
//    echo "<br clear=all>";
  }
  echo "</div>";
  exit;
?>

