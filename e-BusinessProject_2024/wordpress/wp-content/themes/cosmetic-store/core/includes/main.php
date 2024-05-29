<?php

/**
* Get started notice
*/

add_action( 'wp_ajax_cosmetic_store_dismissed_notice_handler', 'cosmetic_store_ajax_notice_handler' );

/**
 * AJAX handler to store the state of dismissible notices.
 */
function cosmetic_store_ajax_notice_handler() {
    if ( isset( $_POST['type'] ) ) {
        // Pick up the notice "type" - passed via jQuery (the "data-notice" attribute on the notice)
        $type = sanitize_text_field( wp_unslash( $_POST['type'] ) );
        // Store it in the options table
        update_option( 'dismissed-' . $type, TRUE );
    }
}

function cosmetic_store_deprecated_hook_admin_notice() {
        // Check if it's been dismissed...
        if ( ! get_option('dismissed-get_started', FALSE ) ) { ?>

            <?php
            $current_screen = get_current_screen();
				if ($current_screen->id !== 'appearance_page_cosmetic-store-guide-page') {
             $cosmetic_store_comments_theme = wp_get_theme(); ?>
            <div class="cosmetic-store-notice-wrapper updated notice notice-get-started-class is-dismissible" data-notice="get_started">
			<div class="cosmetic-store-notice">
				<div class="cosmetic-store-notice-img">
					<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/admin-logo.png'); ?>" alt="<?php esc_attr_e('logo', 'cosmetic-store'); ?>">
				</div>
				<div class="cosmetic-store-notice-content">
					<div class="cosmetic-store-notice-heading"><?php esc_html_e('Thanks for installing ','cosmetic-store'); ?><?php echo esc_html( $cosmetic_store_comments_theme ); ?></div>
					<p><?php printf(__('To avail the benefits of the premium edition, kindly click on <a href="%s">More Options</a>.', 'cosmetic-store'), esc_url(admin_url('themes.php?page=cosmetic-store-guide-page'))); ?></p>
					<?php if (is_child_theme()) { ?>
						<?php $child_theme = wp_get_theme(); ?>
						<?php printf(esc_html__('You\'re using %1$s theme, It\'s a child theme of %2$s.', 'cosmetic-store'), '<strong>' . $child_theme->Name . '</strong>', '<strong>' . esc_html__('cosmetic_store', 'cosmetic-store') . '</strong>'); 
						?>
						<?php
						$copy_link_args = array(
							'page' => 'cosmetic-store',
							'action' => 'show_copy_settings',
						);
						$copy_link = add_query_arg($copy_link_args, admin_url('themes.php'));
						?>
						<?php printf('%s <a href="%s" class="go-to-setting">%s</a>', esc_html__('Now you can copy setting data from parent theme to this child theme', 'cosmetic-store'), esc_url($copy_link), esc_html__('Copy Settings', 'cosmetic-store')); ?>
					<?php } ?>
				</div>
			</div>
		</div>
        <?php }
	}
}

add_action( 'admin_notices', 'cosmetic_store_deprecated_hook_admin_notice' );

add_action( 'admin_menu', 'cosmetic_store_getting_started' );
function cosmetic_store_getting_started() {
	add_theme_page( esc_html__('Get Started', 'cosmetic-store'), esc_html__('Get Started', 'cosmetic-store'), 'edit_theme_options', 'cosmetic-store-guide-page', 'cosmetic_store_test_guide');
}

function cosmetic_store_admin_enqueue_scripts() {
	wp_enqueue_style( 'cosmetic-store-admin-style', esc_url( get_template_directory_uri() ).'/css/main.css' );
	wp_enqueue_script( 'cosmetic-store-admin-script', get_template_directory_uri() . '/js/cosmetic-store-admin-script.js', array( 'jquery' ), '', true );
    wp_localize_script( 'cosmetic-store-admin-script', 'cosmetic_store_ajax_object',
        array( 'ajax_url' => admin_url( 'admin-ajax.php' ) )
    );
}
add_action( 'admin_enqueue_scripts', 'cosmetic_store_admin_enqueue_scripts' );

