<?php

$description = trim(strip_tags(category_description()));
get_header(); ?>

<div class="content-wrap">
    <div class="content">
        <?php
            echo '<header class="archive-header">';
            echo '<div class="catleader"><h1>', single_cat_title(), $pagedtext . '</h1>' . ($description ? '<div class="catleader-desc">' . $description . '</div>' : '') . '</div>';
 
         //   echo '<div class="archive-header-banner">' . category_description() . '</div>';
            echo '</header>';

                 ?>

        <?php /* if (_mtx('git_cat_style') == 'git_cat_card') {
            include 'modules/card.php';
        } elseif (_mtx('git_cat_style') == 'git_cat_list') {
            include 'modules/excerpt.php';

        } */
        include 'modules/excerpt.php'; ?>
    </div>
</div>
<?php get_sidebar();
get_footer(); ?>