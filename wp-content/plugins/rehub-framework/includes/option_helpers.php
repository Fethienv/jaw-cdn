<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php

VP_Security::instance()->whitelist_function('rehub_framework_is_header_six');
function rehub_framework_is_header_six($type)
{
	if( $type === 'header_six' )
		return true;
	return false;
}

VP_Security::instance()->whitelist_function('rehub_framework_is_header_six_five');
function rehub_framework_is_header_six_five($type)
{
	if( $type === 'header_six' || $type === 'header_five' )
		return true;
	return false;
}

VP_Security::instance()->whitelist_function('rehub_framework_is_header_seven_five');
function rehub_framework_is_header_seven_five($type)
{
	if( $type === 'header_seven' || $type === 'header_five' )
		return true;
	return false;
}

VP_Security::instance()->whitelist_function('rehub_framework_is_header_five');
function rehub_framework_is_header_five($type)
{
	if($type === 'header_five')
		return true;
	return false;
}

VP_Security::instance()->whitelist_function('rehub_framework_is_header_seven');
function rehub_framework_is_header_seven($type)
{
	if( $type === 'header_seven' )
		return true;
	return false;
}


VP_Security::instance()->whitelist_function('rehub_framework_post_formats');
function rehub_framework_post_formats() {
return array(
    
    array(
      'value' => 'all',
      'label' => esc_html__('all', 'rehub-framework'),
    ),	

    array(
      'value' => 'regular',
      'label' => esc_html__('regular', 'rehub-framework'),
    ),
    array(
      'value' => 'video',
      'label' => esc_html__('video', 'rehub-framework'),
    ),
    array(
      'value' => 'gallery',
      'label' => esc_html__('gallery', 'rehub-framework'),
    ),
    array(
      'value' => 'review',
      'label' => esc_html__('review', 'rehub-framework'),
    ),
    array(
      'value' => 'music',
      'label' => esc_html__('music', 'rehub-framework'),
    ),              
);
}


VP_Security::instance()->whitelist_function('rehub_framework_post_type_is_regular');
function rehub_framework_post_type_is_regular($type)
{
	if( $type === 'regular' )
		return true;
	return false;
}

VP_Security::instance()->whitelist_function('rehub_framework_post_type_is_video');
function rehub_framework_post_type_is_video($type)
{
	if( $type === 'video' )
		return true;
	return false;
}
VP_Security::instance()->whitelist_function('rehub_framework_post_type_is_gallery');
function rehub_framework_post_type_is_gallery($type)
{
	if( $type === 'gallery' )
		return true;
	return false;
}
VP_Security::instance()->whitelist_function('rehub_framework_post_type_is_review');
function rehub_framework_post_type_is_review($type)
{
	if( $type === 'review' )
		return true;
	return false;
}
VP_Security::instance()->whitelist_function('rehub_framework_post_type_is_music');
function rehub_framework_post_type_is_music($type)
{
	if( $type === 'music' )
		return true;
	return false;
}

VP_Security::instance()->whitelist_function('review_post_schema_type_is_woo_list');
function review_post_schema_type_is_woo_list($type)
{
	if( $type === 'review_woo_list' )
		return true;
	return false;
}
VP_Security::instance()->whitelist_function('review_post_schema_type_is_woo');
function review_post_schema_type_is_woo($type)
{
	if( $type === 'review_woo_product' )
		return true;
	return false;
}


VP_Security::instance()->whitelist_function('rehub_framework_post_music_is_soundcloud');
function rehub_framework_post_music_is_soundcloud($type)
{
	if( $type === 'music_post_soundcloud' )
		return true;
	return false;
}
VP_Security::instance()->whitelist_function('rehub_framework_post_music_is_spotify');
function rehub_framework_post_music_is_spotify($type)
{
	if( $type === 'music_post_spotify' )
		return true;
	return false;
}

//Functions for affiliate links

