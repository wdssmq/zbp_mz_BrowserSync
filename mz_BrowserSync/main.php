<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
  $zbp->ShowError(6);
  die();
}
if (!$zbp->CheckPlugin('mz_BrowserSync')) {
  $zbp->ShowError(48);
  die();
}

$act = GetVars('act', 'GET');
$suc = GetVars('suc', 'GET');
if ($act == 'save') {
  CheckIsRefererValid();
  foreach ($_POST as $key => $val) {
    if (substr($key, 0, 5) == 'read_') {
      continue;
    }
    $zbp->Config('mz_BrowserSync')->$key = trim($val);
  }
  $zbp->SaveConfig('mz_BrowserSync');
  $zbp->BuildTemplate();
  $zbp->SetHint('good');
  Redirect('./main.php' . ($suc == null ? '' : "?act={$suc}"));
}


$blogtitle = '「开发工具」BrowserSync';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<style>
  pre {
    white-space: pre-wrap;
    word-wrap: break-word;
  }
</style>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu">
    <a href="main.php" title="首页"><span class="m-left m-now">首页</span></a>
    <?php require "about.php"; ?>
  </div>
  <div id="divMain2">
    <form action="<?php echo BuildSafeURL("main.php?act=save"); ?>" method="post">
      <table width="100%" class="tableBorder">
        <tr>
          <th width="10%">项目</th>
          <th>内容</th>
          <th width="45%">说明</th>
        </tr>
        <tr>
          <td>实际路径</td>
          <td><?php zbpform::text("usersdir", $zbp->Config("mz_BrowserSync")->usersdir, "90%"); ?></td>
          <td>站点如果在 Docker 内需要手动指定，否则留空；<?php echo $zbp->usersdir ?></td>
        </tr>
        <tr>
          <td>监视文件</td>
          <td><?php zbpform::textarea("fileTypes", htmlspecialchars($zbp->Config("mz_BrowserSync")->fileTypes), "90%"); ?></td>
          <td>留空时使用以下值：<pre><?php echo file_get_contents(mz_BrowserSync_Path('v-fileTypes'));?></pre></td>
        </tr>
        <tr>
          <td>排除文件</td>
          <td><?php zbpform::textarea("ignoreList", htmlspecialchars($zbp->Config("mz_BrowserSync")->ignoreList), "90%"); ?></td>
          <td>需要显示指定，以下仅为示例：<pre><?php echo "**/bin/*.*\n**/cache/*.*";?></pre></td>
        </tr>
        <tr>
          <td></td>
          <td colspan="2">
            <input type="submit" value="提交" />
            <span class="mz-bs-orgi"></span>
          </td>
        </tr>
      </table>
    </form>
    <pre id="mz-bs-pre"><?php echo mz_BrowserSync_BuildConsole(); ?></pre>
  </div>
</div>
<script src="script/script.js"></script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>
