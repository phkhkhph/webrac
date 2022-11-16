<?php

function ScheduleDeny ($Base){
  include '/var/www/.var/adm1c-params.php';
  include '/var/www/.var/adm1c-bases.php';
  $LogPass = "--infobase-user=\"{$Bases[$Base][User1C]}\" --infobase-pwd=\"{$Bases[$Base][Pass1C]}\"";
  $InfoBase = trim(`rac $Params[Server1C] infobase --cluster=$_SESSION[cluster] summary list | grep -w $Base -B 1 | grep -w infobase | awk '{print $3}'`);
  exec ("rac $Params[Server1C] infobase --cluster=$_SESSION[cluster] update --infobase=$InfoBase $LogPass --scheduled-jobs-deny=on 2>&1", $RacOut, $Error);
  }

function ScheduleAllow ($Base){
  include '/var/www/.var/adm1c-params.php';
  include '/var/www/.var/adm1c-bases.php';
  $LogPass = "--infobase-user=\"{$Bases[$Base][User1C]}\" --infobase-pwd=\"{$Bases[$Base][Pass1C]}\"";
  $InfoBase = trim(`rac $Params[Server1C] infobase --cluster=$_SESSION[cluster] summary list | grep -w $Base -B 1 | grep -w infobase | awk '{print $3}'`);
  exec ("rac $Params[Server1C] infobase --cluster=$_SESSION[cluster] update --infobase=$InfoBase $LogPass --scheduled-jobs-deny=off 2>&1", $RacOut, $Error);
  }

function SessionsBlocked ($Base){
  include '/var/www/.var/adm1c-params.php';
  include '/var/www/.var/adm1c-bases.php';
  $LogPass = "--infobase-user=\"{$Bases[$Base][User1C]}\" --infobase-pwd=\"{$Bases[$Base][Pass1C]}\"";
  $InfoBase = trim(`rac $Params[Server1C] infobase --cluster=$_SESSION[cluster] summary list | grep -w $Base -B 1 | grep -w infobase | awk '{print $3}'`);
  exec ("rac $Params[Server1C] infobase --cluster=$_SESSION[cluster] update --infobase=$InfoBase $LogPass --permission-code=blocked --sessions-deny=on 2>&1", $RacOut, $Error);
  }

function SessionsUnblocked ($Base){
  include '/var/www/.var/adm1c-params.php';
  include '/var/www/.var/adm1c-bases.php';
  $LogPass = "--infobase-user=\"{$Bases[$Base][User1C]}\" --infobase-pwd=\"{$Bases[$Base][Pass1C]}\"";
  $InfoBase = trim(`rac $Params[Server1C] infobase --cluster=$_SESSION[cluster] summary list | grep -w $Base -B 1 | grep -w infobase | awk '{print $3}'`);
  exec ("rac $Params[Server1C] infobase --cluster=$_SESSION[cluster] update --infobase=$InfoBase $LogPass --permission-code=\"\" --sessions-deny=off 2>&1", $RacOut, $Error);
  }

function SessionTerminate ($Session){
  `rac $Params[Server1C] session terminate --cluster=$_SESSION[cluster] --session=$Session`;
  }

function SessionsTerminate ($Base){
  $InfoBase = trim(`rac $Params[Server1C] infobase --cluster=$_SESSION[cluster] summary list | grep -w $Base -B 1 | grep -w infobase | awk '{print $3}'`);
  exec ("rac $Params[Server1C] session list --cluster=$_SESSION[cluster] | grep -w $InfoBase -B 2 | grep -w \"session \" | awk '{print $3}'", $RacOut, $Error);
  foreach ($RacOut as $Session) {
    SessionTerminate ($Session);
    }
  }
?>