VP_Security::instance()->whitelist_function('rehub_manual_ids_func');
function rehub_manual_ids_func($top_review_cat='')
{
	$args = array(
		'meta_query' => array(
			array(
				'key' => 'rehub_framework_post_type',
				'value' => 'review'
			),
		),
		'posts_per_page' => -1,
	);
	$query = new WP_Query( $args );
	$data  = array();
	foreach ($query->posts as $post)
	{
		$data[] = array(
			'value' => $post->ID,
			'label' => $post->post_title,
		);
	}
	return $data;
}

VP_Security::instance()->whitelist_function('top_review_choose_is_cat');
function top_review_choose_is_cat($type)
{
	if( $type === 'cat_choose' )
		return true;
	return false;
}
VP_Security::instance()->whitelist_function('top_review_choose_is_manual');
function top_review_choose_is_manual($type)
{
	if( $type === 'manual_choose' )
		return true;
	return false;
}
VP_Security::instance()->whitelist_function('top_review_choose_is_custompost');
function top_review_choose_is_custompost($type)
{
	if( $type === 'custom_post' )
		return true;
	return false;
}
VP_Security::instance()->whitelist_function('rehub_get_cpost_type');
function rehub_get_cpost_type()
{
    $post_types = get_post_types( array('public'   => true) );
    $data  = array();
    foreach ( $post_types as $post_type ) {
        if ( $post_type !== 'revision' && $post_type !== 'nav_menu_item' && $post_type !== 'attachment') {
			$data[] = array(
				'value' => $post_type,
				'label' => $post_type,
			);
        }
    }
	return $data;
}

VP_Security::instance()->whitelist_function('top_table_shortcode');
function top_table_shortcode()
{
	$result = esc_html__("You can use shortcode to insert this top table to another page", "rehub-framework").' <strong>[wpsm_toptable id="'.$_GET['post'].'" full_width="1"]</strong><br />'.__("Delete full_width attribute if you insert shortcode in page with sidebar. You can add also post_ids parameter for manual adding and sorting some posts. Example [wpsm_toptable id=22 post_ids=11,12,13], where id=22 is id of current table page and 11,12,13 are ids of posts which you want to include in table", "rehub-framework");
	return $result;
}

VP_Security::instance()->whitelist_function('top_charts_shortcode');
function top_charts_shortcode()
{
	
		$result = __("You can use shortcode to insert this top charts to another page", "rehub-framework").' <strong>[wpsm_charts id="'.$_GET['post'].'"]</strong>';

	return $result;
}

VP_Security::instance()->whitelist_function('use_fields_as_desc');
function use_fields_as_desc($type)
{
	if( $type === 'field' )
		return true;
	return false;
}

VP_Security::instance()->whitelist_function('rehub_framework_rev_type');
function rehub_framework_rev_type($type)
{
	if( $type === 'full_review' || $type === 'simple')
		return true;
	return false;
}

VP_Security::instance()->whitelist_function('user_rev_type');
function user_rev_type($type)
{
	if( $type === 'user' )
		return true;
	return false;
}

VP_Security::instance()->whitelist_function('rehub_column_is_meta_value');
function rehub_column_is_meta_value($type)
{
	if( $type === 'meta_value' )
		return true;
	return false;
}

VP_Security::instance()->whitelist_function('rehub_column_is_short');
function rehub_column_is_short($type)
{
	if( $type === 'shortcode' )
		return true;
	return false;
}

VP_Security::instance()->whitelist_function('rehub_column_is_review_function');
function rehub_column_is_review_function($type)
{
	if( $type === 'review_function' )
		return true;
	return false;
}

VP_Security::instance()->whitelist_function('rehub_column_is_image');
function rehub_column_is_image($type)
{
	if( $type === 'image' )
		return true;
	return false;
}

VP_Security::instance()->whitelist_function('rehub_column_is_tax');
function rehub_column_is_tax($type)
{
	if( $type === 'taxonomy_value' )
		return true;
	return false;
}

VP_Security::instance()->whitelist_function('rehub_column_is_attr');
function rehub_column_is_attr($type)
{
	if( $type === 'woo_attribute' )
		return true;
	return false;
}