if ( ! defined( 'COSMETIC_STORE_DOCS_FREE' ) ) {
define('COSMETIC_STORE_DOCS_FREE',__('https://demo.misbahwp.com/docs/cosmetic-store-free-docs/','cosmetic-store'));
}
if ( ! defined( 'COSMETIC_STORE_DOCS_PRO' ) ) {
define('COSMETIC_STORE_DOCS_PRO',__('https://demo.misbahwp.com/docs/cosmetic-store-pro-docs','cosmetic-store'));
}
if ( ! defined( 'COSMETIC_STORE_BUY_NOW' ) ) {
define('COSMETIC_STORE_BUY_NOW',__('https://www.misbahwp.com/products/cosmetic-store-wordpress-theme/','cosmetic-store'));
}
if ( ! defined( 'COSMETIC_STORE_SUPPORT_FREE' ) ) {
define('COSMETIC_STORE_SUPPORT_FREE',__('https://wordpress.org/support/theme/cosmetic-store','cosmetic-store'));
}
if ( ! defined( 'COSMETIC_STORE_REVIEW_FREE' ) ) {
define('COSMETIC_STORE_REVIEW_FREE',__('https://wordpress.org/support/theme/cosmetic-store/reviews/#new-post','cosmetic-store'));
}
if ( ! defined( 'COSMETIC_STORE_DEMO_PRO' ) ) {
define('COSMETIC_STORE_DEMO_PRO',__('https://demo.misbahwp.com/cosmetic-store/','cosmetic-store'));
}
if( ! defined( 'COSMETIC_STORE_THEME_BUNDLE' ) ) {
define('COSMETIC_STORE_THEME_BUNDLE',__('https://www.misbahwp.com/products/wordpress-bundle/','cosmetic-store'));
}

