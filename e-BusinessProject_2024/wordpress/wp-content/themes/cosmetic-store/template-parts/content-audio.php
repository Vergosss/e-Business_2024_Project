  <?php
  global $post;
  $post_author_id   = get_post_field( 'post_author', get_queried_object_id() );
  $cosmetic_store_get_post_column_layout = get_theme_mod( 'cosmetic_store_post_column_count', 2 );
  $cosmetic_store_post_column_layout = 'col-sm-12 col-md-6 col-lg-4';
  if ( $cosmetic_store_get_post_column_layout == 2 ) {
    $cosmetic_store_post_column_layout = 'col-lg-6 col-md-12';
  } elseif ( $cosmetic_store_get_post_column_layout == 3 ) {
    $cosmetic_store_post_column_layout = 'col-sm-12 col-md-6 col-lg-4';
  } elseif ( $cosmetic_store_get_post_column_layout == 4 ) {
    $cosmetic_store_post_column_layout = 'col-sm-12 col-md-6 col-lg-3';
  }else{
    $cosmetic_store_post_column_layout = 'col-sm-12 grid-layout';
  }
  ?>
  <?php
    $content = apply_filters( 'the_content', get_the_content() );
    $audio = false;

    // Only get audio from the content if a playlist isn't present.
    if ( false === strpos( $content, 'wp-playlist-script' ) ) {
      $audio = get_media_embedded_in_content( $content, array( 'audio' ) );
    }
  ?>
  <div class="<?php echo esc_attr($cosmetic_store_post_column_layout); ?> blog-grid-layout">
      <div id="post-<?php the_ID(); ?>" <?php post_class('post-box mb-4 p-3'); ?>>
        <?php
            if ( ! is_single() ) {
              // If not a single post, highlight the audio file.
              if ( ! empty( $audio ) ) {
                foreach ( $audio as $audio_html ) {
                  echo '<div class="entry-audio">';
                    echo $audio_html;
                  echo '</div><!-- .entry-audio -->';
                }
              };
            };
          ?>
          <?php
          $cosmetic_store_archive_element_sortable = get_theme_mod('cosmetic_store_archive_element_sortable', array('option1', 'option2', 'option3'));
            foreach ($cosmetic_store_archive_element_sortable as $key => $value) {
              if($value === 'option1') { ?>
                <div class="post-meta my-3">
                  <i class="far fa-user me-2"></i><a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php the_author(); ?></a>
                  <span class="ms-3"><i class="far fa-comments me-2"></i> <?php comments_number(esc_attr('0', 'cosmetic-store'), esc_attr('0', 'cosmetic-store'), esc_attr('%', 'cosmetic-store')); ?> <?php esc_html_e('comments', 'cosmetic-store'); ?></span>
                </div>
              <?php }
              if($value === 'option2') { ?>
                <h3 class="post-title mb-3 mt-0"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
              <?php }
              if($value === 'option3') { ?>
                <div class="post-content mb-2">
                  <?php echo wp_trim_words(get_the_content(), get_theme_mod('cosmetic_store_post_excerpt_number', 15)); ?>
              </div>
              <?php }
            }
          ?>
      </div>
  </div>