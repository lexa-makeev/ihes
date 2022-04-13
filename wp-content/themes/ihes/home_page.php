<?php
	/*
	* Template name: Main
	*/

	get_header();
	?>
    <section id="act">
            <nav>
                <?php
					$prod_cat_args = array(
						'taxonomy'    => 'product_cat',
						'orderby'     => 'id', // здесь по какому полю сортировать
						'hide_empty'  => false, // скрывать категории без товаров или нет
						'parent'      => 0 // id родительской категории
					);
					$cat_ids_sec = array();
				    $woo_categories = get_categories( $prod_cat_args );
					foreach ( $woo_categories as $woo_cat ) {
						$woo_cat_id = $woo_cat->term_id; //category ID
						$woo_cat_name = $woo_cat->name; //category name
						if ($woo_cat_name != "Misc") {
                            array_push($cat_ids_sec, $woo_cat_id);
                            echo '<a href="#cat_'.$woo_cat_id.'">'.$woo_cat_name.'</a>';
						}
					}
				?>
            </nav>
            <?php
				$args = array(
					'post_type' => 'product',
					'posts_per_page' => 15,
					'tax_query' => array(
							array(
								'taxonomy' => 'product_visibility',
								'field'    => 'name',
								'terms'    => 'featured',
							),
						),
					);
				$loop = new WP_Query( $args );
				if ( $loop->have_posts() ) {
					$loop->the_post();
                    global $product;
					$image = wp_get_attachment_image(get_post_thumbnail_id($loop->post->ID), 'full' );
                    $attachment_ids = $product->get_gallery_image_ids();

                    $galery_url = "";
                    if ($attachment_ids) {
                        $galery_url = array();
                        foreach( $attachment_ids as $attachment_id ) 
                        {
                            array_push($galery_url, wp_get_attachment_url($attachment_id));
                        }
                    }
                    echo '
                    <div class="act_block_new">
                        <div class="left_act_new">
                            <img src="'.$galery_url[0].'" alt="">
                        </div>
                        <div class="center_act_new">
                            <a href="'.get_permalink( $loop->post->ID ).'">'.wp_get_attachment_image(get_post_thumbnail_id($loop->post->ID), 'main-size' ).'<p>ACTUAL</p></a>
                        </div>
                        <div class="right_act_new">
                            <img src="'.$galery_url[1].'" alt="">
                        </div>
                    </div>';
				}
				else {
                    echo '
                    <div class="act_block_new">
                        <div class="left_act_new">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/image1.png" alt="">
                        </div>
                        <div class="center_act_new">
                            <a href=""><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/image2.png" alt=""><p>ACTUAL</p></a>
                        </div>
                        <div class="right_act_new">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/image1.png" alt="">
                        </div>
                    </div>';
				}
				wp_reset_postdata();
			?>
    </section>

		<?php 
            foreach ($cat_ids_sec as $cat_id) {
                $args = array(
                    'post_type'             => 'product',
                    'post_status'           => 'publish',
                    'ignore_sticky_posts'   => 1,
                    'posts_per_page'        => '3',
                    'tax_query'             => array(
                        array(
                            'taxonomy'      => 'product_cat',
                            'field' => 'term_id', //This is optional, as it defaults to 'term_id'
                            'terms'         => $cat_id,
                            'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
                        ),
                        array(
                            'taxonomy'      => 'product_visibility',
                            'field'         => 'slug',
                            'terms'         => 'exclude-from-catalog', // Possibly 'exclude-from-search' too
                            'operator'      => 'NOT IN'
                        )
                    )
                );
                echo '<section class="cat_sec" id="cat_'.$cat_id.'">
                    <div class="cont">
                        <h1>'.get_the_category_by_ID($cat_id).'</h1>
                        <div class="products_main">';
                            $loop = new WP_Query($args);
                            while ($loop->have_posts()) { 
                                $loop->the_post();
                                global $product; ?>
                                <div class="undercard">
                                    <div class="card_main"><?php woocommerce_show_product_sale_flash( $post, $product ); ?>
                                        <a href='<?php echo get_permalink( $loop->post->ID ) ?>'>
                                            <?php
                                                if (has_post_thumbnail( $loop->post->ID )) echo wp_get_attachment_image(get_post_thumbnail_id( $loop->post->ID ),'thumbnail');
                                                else echo '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" />';
                                            ?>
                                        </a>
                                        <div class="info_prod">
                                            <div class="left_info_prod">
                                                <a href='<?php echo get_permalink( $loop->post->ID ) ?>' class='name'><?php the_title(); ?></a>
                                                <p class="price"><?php echo $product->get_price_html(); ?></p>
                                            </div>
                                            <a href='<?php echo get_permalink( $loop->post->ID ) ?>'>
                                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/bsk_main.svg" alt="">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            $link = get_term_link( $cat_id, 'product_cat' );
                echo '</div>
                        <a href="'.$link.'" class="more">More</a>
                    </div>
                </section>';
            }
            // echo do_shortcode('[wp-subscribe]');
        ?>
	<?php
	get_footer();
