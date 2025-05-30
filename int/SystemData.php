<?php
  include_once("fest.php");
  A_Check('SysAdmin');
  global $FESTSYS,$VERSION;
  dostaffhead("System Data Settings");

  $FESTSYS = Gen_Get('SystemData',1);
  if (isset($_REQUEST['ACTION'])) { // Not Used
    switch ($_REQUEST['ACTION']) {
      default:
      break;
    }
  }

  echo "<h2>System Data Settings and Global Actions</h2>\n";
  
  echo "<h2><a href=ShowFeatureHelp>Show Feature Help</a></h2>";

// var_dump($_REQUEST);

  if (isset($_REQUEST['Update'])) {

    Update_db_post('SystemData',$FESTSYS);
    Feature_Reset();
    $txt = ":root {\n--main-col:" . Feature('CSSColourMain','#fcb900') . ";\n";
    $txt .= "--main-col-dark:" . Feature('CSSColourDark','#993300') . ";\n";
    $txt .= "--main-contrast:" . Feature('CSSColourContrast','#1a0000') . ";\n";
    $txt .= "--header-link:" . Feature('CSSColourHeader','#404040') . ";\n";
    $txt .= "--main-link:" . Feature('CSSColourMainLink','#b32d00') . ";\n";
    $txt .= "--menu-donate:" . Feature('CSSColourDonate','#e67300') . ";\n";
    $txt .= "--menu-dropdown:" . Feature('CSSColourDropdown','#f0f0f0') . ";\n";
    $txt .= "--menu-navbutton:" . Feature('CSSColourNavButton','#a30046') . ";\n";
    $txt .= "--TableTue:" . Feature('CSSColourTue','slategray') . ";\n";
    $txt .= "--TableWed:" . Feature('CSSColourWed','seagreen') . ";\n";
    $txt .= "--TableThur:" . Feature('CSSColourThur','darkcyan') . ";\n";
    $txt .= "--TableFri:" . Feature('CSSColourFri','slategray') . ";\n";
    $txt .= "--TableSat:" . Feature('CSSColourSat','seagreen') . ";\n";
    $txt .= "--TableSun:" . Feature('CSSColourSun','darkcyan') . ";\n";
    $txt .= "--TableMon:" . Feature('CSSColourMon','peru') . ";\n";
    $txt .= "--DayTabBack:" . Feature('CSSColourDayTabBack','#f4f4f4') . ";\n";
    $txt .= "--DayArtTitle:" . Feature('CSSColourArtTitle','#005C8F') . ";\n";
    $txt .= "--TicketHover:" . Feature('CSSColourTicket','#CF1F84') . ";\n";
 //   $txt .= "--DayArtTitle:" . Feature('CSSColourDonate','#005C8F') . ";\n";
    $txt .= "--private_bar:" . Feature('CSSColourPrivate','#fff0b3') . ";\n}\n";
    $txt .= '/* Do not edit this file it is dynamically created - edit system data or the Basestyle.css*/\n\n';

    if (!file_put_contents('cache/FestStyle.css',$txt)) {
      echo "<h2 class=Err>Failed to FestStyle Style - call Richard</h2>";
    }
    
      
    $Css = file_get_contents('files/Basestyle.css');

    $txt .= $Css;
    if (file_put_contents('cache/Style.css',$txt)) {
  
      $Vtxt = "<?php\n\$VERSION=\"$VERSION-\"\n?>\n";
      file_put_contents('Version.php',$Vtxt);
  
      echo "Data and CSS updated<p>";
    } else {
      echo "<h2 class=Err>Failed to update Style - call Richard</h2>";
    }

  }


  echo "<form method=post>\n";
//  Register_AutoUpdate('SystemData',1); - Switching to an Update button
  echo "<div class=tablecont><table>";
  echo "<tr>" . fm_textarea("Features",$FESTSYS,'Features',6,40);
  if (Access('Internal')) {
    echo "<tr>" . fm_textarea("Capabilities",$FESTSYS,'Capabilities',6,10);
    echo "<tr>" . fm_text('Update Version #',$FESTSYS,'CurVersion') . "<td>Never edit";
    echo "<tr>" . fm_text('Version Changed',$FESTSYS,'VersionDate') . "<td>Never edit";
  }
  echo "<tr>" . fm_textarea("Analytics code",$FESTSYS,'Analytics',3,3);

  if (Access('SysAdmin')) echo "<tr><td class=NotSide>Debug<td colspan=6 class=NotSide><textarea id=Debug></textarea>";
  echo "<tr>" . fm_submit('Update','Update');
  echo "</table></div>\n";

  echo "</form>\n";

/*  $feet = $FESTSYS['Features'];

  $Dat = parse_ini_string($feet);

  var_dump($Dat);*/

  echo "Features: are in php ini format.<br>; Comments start with ';', as in php.ini<p>";
  dotail();

?>