VP_Security::instance()->whitelist_function('rehub_column_is_btn');
function rehub_column_is_btn($type)
{
	if( $type === 'affiliate_btn' )
		return true;
	return false;
}

VP_Security::instance()->whitelist_function('rehub_custom_badge_admin');
function rehub_custom_badge_admin()
{
$custom_badge_admin = array(
	'type' => 'radiobutton',
	'name' => 'is_editor_choice',
	'label' => esc_html__('Add badge', 'rehub-framework'),
	'description' => esc_html__('You can customize badges in theme option', 'rehub-framework'),
	'items' => array(
	    array(
	        'value' => '0',
	        'label' => esc_html__('No', 'rehub-framework'),
	    ),				
	    array(
	        'value' => '1',
	        'label' => (REHub_Framework::get_option('badge_label_1') !='') ? REHub_Framework::get_option('badge_label_1') : esc_html__('Editor choice', 'rehub-framework'),
	    ),
	    array(
	        'value' => '2',
	        'label' => (REHub_Framework::get_option('badge_label_2') !='') ? REHub_Framework::get_option('badge_label_2') : esc_html__('Best seller', 'rehub-framework'),
	    ),
	    array(
	        'value' => '3',
	        'label' => (REHub_Framework::get_option('badge_label_3') !='') ? REHub_Framework::get_option('badge_label_3') : esc_html__('Best value', 'rehub-framework'),
	    ),
	    array(
	        'value' => '4',
	        'label' => (REHub_Framework::get_option('badge_label_4') !='') ? REHub_Framework::get_option('badge_label_4') : esc_html__('Best price', 'rehub-framework'),
	    ),			    
	),			
);	
return $custom_badge_admin;
}

VP_Security::instance()->whitelist_function('admin_badge_preview_html');
function admin_badge_preview_html($label = '', $color = '')
{
	if(empty($label)) {$result = '';}
	else {
		$background = ($color) ? ' style="background-color:'.$color.'"' : '';
		$result = '<div class="starburst_admin_wrapper">';
		$result .= '<span class="re-ribbon-badge"><span'.$background.'>'.$label.'</span></span>';
		$result .= '</div>';
	}
	return $result;
}

VP_Security::instance()->whitelist_function('get_ce_modules_id_for_sinc');
function get_ce_modules_id_for_sinc()
{
	$data  = array();
	if(!defined('\ContentEgg\PLUGIN_PATH')){
		$data[] = array(
			'value' => '',
			'label' => 'Content Egg is not installed',
		);		
	}
	else{
		$modules = \ContentEgg\application\components\ModuleManager::getInstance()->getAffiliateParsers();
		if (!empty($modules)) {		
			foreach ($modules as $module) {
				$data[] = array(
					'value' => $module->getId(),
					'label' => $module->getName(),
				);
		    } 			
		}else{
			$data[] = array(
				'value' => '',
				'label' => 'Content Egg modules not found',
			);			
		}
		
	}

	return $data;
}

