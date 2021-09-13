<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php get_header(); ?>
<?php 

global $WCMp;
$verified_vendor = $vendor_verified_label = '';
$vendor_id = $vendor->id;
$vendor_name = $vendor->display_name;
$address = '';

if ($vendor->city) {
    $address = $vendor->city . ', ';
}
if ($vendor->state) {
    $address .= $vendor->state . ', ';
}
if ($vendor->country) {
    $address .= $vendor->country;
}
$mobile = $vendor->phone;
$email = $vendor->user_data->user_email;
$location = $address;
$verified_vendor = get_user_meta($vendor_id, 'wcmp_vendor_is_verified', true);
$vendor_hide_address = apply_filters('wcmp_vendor_store_header_hide_store_address', get_user_meta($vendor_id, '_vendor_hide_address', true), $vendor_id);
$vendor_hide_phone = apply_filters('wcmp_vendor_store_header_hide_store_phone', get_user_meta($vendor_id, '_vendor_hide_phone', true), $vendor_id);
$vendor_hide_email = apply_filters('wcmp_vendor_store_header_hide_store_email', get_user_meta($vendor_id, '_vendor_hide_email', true), $vendor_id);
$vendor_hide_description = get_user_meta($vendor_id,'_vendor_hide_description', true);
$shop_name = get_user_meta($vendor_id, '_vendor_page_title', true);
$totaldeals = count_user_posts( $vendor_id, $post_type = 'product' );
$count_likes = ( get_user_meta( $vendor_id, 'overall_post_likes', true) ) ? get_user_meta( $vendor_id, 'overall_post_likes', true) : 0;
$count_wishes = ( get_user_meta( $vendor_id, 'overall_post_wishes', true) ) ? get_user_meta( $vendor_id, 'overall_post_wishes', true) : 0;
$count_p_votes = (int)$count_likes + (int)$count_wishes; 
$vendor_address = (!empty($location) && $vendor_hide_address != 'Enable') ? apply_filters( 'vendor_shop_page_location', $location, $vendor_id ) : '';
$vendor_phone = (!empty($mobile) && $vendor_hide_phone != 'Enable') ? apply_filters( 'vendor_shop_page_contact', $mobile, $vendor_id ) : '';
$vendor_email = (!empty($email) && $vendor_hide_email != 'Enable') ? apply_filters( 'vendor_shop_page_email', $email, $vendor_id ) : '';

$vendor_api_key = get_wcmp_vendor_settings('google_api_key');
$vendor_location = get_user_meta($vendor->id, '_store_location', true);
$vendor_store_lat = get_user_meta($vendor->id, '_store_lat', true);
$vendor_store_lng = get_user_meta($vendor->id, '_store_lng', true);
$shop_vendor_map = ( $vendor_location AND $vendor_store_lat AND $vendor_store_lng AND $vendor_api_key ) ? true : false;

$store_url = $vendor->permalink;
$queried_object = get_queried_object();

if (apply_filters('is_vendor_add_external_url_field', true, $vendor->id)) {
	$external_store_url = get_user_meta($vendor_id, '_vendor_external_store_url', true);
	$external_store_label = get_user_meta($vendor_id, '_vendor_external_store_label', true);
	if (empty($external_store_label))
		$external_store_label = esc_html__('External Store URL', 'rehub-theme');
	if (isset($external_store_url) && !empty($external_store_url)) {
		$external_store_url = apply_filters('vendor_shop_page_external_store', esc_url_raw($external_store_url), $vendor_id);
	}
}

$widget_args = array( 'before_widget' => '<div class="rh-cartbox widget"><div>', 'after_widget'  => '</div></div>', 'before_title'  => '<div class="widget-inner-title rehub-main-font">', 'after_title' => '</div>' );
?>

