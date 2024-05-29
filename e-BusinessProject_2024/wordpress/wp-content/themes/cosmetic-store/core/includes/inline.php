<?php


$cosmetic_store_custom_css = '';

/*---------------------------text-transform-------------------*/

$cosmetic_store_text_transform = get_theme_mod( 'menu_text_transform_cosmetic_store','CAPITALISE');

if($cosmetic_store_text_transform == 'CAPITALISE'){

	$cosmetic_store_custom_css .='#main-menu ul li a{';

		$cosmetic_store_custom_css .='text-transform: capitalize ; font-size: 13px;';

	$cosmetic_store_custom_css .='}';

}else if($cosmetic_store_text_transform == 'UPPERCASE'){

	$cosmetic_store_custom_css .='#main-menu ul li a{';

		$cosmetic_store_custom_css .='text-transform: uppercase ; font-size: 13px;';

	$cosmetic_store_custom_css .='}';

}else if($cosmetic_store_text_transform == 'LOWERCASE'){

	$cosmetic_store_custom_css .='#main-menu ul li a{';

		$cosmetic_store_custom_css .='text-transform: lowercase ; font-size: 13px;';

	$cosmetic_store_custom_css .='}';
}

/*---------------------------menu-zoom-------------------*/

			$cosmetic_store_menu_zoom = get_theme_mod( 'cosmetic_store_menu_zoom','None');

		if($cosmetic_store_menu_zoom == 'None'){

			$cosmetic_store_custom_css .='#main-menu ul li a{';

				$cosmetic_store_custom_css .='';

			$cosmetic_store_custom_css .='}';

		}else if($cosmetic_store_menu_zoom == 'Zoominn'){

			$cosmetic_store_custom_css .='#main-menu ul li a:hover{';

				$cosmetic_store_custom_css .='transition: all 0.3s ease-in-out !important; transform: scale(1.2) !important; color: #5fcb91;';

			$cosmetic_store_custom_css .='}';
		}

/*---------------------------Container Width-------------------*/

$cosmetic_store_container_width = get_theme_mod('cosmetic_store_container_width');

	$cosmetic_store_custom_css .='body{';

		$cosmetic_store_custom_css .='width: '.esc_attr($cosmetic_store_container_width).'%; margin: auto;';

	$cosmetic_store_custom_css .='}';

	/*---------------------------Slider-content-alignment-------------------*/

	$cosmetic_store_slider_content_alignment = get_theme_mod( 'cosmetic_store_slider_content_alignment','LEFT-ALIGN');

	 if($cosmetic_store_slider_content_alignment == 'LEFT-ALIGN'){

			$cosmetic_store_custom_css .='.blog_box{';

				$cosmetic_store_custom_css .='text-align:left;';

			$cosmetic_store_custom_css .='}';


		}else if($cosmetic_store_slider_content_alignment == 'CENTER-ALIGN'){

			$cosmetic_store_custom_css .='.blog_box{';

				$cosmetic_store_custom_css .='text-align:center; left: 0%; right: 0%;';

			$cosmetic_store_custom_css .='}';


		}else if($cosmetic_store_slider_content_alignment == 'RIGHT-ALIGN'){

			$cosmetic_store_custom_css .='.blog_box{';

				$cosmetic_store_custom_css .='text-align:right; right: 15%; left: 55%;';

			$cosmetic_store_custom_css .='}';

		}

	/*---------------------------Copyright Text alignment-------------------*/

$cosmetic_store_copyright_text_alignment = get_theme_mod( 'cosmetic_store_copyright_text_alignment','LEFT-ALIGN');

 if($cosmetic_store_copyright_text_alignment == 'LEFT-ALIGN'){

		$cosmetic_store_custom_css .='.copy-text p{';

			$cosmetic_store_custom_css .='text-align:left;';

		$cosmetic_store_custom_css .='}';


	}else if($cosmetic_store_copyright_text_alignment == 'CENTER-ALIGN'){

		$cosmetic_store_custom_css .='.copy-text p{';

			$cosmetic_store_custom_css .='text-align:center;';

		$cosmetic_store_custom_css .='}';


	}else if($cosmetic_store_copyright_text_alignment == 'RIGHT-ALIGN'){

		$cosmetic_store_custom_css .='.copy-text p{';

			$cosmetic_store_custom_css .='text-align:right;';

		$cosmetic_store_custom_css .='}';

	}

	/*---------------------------related Product Settings-------------------*/


$cosmetic_store_related_product_setting = get_theme_mod('cosmetic_store_related_product_setting',true);

	if($cosmetic_store_related_product_setting == false){

		$cosmetic_store_custom_css .='.related.products, .related h2{';

			$cosmetic_store_custom_css .='display: none;';

		$cosmetic_store_custom_css .='}';
	}

/*---------------------------Scroll to Top Alignment Settings-------------------*/

	$cosmetic_store_scroll_top_position = get_theme_mod( 'cosmetic_store_scroll_top_position','Right');

	if($cosmetic_store_scroll_top_position == 'Right'){

		$cosmetic_store_custom_css .='.scroll-up{';

			$cosmetic_store_custom_css .='right: 20px;';

		$cosmetic_store_custom_css .='}';

	}else if($cosmetic_store_scroll_top_position == 'Left'){

		$cosmetic_store_custom_css .='.scroll-up{';

			$cosmetic_store_custom_css .='left: 20px;';

		$cosmetic_store_custom_css .='}';

	}else if($cosmetic_store_scroll_top_position == 'Center'){

		$cosmetic_store_custom_css .='.scroll-up{';

			$cosmetic_store_custom_css .='right: 50%;left: 50%;';

		$cosmetic_store_custom_css .='}';
	}

		/*---------------------------Pagination Settings-------------------*/


