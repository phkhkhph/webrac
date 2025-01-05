<title>Администрирование 1С</title>
<link rel="stylesheet" href="main.css">

<?php
session_start();

$locale='ru_RU.UTF-8';
setlocale(LC_ALL,$locale);
putenv('LC_ALL='.$locale);
$ParamsFile = '/var/www/.var/adm1c-params.php';
$Cluster = $_SESSION['cluster'];
$HostName = strtoupper(gethostname());

include $ParamsFile;

if (isset($_POST['create'])) {
  $BaseName = $_POST['name'];
  $Descr = $_POST['descr'];
  $DBServer = $Params[DBServer];
  $DBMS = $Params[DBMS];
  $DBUser = $Params[DBUser];
  $DBPwd = $Params[DBPwd];
  $LicDistr = "deny";
  if (isset($Params[LicDistr])) {$LicDistr = "allow";}
  exec ("rac $Params[Server1C] infobase --cluster=$Cluster create --create-database --name=$BaseName --descr=\"$Descr\" --dbms=$DBMS --db-server=$DBServer --db-name=$BaseName --locale=ru --db-user=$DBUser --db-pwd=$DBPwd --license-distribution=$LicDistr", $RacOut, $Error);

  $Base = array();
  foreach ($RacOut as $RacStr) {
    $ArStr = explode (" :", $RacStr);
    $ArStr[0] = trim ($ArStr[0]);
    $ArStr[1] = trim ($ArStr[1]);
    $Base[$ArStr[0]] = $ArStr[1];
    }
  $RetBase = $Base['infobase'];
  header('Location:'.$_SERVER['HTTP_REFERER']);
  }

exec ("rac " . $Params['Server1C'] . " cluster list", $RacOut, $Error);
$Clusters = array();
if ($Error == 0){
  foreach ($RacOut as $RacStr) {
    $ArStr = explode (" :", $RacStr);
    $ArStr[0] = trim ($ArStr[0]);
    $ArStr[1] = trim ($ArStr[1]);
    $Clusters[$ArStr[0]] = $ArStr[1];
    }
  }
$Cluster = $Clusters['cluster'];
$_SESSION['cluster'] = $Cluster;
echo ("<a href=\"settings.php\" class=ref>Настройки</a><a href=\"sessions.php\" class=ref>Сеансы</a>");
echo "<h3>$Clusters[name] $Clusters[host]:$Clusters[port] ($HostName)</h3>";

$RacOut = '';
exec ("rac --version", $RacOut, $Error);
if ($Error == 0){
  echo "Версия платформы: " . $RacOut[0];
}

$RacOut = array();
exec ("rac " . $Params['Server1C'] . " infobase --cluster=" . $Params['Cluster'] . " summary list", $RacOut, $Error);

$Bases = array();
if ($Error == 0){
  $Base = array();
  foreach ($RacOut as $RacStr) {
    $ArStr = explode (" :", $RacStr);
    $ArStr[0] = trim ($ArStr[0]);
    $ArStr[1] = trim ($ArStr[1]);
    $Base[$ArStr[0]] = $ArStr[1];
    if ($RacStr == ""){
      $Bases[$Base['name']] = $Base;
      $Base = array();
      }
    }
  }
ksort ($Bases,SORT_FLAG_CASE+SORT_STRING);

echo "<fieldset style=\"width:600px;\" class=fieldset><legend>Информационные базы (".count($Bases)." шт.)</legend>";

echo "<table width=100%><tr><td valign=top width=50%>";
$Row = 0;
foreach (array_keys ($Bases) as $Base){
  echo ("<a href=\"base.php?infobase=" . $Bases[$Base]['infobase'] . "&basename=" . $Bases[$Base]['name'] . "\" onClick=\"Waiting()\">" . $Bases[$Base]['name'] . "</a><br>");
  $Row++;
  if ($Row >= count($Bases)/2){
    echo "</td><td valign=top width=50%>";
    $Row = 0;
    }
  }
echo "</td></tr></table>";
echo "</fieldset>";

echo "<form method=\"post\" id=\"fMain\">";
echo "<fieldset style=\"width:600px;\" class=fieldset><legend>Создать базу</legend><center><table>";
echo "<tr><td align=right><label for=\"name\">Имя новой базы</label></td><td><input type=\"text\" id=\"name\" name=\"name\" value=\"\"></td></tr>";
echo "<tr><td align=right><label for=\"descr\">Описание</label></td><td><input type=\"text\" id=\"descr\" name=\"descr\" value=\"\"></td></tr>";
echo "</table></center>";
echo "<p align=left>Будет создана база 1С с параметрами, установленными по умолчанию. На сервере баз данных будет создана база с тем-же именем, в случае её отсутствия.</p>";
echo "<button style=\"float: right;\" value=\"create\" name=\"create\" id=\"create\" onClick=\"Waiting()\">Создать</button>";
echo "</fieldset>";
echo "</form>";

echo "<div class = \"mess\" id=\"WaitPic\"><img class=\"wait\"src=\"wait2.gif\"></div>";

?>

<script>
function Waiting(){
  const WaitPic = document.getElementById('WaitPic');
  WaitPic.style.display = "block";
  }

window.onload = function () {
  const WaitPic = document.getElementById('WaitPic');
  WaitPic.style.display = "none";
  }
</script>