<div class="wcvendor_store_wrap_bg wcmp-container clearfix">
	<style scoped>#wcvendor_image_bg{<?php echo rh_show_vendor_bg( $vendor_id ); ?>}</style>
	<div id="wcvendor_image_bg">
		<div id="wcvendor_profile_wrap">
			<div class="rh-container">
				<div class="tabledisplay">
		    		<div id="wcvendor_profile_logo" class="wcvendor_profile_cell">
					<?php if( $external_store_url ) : ?><a href="<?php echo esc_url($external_store_url); ?>" rel="nofollow" target="_blank"><?php endif; ?>
						<img src="<?php echo rh_show_vendor_avatar($vendor_id, 150, 150); ?>" width=150 height=150 />
					<?php if( $external_store_url ) : ?></a><?php endif; ?>
		    		</div>
		    		<div id="wcvendor_profile_act_desc" class="wcvendor_profile_cell">
		    			<div class="wcvendor_store_name">
							<?php if ( $verified_vendor ) : ?>
								<?php 				
									if(function_exists('get_wcmp_vendor_verification_badge')){
										$badge_img = get_vendor_verification_settings('badge_img');
										if(!empty($badge_img)){
											$vendor_verified_label = get_wcmp_vendor_verification_badge( $vendor_id, array( 'height' => 32, 'width' =>32 ) );
										}
									}
									if(!$vendor_verified_label){
										$vendor_verified_label = '<div class="wcv-verified-vendor"><i class="rhicon rhi-shield-check" aria-hidden="true"></i> '.__( 'Verified', 'rehub-theme' ).'</div>';
									}
								?>									
								
								<?php echo ''.$vendor_verified_label;?>
								
							<?php endif; ?>			    				    			
		    				<h1><?php echo esc_html( $shop_name ); ?></h1>
		    				<?php do_action('before_wcmp_vendor_information', $vendor_id); ?>
		    			</div>
	    				<div class="wcmvendor_store_ratings">
						<?php 
						if(get_wcmp_vendor_settings('is_sellerreview', 'general') == 'Enable') {
							if(isset($queried_object->term_id) && !empty($queried_object)) {		
								$rating_val_array = wcmp_get_vendor_review_info($queried_object->term_id); 
								$WCMp->template->get_template( 'review/rating.php', array('rating_val_array' => $rating_val_array)); 
							}
						}
						?>
						</div>
						<div class="wcvendor_store_desc">
							<div class="mb5 mt5">
								<?php echo esc_attr($vendor_address); ?> 
							</div>
							<div>
								<?php if($vendor_phone) : ?>
									<a href="tel:<?php echo esc_html($vendor_phone); ?>" class="mr5">
										<i class="rhicon rhi-mobile-android-alt"></i> <?php echo esc_html($vendor_phone); ?>
									</a>
								<?php endif; ?>
								<?php if($vendor_email) : ?>								
									<a href="mailto:<?php echo antispambot( $vendor_email ); ?>" class="mr5">
										<i class="rhicon rhi-envelope"></i> <?php echo ''.$vendor_email; ?>
									</a>
								<?php endif; ?>								
							</div>
						</div>
		    		</div>
		    		<div id="wcvendor_profile_act_btns" class="wcvendor_profile_cell">
		    			<span class="wpsm-button medium red act-rehub-login-popup"><?php echo getShopLikeButton($vendor_id); ?></span>	    
		    			<?php if(is_active_widget( '', '', 'dc-vendor-quick-info')):?>
							<span data-scrollto="#wcmp-vendor-contact-widget-top" class="wpsm-button medium white rehub_scroll"><?php esc_html_e('Contact vendor', 'rehub-theme') ;?></span>

						<?php else :?>			
						    <?php if ( class_exists( 'BuddyPress' ) ) : ?>
						    	<?php if ( bp_loggedin_user_id() && bp_loggedin_user_id() != $vendor_id ) : ?>
									<?php 
										if ( function_exists( 'bp_follow_add_follow_button' ) ) {
									        bp_follow_add_follow_button( array(
									            'leader_id'   => $vendor_id,
									            'follower_id' => bp_loggedin_user_id(),
									            'link_class'  => 'wpsm-button medium green'
									        ) );
									    }
									?>				    		
								    <?php
								        if ( bp_is_active( 'messages' )){
										    $link = (is_user_logged_in()) ? wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( $vendor_id)) : '#';
										    $class = (!is_user_logged_in() && rehub_option('userlogin_enable') == '1') ? ' act-rehub-login-popup' : '';
										    echo ' <a href="'.$link.'" class="wpsm-button medium white'.$class.'">'. esc_html__('Contact vendor', 'rehub-theme') .'</a>';
									    }
								    ?>
							    <?php endif;?>
							<?php endif;?>
						<?php endif;?>
		    		</div>	        			
				</div>
			</div>
		</div>
		<span class="wcvendor-cover-image-mask wcmvendor-cover-mask"></span>
	</div>
	<div id="wcvendor_profile_menu">
		<div class="rh-container litesearchstyle">	
			<?php if( !$is_block ) : ?>		
			<form id="wcvendor_search_shops" role="search" method="get" class="wcvendor-search-inside search-form">
				<input type="text" name="rh_wcv_search" placeholder="<?php esc_html_e('Search in this shop', 'rehub-theme');?>" value="">
				<button type="submit" class="btnsearch"><i class="rhicon rhi-search"></i></button>					
			</form>	
			<?php endif; ?>
			<ul class="wcvendor_profile_menu_items">		
				<li class="active"><a href="#vendor-items" aria-controls="vendor-items" role="tab" data-toggle="tab" aria-expanded="true"><?php esc_html_e('Items', 'rehub-theme'); ?></a></li>
				<?php if ( !$vendor_hide_description ) : ?>
				<li><a href="#vendor-about" aria-controls="vendor-about" role="tab" data-toggle="tab" aria-expanded="true" data-scrollto="#vendor-about"><?php esc_html_e('About', 'rehub-theme');?></a></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>