$cosmetic_store_pagination_setting = get_theme_mod('cosmetic_store_pagination_setting',true);

	if($cosmetic_store_pagination_setting == false){

		$cosmetic_store_custom_css .='.nav-links{';

			$cosmetic_store_custom_css .='display: none;';

		$cosmetic_store_custom_css .='}';
	}

	/*--------------------------- Slider Opacity -------------------*/

	$cosmetic_store_slider_opacity_color = get_theme_mod( 'cosmetic_store_slider_opacity_color','0.6');

	if($cosmetic_store_slider_opacity_color == '0'){

		$cosmetic_store_custom_css .='.blog_inner_box img{';

			$cosmetic_store_custom_css .='opacity:0';

		$cosmetic_store_custom_css .='}';

		}else if($cosmetic_store_slider_opacity_color == '0.1'){

		$cosmetic_store_custom_css .='.blog_inner_box img{';

			$cosmetic_store_custom_css .='opacity:0.1';

		$cosmetic_store_custom_css .='}';

		}else if($cosmetic_store_slider_opacity_color == '0.2'){

		$cosmetic_store_custom_css .='.blog_inner_box img{';

			$cosmetic_store_custom_css .='opacity:0.2';

		$cosmetic_store_custom_css .='}';

		}else if($cosmetic_store_slider_opacity_color == '0.3'){

		$cosmetic_store_custom_css .='.blog_inner_box img{';

			$cosmetic_store_custom_css .='opacity:0.3';

		$cosmetic_store_custom_css .='}';

		}else if($cosmetic_store_slider_opacity_color == '0.4'){

		$cosmetic_store_custom_css .='.blog_inner_box img{';

			$cosmetic_store_custom_css .='opacity:0.4';

		$cosmetic_store_custom_css .='}';

		}else if($cosmetic_store_slider_opacity_color == '0.5'){

		$cosmetic_store_custom_css .='.blog_inner_box img{';

			$cosmetic_store_custom_css .='opacity:0.5';

		$cosmetic_store_custom_css .='}';

		}else if($cosmetic_store_slider_opacity_color == '0.6'){

		$cosmetic_store_custom_css .='.blog_inner_box img{';

			$cosmetic_store_custom_css .='opacity:0.6';

		$cosmetic_store_custom_css .='}';

		}else if($cosmetic_store_slider_opacity_color == '0.7'){

		$cosmetic_store_custom_css .='.blog_inner_box img{';

			$cosmetic_store_custom_css .='opacity:0.7';

		$cosmetic_store_custom_css .='}';

		}else if($cosmetic_store_slider_opacity_color == '0.8'){

		$cosmetic_store_custom_css .='.blog_inner_box img{';

			$cosmetic_store_custom_css .='opacity:0.8';

		$cosmetic_store_custom_css .='}';

		}else if($cosmetic_store_slider_opacity_color == '0.9'){

		$cosmetic_store_custom_css .='.blog_inner_box img{';

			$cosmetic_store_custom_css .='opacity:0.9';

		$cosmetic_store_custom_css .='}';

		}else if($cosmetic_store_slider_opacity_color == '1.0'){

		$cosmetic_store_custom_css .='.blog_inner_box img{';

			$cosmetic_store_custom_css .='opacity:0.9';

		$cosmetic_store_custom_css .='}';

		}

	/*---------------------- Slider Image Overlay ------------------------*/

	$cosmetic_store_overlay_option = get_theme_mod('cosmetic_store_overlay_option', true);

	if($cosmetic_store_overlay_option == false){

		$cosmetic_store_custom_css .='.blog_inner_box img{';

			$cosmetic_store_custom_css .='opacity:0.6;';

		$cosmetic_store_custom_css .='}';
	}

	$cosmetic_store_slider_image_overlay_color = get_theme_mod('cosmetic_store_slider_image_overlay_color', true);

	if($cosmetic_store_slider_image_overlay_color != false){

		$cosmetic_store_custom_css .='.blog_inner_box{';

			$cosmetic_store_custom_css .='background-color: '.esc_attr($cosmetic_store_slider_image_overlay_color).';';

		$cosmetic_store_custom_css .='}';
	}

			/*---------------------------Woocommerce Pagination Alignment Settings-------------------*/

		$cosmetic_store_woocommerce_pagination_position = get_theme_mod( 'cosmetic_store_woocommerce_pagination_position','Center');

		if($cosmetic_store_woocommerce_pagination_position == 'Left'){

			$cosmetic_store_custom_css .='.woocommerce nav.woocommerce-pagination{';

				$cosmetic_store_custom_css .='text-align: left;';

			$cosmetic_store_custom_css .='}';

		}else if($cosmetic_store_woocommerce_pagination_position == 'Center'){

			$cosmetic_store_custom_css .='.woocommerce nav.woocommerce-pagination{';

				$cosmetic_store_custom_css .='text-align: center;';

			$cosmetic_store_custom_css .='}';

		}else if($cosmetic_store_woocommerce_pagination_position == 'Right'){

			$cosmetic_store_custom_css .='.woocommerce nav.woocommerce-pagination{';

				$cosmetic_store_custom_css .='text-align: right;';

			$cosmetic_store_custom_css .='}';
		}
