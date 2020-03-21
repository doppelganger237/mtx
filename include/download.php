<?php

class DownloadFront
{


  public static $meta_fields = array('download_name', 'download_size', 'download_link','download_login');


  public function __construct()
  {


    add_filter('the_content', array($this, 'the_content'), 10);
  }
  public function the_content($content)
  {
    if (is_single()) {
      $content .= $this->downHtml();
    }

    return $content;
  }
  public function meta_values($post_id)
  {

    //self::compat();

    $meta_values = array();
    foreach (self::$meta_fields as $field) {
      $meta_values[$field] = get_post_meta($post_id, 'mtx_' . $field, true);
    }

    //print_r($meta_values);


    return $meta_values;
  }


  public function downHtml($with_title = true)
  {

    $post_id = get_the_ID();
    $html = '';

    do {
      if (!$post_id) {
        break;
      }

      $this->post_id = $post_id;
      $meta_value = $this->meta_values($post_id);

      //关闭资源
      /*           if(!$meta_value['wb_dl_type']){
            break;
          } */

      if (!$meta_value['download_link']) {
        break;
      }


      $dl_info = array();

      $links = explode("\n", $meta_value['download_link']);
      foreach ($links as $link) {
        $l = explode(",", $link);
        $dl_info[$l[0]] = array(
          'name' => trim($l[0]),
          'url' =>  trim($l[1])
        );
      }


      $is_login = is_user_logged_in();
      //$need_comment = isset($meta_value['wb_dl_mode']) && $meta_value['wb_dl_mode'] == 1 ? 1 : 0;
      //   $is_comment = $this->wb_is_comment($post_id);

      $need_login = isset($meta_value['download_login']) && $meta_value['download_login'] == 1 ? 1 : 0;;
      ob_start();
      include dirname(__FILE__) . '/download_tpl.php';

      $html = ob_get_clean();
    } while (false);

    return $html;
  }
}
