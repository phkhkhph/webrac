<title>Сеансы</title>
<link rel="stylesheet" href="main.css">
<?php
session_start();

$locale='ru_RU.UTF-8';
setlocale(LC_ALL,$locale);
putenv('LC_ALL='.$locale);
$ParamsFile = '/var/www/.var/adm1c-params.php';
include $ParamsFile;
include 'functions.php';
$Cluster = $_SESSION['cluster'];
$HostName = strtoupper(gethostname());

if (isset($_POST['terminate'])) {
  SessionTerminate ($_POST['terminate']);
  header('Location:'.$_SERVER['HTTP_REFERER']);
  }

if (isset($_POST['SessClose'])) {
  if (isset($_POST['SchedDeny'])){
    ScheduleDeny ($_POST['Base']);
    }
  if (isset($_POST['SessDeny'])){
    SessionsBlocked ($_POST['Base']);
    }
  SessionsTerminate ($_POST['Base']);
  sleep (2);
  header('Location:'.$_SERVER['HTTP_REFERER']);
  }

echo "<a href=\"/adm1c/\" class=ref>На главную</a><a href=\"settings.php\" class=ref>Настройки</a>";
echo "<h3>Сеансы на " . $Params['Server1C'] . "(" . $HostName . ")</h3>";

$RacOut = array();
exec ("rac " . $Params['Server1C'] . " infobase --cluster=" . $Params['Cluster'] . " summary list", $RacOut, $Error);

$Client = array(
  'BackgroundJob'=>'Фоновое задание',
  '1CV8'=>'Толстый клиент',
  '1CV8C'=>'Тонкий клиент',
  'Designer'=>'Конфигуратор',
  );

$Bases = array();
if ($Error == 0){
  $Base = array();
  foreach ($RacOut as $RacStr) {
    $ArStr = explode (" :", $RacStr);
    $ArStr[0] = trim ($ArStr[0]);
    $ArStr[1] = trim ($ArStr[1]);
    $Base[$ArStr[0]] = $ArStr[1];
    if ($RacStr == ""){
      $Bases[$Base[infobase]] = $Base;
      $Base = array();
      }
    }
  }

$RacOut = array();
exec ("rac " . $Params['Server1C'] . " session list --cluster=" . $Params['Cluster'], $RacOut, $Error);

$Sessions = array();
$UsersSess = array();
$BasesSess = array();
if ($Error == 0){
  $Session = array();
  foreach ($RacOut as $RacStr) {
    $ArStr = explode (" :", $RacStr);
    $ArStr[0] = trim ($ArStr[0]);
    $ArStr[1] = trim ($ArStr[1]);
    $Session[$ArStr[0]] = $ArStr[1];
    if ($RacStr == ""){
      $Session['infobase-name'] = $Bases[$Session[infobase]][name];
      $Sessions[$Session[session]] = $Session;
      $UsersSess[] = $Session['user-name'];
      $BasesSess[] = $Session['infobase-name'];
      $Session = array();
      }
    }
  }

$UsersSess = array_unique ($UsersSess);
asort ($UsersSess,SORT_FLAG_CASE+SORT_STRING);
$BasesSess = array_unique ($BasesSess);
asort ($BasesSess,SORT_FLAG_CASE+SORT_STRING);

echo "<form method=post style=\"width:1500px;\">";
echo "<table class=tok width=98%><tr><th style=\"display:none;\">Сессия</th><th></th><th>База</th><th>Пользователь</th><th>Узел</th><th>IP клиента</th><th>Приложение</th><th>Начало сеанса</th><th>Последняя активность</th></tr>\n";
foreach ($Sessions as $Sess){
  echo "<tr>";
  $button = "<button type=\"submit\" name=\"terminate\" value=\"{$Sess['session']}\" title=\"Завершить сеанс\"><img src=close.png width=12 height=12></button>";
  echo "<td style=\"display:none;\">{$Sess['session']}</td><td>$button</td><td>{$Sess['infobase-name']}</td><td>{$Sess['user-name']}</td><td>{$Sess['host']}</td><td>{$Sess['client-ip']}</td><td>{$Client[$Sess['app-id']]}</td><td>{$Sess['started-at']}</td><td>{$Sess['last-active-at']}</td>\n";
  echo "</tr>";
  }
echo "</table>";
echo "<fieldset class=fieldset><legend>Завершить сеансы в базе</legend><table>";

echo "<select id=\"Base\" name=\"Base\">";
foreach ($Bases as $El){
  $ElStr = "<option>$El[name]</option>";
  echo "$ElStr";
  }
echo "</select>";
echo "&nbsp;&nbsp;<input type=\"checkbox\" name=\"SchedDeny\" $SchedDenyChecked><label for=\"SchedDeny\">Запретить фоновые задания</label>";
echo "&nbsp;&nbsp;<input type=\"checkbox\" name=\"SessDeny\" $SessDenyChecked><label for=\"SessDeny\">Запретить новые сеансы (код разблокировки \"blocked\")</label>";
echo "<button style=\"float: right;\" value=\"SessClose\" name=\"SessClose\" id=\"SessClose\" onClick=\"Waiting()\">Завершить сеансы</button>";

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
