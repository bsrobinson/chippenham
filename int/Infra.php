<?php

// List of Infrastructure and where it is and power needs

// Quick hack for power first

include_once("fest.php");
//include_once("TradeLib.php");
  A_Check('Committee','Venues');

  dostaffhead("Manage Other Infrastructure");
  global $PLANYEAR;

  $TradePower = Gen_Get_All("TradePower");
  $Powers = [];
  foreach ($TradePower as $i=>$P) $Powers[$i] = $P['Name'];

  
  echo "<div class=content><h2>Manage Infrastructure</h2>\n";
  echo "This is a short term hack, better and more coming.<p>";
  
  $Things = Gen_Get_All('Infrastructure');
  if (UpdateMany('Infrastructure','',$Things,0)) $Things=Gen_Get_All('Infrastructure');

  
  $coln = 0;
  $t = [];
  
  echo "<form method=post>";
  echo "<div class=Scrolltable+><table id=indextable border>\n";
  echo "<thead><tr>";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'N')>Index</a>\n";

  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Name</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>ShortName</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Category</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Status</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Colour</a>\n";

  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'N')>X pos</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'N')>Y pos</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'N')>Angle</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'N')>X size</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'N')>Y size</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'N')>Power</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Power From</a>\n";
  echo "<th><a href=javascript:SortTable(" . $coln++ . ",'T')>Power To</a>\n";

  
  echo "</thead><tbody>";
  if ($Things) foreach($Things as $t) {
    $i = $t['id'];
    echo "<tr><td>$i" . fm_text1("",$t,'Name',1,'','',"Name$i") . fm_text1("",$t,'ShortName',1,'','',"ShortName$i");
    echo "<td>"; // Category
    echo fm_number1('',$t,'Status','','',"Status$i");
    echo fm_text1("",$t,'MapColour',1,'','',"MapColour$i");
    echo fm_text1("",$t,'X',0.20,'','',"X$i") . fm_text1("",$t,'Y',0.20,'','',"Y$i");
    echo fm_text1("",$t,'Angle',0.20,'','',"Angle$i");
    echo fm_text1("",$t,'Xsize',0.20,'','',"Xsize$i") . fm_text1("",$t,'Ysize',0.20,'','',"Ysize$i");
    echo "<td>". fm_select($Powers,$t,'Power','','',"Power$i");
    echo fm_text1("",$t,'PowerFrom',1,'','',"PowerFrom$i") . fm_text1("",$t,'PowerTo',1,'','',"PowerTo$i");

    echo "\n";
  }
  $t = [];
  $i = 0;
    echo "<tr><td>$i" . fm_text1("",$t,'Name',1,'','',"Name$i") . fm_text1("",$t,'ShortName',1,'','',"ShortName$i");
    echo "<td>"; // Category
    echo fm_number1('',$t,'Status','','',"Status$i");
    echo fm_text1("",$t,'MapColour',1,'','',"MapColour$i");
    echo fm_text1("",$t,'X',0.20,'','',"X$i") . fm_text1("",$t,'Y',0.20,'','',"Y$i");
    echo fm_text1("",$t,'Angle',0.20,'','',"Angle$i");
    echo fm_text1("",$t,'Xsize',0.20,'','',"Xsize$i") . fm_text1("",$t,'Ysize',0.20,'','',"Ysize$i");
    echo "<td>". fm_select($Powers,$t,'Power','','',"Power$i");
    echo fm_text1("",$t,'PowerFrom',1,'','',"PowerFrom$i") . fm_text1("",$t,'PowerTo',1,'','',"PowerTo$i");

  echo "</table></div>\n";
  
  echo "<input type=submit name=Update value=Update>\n";
  echo "</form></div>";

  dotail();

?>