function cosmetic_store_test_guide() { ?>
	<?php $cosmetic_store_theme = wp_get_theme(); ?>

	<div class="wrap" id="main-page">
		<div id="lefty">
			<div id="admin_links">
				<a href="<?php echo esc_url( COSMETIC_STORE_DOCS_FREE ); ?>" target="_blank" class="blue-button-1"><?php esc_html_e( 'Documentation', 'cosmetic-store' ) ?></a>
				<a href="<?php echo esc_url( admin_url('customize.php') ); ?>" id="customizer" target="_blank"><?php esc_html_e( 'Customize', 'cosmetic-store' ); ?> </a>
				<a class="blue-button-1" href="<?php echo esc_url( COSMETIC_STORE_SUPPORT_FREE ); ?>" target="_blank" class="btn3"><?php esc_html_e( 'Support', 'cosmetic-store' ) ?></a>
				<a class="blue-button-2" href="<?php echo esc_url( COSMETIC_STORE_REVIEW_FREE ); ?>" target="_blank" class="btn4"><?php esc_html_e( 'Review', 'cosmetic-store' ) ?></a>
			</div>
			<div id="description">
				<h3><?php esc_html_e('Welcome! Thank you for choosing ','cosmetic-store'); ?><?php echo esc_html( $cosmetic_store_theme ); ?>  <span><?php esc_html_e('Version: ', 'cosmetic-store'); ?><?php echo esc_html($cosmetic_store_theme['Version']);?></span></h3>
				<img class="img_responsive" style="width:100%;" src="<?php echo esc_url( get_template_directory_uri() ); ?>/screenshot.png">
				<div id="description-insidee">
					<?php
						$cosmetic_store_theme = wp_get_theme();
						echo wp_kses_post( apply_filters( 'misbah_theme_description', esc_html( $cosmetic_store_theme->get( 'Description' ) ) ) );
					?>
				</div>
			</div>
		</div>

		<div id="righty">
			<div class="postboxx donate">
				<h3 class="hndle"><?php esc_html_e( 'Upgrade to Premium', 'cosmetic-store' ); ?></h3>
				<div class="insidee">
					<p><?php esc_html_e('Discover upgraded pro features with premium version click to upgrade.','cosmetic-store'); ?></p>
					<div id="admin_pro_links">
						<a class="blue-button-2" href="<?php echo esc_url( COSMETIC_STORE_BUY_NOW ); ?>" target="_blank"><?php esc_html_e( 'Go Pro', 'cosmetic-store' ); ?></a>
						<a class="blue-button-1" href="<?php echo esc_url( COSMETIC_STORE_DEMO_PRO ); ?>" target="_blank"><?php esc_html_e( 'Live Demo', 'cosmetic-store' ) ?></a>
						<a class="blue-button-2" href="<?php echo esc_url( COSMETIC_STORE_DOCS_PRO ); ?>" target="_blank"><?php esc_html_e( 'Pro Docs', 'cosmetic-store' ) ?></a>
					</div>
				</div>

				<h3 class="hndle bundle"><?php esc_html_e( 'Go For Theme Bundle', 'cosmetic-store' ); ?></h3>
				<div class="insidee theme-bundle">
					<p class="offer"><?php esc_html_e('Get 80+ Perfect WordPress Theme In A Single Package at just $79."','cosmetic-store'); ?></p>
					<p class="coupon"><?php esc_html_e('Get Our Theme Pack of 80+ WordPress Themes At 15% Off','cosmetic-store'); ?><span class="coupon-code"><?php esc_html_e('"Bundleup15"','cosmetic-store'); ?></span></p>
					<div id="admin_pro_linkss">
						<a class="blue-button-1" href="<?php echo esc_url( COSMETIC_STORE_THEME_BUNDLE ); ?>" target="_blank"><?php esc_html_e( 'Theme Bundle', 'cosmetic-store' ) ?></a>
					</div>
				</div>
				<div class="d-table">
			    <ul class="d-column">
			      <li class="feature"><?php esc_html_e('Features','cosmetic-store'); ?></li>
			      <li class="free"><?php esc_html_e('Pro','cosmetic-store'); ?></li>
			      <li class="plus"><?php esc_html_e('Free','cosmetic-store'); ?></li>
			    </ul>
			    <ul class="d-row">
			      <li class="points"><?php esc_html_e('24hrs Priority Support','cosmetic-store'); ?></li>
			      <li class="right"><span class="dashicons dashicons-yes"></span></li>
			      <li class="wrong"><span class="dashicons dashicons-yes"></span></li>
			    </ul>
			    <ul class="d-row">
			      <li class="points"><?php esc_html_e('Kirki Framework','cosmetic-store'); ?></li>
			      <li class="right"><span class="dashicons dashicons-yes"></span></li>
			      <li class="wrong"><span class="dashicons dashicons-yes"></span></li>
			    </ul>
			    <ul class="d-row">
			      <li class="points"><?php esc_html_e('Advance Posttype','cosmetic-store'); ?></li>
			      <li class="right"><span class="dashicons dashicons-yes"></span></li>
			      <li class="wrong"><span class="dashicons dashicons-no"></span></li>
			    </ul>
			    <ul class="d-row">
			      <li class="points"><?php esc_html_e('One Click Demo Import','cosmetic-store'); ?></li>
			      <li class="right"><span class="dashicons dashicons-yes"></span></li>
			      <li class="wrong"><span class="dashicons dashicons-no"></span></li>
			    </ul>
			    <ul class="d-row">
			      <li class="points"><?php esc_html_e('Section Reordering','cosmetic-store'); ?></li>
			      <li class="right"><span class="dashicons dashicons-yes"></span></li>
			      <li class="wrong"><span class="dashicons dashicons-no"></span></li>
			    </ul>
			    <ul class="d-row">
			      <li class="points"><?php esc_html_e('Enable / Disable Option','cosmetic-store'); ?></li>
			      <li class="right"><span class="dashicons dashicons-yes"></span></li>
			      <li class="wrong"><span class="dashicons dashicons-yes"></span></li>
			    </ul>
			    <ul class="d-row">
			      <li class="points"><?php esc_html_e('Multiple Sections','cosmetic-store'); ?></li>
			      <li class="right"><span class="dashicons dashicons-yes"></span></li>
			      <li class="wrong"><span class="dashicons dashicons-no"></span></li>
			    </ul>
			    <ul class="d-row">
			      <li class="points"><?php esc_html_e('Advance Color Pallete','cosmetic-store'); ?></li>
			      <li class="right"><span class="dashicons dashicons-yes"></span></li>
			      <li class="wrong"><span class="dashicons dashicons-no"></span></li>
			    </ul>
			    <ul class="d-row">
			      <li class="points"><?php esc_html_e('Advance Widgets','cosmetic-store'); ?></li>
			      <li class="right"><span class="dashicons dashicons-yes"></span></li>
			      <li class="wrong"><span class="dashicons dashicons-yes"></span></li>
			    </ul>
			    <ul class="d-row">
			      <li class="points"><?php esc_html_e('Page Templates','cosmetic-store'); ?></li>
			      <li class="right"><span class="dashicons dashicons-yes"></span></li>
			      <li class="wrong"><span class="dashicons dashicons-no"></span></li>
			    </ul>
			    <ul class="d-row">
			      <li class="points"><?php esc_html_e('Advance Typography','cosmetic-store'); ?></li>
			      <li class="right"><span class="dashicons dashicons-yes"></span></li>
			      <li class="wrong"><span class="dashicons dashicons-no"></span></li>
			    </ul>
			    <ul class="d-row">
			      <li class="points"><?php esc_html_e('Section Background Image / Color ','cosmetic-store'); ?></li>
			      <li class="right"><span class="dashicons dashicons-yes"></span></li>
			      <li class="wrong"><span class="dashicons dashicons-no"></span></li>
			    </ul>
	  		</div>
			</div>
		</div>
	</div>

<?php } ?>
