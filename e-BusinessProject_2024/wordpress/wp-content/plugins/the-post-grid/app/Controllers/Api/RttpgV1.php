<?php

namespace RT\ThePostGrid\Controllers\Api;

use Exception;
use RT\ThePostGrid\Helpers\Fns;
use WP_REST_Request;
use WP_REST_Server;

class RttpgV1 {

	public function register_routes() {
		// For css file save
		register_rest_route(
			'rttpg/v1',
			'/block-save-css/',
			[
				[
					'methods'             => 'POST',
					'callback'            => [ $this, 'save_block_css' ],
					'permission_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
					'args'                => [],
				],
			]
		);
		// Get the Content by ID
		register_rest_route(
			'rttpg/v1',
			'/get-post-content/',
			[
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'get_post_content' ],
					'permission_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
					'args'                => [],
				],
			]
		);
		// Append Block CSS
		register_rest_route(
			'rttpg/v1',
			'/block-append-css/',
			[
				[
					'methods'             => 'POST',
					'callback'            => [ $this, 'append_block_css_callback' ],
					'permission_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
					'args'                => [],
				],
			]
		);

		register_rest_route(
			'rttpg/v1',
			'/block-append-reusable-css/',
			[
				[
					'methods'             => 'POST',
					'callback'            => [ $this, 'append_reusable_css_callback' ],
					'permission_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
					'args'                => [],
				],
			]
		);
	}


	/**
	 * Get post content
	 *
	 * @param WP_REST_Request $request Wp $request variable
	 *
	 * @return array|void
	 */
	public function get_post_content( WP_REST_Request $request ) {
		$params = $request->get_params();
		try {
			if ( isset( $params['postId'] ) ) {
				return [
					'success' => true,
					'data'    => ! empty( $params['postId'] ) ? get_post( $params['postId'] )->post_content : '',
					'message' => 'Get Data Success!!',
				];
			}
		} catch ( Exception $e ) {
			return [
				'success' => false,
				'message' => $e->getMessage(),
			];
		}
	}

	/**
	 * Save block css for each post in a css file and enqueue the file to the post page
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return array
	 */
	public function save_block_css( WP_REST_Request $request ) {
		try {
			global $wp_filesystem;
			if ( ! $wp_filesystem ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			$params        = $request->get_params();
			$post_id       = (int) sanitize_text_field( $params['post_id'] );
			$is_previewing = $params['isPreviewing'];

			if ( $params['is_remain'] ) {
				$block_css = $params['block_css'];
				$filename  = "rttpg-block-{$post_id}.css";

				$upload_dir = wp_upload_dir();
				$dir        = trailingslashit( $upload_dir['basedir'] ) . 'rttpg/';

				// Add Import in first
				$import_first = $this->set_import_url_to_top_css( $block_css );

				if ( true === $is_previewing ) {
					$filename = 'rttpg-block-preview.css';
				} else {
					update_post_meta( $post_id, '_rttpg_block_css', $import_first );
				}

				WP_Filesystem( false, $upload_dir['basedir'], true );

				if ( ! $wp_filesystem->is_dir( $dir ) ) {
					$wp_filesystem->mkdir( $dir );
				}
				// If fail to save css in directory, then it will show a message to user
				if ( ! $wp_filesystem->put_contents( $dir . $filename, $import_first ) ) {
					throw new Exception( __( 'CSS can not be saved due to permission!!!', 'the-post-grid' ) );
				}
			} else {
				if ( false === $is_previewing ) {
					delete_post_meta( $post_id, '_rttpg_block_css' );
					$this->delete_post_resource( $post_id );
				}
			}

			$success_message = __( 'The Post Grid preview css file has been updated.', 'the-post-grid' );
			// set block meta
			if ( false === $is_previewing ) {
				// ignore: phpcs
				update_post_meta( $post_id, '__rttpg_available_blocks', serialize( $params['available_blocks'] ) );
				$success_message = __( 'The Post Grid block css file has been updated.', 'the-post-grid' );
			}

			return [
				'success' => true,
				'message' => $success_message,
				'data'    => $params,
			];
		} catch ( Exception $e ) {
			return [
				'success' => false,
				'message' => $e->getMessage(),
			];
		}
	}

	/**
	 * @param string $get_css
	 *
	 * @return mixed|string
	 */
	public function set_import_url_to_top_css( $get_css = '' ) {
		$css_url            = "@import url('https://fonts.googleapis.com/css?family=";
		$google_font_exists = substr_count( $get_css, $css_url );

		if ( $google_font_exists ) {
			$pattern = sprintf(
				'/%s(.+?)%s/ims',
				preg_quote( $css_url, '/' ),
				preg_quote( "');", '/' )
			);

			if ( preg_match_all( $pattern, $get_css, $matches ) ) {
				$fonts   = $matches[0];
				$get_css = str_replace( $fonts, '', $get_css );
				if ( preg_match_all( '/font-weight[ ]?:[ ]?[\d]{3}[ ]?;/', $get_css, $matche_weight ) ) { // short out font weight
					$weight = array_map(
						function ( $val ) {
							$process = trim( str_replace( [ 'font-weight', ':', ';' ], '', $val ) );
							if ( is_numeric( $process ) ) {
								return $process;
							}
						},
						$matche_weight[0]
					);
					foreach ( $fonts as $key => $val ) {
						$fonts[ $key ] = str_replace( "');", '', $val ) . ':' . implode( ',', $weight ) . "');";
					}
				}

				// Multiple same fonts to single font
				$fonts   = array_unique( $fonts );
				$get_css = implode( '', $fonts ) . $get_css;
			}
		}

		return $get_css;
	}


	/**
	 * Delete post related data
	 *
	 * @delete post css file
	 */
	private function delete_post_resource( $post_id = '' ) {
		$post_id = $post_id ? $post_id : $this->is_single();
		if ( $post_id ) {
			$upload_dir = wp_upload_dir()['basedir'] . '/rttpg/';
			$css_path   = $upload_dir . 'rttpg-block-' . $post_id . '.css';
			if ( file_exists( $css_path ) ) {
				unlink( $css_path ); //phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
			}
		}
	}

	/**
	 * Determine if current single page is WP Page Builder Page
	 *
	 * @return bool|false|int
	 */
	private function is_single() {
		$post_id = get_the_ID();

		if ( ! $post_id ) {
			return false;
		}

		return $post_id;
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return void
	 */
	public function append_block_css_callback( WP_REST_Request $request ) {
		try {
			global $wp_filesystem;
			if ( ! $wp_filesystem ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}
			$params  = $request->get_params();
			$css     = $params['css'];
			$post_id = (int) sanitize_text_field( $params['post_id'] );
			if ( $post_id ) {
				$filename   = "block-css-{$post_id}.css";
				$upload_dir = wp_upload_dir();
				$dir        = trailingslashit( $upload_dir['basedir'] ) . 'rttpg/';
				if ( file_exists( $dir . $filename ) ) {
					$file = fopen( $dir . $filename, 'a' ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
					fwrite( $file, $css ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
					fclose( $file ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
				}
				$get_data = get_post_meta( $post_id, '_rttpg_block_css', true );
				update_post_meta( $post_id, '_rttpg_block_css', $get_data . $css );

				wp_send_json_success(
					[
						'success' => true,
						'message' => 'Update done' . $get_data,
					]
				);
			}
		} catch ( Exception $e ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => $e->getMessage(),
				]
			);
		}
	}


	/**
	 * @param WP_REST_Request $request
	 *
	 * @return void
	 */
	public function append_reusable_css_callback( $request ) {
		try {
			global $wp_filesystem;
			if ( ! $wp_filesystem ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}
			$params = $request->get_params();
			$css    = $params['css'];

			$filename   = 'blocks-preview.css';
			$upload_dir = wp_upload_dir();
			$dir        = trailingslashit( $upload_dir['basedir'] ) . 'rttpg/';
			if ( file_exists( $dir . $filename ) ) {
				$file = fopen( $dir . $filename, 'a' ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
				fwrite( $file, $css ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
				fclose( $file ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
			}
			wp_send_json_success(
				[
					'success' => true,
					'message' => 'appended reusable css in preview file',
				]
			);

		} catch ( Exception $e ) {
			wp_send_json_error(
				[
					'success' => false,
					'message' => $e->getMessage(),
				]
			);
		}
	}

}