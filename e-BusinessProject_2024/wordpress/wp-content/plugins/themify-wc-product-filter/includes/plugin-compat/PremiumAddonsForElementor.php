<?php

/**
 * Premium Addons for Elementor
 * @link https://premiumaddons.com/
 */
class Themify_WPF_Plugin_Compat_PremiumAddonsForElementor {

	public static function init() {
		add_filter( 'premium_woo_products_query_args', [ __CLASS__, 'premium_woo_products_query_args' ] );
	}

	/**
	 * flag queries from Woo Products module to be filtered by WPF
	 */
	public static function premium_woo_products_query_args( $query_args ) {
		$query_args['themify_wpf'] = true;

		return $query_args;
	}
}