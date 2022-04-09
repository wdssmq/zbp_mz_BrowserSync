<?php
#注册插件
RegisterPlugin("mz_BrowserSync", "ActivePlugin_mz_BrowserSync");

function ActivePlugin_mz_BrowserSync()
{
}

function mz_BrowserSync_GenFileTypes($strList, $type = "all")
{
  $arrRlt = array();
  $strList = trim($strList);
  $arrList = explode("\n", $strList);
  $arrList = array_map('trim', $arrList);
  foreach ($arrList as $key => $val) {
    if ($type == "all" || $type == "plugin") {
      $arrRlt[] = "plugin/{$val}";
    }
    if ($type == "all" || $type == "theme") {
      $arrRlt[] = "theme/{$val}";
    }
  }
  return implode(", ", $arrRlt);
}

function mz_BrowserSync_BuildConsole()
{
  global $zbp;
  $console = file_get_contents(mz_BrowserSync_Path('v-console'));

  $arrLog = array();
  $arrLog["zbpHost"] = $zbp->host;

  // 获取实际路径
  $usersdir = $zbp->Config('mz_BrowserSync')->usersdir;
  if (empty($usersdir)) {
    $arrLog["appPath"] = $zbp->usersdir;
  } else {
    $arrLog["appPath"] = $usersdir;
  }

  // 获取文件类型
  $defFileTypes = file_get_contents(mz_BrowserSync_Path('v-fileTypes'));
  $fileTypes = $zbp->Config('mz_BrowserSync')->fileTypes;
  if (empty($fileTypes)) {
    $fileTypes = $defFileTypes;
  }
  $arrLog["fileTypes-plugin"] = mz_BrowserSync_GenFileTypes($fileTypes, "plugin");
  $arrLog["fileTypes-theme"] = mz_BrowserSync_GenFileTypes($fileTypes, "theme");
  $arrLog["fileTypes"] = mz_BrowserSync_GenFileTypes($fileTypes);

  return strtr($console, $arrLog);
}

function mz_BrowserSync_Path($file, $t = 'path')
{
  global $zbp;
  $result = $zbp->$t . 'zb_users/plugin/mz_BrowserSync/';
  switch ($file) {
    case 'v-console':
      return $result . 'var/console.txt';
      break;
    case 'v-fileTypes':
      return $result . 'var/fileTypes.txt';
      break;
    case 'usr':
      return $result . 'usr/';
      break;
    case 'var':
      return $result . 'var/';
      break;
    case 'main':
      return $result . 'main.php';
      break;
    default:
      return $result . $file;
  }
}

function InstallPlugin_mz_BrowserSync()
{
}

function UninstallPlugin_mz_BrowserSync()
{
}
