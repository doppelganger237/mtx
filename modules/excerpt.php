<?php
if (is_home() && _mtx('hot_list_check') && $paged == 1) { ?>
    <div class="hot-posts-card">
        <div class="left-ad" style="clear: both;background-color: #fff; width: 30%;float: left;margin-right:2%;"></div>
        <div class="hot-posts">
            <h2 class="title"><?php echo _mtx('hot_list_title') ?></h2>
            <ul><?php hot_posts_list(); ?></ul>
        </div>
    </div>

<?php
} ?>

<!-- 跟个弱智一样 -->
<?php
while (have_posts()) :
    the_post(); ?>

    <article class="excerpt">
        <header>
            <?php
            if (!is_category()) {
                $category = get_the_category();
                if ($category[0]) {
                    echo '<a class="label label-important" href="' . get_category_link($category[0]->term_id) . '">' . $category[0]->cat_name . '<i class="label-arrow"></i></a>';
                }
            }

            ?>

            <h2><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title(); ?>
                    <?php $t1 = $post->post_date;
                    $t2 = date("Y-m-d H:i:s");
                    $diff = (strtotime($t2) - strtotime($t1)) / 3600;
                    if ($diff < 12) {
                        echo '<img src="' . MTX_URL . '/assets/img/new.gif" alt="24小时内最新">';
                    } ?>

                </a></h2>
        </header>



        <?php

        $_thumb =  post_thumbnail(_mtx('cdn_url_style') ? '!githumb4.jpg' : '?imageView2/1/w/260/h/160/q/75');

        if ($_thumb) {
            echo '<div class="focus"><a href="' . get_permalink() . '" >';
            echo $_thumb;
            echo '</a></div>';
        }




        echo '<span class="note">';

        $excerpt = $post->post_excerpt;
        if (!post_password_required()) {
            if (empty($excerpt)) {
                echo deel_strimwidth(strip_tags(apply_filters('the_content', strip_shortcodes($post->post_content))), 0, _mtx('excerpt_length') ? _mtx('excerpt_length') : 210, '……<a href="' . get_permalink() . '" rel="nofollow" class="more-link">继续阅读 &raquo;</a>');
            } else {
                echo deel_strimwidth(strip_tags(apply_filters('the_excerpt', strip_shortcodes($post->post_excerpt))), 0, _mtx('excerpt_length') ? _mtx('excerpt_length') : 210, '……<a href="' . get_permalink() . '" rel="nofollow" class="more-link">继续阅读 &raquo;</a>');
            }
        } else {
            echo '本篇文章为密码保护文章，不提供摘要';
        }

        echo '</span>';

        echo '<p class="auth-span">';
        if (!is_author()) {
            echo '<span class="muted"><i class="fa fa-user"></i> <a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_the_author() . '</a></span>';
        }

        echo '<span class="muted"><i class="fa fa-clock-o"></i>' . timeago(get_gmt_from_date(get_the_time('Y-m-d G:i:s'))) . '</span>';


        echo '<span class="muted"><i class="fa fa-eye"></i>' . deel_views('浏览') . '</span>';
        if (comments_open()) {
            echo '<span class="muted"><i class="fa fa-comments-o"></i> <a target="_blank" href="' . get_comments_link() . '">' . get_comments_number('0', '1', '%') . '评论</a></span>';
        }


        echo '<span class="muted">' . mtx_get_post_like($class = "action") . '</span>';
        echo '</p>';

        ?>





    </article>
<?php
endwhile;
wp_reset_query(); ?>


<?php
//广告,伪代码

deel_paging(); ?>