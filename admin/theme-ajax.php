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


    //骚的一批
    mtx_comment_list(get_comment(), array('max_depth' => 1), 1);






?>



<?php
   
    die();
  }

endif;

add_action('wp_enqueue_scripts', 'fa_ajax_comment_scripts');
add_action('wp_ajax_nopriv_ajax_comment', 'fa_ajax_comment_callback');
add_action('wp_ajax_ajax_comment', 'fa_ajax_comment_callback');
