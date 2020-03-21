<?php

/**
 * This was contained in an addon until version 1.0.0 when it was rolled into
 * core.
 *
 * @package    WBOLT
 * @author     WBOLT
 * @since      1.1.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2019, WBOLT
 */

?>

<div id="J_DLIPPCont" class="dlipp-cont-wp">
  <div class="dlipp-cont-inner">
    <div class="dlipp-cont-hd">
      <span>相关文件下载地址</span>
    </div>

    <div class="dlipp-cont-bd">
      <?php if ($need_login && !$is_login) { //if login 
      ?>
        <div class="wb-tips">该资源需登录后下载，去<a class="signin-loader fancy-sign link" href="#sign">登录</a>?</div>

      <?php } elseif ($need_comment && !$is_comment) { //else if need comment 
      ?>
        <div class="wb-tips">*该资源需回复评论后下载，马上去<a class="link" href="#respond">发表评论</a>?</div>

      <?php } else { //else if login 
      ?>

        <?php foreach ($dl_info as $k => $v) : ?>
          <a class="dlipp-dl-btn j-wbdlbtn-dlipp" rel="nofollow" target="_blank" href=<?php echo $v['url'] ?>>
            <span><?php echo $v['name']; ?></span>
          </a>
        <?php endforeach; ?>

      <?php } //end if login 
      ?>
    </div>

    <div class="dlipp-cont-ft"><?php echo $remark_info ? $remark_info : '&copy;本站所有资源仅供学习使用,下载资源版权归作者所有.'; ?></div>
  </div>
</div>