<link rel="stylesheet" href="main.css">
<?php
session_start();

$locale='ru_RU.UTF-8';
setlocale(LC_ALL,$locale);
putenv('LC_ALL='.$locale);
$ParamsFile = '/var/www/.var/adm1c-params.php';
include $ParamsFile;

$LogPass = "--infobase-user=$Params[User1C] --infobase-pwd=$Params[Pwd1C]";
$Cluster = $_SESSION[cluster];
$InfoBase = $_GET[infobase];

if (isset($_POST['delete'])) {
  $DBState = "";
  if ($_POST['db-state'] == "db-delete") {
    $DBState = "--drop-database";
    }
  if ($_POST['db-state'] == "db-clean") {
    $DBState = "--clear-database";
    }
  exec ("rac $Params[Server1C] infobase --cluster=$Cluster drop --infobase=$InfoBase $LogPass $DBState", $RacOut, $Error);
  header('Location: /adm1c/');
  }

if (isset($_POST['update'])) {
  $InfoBase = $_POST[infobase];
  $DenFrom = $_POST['den-from'];
  $DenTo = $_POST['den-to'];
  $DenMess = $_POST['den-mess'];
  $PermCode = $_POST['perm-code'];
  $JobsDeny = "off";
  if (isset($_POST['jobs-deny'])) {
    $JobsDeny = "on";
    }
  $LicDistr = "deny";
  if (isset($_POST['lic-distr'])) {
    $LicDistr = "allow";
    }
   exec ("rac $Params[Server1C] infobase --cluster=$Cluster update --infobase=$InfoBase $LogPass --descr=\"$_POST[descr]\" --denied-from=\"$DenFrom\" --denied-to=\"$DenTo\" --denied-message=\"$DenMess\" --permission-code=\"$PermCode\" --sessions-deny=off --scheduled-jobs-deny=$JobsDeny --license-distribution=$LicDistr", $RacOut, $Error);
  }

$RacOut = array();
exec ("rac $Params[Server1C] infobase --cluster=$Cluster info --infobase=$InfoBase $LogPass", $RacOut, $Error);
$Base = array();
if ($Error == 0){
  foreach ($RacOut as $RacStr) {
    $ArStr = explode (" :", $RacStr);
    $ArStr[0] = trim ($ArStr[0]);
    $ArStr[1] = trim ($ArStr[1]);
    $Base[$ArStr[0]] = $ArStr[1];
    }
  }

echo "<title>Управление базой $Base[name]</title>";

// echo '<pre>';
// print_r ($Base);
// echo '</pre>';

$DBServer = $Base['db-server'];
$DBName = $Base['db-name'];
$DBUser = $Base['db-user'];
$DBPwd = "logotip";
$LicDistr = $Base['license-distribution'];
$LicDistrChecked = '';
if ($LicDistr == "allow") { $LicDistrChecked = "checked"; }
$DenFrom = $Base['denied-from'];
$DenTo = $Base['denied-to'];
$DenMess = str_replace('"', '', $Base['denied-message']);
$PermCode = str_replace('"', '', $Base['permission-code']);
$JobsDeny = $Base['scheduled-jobs-deny'];
$JobsDenyChecked = '';
$JobsDenyValue = 'off';
if ($JobsDeny == "on") {
  $JobsDenyChecked = "checked";
  $JobsDenyValue = "on";
  }
$SessDeny = $Base['sessions-deny'];
$SessDenyChecked = '';
$SessDenyValue = 'off';
if ($SessDeny == "on") {
  $SessDenyChecked = "checked";
  $SessDenyValue = "on";
  }
$Descr = str_replace('"', '', $Base[descr]);

echo "<form method=\"post\" style=\"width:600px;\">";
echo "<p><a href=\"/adm1c/\" class=ref>На главную</a><a href=\"settings.php\" class=ref>Настройки</a></p>";
echo "<h3>База $Base[name]</h3>";
echo "<fieldset class=fieldset><legend>Свойства базы</legend><table>";

