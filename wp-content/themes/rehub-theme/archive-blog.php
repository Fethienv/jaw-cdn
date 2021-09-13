<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php get_header(); ?>
<!-- CONTENT -->
<div class="rh-container"> 
    <div class="rh-content-wrap clearfix">
        <!-- Main Side -->
        <div class="main-side clearfix<?php if (rehub_option('blog_archive_layout') == 'gridfull_blog' || rehub_option('blog_archive_layout') == 'cardblogfull') : ?> full_width<?php endif ;?>">
            <?php if (rehub_option('blog_archive_layout') == 'grid_blog' || rehub_option('blog_archive_layout') == 'cardblog') : ?>
                <div class="rh-flex-eq-height col_wrap_three">
            <?php elseif (rehub_option('blog_archive_layout') == 'gridfull_blog' || rehub_option('blog_archive_layout') == 'cardblogfull') : ?>   
                <div class="rh-flex-eq-height col_wrap_fourth">                    
            <?php endif ;?>                        
            <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <?php if (rehub_option('blog_archive_layout') == 'big_blog') : ?>
                    <?php include(rh_locate_template('inc/parts/query_type2.php')); ?>
                <?php elseif (rehub_option('blog_archive_layout') == 'list_blog') : ?>
                    <?php include(rh_locate_template('inc/parts/query_type1.php')); ?>
                <?php elseif (rehub_option('blog_archive_layout') == 'grid_blog' || rehub_option('blog_archive_layout') == 'gridfull_blog') : ?>
                    <?php include(rh_locate_template('inc/parts/column_grid.php')); ?>   
                <?php elseif (rehub_option('blog_archive_layout') == 'cardblog' || rehub_option('blog_archive_layout') == 'cardblogfull') : ?>
                    <?php include(rh_locate_template('inc/parts/color_grid.php')); ?>
                <?php else : ?>
                    <?php include(rh_locate_template('inc/parts/query_type1.php')); ?>	
                <?php endif ;?>
            <?php endwhile; ?>
            <?php else : ?>		
            <h5 class="font140"><?php esc_html_e('Sorry. No posts in this category yet', 'rehub-theme'); ?></h5>	
            <?php endif; ?>	
            <?php if (rehub_option('blog_archive_layout') == 'grid_blog' || rehub_option('blog_archive_layout') == 'gridfull_blog' || rehub_option('blog_archive_layout') == 'cardblog' || rehub_option('blog_archive_layout') == 'cardblogfull') : ?></div><?php endif ;?>
            <div class="clearfix"></div>
            <?php rehub_pagination(); ?>
        </div>	
        <!-- /Main Side -->
        <?php if (rehub_option('blog_archive_layout') != 'gridfull_blog' && rehub_option('blog_archive_layout') != 'cardblogfull') : ?>
            <!-- Sidebar -->
            <?php get_sidebar(); ?>
            <!-- /Sidebar --> 
        <?php endif ;?>
    </div>
</div>
<!-- /CONTENT -->     
<!-- FOOTER -->
<?php get_footer(); ?>