<?php

namespace RT\ThePostGrid\Controllers\Api;

use RT\ThePostGrid\Helpers\Fns;

class CountLayoutInstall {
	public function __construct() {
		add_action( "rest_api_init", [ $this, 'register_count_layout_route' ] );
	}

	public function register_count_layout_route() {
		register_rest_route( 'rttpg/v1', 'countlayout', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'count_layout' ],
			'permission_callback' => function () {
				return true;
			}
		] );
	}

	public function count_layout() {

	}

}