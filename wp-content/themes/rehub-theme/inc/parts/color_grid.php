<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php 
global $post;
?>  
<?php
$taxonomy = rh_get_taxonomy_of_post( $post );
$categories = get_the_terms( $post->ID, $taxonomy );
$catname = '';
if( !empty($categories)){
    $category = $categories[0];
    $catname = $category->name;
}
?>
<div class="col_item whitebg rh-shadow3 rehub-sec-smooth rh-hover-up rh-main-bg-hover csstransall position-relative rh-hovered-wrap">
<a class="abdfullwidth" href="<?php the_permalink();?>"></a>
<div class="rh-borderinside rh-hovered-scale pointernone"></div>
<div class="padd20">
    <div class="pt10 pr20 pl20 pb10">
        <div class="mt0 mb10 font70 upper-text-trans rehub-main-color whitehovered catforcgrid"><?php echo ''.$catname;?></div>
        <h3 class="mb30 mt0 font120 lineheight20 whitehovered"><?php the_title();?></h3>
        <div class="mb15 greycolor font90 whitehovered excerptforcgrid">                                 
            <?php kama_excerpt('maxchar=90'); ?>                       
        </div>
        <i class="rhicon <?php echo (is_rtl()) ? 'rhi-arrow-left' : 'rhi-arrow-right';?> rehub-main-color font130 whitehovered csstranstrans rh-hovered-rotate position-relative"></i>

    </div>                                     
</div>
</div>