<style>
  .download-area {

  }

  .download-area li {
    color: #FFF;
    margin-bottom: 30px;

    margin-right: 5px;
    float: left;

  }

  .download-area a {
    display: inline-block;

    background: #5fbaac;
    border: 2px solid #fff;
    border-radius: 2px;
    background: #5fbaac;
    box-shadow: 0 0 0 1px #EEE;

    padding: 10px 10px;
    color: white;
  }
</style>

<?php

$args = get_query_var('args');
extract($args);


?>

<table class="dltable">
  <tbody>
    <tr>
      <td style="background-color:#F9F9F9;" rowspan="3">
        <p>文件下载</p>
      </td>
      <td><i class="fa fa-list-alt"></i> 文件名称: <?php echo $filename; ?></td>
      <td><i class="fa fa-th-large"></i> 文件大小: <?php echo $size; ?></td>
    </tr>
    <tr>
      <td colspan="2"><i class="fa fa-volume-up"></i> 下载声明: <?php echo _mtx('git_fancydlcp') ?></td>
    </tr>
    <tr>
      <td colspan="2"><i class="fa fa-download"></i> 下载地址: <a class="dl" id="showdiv" href="#bdybox">点击下载</a></td>
    </tr>
  </tbody>
</table>


<div id="bdybox" style="display:none; ">
  <div class="part">
    <h2>下载声明:</h2>
    <div class="fancydlads" text-align="left">
      <p><?php echo _mtx('git_fancydlcp') ?> </p>
    </div>
  </div>
  <div class="part">
    <h2>文件信息：</h2>
    <div class="dlnotice" textalign="left">
      <p>文件名称: <?php echo $filename  ?></p>
      <?php if ($password) echo '<p>下载密码: ' . $password . '</p>'; ?>
      <?php if ($size) echo '<p>文件大小: ' . $size . '</p>'; ?>



    </div>
  </div>

  <div class="part download-area">
    <h2>下载地址：</h2>
    
<?php
//马的这也太危险了
foreach (explode('|', $a) as $v) {

  $b = explode(':', $v);

  echo sprintf('
<a target="_blank" href="%s"  rel="nofollow" title=""><span>%s</span></a>

', $b[1], $b[0]);
}

?>

  </div>
  <div class="dlfooter"></div>
</div>