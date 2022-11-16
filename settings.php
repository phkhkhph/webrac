<title>Настройки</title>
<link rel="stylesheet" href="main.css">
<?php
session_start();

$locale='ru_RU.UTF-8';
setlocale(LC_ALL,$locale);
putenv('LC_ALL='.$locale);
$ParamsFile = '/var/www/.var/adm1c-params.php';

if (isset($_POST['apply'])) {
  file_put_contents ($ParamsFile, '<?php $Params = ' . var_export($_POST, true) . '; ?>');
  session_unset();
  }

include $ParamsFile;

echo "<form method=\"post\" style=\"width:600px;\">";
echo "<a href=\"/adm1c/\" class=ref>На главную</a>";
echo "<h3>Настройки</h3>";

$LicDistrChecked = "";
if ($Params[LicDistr] == "allow") {$LicDistrChecked = "checked";}
$JobsDenyChecked = "";
if ($Params[JobsDeny] == "yes") {$JobsDenyChecked = "checked";}

echo "<fieldset class=fieldset><legend>Параметры подключения к серверу 1С</legend><table>";
echo "<tr><td align=right><label for=\"Server1C\">Сервер 1C с запущенным RAS</label></td><td><input type=\"text\" id=\"Server1C\" name=\"Server1C\" value=\"$Params[Server1C]\"></td></tr>";
echo "</table><br>";
echo "</fieldset>";

echo "<fieldset class=fieldset><legend>Параметры по умолчанию для создания базы 1С</legend><table>";
echo "<tr><td align=right><label for=\"DBServer\">Сервер баз данных</label></td><td><input type=\"text\" id=\"DBServer\" name=\"DBServer\" value=\"$Params[DBServer]\"></td></tr>";

echo "\n<tr><td align=right><label for=\"DBMS\">Тип СУБД</label></td><td><select id=\"DBMS\" name=\"DBMS\">";
$DBMSAr = array ("PostgreSQL","MSSQLServer","IBMDB2","OracleDatabase");
foreach ($DBMSAr as $El){
  if ($Params[DBMS] == $El){
    $ElStr = "<option selected>$El</option>";
    } else {
    $ElStr = "<option>$El</option>";
    }
  echo "$ElStr";
  }
echo "</select></td></tr>\n";

echo "<tr><td align=right><label for=\"DBUser\">Пользователь сервера БД</label></td><td><input type=\"text\" id=\"DBUser\" name=\"DBUser\" value=\"$Params[DBUser]\"></td></tr>";
echo "<tr><td align=right><label for=\"DBPwd\">Пароль пользователя БД</label></td><td><input type=\"password\" id=\"DBPwd\" name=\"DBPwd\" value=\"$Params[DBPwd]\" title=\"$Params[DBPwd]\"></td></tr>";
echo "<tr><td align=right><label for=\"LicDistr\">Разрешить выдачу лицензий сервером 1С</label></td><td><input type=\"checkbox\" name=\"LicDistr\" value=\"allow\" $LicDistrChecked></td></tr>";
echo "<tr><td align=right><label for=\"JobsDeny\">Блокировка регламентных заданий включена</label></td><td><input type=\"checkbox\" name=\"JobsDeny\" value=\"yes\" $JobsDenyChecked></td></tr>";
echo "</table><br>";
echo "</fieldset>";

echo "<button style=\"float: right;\" type=\"submit\" value=\"apply\" name=\"apply\" id=\"apply\">Применить</button>";
echo "</form><br>";

?>