echo "<tr><td align=right><label for=\"name\">Имя</label></td><td><input type=\"text\" id=\"name\" name=\"name\" value=\"$Base[name]\" disabled></td></tr>";
echo "<tr><td align=right><label for=\"descr\">Описание</label></td><td><input type=\"text\" id=\"descr\" name=\"descr\" value=\"$Descr\"></td></tr>";
echo "<tr><td align=right><label for=\"db-server\">Сервер баз данных</label></td><td><input type=\"text\" id=\"db-server\" name=\"db-server\" value=\"$DBServer\" disabled></td></tr>";
echo "<tr><td align=right><label for=\"dbms\">Тип СУБД</label></td><td><input type=\"text\" id=\"dbms\" name=\"dbms\" value=\"$Base[dbms]\" disabled></td></tr>";
echo "<tr><td align=right><label for=\"db-name\">База данных</label></td><td><input type=\"text\" id=\"db-name\" name=\"db-name\" value=\"$DBName\" disabled></td></tr>";
echo "<tr><td align=right><label for=\"db-user\">Пользователь сервера БД</label></td><td><input type=\"text\" id=\"db-user\" name=\"db-user\" value=\"$DBUser\" disabled></td></tr>";
echo "<tr><td align=right><label for=\"db-pwd\">Пароль пользователя БД</label></td><td><input type=\"password\" id=\"db-pwd\" name=\"db-pwd\" value=\"$DBPwd\" title=\"$DBPwd\" disabled></td></tr>";
echo "<tr><td align=right><label for=\"lic-distr\">Разрешить выдачу лицензий сервером 1С</label></td><td><input type=\"checkbox\" name=\"lic-distr\" $LicDistrChecked></td></tr>";
echo "<tr><td align=right><label for=\"jobs-deny\">Блокировка регламентных заданий включена</label></td><td><input type=\"checkbox\" name=\"jobs-deny\" value=\"$JobsDenyValue\" $JobsDenyChecked></td></tr>";

echo "</table>";
echo "<fieldset name=\"SessDenyFields\" class=fset><legend>Блокировка начала сеансов включена<input type=\"checkbox\" name=\"SessDeny\" value=\"$SessDenyValue\" $SessDenyChecked></legend><table>";

echo "<tr><td align=right><label for=\"den-from\">Начало (yyyy-mm-ddThh:mm:ss)</label></td><td><input type=\"text\" id=\"den-from\" name=\"den-from\" value=$DenFrom></td></tr>";
echo "<tr><td align=right><label for=\"den-to\">Окончание (yyyy-mm-ddThh:mm:ss)</label></td><td><input type=\"text\" id=\"den-to\" name=\"den-to\" value=$DenTo></td></tr>";
echo "<tr><td align=right><label for=\"den-mess\">Сообщение</label></td><td><input type=\"text\" id=\"den-mess\" name=\"den-mess\" value=\"$DenMess\"></td></tr>";
echo "<tr><td align=right><label for=\"perm-code\">Код разблокировки</label></td><td><input type=\"text\" id=\"perm-code\" name=\"perm-code\" value=\"$PermCode\"></td></tr>";

echo "<input type=\"hidden\" name=\"infobase\" value=\"$InfoBase\">";

echo "</table></fieldset>";
echo "<button style=\"float: right;\" type=\"submit\" value=\"update\" name=\"update\" id=\"update\" onClick=\"Waiting()\">Обновить</button>";
echo "</fieldset>";

echo "<fieldset class=fieldset><legend>Удалить базу</legend>";
echo "<p align=left><input name=\"db-state\" type=\"radio\" value=\"db-not-change\"> Оставить базу данных без изменений</p>";
echo "<p align=left><input name=\"db-state\" type=\"radio\" value=\"db-delete\" checked> Удалить базу данных</p>";
echo "<p align=left><input name=\"db-state\" type=\"radio\" value=\"db-clean\"> Очистить базу данных</p>";
echo "<button style=\"float: right;\" value=\"delete\" name=\"delete\" id=\"delete\" onClick=\"return SureDeleted()\">Удалить...</button>";
echo "</fieldset>";

echo "</form>";

echo "<div class = \"mess\" id=\"WaitPic\"><img class=\"wait\"src=\"wait2.gif\"></div>";

// echo '<pre>';
// print_r ($_POST);
// echo '</pre>';

?>

<script>
function SureDeleted(){
  const BaseName = document.getElementById('name');
  result = confirm("База "+BaseName.value+" будет удалена. Вы точно это желаете?");
  if (result){
    Waiting();
    }
  return result;
  }
function Waiting(){
  const WaitPic = document.getElementById('WaitPic');
  WaitPic.style.display = "block";
  }

window.onload = function () {
  const WaitPic = document.getElementById('WaitPic');
  WaitPic.style.display = "none";
  }
</script>