VP_Security::instance()->whitelist_function('rehub_get_post_layout_array');
function rehub_get_post_layout_array()
{
	$postlayout = apply_filters( 'rehub_post_layout_array', array(
		array(
			'value' => 'default',
			'label' => esc_html__('Simple', 'rehub-framework'),
		),
		array(
			'value' => 'default_full_opt',
			'label' => esc_html__('Optimized Full width', 'rehub-framework'),
		),
		array(
			'value' => 'gutencustom',
			'label' => esc_html__('Customizable Full width', 'rehub-framework'),
		),
		array(
			'value' => 'meta_outside',
			'label' => esc_html__('Title is outside content', 'rehub-framework'),
		),
		array(
			'value' => 'guten_auto',
			'label' => esc_html__('Gutenberg Auto Contents', 'rehub-framework'),
		),
		array(
			'value' => 'default_text_opt',
			'label' => esc_html__('Optimized for reading with sidebar', 'rehub-framework'),
		),
		array(
			'value' => 'video_block',
			'label' => esc_html__('Video Block', 'rehub-framework'),
		),
		array(
			'value' => 'meta_center',
			'label' => esc_html__('Center aligned (Rething style)', 'rehub-framework'),
		),				
		array(
			'value' => 'meta_compact',
			'label' => esc_html__('Compact (Button Block Under Title)', 'rehub-framework'),
		),
		array(
			'value' => 'meta_compact_dir',
			'label' => esc_html__('Compact (Button Block Before Title)', 'rehub-framework'),
		),				
		array(
			'value' => 'corner_offer',
			'label' => esc_html__('Button in corner (Repick style)', 'rehub-framework'),
		),								
		array(
			'value' => 'meta_in_image',
			'label' => esc_html__('Title Inside image', 'rehub-framework'),
		),	
		array(
			'value' => 'meta_in_imagefull',
			'label' => esc_html__('Title Inside full image', 'rehub-framework'),
		),
		array(
			'value' => 'big_post_offer',
			'label' => esc_html__('Big post offer block in top', 'rehub-framework'),
		),		
		array(
			'value' => 'offer_and_review',
			'label' => esc_html__('Offer and review score', 'rehub-framework'),
		),				
	));

	return $postlayout;   
}

VP_Security::instance()->whitelist_function('rehub_get_product_layout_array');
function rehub_get_product_layout_array()
{
	$productlayout = apply_filters( 'rehub_global_product_layout_array', array(
		array(
			'value' => 'default_with_sidebar',
			'label' => esc_html__('Default with sidebar', 'rehub-framework'),
		),
		array(
			'value' => 'default_no_sidebar',
			'label' => esc_html__('Default without sidebar 3 column', 'rehub-framework'),
		),
		array(
			'value' => 'default_full_width',
			'label' => esc_html__('Default without sidebar 2 column', 'rehub-framework'),
		),
		array(
			'value' => 'full_width_extended',
			'label' => esc_html__('Full width Extended', 'rehub-framework'),
		),
		array(
			'value' => 'full_width_advanced',
			'label' => esc_html__('Full width Advanced', 'rehub-framework'),
		),	
		array(
			'value' => 'side_block',
			'label' => esc_html__('Side Block', 'rehub-framework'),
		),
		array(
			'value' => 'side_block_light',
			'label' => esc_html__('Side Block Light', 'rehub-framework'),
		),
		array(
			'value' => 'side_block_video',
			'label' => esc_html__('Video Block', 'rehub-framework'),
		),			
		array(
			'value' => 'ce_woo_blocks',
			'label' => esc_html__('Review with Blocks', 'rehub-framework'),
		),			
		array(
			'value' => 'vendor_woo_list',
			'label' => esc_html__('Compare prices by shortcode', 'rehub-framework'),
		),
		array(
			'value' => 'compare_woo_list',
			'label' => esc_html__('Compare Prices by sku', 'rehub-framework'),
		),						
		array(
			'value' => 'ce_woo_list',
			'label' => esc_html__('Content Egg List', 'rehub-framework'),
		),	
		array(
			'value' => 'sections_w_sidebar',
			'label' => esc_html__('Sections with sidebar', 'rehub-framework'),
		),		
		array(
			'value' => 'ce_woo_sections',
			'label' => esc_html__('Content Egg Auto Sections', 'rehub-framework'),
		),
		array(
			'value' => 'full_photo_booking',
			'label' => esc_html__('Full width Photo', 'rehub-framework'),
		),
		array(
			'value' => 'woo_compact',
			'label' => esc_html__('Compact Style', 'rehub-framework'),
		),	
		array(
			'value' => 'woo_directory',
			'label' => esc_html__('Directory Style', 'rehub-framework'),
		),	
		array(
			'value' => 'darkwoo',
			'label' => esc_html__('Dark Layout', 'rehub-framework'),
		),
		array(
			'value' => 'woostack',
			'label' => esc_html__('Photo Stack Layout', 'rehub-framework'),
		),											
	));

	return $productlayout;   
}

////////



/**
 * EOF
 */