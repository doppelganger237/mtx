<?php
define('AC_VERSION', '2.0.0');

if (version_compare($GLOBALS['wp_version'], '4.4-alpha', '<')) {
  wp_die('请升级到4.4以上版本');
}

if (!function_exists('fa_ajax_comment_scripts')) :

  function fa_ajax_comment_scripts()
  {
    if (is_singular() && comments_open() && get_option('thread_comments')) {
      wp_enqueue_script('comment-reply');
    }
/*     wp_enqueue_style('ajax-comment', get_template_directory_uri() . '/dist/css/ajax-comment.css', array(), AC_VERSION); */
    wp_enqueue_script('ajax-comment', get_template_directory_uri() . '/dist/js/ajax-comment.js', array('jquery'), AC_VERSION, true);
    wp_localize_script('ajax-comment', 'ajaxcomment', array(
      'ajax_url'   => admin_url('admin-ajax.php'),
      'order' => get_option('comment_order'),
      'formpostion' => 'bottom', //默认为bottom，如果你的表单在顶部则设置为top。
    ));
  }

endif;

if (!function_exists('fa_ajax_comment_err')) :

  function fa_ajax_comment_err($a)
  {
    header('HTTP/1.0 500 Internal Server Error');
    header('Content-Type: text/plain;charset=UTF-8');
    echo $a;
    exit;
  }

endif;

if (!function_exists('fa_ajax_comment_callback')) :

  function fa_ajax_comment_callback()
  {
    $comment = wp_handle_comment_submission(wp_unslash($_POST));
    if (is_wp_error($comment)) {
      $data = $comment->get_error_data();
      if (!empty($data)) {
        fa_ajax_comment_err($comment->get_error_message());
      } else {
        exit;
      }
    }
    $user = wp_get_current_user();
    do_action('set_comment_cookies', $comment, $user);
    $GLOBALS['comment'] = $comment; //根据你的评论结构自行修改，如使用默认主题则无需修改
?>
    <li <?php comment_class();
        echo ' id="comment-' . get_comment_ID() . '"';  ?>>

      <div class="c-avatar">
        <?php echo get_avatar($comment->comment_author_email, $size = '54', deel_avatar_default()); ?>

        <div class="c-main" id="div-comment-<?php echo get_comment_ID(); ?>">

          <?php echo comment_text();

          if ($comment->comment_approved == '0') {
            echo '<span class="c-approved">您的评论正在排队审核中，请稍后！</span><br />';
          }
          ?>
          <div class="c-meta">
            <span class="c-author">
              <?php get_comment_author_link(); ?>
            </span>
            <?php echo get_comment_time('m-d H:i ');
            echo time_ago(); ?>
          </div>
        </div>

    </li>


<?php die();
  }

endif;

add_action('wp_enqueue_scripts', 'fa_ajax_comment_scripts');
add_action('wp_ajax_nopriv_ajax_comment', 'fa_ajax_comment_callback');
add_action('wp_ajax_ajax_comment', 'fa_ajax_comment_callback');
