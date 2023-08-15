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

function mz_BrowserSync_GenIgnoreList($strList, $returnArr = false)
{
  $strList = trim($strList);
  $arrList = explode("\n", $strList);
  $arrList = array_map('trim', $arrList);
  if ($returnArr) {
    return $arrList;
  }
  return join(", ", $arrList);
}

function mz_BrowserSync_BuildConsole()
{
  global $zbp;
  $tpl = <<<EOF

<!-- br -->

browser-sync start --config 'confPath' --proxy "zbpHost" \
--cwd "appPath" \
--files "fileTypes"
# —— Tips
EOF;

  $console = file_get_contents(mz_BrowserSync_Path('v-console'));
  $console .= strtr($tpl, array(
    "Tips" => "只监听插件变化",
    "fileTypes" => "fileTypes-plugin",
  ));
  $console .= strtr($tpl, array(
    "Tips" => "只监听主题变化",
    "fileTypes" => "fileTypes-theme",
  ));
  $console .= strtr($tpl, array(
    "Tips" => "全都要",
  ));

  $arrLog = array();
  $arrLog["zbpHost"] = $zbp->host;

  // 获取实际路径
  $usersdir = $zbp->Config('mz_BrowserSync')->usersdir;
  if (empty($usersdir)) {
    $arrLog["appPath"] = $zbp->usersdir;
  } else {
    $arrLog["appPath"] = $usersdir;
  }

  // usr/bs-config.js 拼接
  $arrLog["confPath"] = $arrLog["appPath"] . "/plugin/mz_BrowserSync/usr/bs-config.js";

  // 获取文件类型
  $defFileTypes = file_get_contents(mz_BrowserSync_Path('v-fileTypes'));
  $fileTypes = $zbp->Config('mz_BrowserSync')->fileTypes;
  $ignoreList = $zbp->Config('mz_BrowserSync')->ignoreList;
  if (empty($fileTypes)) {
    $fileTypes = $defFileTypes;
  }
  $arrLog["fileTypes-plugin"] = mz_BrowserSync_GenFileTypes($fileTypes, "plugin");
  $arrLog["fileTypes-theme"] = mz_BrowserSync_GenFileTypes($fileTypes, "theme");
  $arrLog["fileTypes"] = mz_BrowserSync_GenFileTypes($fileTypes);
  $arrLog["ignoreList"] = mz_BrowserSync_GenIgnoreList($ignoreList);

  $tplTr = "<tr><td><pre>%s</pre></td></tr>";

  $str = strtr($console, $arrLog);
  $arr = explode("<!-- br -->\n", $str);

  $result = "";
  foreach ($arr as $key => $val) {
    $val = trim($val) . "\n ";
    $result .= sprintf($tplTr, $val);
  }

  return $result;
}

function mz_BrowserSync_GenUsrConf()
{
  global $zbp;

  $dir = mz_BrowserSync_Path('usr');
  if (!is_dir($dir)) {
    @mkdir($dir, 0755);
  }

  $uConfFile = mz_BrowserSync_Path('u-config');
  $vConfFile = mz_BrowserSync_Path('v-config');
  $vConf = file_get_contents($vConfFile);
  $ignoreList = $zbp->Config('mz_BrowserSync')->ignoreList;

  $arrLog = array();
  $arrLog['["ignoreList"]'] = json_encode(mz_BrowserSync_GenIgnoreList($ignoreList, true));

  $str = strtr($vConf, $arrLog);
  file_put_contents($uConfFile, $str);
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
    case 'v-config':
      return $result . 'var/bs-config.js';
      break;
    case 'u-config':
      return $result . 'usr/bs-config.js';
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
  mz_BrowserSync_GenUsrConf();
}

function UninstallPlugin_mz_BrowserSync()
{
}
