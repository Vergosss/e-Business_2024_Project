<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.2
 */


use RT\ThePostGrid\Helpers\Fns;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class rtTPGElementorQuery {

	static function get_terms_id( $id, $type ) {
		$data = [];
		$arr  = get_the_terms( $id, $type );
		if ( is_array( $arr ) ) {
			foreach ( $arr as $key => $val ) {
				$data[] = $val->term_id;
			}
		}

		return $data;
	}

	/**
	 * Post Query for normal grid widget
	 *
	 * @param          $data
	 * @param string $prefix
	 *
	 * @return array
	 */
	public static function post_query( $data, $prefix = '' ): array {

		$post_type = isset( $data['post_type'] ) ? $data['post_type'] : 'post';
		$args      = [
			'post_type'   => [ $post_type ],
			'post_status' => isset( $data['post_status'] ) ? $data['post_status'] : 'publish',
		];

		if ( $data['post_id'] ) {
			$post_ids = explode( ',', $data['post_id'] );
			$post_ids = array_map( 'trim', $post_ids );

			$args['post__in'] = $post_ids;
		}

		if ( $prefix !== 'slider' && 'show' === $data['show_pagination'] ) {
			$_paged        = is_front_page() ? "page" : "paged";
			$args['paged'] = get_query_var( $_paged ) ? absint( get_query_var( $_paged ) ) : 1;
		}

		if ( rtTPG()->hasPro() && 'yes' == $data['ignore_sticky_posts'] ) {
			$args['ignore_sticky_posts'] = 1;
		}


		//TODO: should display conditionally
		if ( $orderby = $data['orderby'] ) {

			$order_by        = ( $orderby == 'meta_value_datetime' ) ? 'meta_value_num' : $orderby;
			$args['orderby'] = $order_by;

			if ( in_array( $orderby, [ 'meta_value', 'meta_value_num', 'meta_value_datetime' ] ) && $data['meta_key'] ) {
				$args['meta_key'] = $data['meta_key']; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			}
		}

		if ( $data['order'] ) {
			$args['order'] = $data['order'];
		}

		if ( $data['instant_query'] ) {
			$args = Fns::get_instant_query( $data['instant_query'], $args );
		}

		if ( $data['author'] ) {
			$args['author__in'] = $data['author'];
		}

		if ( isset( $data['date_range'] ) ) :
			if ( rtTPG()->hasPro() && $data['date_range'] ) {
				if ( strpos( $data['date_range'], 'to' ) ) {
					$date_range         = explode( 'to', $data['date_range'] );
					$args['date_query'] = [
						[
							'after'     => trim( $date_range[0] ),
							'before'    => trim( $date_range[1] ),
							'inclusive' => true,
						],
					];
				}
			}
		endif;

		$_taxonomies = get_object_taxonomies( $data['post_type'], 'objects' );

		foreach ( $_taxonomies as $index => $object ) {
			if ( in_array( $object->name, Fns::get_excluded_taxonomy() ) ) {
				continue;
			}

			$setting_key = $object->name . '_ids';

			if ( $prefix !== 'slider' && rtTPG()->hasPro() && 'show' === $data['show_taxonomy_filter'] ) {
				if ( ( $data[ $data['post_type'] . '_filter_taxonomy' ] == $object->name ) && isset( $data[ $object->name . '_default_terms' ] ) && $data[ $object->name . '_default_terms' ] !== '0' ) {
					//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					$args['tax_query'][] = [
						'taxonomy' => $data[ $data['post_type'] . '_filter_taxonomy' ],
						'field'    => 'term_id',
						'terms'    => $data[ $object->name . '_default_terms' ],
					];
				} else {
					if ( ! empty( $data[ $setting_key ] ) ) {
						//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						$args['tax_query'][] = [
							'taxonomy' => $object->name,
							'field'    => 'term_id',
							'terms'    => $data[ $setting_key ],
						];
					}
				}
			} else {
				if ( ! empty( $data[ $setting_key ] ) ) {
					//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					$args['tax_query'][] = [
						'taxonomy' => $object->name,
						'field'    => 'term_id',
						'terms'    => $data[ $setting_key ],
					];
				}
			}
		}

		if ( ! empty( $args['tax_query'] ) && $data['relation'] ) {
			$args['tax_query']['relation'] = $data['relation']; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		}

		if ( $data['post_keyword'] ) {
			$args['s'] = $data['post_keyword'];
		}


		$offset_posts = $excluded_ids = [];
		if ( $data['exclude'] || $data['offset'] ) {
			if ( $data['exclude'] ) {
				$excluded_ids = explode( ',', $data['exclude'] );
				$excluded_ids = array_map( 'trim', $excluded_ids );
			}

			if ( $data['offset'] ) {
				$_temp_args = $args;
				unset( $_temp_args['paged'] );
				$_temp_args['posts_per_page'] = $data['offset'];
				$_temp_args['fields']         = 'ids';

				$offset_posts = get_posts( $_temp_args );
			}

			$excluded_post_ids    = array_merge( $offset_posts, $excluded_ids );
			$args['post__not_in'] = array_unique( $excluded_post_ids );  //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
		}

		if ( $prefix !== 'slider' ) {
			if ( $data['post_limit'] ) {
				if ( 'show' !== $data['show_pagination'] ) {
					$args['posts_per_page'] = $data['post_limit'];
				} else {
					$tempArgs                   = $args;
					$tempArgs['posts_per_page'] = $data['post_limit'];
					$tempArgs['paged']          = 1;
					$tempArgs['fields']         = 'ids';
					if ( ! empty( $offset_posts ) ) {
						$tempArgs['post__not_in'] = $offset_posts; //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
					}
					$tempQ = new WP_Query( $tempArgs );
					if ( ! empty( $tempQ->posts ) ) {
						$args['post__in']       = $tempQ->posts;
						$args['posts_per_page'] = $data['post_limit'];
					}
				}
			} else {
				$_posts_per_page = 9;
				if ( 'grid' === $prefix ) {
					if ( $data['grid_layout'] == 'grid-layout5' ) {
						$_posts_per_page = 5;
					} elseif ( in_array( $data['grid_layout'], [ 'grid-layout6', 'grid-layout6-2' ] ) ) {
						$_posts_per_page = 3;
					} elseif ( in_array( $data['grid_layout'], [ 'grid-layout5', 'grid-layout5-2' ] ) ) {
						$_posts_per_page = 5;
					}
				} elseif ( 'list' === $prefix ) {
					if ( in_array( $data['list_layout'], [ 'list-layout2', 'list-layout2-2' ] ) ) {
						$_posts_per_page = 7;
					} elseif ( in_array( $data['list_layout'], [ 'list-layout3', 'list-layout3-2' ] ) ) {
						$_posts_per_page = 5;
					}
				} elseif ( 'grid_hover' === $prefix ) {
					if ( in_array( $data['grid_hover_layout'], [ 'grid_hover-layout4', 'grid_hover-layout4-2' ] ) ) {
						$_posts_per_page = 7;
					} elseif ( in_array( $data['grid_hover_layout'], [
						'grid_hover-layout5',
						'grid_hover-layout5-2'
					] ) ) {
						$_posts_per_page = 3;
					} elseif ( in_array( $data['grid_hover_layout'],
						[
							'grid_hover-layout6',
							'grid_hover-layout6-2',
							'grid_hover-layout9',
							'grid_hover-layout9-2',
							'grid_hover-layout10',
							'grid_hover-layout11'
						] )
					) {
						$_posts_per_page = 4;
					} elseif ( in_array( $data['grid_hover_layout'], [
						'grid_hover-layout7',
						'grid_hover-layout7-2',
						'grid_hover-layout8'
					] ) ) {
						$_posts_per_page = 5;
					} elseif ( in_array( $data['grid_hover_layout'], [
						'grid_hover-layout6',
						'grid_hover-layout6-2'
					] ) ) {
						$_posts_per_page = 4;
					}
				}

				$args['posts_per_page'] = $_posts_per_page;
			}

			if ( 'show' === $data['show_pagination'] && $data['display_per_page'] ) {
				$args['posts_per_page'] = $data['display_per_page'];
			}
		} else {
			$slider_per_page = $data['post_limit'];
			if ( $data['slider_layout'] == 'slider-layout10' ) {
				$slider_reminder = ( intval( $data['post_limit'], 10 ) % 5 );
				if ( $slider_reminder ) {
					$slider_per_page = ( $data['post_limit'] - $slider_reminder + 5 );
				}
			}
			$args['posts_per_page'] = $slider_per_page;
		}


		return $args;
	}

	/**
	 * Post Query for page builder block
	 *
	 * @param          $data
	 * @param string $prefix
	 * @param string $template_type
	 *
	 * @return array
	 */
	public static function post_query_builder( $data, $prefix = '', $template_type = '' ): array {
		if ( 'single' === $template_type ) {
			$rt_post_cat = wp_get_object_terms( $data['last_post_id'], $data['taxonomy_lists'], [ 'fields' => 'ids' ] );
			$args        = [
				'post_type'    => 'post',
				'post_status'  => 'publish',
				//phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				'tax_query'    => [
					[
						'taxonomy' => $data['taxonomy_lists'],
						'field'    => 'id',
						'terms'    => $rt_post_cat,
					],
				],
				'post__not_in' => [ $data['last_post_id'] ], //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
			];

			if ( $orderby = $data['orderby'] ) {
				$order_by        = $data['orderby'] == 'meta_value_datetime' ? 'meta_value_num' : $data['orderby'];
				$args['orderby'] = $order_by;

				if ( in_array( $orderby, [ 'meta_value', 'meta_value_num' ] ) && $data['meta_key'] ) {
					$args['meta_key'] = $data['meta_key']; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				}
			}

			if ( $data['order'] ) {
				$args['order'] = $data['order'];
			}

			$slider_per_page = $data['post_limit'];
			if ( $data['slider_layout'] == 'slider-layout10' ) {
				$slider_reminder = ( intval( $data['post_limit'], 10 ) % 5 );
				if ( $slider_reminder ) {
					$slider_per_page = ( $data['post_limit'] - $slider_reminder + 5 );
				}
			}
			$args['posts_per_page'] = $slider_per_page;
		} else {
			$args = [
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => $data['post_limit'],
			];

			$excluded_ids = null;

			if ( $data['exclude'] || $data['offset'] ) {
				$excluded_ids = [];
				if ( $data['exclude'] ) {
					$excluded_ids = explode( ',', $data['exclude'] );
					$excluded_ids = array_map( 'trim', $excluded_ids );
				}

				$offset_posts = [];
				if ( $data['offset'] ) {
					$_temp_args = [
						'post_type'      => 'post',
						'posts_per_page' => $data['offset'],
						'post_status'    => 'publish',
						'fields'         => 'ids',
					];

					if ( is_tag() ) {
						$_temp_args['tag'] = get_query_var( 'tag' );
					}

					if ( is_category() ) {
						$_temp_args['cat'] = get_query_var( 'cat' );
					}

					if ( is_author() ) {
						$_temp_args['author'] = get_query_var( 'author' );
					}

					if ( is_date() ) {
						$year     = get_query_var( 'year' );
						$monthnum = get_query_var( 'monthnum' );
						$day      = get_query_var( 'day' );

						$_temp_args = [
							'date_query' => [
								[
									'year'  => $year,
									'month' => $monthnum,
									'day'   => $day,
								],
							],
						];
					}

					$offset_posts = get_posts( $_temp_args );
				}

				$excluded_post_ids    = array_merge( $offset_posts, $excluded_ids );
				$args['post__not_in'] = array_unique( $excluded_post_ids ); //phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
			}

			if ( $data['post_id'] ) {
				$post_ids = explode( ',', $data['post_id'] );
				$post_ids = array_map( 'trim', $post_ids );

				$args['post__in'] = $post_ids;

				if ( $excluded_ids != null && is_array( $excluded_ids ) ) {
					$args['post__in'] = array_diff( $post_ids, $excluded_ids );
				}
			}

			if ( 'slider' !== $prefix && 'show' === $data['show_pagination'] ) {
				$args['paged'] = get_query_var( "paged" ) ? absint( get_query_var( "paged" ) ) : 1;
			}

			if ( is_tag() ) {
				$args['tag'] = get_query_var( 'tag' );
			}

			if ( is_category() ) {
				$args['cat'] = get_query_var( 'cat' );
			}

			if ( is_author() ) {
				$args['author'] = get_query_var( 'author' );
			}

			if ( is_date() ) {
				$year     = get_query_var( 'year' );
				$monthnum = get_query_var( 'monthnum' );
				$day      = get_query_var( 'day' );

				$args = [
					'date_query' => [
						[
							'year'  => $year,
							'month' => $monthnum,
							'day'   => $day,
						],
					],
				];
			}

			if ( is_search() ) {
				$search    = get_query_var( 's' );
				$args['s'] = $search;
			}
		}

		return $args;
	}

}