<?php

if ( class_exists("Kirki")){

	// LOGO

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'slider',
		'settings'    => 'cosmetic_store_logo_resizer',
		'label'       => esc_html__( 'Adjust Your Logo Size ', 'cosmetic-store' ),
		'section'     => 'title_tagline',
		'default'     => 70,
		'choices'     => [
			'min'  => 10,
			'max'  => 300,
			'step' => 10,
		],
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'cosmetic_store_enable_logo_text',
		'section'     => 'title_tagline',
			'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Enable / Disable Site Title and Tagline', 'cosmetic-store' ) . '</h3>',
		'priority'    => 10,
	] );

    Kirki::add_field( 'theme_config_id', [
		'type'        => 'switch',
		'settings'    => 'cosmetic_store_display_header_title',
		'label'       => esc_html__( 'Site Title Enable / Disable Button', 'cosmetic-store' ),
		'section'     => 'title_tagline',
		'default'     => '1',
		'priority'    => 10,
		'choices'     => [
			'on'  => esc_html__( 'Enable', 'cosmetic-store' ),
			'off' => esc_html__( 'Disable', 'cosmetic-store' ),
		],
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'switch',
		'settings'    => 'cosmetic_store_display_header_text',
		'label'       => esc_html__( 'Tagline Enable / Disable Button', 'cosmetic-store' ),
		'section'     => 'title_tagline',
		'default'     => false,
		'priority'    => 10,
		'choices'     => [
			'on'  => esc_html__( 'Enable', 'cosmetic-store' ),
			'off' => esc_html__( 'Disable', 'cosmetic-store' ),
		],
	] );

	// FONT STYLE TYPOGRAPHY

	Kirki::add_panel( 'cosmetic_store_panel_id', array(
	    'priority'    => 10,
	    'title'       => esc_html__( 'Typography', 'cosmetic-store' ),
	) );

	Kirki::add_section( 'cosmetic_store_font_style_section', array(
		'title'      => esc_attr__( 'Typography Option',  'cosmetic-store' ),
		'priority'   => 2,
		'capability' => 'edit_theme_options',
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'cosmetic_store_all_headings_typography',
		'section'     => 'cosmetic_store_font_style_section',
			'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Heading Of All Sections',  'cosmetic-store' ) . '</h3>',
		'priority'    => 10,
	] );

	Kirki::add_field( 'global', array(
		'type'        => 'typography',
		'settings'    => 'cosmetic_store_all_headings_typography',
		'label'       => esc_attr__( 'Heading Typography',  'cosmetic-store' ),
		'description' => esc_attr__( 'Select the typography options for your heading.',  'cosmetic-store' ),
		'section'     => 'cosmetic_store_font_style_section',
		'priority'    => 10,
		'default'     => array(
			'font-family'    => '',
			'variant'        => '',
		),
		'output' => array(
			array(
				'element' => array( 'h1','h2','h3','h4','h5','h6', ),
			),
		),
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'cosmetic_store_body_content_typography',
		'section'     => 'cosmetic_store_font_style_section',
			'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Body Content',  'cosmetic-store' ) . '</h3>',
		'priority'    => 10,
	] );

	Kirki::add_field( 'global', array(
		'type'        => 'typography',
		'settings'    => 'cosmetic_store_body_content_typography',
		'label'       => esc_attr__( 'Content Typography',  'cosmetic-store' ),
		'description' => esc_attr__( 'Select the typography options for your content.',  'cosmetic-store' ),
		'section'     => 'cosmetic_store_font_style_section',
		'priority'    => 10,
		'default'     => array(
			'font-family'    => '',
			'variant'        => '',
		),
		'output' => array(
			array(
				'element' => array( 'body', ),
			),
		),
	) );

	// COLOR SECTION

	Kirki::add_section( 'cosmetic_store_section_color', array(
	    'title'          => esc_html__( 'Global Color', 'cosmetic-store' ),
	    'description'    => esc_html__( 'Theme Color Settings', 'cosmetic-store' ),
	    'panel'          => 'cosmetic_store_panel_id',
	    'priority'       => 160,
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'cosmetic_store_global_colors',
		'section'     => 'cosmetic_store_section_color',
		'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Here you can change your theme color on one click.', 'cosmetic-store' ) . '</h3>',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'color',
		'settings'    => 'cosmetic_store_global_color',
		'label'       => __( 'choose your Appropriate Color', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_section_color',
		'default'     => '#5fcb91',
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'color',
		'settings'    => 'cosmetic_store_global_color_2',
		'label'       => __( 'choose your Appropriate Color', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_section_color',
		'default'     => '#0d0d0d',
	] );

	// PANEL

	Kirki::add_panel( 'cosmetic_store_panel_id', array(
	    'priority'    => 10,
	    'title'       => esc_html__( 'Theme Options', 'cosmetic-store' ),
	) );

	// Additional Settings

	Kirki::add_section( 'cosmetic_store_additional_settings', array(
	    'title'          => esc_html__( 'Additional Settings', 'cosmetic-store' ),
	    'description'    => esc_html__( 'Scroll To Top', 'cosmetic-store' ),
	    'panel'          => 'cosmetic_store_panel_id',
	    'priority'       => 160,
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'settings'    => 'cosmetic_store_scroll_enable_setting',
		'label'       => esc_html__( 'Here you can enable or disable your scroller.', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_additional_settings',
		'default'     => '1',
		'priority'    => 10,
	] );

	new \Kirki\Field\Radio_Buttonset(
		[
			'settings'    => 'cosmetic_store_scroll_top_position',
			'label'       => esc_html__( 'Alignment for Scroll To Top', 'cosmetic-store' ),
			'section'     => 'cosmetic_store_additional_settings',
			'default'     => 'Right',
			'priority'    => 10,
			'choices'     => [
				'Left'   => esc_html__( 'Left', 'cosmetic-store' ),
				'Center' => esc_html__( 'Center', 'cosmetic-store' ),
				'Right'  => esc_html__( 'Right', 'cosmetic-store' ),
			],
		]
		);

	Kirki::add_field( 'theme_config_id', [
		'type'     => 'dashicons',
		'settings' => 'cosmetic_store_scroll_top_icon',
		'label'    => esc_html__( 'Select Appropriate Scroll Top Icon', 'cosmetic-store' ),
		'section'  => 'cosmetic_store_additional_settings',
		'default'  => 'dashicons dashicons-arrow-up-alt',
		'priority' => 10,
	] );


	new \Kirki\Field\Select(
	[
		'settings'    => 'menu_text_transform_cosmetic_store',
		'label'       => esc_html__( 'Menus Text Transform', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_additional_settings',
		'default'     => 'CAPITALISE',
		'placeholder' => esc_html__( 'Choose an option', 'cosmetic-store' ),
		'choices'     => [
			'CAPITALISE' => esc_html__( 'CAPITALISE', 'cosmetic-store' ),
			'UPPERCASE' => esc_html__( 'UPPERCASE', 'cosmetic-store' ),
			'LOWERCASE' => esc_html__( 'LOWERCASE', 'cosmetic-store' ),

		],
	]
	);

	new \Kirki\Field\Select(
	[
		'settings'    => 'cosmetic_store_menu_zoom',
		'label'       => esc_html__( 'Menu Transition', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_additional_settings',
		'default' => 'None',
		'placeholder' => esc_html__( 'Choose an option', 'cosmetic-store' ),
		'choices'     => [
			'None' => __('None','cosmetic-store'),
            'Zoominn' => __('Zoom Inn','cosmetic-store'),
            
		],
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'slider',
		'settings'    => 'cosmetic_store_container_width',
		'label'       => esc_html__( 'Theme Container Width', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_additional_settings',
		'default'     => 100,
		'choices'     => [
			'min'  => 50,
			'max'  => 100,
			'step' => 1,
		],
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'settings'    => 'cosmetic_store_site_loader',
		'label'       => esc_html__( 'Here you can enable or disable your Site Loader.', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_additional_settings',
		'default'     => false,
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'settings'    => 'cosmetic_store_sticky_header',
		'label'       => esc_html__( 'Here you can enable or disable your Sticky Header.', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_additional_settings',
		'default'     => false,
		'priority'    => 10,
	] );

	new \Kirki\Field\Select(
	[
		'settings'    => 'cosmetic_store_page_layout',
		'label'       => esc_html__( 'Page Layout Setting', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_additional_settings',
		'default' => 'Right Sidebar',
		'placeholder' => esc_html__( 'Choose an option', 'cosmetic-store' ),
		'choices'     => [
			'Left Sidebar' => __('Left Sidebar','cosmetic-store'),
            'Right Sidebar' => __('Right Sidebar','cosmetic-store'),
            'One Column' => __('One Column','cosmetic-store')
		],
	] );

	if ( class_exists("woocommerce")){

	// Woocommerce Settings

	Kirki::add_section( 'cosmetic_store_woocommerce_settings', array(
			'title'          => esc_html__( 'Woocommerce Settings', 'cosmetic-store' ),
			'description'    => esc_html__( 'Shop Page', 'cosmetic-store' ),
			'panel'          => 'cosmetic_store_panel_id',
			'priority'       => 160,
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'settings'    => 'cosmetic_store_shop_sidebar',
		'label'       => esc_html__( 'Here you can enable or disable shop page sidebar.', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_woocommerce_settings',
		'default'     => '1',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'settings'    => 'cosmetic_store_product_sidebar',
		'label'       => esc_html__( 'Here you can enable or disable product page sidebar.', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_woocommerce_settings',
		'default'     => '1',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'settings'    => 'cosmetic_store_related_product_setting',
		'label'       => esc_html__( 'Here you can enable or disable your related products.', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_woocommerce_settings',
		'default'     => true,
		'priority'    => 10,
	] );

	new \Kirki\Field\Number(
	[
		'settings' => 'cosmetic_store_per_columns',
		'label'    => esc_html__( 'Product Per Row', 'cosmetic-store' ),
		'section'  => 'cosmetic_store_woocommerce_settings',
		'default'  => 3,
		'choices'  => [
			'min'  => 1,
			'max'  => 4,
			'step' => 1,
		],
	]
	);

	new \Kirki\Field\Number(
	[
		'settings' => 'cosmetic_store_product_per_page',
		'label'    => esc_html__( 'Product Per Page', 'cosmetic-store' ),
		'section'  => 'cosmetic_store_woocommerce_settings',
		'default'  => 9,
		'choices'  => [
			'min'  => 1,
			'max'  => 15,
			'step' => 1,
		],
	]
	);

	new \Kirki\Field\Number(
	[
		'settings' => 'custom_related_products_number_per_row',
		'label'    => esc_html__( 'Related Product Per Column', 'cosmetic-store' ),
		'section'  => 'cosmetic_store_woocommerce_settings',
		'default'  => 3,
		'choices'  => [
			'min'  => 1,
			'max'  => 4,
			'step' => 1,
		],
	]
	);

	new \Kirki\Field\Number(
	[
		'settings' => 'custom_related_products_number',
		'label'    => esc_html__( 'Related Product Per Page', 'cosmetic-store' ),
		'section'  => 'cosmetic_store_woocommerce_settings',
		'default'  => 3,
		'choices'  => [
			'min'  => 1,
			'max'  => 10,
			'step' => 1,
		],
	]
	);

	new \Kirki\Field\Select(
	[
		'settings'    => 'cosmetic_store_shop_page_layout',
		'label'       => esc_html__( 'Shop Page Layout Setting', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_woocommerce_settings',
		'default' => 'Right Sidebar',
		'placeholder' => esc_html__( 'Choose an option', 'cosmetic-store' ),
		'choices'     => [
			'Left Sidebar' => __('Left Sidebar','cosmetic-store'),
            'Right Sidebar' => __('Right Sidebar','cosmetic-store')
		],
	] );

	new \Kirki\Field\Select(
	[
		'settings'    => 'cosmetic_store_product_page_layout',
		'label'       => esc_html__( 'Product Page Layout Setting', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_woocommerce_settings',
		'default' => 'Right Sidebar',
		'placeholder' => esc_html__( 'Choose an option', 'cosmetic-store' ),
		'choices'     => [
			'Left Sidebar' => __('Left Sidebar','cosmetic-store'),
            'Right Sidebar' => __('Right Sidebar','cosmetic-store')
		],
	] );

		new \Kirki\Field\Radio_Buttonset(
	[
		'settings'    => 'cosmetic_store_woocommerce_pagination_position',
		'label'       => esc_html__( 'Woocommerce Pagination Alignment', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_woocommerce_settings',
		'default'     => 'Center',
		'priority'    => 10,
		'choices'     => [
			'Left'   => esc_html__( 'Left', 'cosmetic-store' ),
			'Center' => esc_html__( 'Center', 'cosmetic-store' ),
			'Right'  => esc_html__( 'Right', 'cosmetic-store' ),
		],
	]
	);

}

	// POST SECTION

	Kirki::add_section( 'cosmetic_store_section_post', array(
	    'title'          => esc_html__( 'Post Settings', 'cosmetic-store' ),
	    'description'    => esc_html__( 'Here you can get different post settings', 'cosmetic-store' ),
	    'panel'          => 'cosmetic_store_panel_id',
	    'priority'       => 160,
	) );

	new \Kirki\Field\Sortable(
	[
		'settings' => 'cosmetic_store_archive_element_sortable',
		'label'    => __( 'Archive Post Page element Reordering', 'cosmetic-store' ),
		'section'  => 'cosmetic_store_section_post',
		'default'  => [ 'option1', 'option2', 'option3' ],
		'choices'  => [
			'option1' => esc_html__( 'Post Meta', 'cosmetic-store' ),
			'option2' => esc_html__( 'Post Title', 'cosmetic-store' ),
			'option3' => esc_html__( 'Post Content', 'cosmetic-store' ),
		],
	]
	);

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'slider',
		'settings'    => 'cosmetic_store_post_excerpt_number_1',
		'label'       => esc_html__( 'Post Content Range', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_section_post',
		'default'     => 15,
		'choices'     => [
			'min'  => 10,
			'max'  => 100,
			'step' => 1,
		],
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'toggle',
		'settings'    => 'cosmetic_store_pagination_setting',
		'label'       => esc_html__( 'Here you can enable or disable your Pagination.', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_section_post',
		'default'     => true,
		'priority'    => 10,
	] );

		new \Kirki\Field\Select(
	[
		'settings'    => 'cosmetic_store_archive_sidebar_layout',
		'label'       => esc_html__( 'Archive Post Sidebar Layout Setting', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_section_post',
		'default' => 'Right Sidebar',
		'placeholder' => esc_html__( 'Choose an option', 'cosmetic-store' ),
		'choices'     => [
			'Left Sidebar' => __('Left Sidebar','cosmetic-store'),
            'Right Sidebar' => __('Right Sidebar','cosmetic-store')
		],
	] );

	new \Kirki\Field\Select(
	[
		'settings'    => 'cosmetic_store_single_post_sidebar_layout',
		'label'       => esc_html__( 'Single Post Sidebar Layout Setting', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_section_post',
		'default' => 'Right Sidebar',
		'placeholder' => esc_html__( 'Choose an option', 'cosmetic-store' ),
		'choices'     => [
			'Left Sidebar' => __('Left Sidebar','cosmetic-store'),
            'Right Sidebar' => __('Right Sidebar','cosmetic-store')
		],
	] );

	new \Kirki\Field\Select(
	[
		'settings'    => 'cosmetic_store_search_sidebar_layout',
		'label'       => esc_html__( 'Search Page Sidebar Layout Setting', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_section_post',
		'default' => 'Right Sidebar',
		'placeholder' => esc_html__( 'Choose an option', 'cosmetic-store' ),
		'choices'     => [
			'Left Sidebar' => __('Left Sidebar','cosmetic-store'),
            'Right Sidebar' => __('Right Sidebar','cosmetic-store')
		],
	] );

	Kirki::add_field( 'cosmetic_store_config', [
		'type'        => 'select',
		'settings'    => 'cosmetic_store_post_column_count',
		'label'       => esc_html__( 'Grid Column for Archive Page', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_section_post',
		'default'    => '2',
		'choices' => [
				'1' => __( '1 Column', 'cosmetic-store' ),
				'2' => __( '2 Column', 'cosmetic-store' ),
				'3' => __( '3 Column', 'cosmetic-store' ),
				'4' => __( '4 Column', 'cosmetic-store' ),
			],
	] );


	// HEADER SECTION

	Kirki::add_section( 'cosmetic_store_section_header', array(
	    'title'          => esc_html__( 'Header Settings', 'cosmetic-store' ),
	    'description'    => esc_html__( 'Here you can add header information.', 'cosmetic-store' ),
	    'panel'          => 'cosmetic_store_panel_id',
	    'priority'       => 160,
	) );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'cosmetic_store_free_delivery_text_heading',
		'section'     => 'cosmetic_store_section_header',
		'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Free Delivery', 'cosmetic-store' ) . '</h3>',
		'priority'    => 10,
	] );

    Kirki::add_field( 'theme_config_id', [
		'type'     => 'text',
		'label'       => esc_html__( 'Text', 'cosmetic-store' ),
		'settings' => 'cosmetic_store_free_delivery_text',
		'section'  => 'cosmetic_store_section_header',
		'default'  => '',
		'priority' => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'     => 'url',
		'label'       => esc_html__( 'URL', 'cosmetic-store' ),
		'settings' => 'cosmetic_store_free_delivery_link',
		'section'  => 'cosmetic_store_section_header',
		'default'  => '',
		'priority' => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'cosmetic_store_live_chat_text_heading',
		'section'     => 'cosmetic_store_section_header',
		'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Live Chat', 'cosmetic-store' ) . '</h3>',
		'priority'    => 10,
	] );

    Kirki::add_field( 'theme_config_id', [
		'type'     => 'text',
		'label'       => esc_html__( 'Text', 'cosmetic-store' ),
		'settings' => 'cosmetic_store_live_chat_text',
		'section'  => 'cosmetic_store_section_header',
		'default'  => '',
		'priority' => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'     => 'url',
		'label'       => esc_html__( 'URL', 'cosmetic-store' ),
		'settings' => 'cosmetic_store_live_chat_link',
		'section'  => 'cosmetic_store_section_header',
		'default'  => '',
		'priority' => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'cosmetic_store_track_order_text_heading',
		'section'     => 'cosmetic_store_section_header',
		'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Track Orders', 'cosmetic-store' ) . '</h3>',
		'priority'    => 10,
	] );

    Kirki::add_field( 'theme_config_id', [
		'type'     => 'text',
		'label'       => esc_html__( 'Text', 'cosmetic-store' ),
		'settings' => 'cosmetic_store_track_order_text',
		'section'  => 'cosmetic_store_section_header',
		'default'  => '',
		'priority' => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'     => 'url',
		'label'       => esc_html__( 'URL', 'cosmetic-store' ),
		'settings' => 'cosmetic_store_track_order_link',
		'section'  => 'cosmetic_store_section_header',
		'default'  => '',
		'priority' => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'cosmetic_store_myaccount_link_heading',
		'section'     => 'cosmetic_store_section_header',
		'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'My Account URL', 'cosmetic-store' ) . '</h3>',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'     => 'url',
		'settings' => 'cosmetic_store_myaccount_link',
		'section'  => 'cosmetic_store_section_header',
		'default'  => '',
		'priority' => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'cosmetic_store_features_heading',
		'section'     => 'cosmetic_store_section_header',
		'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Features', 'cosmetic-store' ) . '</h3>',
		'priority'    => 10,
	] );

    Kirki::add_field( 'theme_config_id', [
		'type'     => 'text',
		'label'       => esc_html__( 'Features 1', 'cosmetic-store' ),
		'settings' => 'cosmetic_store_feature_text1',
		'section'  => 'cosmetic_store_section_header',
		'default'  => '',
		'priority' => 10,
	] );

    Kirki::add_field( 'theme_config_id', [
		'type'     => 'text',
		'label'       => esc_html__( 'Features 2', 'cosmetic-store' ),
		'settings' => 'cosmetic_store_feature_text2',
		'section'  => 'cosmetic_store_section_header',
		'default'  => '',
		'priority' => 10,
	] );


    Kirki::add_field( 'theme_config_id', [
		'type'     => 'text',
		'label'       => esc_html__( 'Features 3', 'cosmetic-store' ),
		'settings' => 'cosmetic_store_feature_text3',
		'section'  => 'cosmetic_store_section_header',
		'default'  => '',
		'priority' => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'cosmetic_store_enable_socail_link',
		'section'     => 'cosmetic_store_section_header',
		'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Social Media Link', 'cosmetic-store' ) . '</h3>',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'repeater',
		'section'     => 'cosmetic_store_section_header',
		'priority'    => 10,
		'row_label' => [
			'type'  => 'field',
			'value' => esc_html__( 'Social Icon', 'cosmetic-store' ),
			'field' => 'link_text',
		],
		'button_label' => esc_html__('Add New Social Icon', 'cosmetic-store' ),
		'settings'     => 'cosmetic_store_social_links_settings',
		'default'      => '',
		'fields' 	   => [
			'link_text' => [
				'type'        => 'text',
				'label'       => esc_html__( 'Icon', 'cosmetic-store' ),
				'description' => esc_html__( 'Add the fontawesome class ex: "fab fa-facebook-f".', 'cosmetic-store' ),
				'default'     => '',
			],
			'link_url' => [
				'type'        => 'url',
				'label'       => esc_html__( 'Social Link', 'cosmetic-store' ),
				'description' => esc_html__( 'Add the social icon url here.', 'cosmetic-store' ),
				'default'     => '',
			],
		],
		'choices' => [
			'limit' => 5
		],
	] );

	// SLIDER SECTION

	Kirki::add_section( 'cosmetic_store_blog_slide_section', array(
        'title'          => esc_html__( ' Slider Settings', 'cosmetic-store' ),
        'description'    => esc_html__( 'You have to select post category to show slider.', 'cosmetic-store' ),
        'panel'          => 'cosmetic_store_panel_id',
        'priority'       => 160,
    ) );

    Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'cosmetic_store_enable_heading',
		'section'     => 'cosmetic_store_blog_slide_section',
			'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Enable / Disable Slider', 'cosmetic-store' ) . '</h3>',
		'priority'    => 10,
	] );

    Kirki::add_field( 'theme_config_id', [
		'type'        => 'switch',
		'settings'    => 'cosmetic_store_blog_box_enable',
		'label'       => esc_html__( 'Section Enable / Disable', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_blog_slide_section',
		'default'     => '0',
		'priority'    => 10,
		'choices'     => [
			'on'  => esc_html__( 'Enable', 'cosmetic-store' ),
			'off' => esc_html__( 'Disable', 'cosmetic-store' ),
		],
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'switch',
		'settings'    => 'cosmetic_store_title_unable_disable',
		'label'       => esc_html__( 'Slide Title Enable / Disable', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_blog_slide_section',
		'default'     => '0',
		'priority'    => 10,
		'choices'     => [
			'on'  => esc_html__( 'Enable', 'cosmetic-store' ),
			'off' => esc_html__( 'Disable', 'cosmetic-store' ),
		],
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'switch',
		'settings'    => 'cosmetic_store_text_unable_disable',
		'label'       => esc_html__( 'Slide Text Enable / Disable', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_blog_slide_section',
		'default'     => '0',
		'priority'    => 10,
		'choices'     => [
			'on'  => esc_html__( 'Enable', 'cosmetic-store' ),
			'off' => esc_html__( 'Disable', 'cosmetic-store' ),
		],
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'switch',
		'settings'    => 'cosmetic_store_button_unable_disable',
		'label'       => esc_html__( 'Slide Button Enable / Disable', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_blog_slide_section',
		'default'     => '0',
		'priority'    => 10,
		'choices'     => [
			'on'  => esc_html__( 'Enable', 'cosmetic-store' ),
			'off' => esc_html__( 'Disable', 'cosmetic-store' ),
		],
	] );

    Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'cosmetic_store_slider_heading',
		'section'     => 'cosmetic_store_blog_slide_section',
			'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Slider', 'cosmetic-store' ) . '</h3>',
		'priority'    => 10,
	] );

    Kirki::add_field( 'theme_config_id', [
		'type'        => 'number',
		'settings'    => 'cosmetic_store_blog_slide_number',
		'label'       => esc_html__( 'Number of slides to show', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_blog_slide_section',
		'default'     => 0,
		'choices'     => [
			'min'  => 1,
			'max'  => 5,
			'step' => 1,
		],
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'select',
		'settings'    => 'cosmetic_store_blog_slide_category',
		'label'       => esc_html__( 'Select the category to show slider ( Image Dimension 1600 x 600 )', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_blog_slide_section',
		'default'     => '',
		'placeholder' => esc_html__( 'Select an category...', 'cosmetic-store' ),
		'priority'    => 10,
		'choices'     => cosmetic_store_get_categories_select(),
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'cosmetic_store_slider_short_heading_12',
		'section'     => 'cosmetic_store_blog_slide_section',
			'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Number of Text', 'cosmetic-store' ) . '</h3>',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'slider',
		'settings'    => 'cosmetic_store_post_excerpt_number',
		'label'       => esc_html__( 'Number of Slide Text To Show ', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_blog_slide_section',
		'default'     => 22,
		'choices'     => [
			'min'  => 10,
			'max'  => 50,
			'step' => 1,
		],
	] );

    Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'cosmetic_store_slider_short_heading',
		'section'     => 'cosmetic_store_blog_slide_section',
			'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Slider Sub Title', 'cosmetic-store' ) . '</h3>',
		'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'     => 'text',
		'settings' => 'cosmetic_store_slider_short_title',
		'section'  => 'cosmetic_store_blog_slide_section',
		'priority' => 10,
    ] );

		Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'cosmetic_store_enable_heading_22',
		'section'     => 'cosmetic_store_blog_slide_section',
			'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Content alignment', 'cosmetic-store' ) . '</h3>',
		'priority'    => 10,
	] );

		new \Kirki\Field\Select(
	[
		'settings'    => 'cosmetic_store_slider_content_alignment',
		'label'       => esc_html__( 'Slider Content Alignment', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_blog_slide_section',
		'default'     => 'LEFT-ALIGN',
		'placeholder' => esc_html__( 'Choose an option', 'cosmetic-store' ),
		'choices'     => [
			'LEFT-ALIGN' => esc_html__( 'LEFT-ALIGN', 'cosmetic-store' ),
			'CENTER-ALIGN' => esc_html__( 'CENTER-ALIGN', 'cosmetic-store' ),
			'RIGHT-ALIGN' => esc_html__( 'RIGHT-ALIGN', 'cosmetic-store' ),

		],
	] );

		new \Kirki\Field\Select(
	[
		'settings'    => 'cosmetic_store_slider_opacity_color',
		'label'       => esc_html__( 'Slider Opacity Option', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_blog_slide_section',
		'default'     => '0.6',
		'placeholder' => esc_html__( 'Choose an option', 'cosmetic-store' ),
		'choices'     => [
			'0' => esc_html__( '0', 'cosmetic-store' ),
			'0.1' => esc_html__( '0.1', 'cosmetic-store' ),
			'0.2' => esc_html__( '0.2', 'cosmetic-store' ),
			'0.3' => esc_html__( '0.3', 'cosmetic-store' ),
			'0.4' => esc_html__( '0.4', 'cosmetic-store' ),
			'0.5' => esc_html__( '0.5', 'cosmetic-store' ),
			'0.6' => esc_html__( '0.6', 'cosmetic-store' ),
			'0.7' => esc_html__( '0.7', 'cosmetic-store' ),
			'0.8' => esc_html__( '0.8', 'cosmetic-store' ),
			'0.9' => esc_html__( '0.9', 'cosmetic-store' ),
			'1.0' => esc_html__( '1.0', 'cosmetic-store' ),
			

		],
	] );

	 Kirki::add_field( 'theme_config_id', [
		'type'        => 'switch',
		'settings'    => 'cosmetic_store_overlay_option',
		'label'       => esc_html__( 'Enable / Disable Slider Overlay', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_blog_slide_section',
		'default'     => false,
		'priority'    => 10,
		'choices'     => [
			'on'  => esc_html__( 'Enable', 'cosmetic-store' ),
			'off' => esc_html__( 'Disable', 'cosmetic-store' ),
		],
	] );

	 Kirki::add_field( 'theme_config_id', [
		'type'        => 'color',
		'settings'    => 'cosmetic_store_slider_image_overlay_color',
		'label'       => __( 'choose your Appropriate Overlay Color', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_blog_slide_section',
		'default'     => '',
	] );

	// HOT PRODUCTS SECTION

	Kirki::add_section( 'cosmetic_store_hot_products_section', array(
        'title'          => esc_html__( 'Hot Products Settings', 'cosmetic-store' ),
        'description'    => esc_html__( 'You have to select product category to show cosmetic section.', 'cosmetic-store' ),
        'panel'          => 'cosmetic_store_panel_id',
        'priority'       => 160,
    ) );

    Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'cosmetic_store_hot_products_section_enable_heading',
		'section'     => 'cosmetic_store_hot_products_section',
			'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Enable / Disable Hot Products Section', 'cosmetic-store' ) . '</h3>',
		'priority'    => 1,
	] );

    Kirki::add_field( 'theme_config_id', [
		'type'        => 'switch',
		'settings'    => 'cosmetic_store_hot_products_section_enable',
		'label'       => esc_html__( 'Section Enable / Disable', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_hot_products_section',
		'default'     => '0',
		'priority'    => 2,
		'choices'     => [
			'on'  => esc_html__( 'Enable', 'cosmetic-store' ),
			'off' => esc_html__( 'Disable', 'cosmetic-store' ),
		],
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'cosmetic_store_hot_products_heading',
		'section'     => 'cosmetic_store_hot_products_section',
			'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Hot Products Headings',  'cosmetic-store' ) . '</h3>',
		'priority'    => 3,
	] );

    Kirki::add_field( 'theme_config_id', [
		'type'     => 'text',
		'settings' => 'cosmetic_store_hot_products_main_heading',
		'label'    => esc_html__( 'Main Heading', 'cosmetic-store' ),
		'section'  => 'cosmetic_store_hot_products_section',
		'priority' => 5,
    ] );

    Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'cosmetic_store_game_product_heading',
		'section'     => 'cosmetic_store_hot_products_section',
			'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Hot Products', 'cosmetic-store' ) . '</h3>',
		'priority'    => 7,
	] );

    Kirki::add_field( 'theme_config_id', [
		'type'        => 'number',
		'settings'    => 'cosmetic_store_hot_products_per_page',
		'label'       => esc_html__( 'Number of products to show', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_hot_products_section',
		'default'     => 8,
		'choices'     => [
			'min'  => 0,
			'max'  => 25,
			'step' => 1,
		],
	] );

	// FOOTER SECTION

	Kirki::add_section( 'cosmetic_store_footer_section', array(
        'title'          => esc_html__( 'Footer Settings', 'cosmetic-store' ),
        'description'    => esc_html__( 'Here you can change copyright text', 'cosmetic-store' ),
        'panel'          => 'cosmetic_store_panel_id',
        'priority'       => 160,
    ) );

    Kirki::add_field( 'theme_config_id', [
		'type'        => 'custom',
		'settings'    => 'cosmetic_store_footer_text_heading',
		'section'     => 'cosmetic_store_footer_section',
			'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Footer Copyright Text', 'cosmetic-store' ) . '</h3>',
		'priority'    => 10,
	] );

    Kirki::add_field( 'theme_config_id', [
		'type'     => 'text',
		'settings' => 'cosmetic_store_footer_text',
		'section'  => 'cosmetic_store_footer_section',
		'default'  => '',
		'priority' => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
	'type'        => 'custom',
	'settings'    => 'cosmetic_store_footer_text_heading_2',
	'section'     => 'cosmetic_store_footer_section',
	'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Footer Copyright Alignment', 'cosmetic-store' ) . '</h3>',
	'priority'    => 10,
	] );

	new \Kirki\Field\Select(
	[
		'settings'    => 'cosmetic_store_copyright_text_alignment',
		'label'       => esc_html__( 'Copyright text Alignment', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_footer_section',
		'default'     => 'LEFT-ALIGN',
		'placeholder' => esc_html__( 'Choose an option', 'cosmetic-store' ),
		'choices'     => [
			'LEFT-ALIGN' => esc_html__( 'LEFT-ALIGN', 'cosmetic-store' ),
			'CENTER-ALIGN' => esc_html__( 'CENTER-ALIGN', 'cosmetic-store' ),
			'RIGHT-ALIGN' => esc_html__( 'RIGHT-ALIGN', 'cosmetic-store' ),

		],
	] );

	Kirki::add_field( 'theme_config_id', [
	'type'        => 'custom',
	'settings'    => 'cosmetic_store_footer_text_heading_1',
	'section'     => 'cosmetic_store_footer_section',
	'default'         => '<h3 style="color: #2271b1; padding:10px; background:#fff; margin:0; border-left: solid 5px #2271b1; ">' . __( 'Footer Copyright Background Color', 'cosmetic-store' ) . '</h3>',
	'priority'    => 10,
	] );

	Kirki::add_field( 'theme_config_id', [
		'type'        => 'color',
		'settings'    => 'cosmetic_store_copyright_bg',
		'label'       => __( 'Choose Your Copyright Background Color', 'cosmetic-store' ),
		'section'     => 'cosmetic_store_footer_section',
		'default'     => '',
	] );
}

add_action( 'customize_register', 'cosmetic_store_customizer_settings' );
function cosmetic_store_customizer_settings( $wp_customize ) {
	//Hot Products Section
	$cosmetic_store_args = array(
       'type'                     => 'product',
        'child_of'                 => 0,
        'parent'                   => '',
        'orderby'                  => 'term_group',
        'order'                    => 'ASC',
        'hide_empty'               => false,
        'hierarchical'             => 1,
        'number'                   => '',
        'taxonomy'                 => 'product_cat',
        'pad_counts'               => false
    );
	$categories = get_categories($cosmetic_store_args);
	$cat_posts = array();
	$m = 0;
	$cat_posts[]='Select';
	foreach($categories as $category){
		if($m==0){
			$default = $category->slug;
			$m++;
		}
		$cat_posts[$category->slug] = $category->name;
	}

	$wp_customize->add_setting('cosmetic_store_hot_products_category',array(
		'default'	=> 'select',
		'sanitize_callback' => 'cosmetic_store_sanitize_select',
	));
	$wp_customize->add_control('cosmetic_store_hot_products_category',array(
		'type'    => 'select',
		'choices' => $cat_posts,
		'label' => __('Select category to display games ','cosmetic-store'),
		'section' => 'cosmetic_store_hot_products_section',
	));
}