<div class="clearfix"></div>

<!-- CONTENT -->
<div class="rh-container wcvcontent"> 
    <div class="rh-content-wrap clearfix">
        <!-- Main Side -->
        <div class="rh-mini-sidebar-content-area floatright page clearfix tabletblockdisplay">
            <article class="post" id="page-<?php the_ID(); ?>">
                <?php 
					do_action( 'woocommerce_before_main_content' );
				?>   		
	        	<div role="tabvendor" class="tab-pane active" id="vendor-items">
					<?php if ( have_posts() ) : ?>
						<?php 
							do_action( 'woocommerce_before_shop_loop' ); 
						?>
						<?php $classes = array(); ?>
						<?php 
							if(rehub_option('width_layout') == 'extended'){
								$classes[] = 'col_wrap_fourth';
							}
							else{
								$classes[] = 'col_wrap_three';
							}
						?>						
						<?php 
						$current_design = rehub_option('woo_design');
						if ($current_design == 'grid') {
							$classes[] = 'rh-flex-eq-height grid_woo';
						}
						elseif ($current_design == 'gridtwo'){
							echo rh_generate_incss('offergrid');
						    $classes[] = 'eq_grid pt5 rh-flex-eq-height';
						}
						elseif ($current_design == 'gridrev' || $current_design == 'griddigi') {
							$classes[] = 'rh-flex-eq-height woogridrev';
						}						
						elseif ($current_design == 'list' || $current_design == 'deallist' || $current_design == 'compactlist') {
							$classes[] = 'list_woo';
							if ($current_design == 'deallist') {
								$classes[] = 'woo_offer_list';
							}
						}
						else {
							$classes[] = 'column_woo';
						}
						?>					
						<div class="products <?php echo implode(' ',$classes);?>">
							<?php while ( have_posts() ) : the_post(); ?>
								<?php 
									if(rehub_option('width_layout') == 'extended'){
										$columns = '4_col';
									}
									else{
										$columns = '3_col';
									}
								?>									
								<?php if ($current_design == 'list'){
								    include(rh_locate_template('inc/parts/woolistmain.php'));
								}
								elseif ($current_design == 'deallist'){
								    include(rh_locate_template('inc/parts/woolistpart.php'));
								}
								elseif ($current_design == 'compactlist'){
									include(rh_locate_template('inc/parts/woolistcompact.php'));
								}
								elseif ($current_design == 'grid'){
								    include(rh_locate_template('inc/parts/woogridpart.php'));
								}
								elseif ($current_design == 'gridrev'){
    								include(rh_locate_template('inc/parts/woogridrev.php'));
								}
								elseif ($current_design == 'griddigi'){
    								include(rh_locate_template('inc/parts/woogriddigi.php'));
								}
								elseif ($current_design == 'gridtwo'){
								    include(rh_locate_template('inc/parts/woogridcompact.php'));
								}									
								else{										
								    include(rh_locate_template('inc/parts/woocolumnpart.php'));
								} ?>
							<?php endwhile; // end of the loop. ?>
						</div>
						<?php 
							do_action( 'woocommerce_after_shop_loop' ); 
						?>
					<?php else: ?>
							<?php wc_get_template( 'loop/no-products-found.php' ); ?>
					<?php endif; ?>
				</div>
				<?php if ( !$vendor_hide_description ) : ?>
				<div role="tabvendor" class="tab-pane" id="vendor-about">
					<?php echo wp_specialchars_decode( wpautop( $vendor->description ), ENT_QUOTES ); ?>
				</div>
				<?php endif; ?>
				<?php
					do_action( 'woocommerce_after_main_content' ); 
				?>                
    		</article>
    	</div>
		<!-- /Main Side --> 

	    <!-- Sidebar -->
	    <aside class="rh-mini-sidebar user-profile-div floatleft tabletblockdisplay">    
			<div class="rh-cartbox widget user-stat-in-vendor">
				<div>
					<div class="widget-inner-title rehub-main-font"><?php esc_html_e('Shop owner:', 'rehub-theme');?></div>
					<div class="profile-avatar text-center">
					<?php if ( function_exists('bp_displayed_user_avatar') ) : ?>
						<?php bp_displayed_user_avatar( 'type=full&width=110&height=110&&item_id='.$vendor_id ); ?>
					<?php else : ?>
						<?php echo get_avatar( $comment, 110 ); ?>
					<?php endif; ?>
					</div>
					<div class="profile-usertitle text-center mt20">
						<div class="profile-usertitle-name font110 fontbold mb20">
						<?php if ( function_exists('bp_core_get_user_domain') ) : ?>
							<a href="<?php echo bp_core_get_user_domain( $vendor_id ); ?>">
						<?php endif;?>
							<?php the_author_meta( 'nickname',$vendor_id); ?> 						
							<?php if (!empty($mycredrank) && is_object( $mycredrank)) :?>
								<span class="rh-user-rank-mc rh-user-rank-<?php echo (int)$mycredrank->post_id; ?>">
									<?php echo esc_html($mycredrank->title) ;?>
								</span>
							<?php endif;?>
							<?php if ( function_exists('bp_core_get_user_domain') ) : ?></a><?php endif;?>
						</div>
					</div>
					<div class="lineheight25 margincenter mb10 profile-stats">
						<div class="pt5 pb5 pl10 pr10"><i class="rhicon rhi-user-circle mr5 rtlml5"></i> <?php esc_html_e( 'Registration', 'rehub-theme' );  echo ': ' . date_i18n( get_option( "date_format" ), strtotime( $vendor->user_data->user_registered )); ?></div>
						<div class="pt5 pb5 pl10 pr10"><i class="rhicon rhi-heartbeat mr5 rtlml5"></i><?php esc_html_e( 'Product Votes', 'rehub-theme' ); echo ': ' . $count_p_votes; ?></div>
						<div class="pt5 pb5 pl10 pr10"><i class="rhicon rhi-briefcase mr5 rtlml5"></i><?php esc_html_e( 'Total submitted', 'rehub-theme' ); echo ': ' . $totaldeals; ?></div>
	                    <?php if (!empty($mycredpoint)) :?><div class="pt5 pb5 pl10 pr10"><i class="rhicon rhi-chart-bar mr5 rtlml5"></i><?php echo esc_html($mycredlabel);?>: <?php echo ''.$mycredpoint;?> </div><?php endif;?>
					</div>
					<?php
					$vendor_fb_profile = get_user_meta($vendor_id,'_vendor_fb_profile', true);
					$vendor_twitter_profile = get_user_meta($vendor_id,'_vendor_twitter_profile', true);
					$vendor_linkdin_profile = get_user_meta($vendor_id,'_vendor_linkdin_profile', true);
					$vendor_google_plus_profile = get_user_meta($vendor_id,'_vendor_google_plus_profile', true);
					$vendor_youtube = get_user_meta($vendor_id,'_vendor_youtube', true);
					$vendor_instagram = get_user_meta($vendor_id,'_vendor_instagram', true);
					?>
					<?php if ($vendor_twitter_profile OR $vendor_instagram OR $vendor_fb_profile OR $vendor_linkdin_profile OR $vendor_youtube OR $vendor_google_plus_profile) : ?>
					<div class="profile-socbutton lineheight25 margincenter mb10">
						<div class="social_icon small_i pt5 pb5 pl10 pr10">
							<?php if ( $vendor_fb_profile != '') { ?><a href="<?php echo esc_url_raw($vendor_fb_profile); ?>" target="_blank" class="author-social fb" rel="nofollow"><i class="rhicon rhi-facebook"></i></a><?php } ?>
							<?php if ( $vendor_twitter_profile != '') { ?><a href="<?php echo esc_url_raw($vendor_twitter_profile); ?>" target="_blank" class="author-social tw" rel="nofollow"><i class="rhicon rhi-twitter"></i></a><?php } ?>
							<?php if ( $vendor_linkdin_profile != '') { ?><a href="<?php echo esc_url_raw($vendor_linkdin_profile); ?>" target="_blank" class="author-social fb" rel="nofollow"><i class="rhicon rhi-linkedin"></i></a><?php } ?>
							<?php if ( $vendor_google_plus_profile != '') { ?><a href="<?php echo esc_url_raw($vendor_google_plus_profile); ?>" target="_blank" class="author-social gp" rel="nofollow"><i class="rhicon rhi-google-plus"></i></a><?php } ?>
							<?php if ( $vendor_youtube != '') { ?><a href="<?php echo esc_url_raw($vendor_youtube); ?>" target="_blank" class="author-social yt" rel="nofollow"><i class="rhicon rhi-youtube"></i></a><?php } ?>
							<?php if ( $vendor_instagram != '') { ?><a href="<?php echo esc_url_raw($vendor_instagram); ?>" target="_blank" class="author-social fb" rel="nofollow"><i class="rhicon rhi-instagram"></i></a><?php } ?>
						 </div>
					</div>
					<?php endif; ?>
					<?php if( $vendor_address OR $vendor_email OR $vendor_phone OR $external_store_url ) : ?>
					<div class="profile-description lineheight25 margincenter mb10 wcmp-profile-contacts">
						<div class="pt5 pb5 pl10 pr10">
						<span class="border-grey-bottom blockstyle width-100p mb5 fontbold"><?php esc_html_e( 'Contact', 'rehub-theme' ); ?></span>
						<ul class="font90 lineheight20">
						<?php if($vendor_address) : ?>
							<li class="profile-description-address"><?php echo esc_attr($vendor_address); ?></li>
						<?php endif; ?>
						<?php if ($vendor_email ) : ?>
							<li class="profile-description-email"><a href="mailto:<?php echo antispambot( $vendor_email ); ?>"><?php echo antispambot( $vendor_email ); ?></a></li>
						<?php endif; ?>
						<?php if ($vendor_phone):?>
							<li class="profile-description-phone"><a href="tel:<?php echo esc_html($vendor_phone); ?>"><?php echo esc_html($vendor_phone); ?></a></li>
						<?php endif; ?>
						<?php if ($external_store_url):?>
							<li class="profile-description-url"><a href="<?php echo esc_url($external_store_url); ?>" rel="nofollow" target="_blank"><?php echo esc_attr($external_store_label); ?></a></li>
						<?php endif; ?>
						</ul>
						</div>
					</div>					
					<?php endif; ?>
					<?php if ( function_exists( 'mycred_get_users_badges' ) ) : ?>
	                <div class="profile-achievements mb15 text-center">
                        <div>
                            <?php rh_mycred_display_users_badges( $vendor_id ) ?>
                        </div>
	                </div>
	            <?php endif; ?>
                <?php if ( function_exists('bp_core_get_user_domain') ) : ?>
                	<?php if ( bp_is_active( 'xprofile' ) ) : ?>
						<?php if ( bp_has_profile( array( 'profile_group_id' => 1, 'fetch_field_data' => true, 'user_id'=>$vendor_id ) ) ) : while ( bp_profile_groups() ) : bp_the_profile_group(); ?>
							<?php $numberfields = explode(',', bp_get_the_profile_field_ids());?>
							<?php $count = (!empty($numberfields)) ? count($numberfields) : '';?>
							<?php $bp_profile_description = rehub_option('rh_bp_seo_description');?>
							<?php $bp_profile_phone = rehub_option('rh_bp_phone');	?>

							<?php if ($count > 1) :?>
								<ul id="xprofile-in-wcmstore" class="flowhidden">
									<?php $fieldid = 0; while ( bp_profile_fields() ) : bp_the_profile_field(); $fieldid++; ?>
										<?php if ($fieldid == 1) continue;?>
										<?php $fieldname = bp_get_the_profile_field_name();?>
										<?php if($fieldname == $bp_profile_phone) continue;?>
										<?php if($fieldname == $bp_profile_description) continue;?>
										<?php if ( bp_field_has_data() ) : ?>
											<li>
												<div class="floatleft mr5"><?php echo esc_attr($fieldname) ?>: </div>
												<div class="floatleft"><?php bp_the_profile_field_value() ?></div>	
											</li>
										<?php endif; ?>
									<?php endwhile; ?>
								</ul>
							<?php endif; ?>
						<?php endwhile; endif; ?>
                	<?php endif;?>
					
                    <div class="profile-usermenu mt20">
	                    <ul class="user-menu-tab" role="tablist">
	                        <li class="text-center">
	                            <a href="<?php echo bp_core_get_user_domain( $vendor_id ); ?>" class="position-relative blockstyle pt10 pb10 pl15 pr15"><i class="rhicon rhi-folder-open mr5 rtlml5"></i><?php esc_html_e( 'Show full profile', 'rehub-theme' ); ?></a>
	                        </li>
	                    </ul>
                    </div>
					<?php endif; ?>
	            </div>	    		
			</div>

	        <?php if ( is_active_sidebar( 'wcw-storepage-sidebar' ) ) : ?>
	            <?php dynamic_sidebar( 'wcw-storepage-sidebar' ); ?>
	        <?php endif;?>		    	
		</aside>
    </div>
</div>
<!-- /CONTENT -->	

<?php get_footer(); ?>