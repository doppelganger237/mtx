</section>
<?php
if (_mtx('superfoot_enable') && !mtx_is_mobile()) { ?>
  <div id="footbar">
    <ul>
      <li>
        <p class="first"><?php echo _mtx('superfoot_1_title'); ?></p><span><?php echo _mtx('superfoot_1_content'); ?></span>
      
      <li>
        <p class="second"><?php echo _mtx('superfoot_2_title'); ?></p><span><?php echo _mtx('superfoot_2_content'); ?></span>
      
      <li>
        <p class="third"><?php echo _mtx('superfoot_3_title'); ?></p><span><?php echo _mtx('superfoot_3_content'); ?></span>
      
      <li>
        <p class="fourth"><?php echo _mtx('superfoot_4_title'); ?></p><span><?php echo _mtx('superfoot_4_content'); ?></span>
      </li>
    </ul>
  </div>
<?php
} ?>
<footer style="border-top: 1px solid ;background-color: #232222;" class="footer">
  <div class="footer-inner">
    <div class="footer-copyright">
      <?php
      if (_mtx('foot_code')) {
        echo  _mtx('foot_code');
      } ?>
      <?php
      if (_mtx('tongji_code')) {
        echo '<span class="trackcode pull-right">' . _mtx('git_track') . '</span>';
      } ?>
    </div>
  </div>
</footer>
<?php
if (_mtx('copydialog_enable') && is_singular()) {
/*   echo '<script>document.body.oncopy = function() { Swal.fire(
  \'复制成功\',
  \'转载原创文章务必保留原文链接,谢谢你!\',
  \'success\'
)};</script>'; */
} ?>
<?php
if (_mtx('git_copy_b') && is_singular()) {
  echo '<script type="text/Javascript">document.oncontextmenu=function(e){return false;};document.onselectstart=function(e){return false;};</script><style>body{ -moz-user-select:none;}</style><SCRIPT LANGUAGE=javascript>if (top.location != self.location)top.location=self.location;</SCRIPT><noscript><iframe src=*.html></iframe></noscript>';
}
?>
<?php
if (_mtx('footer_code')) {
  echo _mtx('footer_code');
}

?>

<?php

$roll = '';

if (_mtx('sideroll_enable')) {

  $roll = _mtx('sideroll_index');
}
if ($roll) {
  $roll = json_encode(explode(' ', $roll));
} else {
  $roll = json_encode(array());
}

//这写的也太蛋疼了,还不如不结合Jquery，直接用自带的呢
?>

<script>
  window.Mtx = {
    name: '<?php echo bloginfo('name') ?>',
    url: '<?php echo MTX_URL ?>',
    ajaxpager: '<?php echo _mtx('ajaxpager') ?>',
    roll: '<?php echo $roll ?>',
    cdn: '<?php echo _mtx('cdn_enable') ? str_replace(home_url(), _mtx('cdn_url'), get_stylesheet_directory_uri()) : '' ?>',
    url_resetpassword: '<?php echo _mtx('user_rp') ? get_permalink(_mtx('user_rp')) : '' ?>',
    ajax_url: '<?php echo admin_url('admin-ajax.php')?>',
    order : '<?php echo get_option('comment_order')?>',
    formpostion : 'bottom'
  }
</script>
<?php
wp_footer();
global $dHasShare;
if ($dHasShare == true) {
  echo '<script>
    
    window._bd_share_config = {
        common: {
            bdText: "",
            bdMini: "2",
            bdMiniList: false,
            bdPic: "",
            bdStyle: "0",
            bdSize: "24"
        },
        share: [{
            bdCustomStyle: "/"
        }]
    };
    
    with(document)0[(getElementsByTagName("head")[0]||body).appendChild(createElement("script")).src="https://cdn.jsdelivr.net/gh/yunluo/GitCafeApi/static/api/js/share.js?v=89860593.js?cdnversion="+~(-new Date()/36e5)];</script>';
}
?>
</body>

</html>