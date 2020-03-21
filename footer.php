</section>
<?php
if (_mtx('superfoot_enable') && !mtx_is_mobile()) { ?>
  <div id="footbar" style="border-top: 2px solid #8E44AD;">
    <ul>
      <li>
        <p class="first"><?php echo _mtx('superfoot_1_title'); ?></p><span><?php echo _mtx('superfoot_1_content'); ?></span>
      </li>
      <li>
        <p class="second"><?php echo _mtx('superfoot_2_title'); ?></p><span><?php echo _mtx('superfoot_2_content'); ?></span>
      </li>
      <li>
        <p class="third"><?php echo _mtx('superfoot_3_title'); ?></p><span><?php echo _mtx('superfoot_3_content'); ?></span>
      </li>
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
  echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>';
  echo '<script>document.body.oncopy = function() { Swal.fire(
  \'复制成功\',
  \'转载原创文章务必保留原文链接,谢谢你!\',
  \'success\'
)};</script>';
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

_moloader('mo_get_user_rp');
?>

<?php

$roll ='';

if(_mtx('sideroll_enable')){

  $roll=_mtx('sideroll_index');
}
if( $roll ){
  $roll = json_encode(explode(' ', $roll));
}else{
  $roll = json_encode(array());
}


?>
<script>
  window._deel = {
    name: '<?php bloginfo('name') ?>',
    url: '<?php echo MTX_URL ?>',
    ajaxpager: '<?php echo _mtx('ajaxpager') ?>',
    commenton: 1,
    roll: <?php echo $roll?>
  };

  window.jsui = {
    www: '<?php echo home_url() ?>',
    uri: '<?php echo get_stylesheet_directory_uri(); ?>',
    cdn: '<?php echo getCDNURI(); ?>',
    url_rp: '<?php echo mo_get_user_rp() ?>'
  };
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