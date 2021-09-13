<?php


namespace Rehub\Gutenberg\Blocks;
defined('ABSPATH') OR exit;


class Postelement extends Basic{

	protected $name = 'postelement';

	protected $attributes = array(
		'align'      => array(
			'type'    => 'string',
			'default' => '',
		),
		'blockId'      => array(
			'type'    => 'string',
			'default' => '',
		),
		'color'      => array(
			'type'    => 'string',
			'default' => '',
		),
		'type' => array(
			'type'    => 'string',
			'default' => 'favorite',
		),
		'labeltext' => array(
			'type'    => 'string',
			'default' => 'Contact',
		),
		'loading' => array(
			'type'    => 'boolean',
			'default' => false,
		),
		'fontSize'      => array(
			'type'    => 'number',
		),
		'pSide'      => array(
			'type'    => 'number',
		),
		'pTop'      => array(
			'type'    => 'number',
		),
		'bradius'      => array(
			'type'    => 'number',
		),
		'imageheight'      => array(
			'type'    => 'number',
			'default' => 20
		),
		'avatarblock' => array(
			'type'    => 'boolean',
			'default' => false,
		),
	);

	protected function render($settings = array(), $inner_content = ''){
		extract($settings);
		global $post;
		$postId = $post->ID;
		$id = 'rh-postel-'.mt_rand();
		$alignflex = '';
		if($align === 'left'){
			$alignflex = 'start';
		}
		else if($align === 'right'){
			$alignflex = 'end';
		}
		else if($align === 'center'){
			$alignflex = 'center';
		}
		$out = $class=$textalign='';
		$class = 'rh-flex-center-align rh-flex-justify-'.$alignflex;
		$out .= '<div id="'.$id.'" class="'.$class.'"'.$textalign.'>
		<style scoped>
			#'.$id.' .heart_thumb_wrap .heartplus{
				'.((isset($fontSize) && $type=='favorite') ? "font-size:".$fontSize."px;" : "").'
			}
			#'.$id.' .price_count{
				'.((isset($fontSize) && $type=='offerprice') ? "font-size:".$fontSize."px;" : "").'
			}
			#'.$id.' a.admin{
				'.((isset($fontSize) && $type=='author') ? "font-size:".$fontSize."px;" : "").'
				'.(($color  && $type=='author') ? "color:".$color.";" : "").'
				'.(($type=='author') ? "text-decoration:none;" : "").'
				'.(($avatarblock && $type=='author') ? "display:block;" : "").'
			}
			#'.$id.' .admin-name{
				'.(($avatarblock && $type=='author') ? "display:block;" : "").'
			}
			#'.$id.' a.admin img{
				'.(($avatarblock && $type=='author') ? "margin:0 !important;" : "").'
			}
			#'.$id.' .admin_meta_el{
				'.(($avatarblock && $type=='author') ? "text-align:center;" : "").'
				'.(($type=='author') ? "text-decoration:none;" : "").'
			}
			#'.$id.' .priced_block .btn_offer_block{
				'.((isset($fontSize) && ($type=='offerbutton' || $type=='bpbutton')) ? "font-size:".$fontSize."px;" : "").'
				'.((isset($pTop) && ($type=='offerbutton' || $type=='bpbutton')) ? "padding-top:".$pTop."px;padding-bottom:".$pTop."px;" : "").'
				'.((isset($pSide) && ($type=='offerbutton' || $type=='bpbutton')) ? "padding-left:".$pSide."px;padding-right:".$pSide."px;" : "").'
				'.((isset($bradius) && ($type=='offerbutton' || $type=='bpbutton')) ? "border-radius:".$bradius."px !important;" : "").'
			}
			#'.$id.' .price_count .rh_regular_price{
				'.(($color  && $type=='offerprice') ? "color:".$color.";" : "").'
			}
			#'.$id.' .price_count del{
				'.((isset($fontSize) && $type=='offerprice') ? "font-size:".$fontSize*0.8."px;" : "").'
				'.(($type=='offerprice') ? "opacity:0.2;" : "").'
			}
			#'.$id.' .row_social_inpost span.share-link-image{
				'.((isset($bradius) && $type=='share') ? "border-radius:".$bradius."px;" : "").'
			}
			#'.$id.' .favour_btn_red .heart_thumb_wrap{
				'.((isset($pTop) && $type=='favorite') ? "padding-top:".$pTop."px;padding-bottom:".$pTop."px;" : "").'
				'.((isset($pSide) && $type=='favorite') ? "padding-left:".$pSide."px;padding-right:".$pSide."px;" : "").'
				'.((isset($bradius) && $type=='favorite') ? "border-radius:".$bradius."px;" : "").'
			}
			#'.$id.' .favour_in_row{
				'.(($type=='favorite') ? "margin-right:0px !important;" : "").'
			}
		</style>';
		if($type=='favorite'){
			$wishlistadd = esc_html__('Save', 'rehub-theme');
			$wishlistadded = esc_html__('Saved', 'rehub-theme');
			$wishlistremoved = esc_html__('Removed', 'rehub-theme');      
			$out .='<div class="favour_in_row favour_btn_red">'.RH_get_wishlist($postId, $wishlistadd, $wishlistadded, $wishlistremoved).'</div>';
		}
		else if($type=='share'){   
			$out .=rehub_social_share("row");
		}
		else if($type=='sharesquare'){   
			$out .=rehub_social_share("square");
		}
		else if($type=='thumb'){   
			$out .=getHotThumb($postId, false, true);
		}
		else if($type=='thumbsmall'){   
			$out .=getHotThumb($postId, false);
		}
		else if($type=='wishlisticon'){   
			$out .=RHF_get_wishlist($postId);
		}
		else if($type=='hot'){   
			$out .= RHgetHotLike($postId);
		}
		else if($type=='author'){   
			$author_id = get_post_field( 'post_author', $postId );
			$out .= '<span class="admin_meta_el"><a class="admin rh-flex-center-align" href="'.get_author_posts_url( $author_id ).'">'.get_avatar( $author_id, $imageheight,'', '', array('class'=>'mr10 roundborder50p') ).'<span class="admin-name">'.get_the_author_meta( 'display_name', $author_id ).'</span></a></span>';
		}
		else if($type=='bpbutton'){   
			$author_id = get_post_field( 'post_author', $postId );
			if(class_exists( 'BuddyPress' ) &&  bp_is_active( 'messages' )){
				$class_show = 'btn_offer_block';
				$link = (is_user_logged_in()) ? wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username($author_id) .'&ref='. urlencode(get_permalink($postId))) : '#';
				$class_show = (!is_user_logged_in() && rehub_option('userlogin_enable') == '1') ? $class_show.' act-rehub-login-popup' : $class_show; 
				$out .= '<div class="priced_block clearfix  fontbold mb0 lineheight25"><a href="'.$link.'" class="'.$class_show.'">'.$labeltext.'</a></div>';
			}else{
				$out .= __('Please, enable message addon in Buddypress', 'rehub-framework');
			}
		}
		else if($type=='offerprice'){  
			ob_start();
			rehub_generate_offerbtn('showme=price&wrapperclass=fontbold mb0 lineheight25&postId='.$postId.'');
			$out .= ob_get_contents();
			ob_end_clean(); 
		}
		else if($type=='authorbox'){  
			ob_start();
			rh_author_detail_box($postId);
			$out .= ob_get_contents();
			ob_end_clean(); 
		}
		else if($type=='postgallery'){  
			$out .= rh_get_post_thumbnails(array('video'=>1, 'columns'=>5, 'height'=>$imageheight, 'post_id'=>$postId)); 
		}
		else if($type=='offerbutton'){  
			ob_start();
			rehub_generate_offerbtn('showme=button&wrapperclass=fontbold mb0 lineheight25&updateclean=1&postId='.$postId.'');
			$out .= ob_get_contents();
			ob_end_clean(); 
		}
		$out .='</div>';

		return $out;
	}
}