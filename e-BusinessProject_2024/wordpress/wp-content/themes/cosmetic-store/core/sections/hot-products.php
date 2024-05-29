<?php if ( get_theme_mod('cosmetic_store_hot_products_section_enable', false) == true ) : ?>
<div id="hot_products" class="py-5">
	<div class="container text-center">
    <?php if ( get_theme_mod('cosmetic_store_hot_products_main_heading') ) : ?>
		  <h3 class="mt-3"><?php echo esc_html(get_theme_mod('cosmetic_store_hot_products_main_heading'));?></h3>
    <?php endif; ?>
    <div class="row">
      <?php
      $cosmetic_store_product_data = get_theme_mod('cosmetic_store_hot_products_category');
      if ( class_exists( 'WooCommerce' ) ) {
        $cosmetic_store_args = array(
          'post_type' => 'product',
          'posts_per_page' => get_theme_mod('cosmetic_store_hot_products_per_page'),
          'product_cat' => $cosmetic_store_product_data,
          'order' => 'ASC'
        );
        $loop = new WP_Query( $cosmetic_store_args );
        while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>
        	<div class="col-xl-3 col-lg-4 col-md-6">
            <div class="tab-product">
              <div class="product-image my-4">
                <figure>
                  <?php if (has_post_thumbnail( $loop->post->ID )) echo get_the_post_thumbnail($loop->post->ID, 'shop_catalog'); else echo '<img src="'.esc_url(wc_placeholder_img_src()).'" />'; ?>
                </figure>
                <div class="cart-button">
                  <span class="icon" href="<?php if(function_exists('wc_get_cart_url')){ echo esc_url(wc_get_cart_url()); } ?>"><span class="button1"><?php if( $product->is_type( 'simple' ) ) { woocommerce_template_loop_add_to_cart(  $loop->post, $product );} ?></span></span>
                </div>
              </div>
              <div class="product-details text-center align-self-center">
              	<span class="align-self-center">
            	    <h4 class="product-text mt-2 "><a href="<?php echo esc_url(get_permalink( $loop->post->ID )); ?>"><?php the_title(); ?></a></h4>
                  <h5 class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>"><?php echo $product->get_price_html(); ?></h5>
                </span>
              </div>
            </div>
          </div>
        <?php endwhile; wp_reset_query(); ?>
      <?php } ?>
	  </div>
	</div>
</div>
<?php endif; ?>
