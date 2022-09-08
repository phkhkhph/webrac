<title>Сеансы</title>
<link rel="stylesheet" href="main.css">
<?php
session_start();

$locale='ru_RU.UTF-8';
setlocale(LC_ALL,$locale);
putenv('LC_ALL='.$locale);
$ParamsFile = '/var/www/.var/adm1c-params.php';
include $ParamsFile;
$Cluster = $_SESSION[cluster];

echo "<a href=\"/adm1c/\" class=ref>На главную</a><a href=\"settings.php\" class=ref>Настройки</a>";
echo "<h3>Сеансы на $Params[Server1C]</h3>";

$RacOut = array();
exec ("rac $Params[Server1C] infobase --cluster=$Cluster summary list", $RacOut, $Error);

// echo '<pre>';
// print_r ($RacOut);
// echo '</pre>';

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

// echo '<pre>';
// print_r ($Bases);
// echo '</pre>';

$RacOut = array();
exec ("rac $Params[Server1C] session list --cluster=$Cluster", $RacOut, $Error);

// echo '<pre>';
// echo "Массив $RacOut:\n";
// print_r ($RacOut);
// echo '</pre>';

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

echo '<pre>';
echo "Массив \$UsersSess:\n";
print_r ($UsersSess);
echo '</pre>';

echo '<pre>';
echo "Массив \$BasesSess:\n";
print_r ($BasesSess);
echo '</pre>';

echo "<table class=tok><tr><th style=\"display:none;\">Сессия</th><th>База</th><th>Пользователь</th><th>Узел</th><th>IP клиента</th><th>Приложение</th><th>Начало сеанса</th><th>Последняя активность</th></tr>";
foreach ($Sessions as $Sess){
  echo "<tr>";
  echo "<td style=\"display:none;\">{$Sess['session']}</td><td>{$Sess['infobase-name']}</td><td>{$Sess['user-name']}</td><td>{$Sess['host']}</td><td>{$Sess['client-ip']}</td><td>{$Sess['app-id']}</td><td>{$Sess['started-at']}</td><td>{$Sess['last-active-at']}</td>";
  echo "</tr>";
  }
echo "</table>";

// echo '<pre>';
// echo "Массив $Sessions:\n";
// print_r ($Sessions);
// echo '</pre>';

?>
