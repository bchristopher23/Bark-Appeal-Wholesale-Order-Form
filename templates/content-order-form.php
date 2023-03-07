<h1>Wholesale Order Form</h1>

<?php
$is_wholesale = false;

if( is_user_logged_in() ) {

    $user = wp_get_current_user();

    $roles = ( array ) $user->roles;
    $allowed_roles = array( 'wholesale_customer', 'administrator' );

    if ( !empty( array_intersect( $roles, $allowed_roles ) ) ) {
        $is_wholesale = true;
    }
    
}

if ($is_wholesale):
?>

<div class="ba-categories-container">
<?php



    $selected_categories = str_replace('\\', '', get_option( 'ba_categories' ) );
    $selected_categories_array = json_decode( $selected_categories, true );
    $cat_ids = array();

    foreach( $selected_categories_array as $key => $value ) {
        array_push( $cat_ids, $value );
    }

    $taxonomy     = 'product_cat';
    $orderby      = 'name';  
    $hierarchical = 0;
    $empty        = 0;

    $args = array(
        'taxonomy'     => $taxonomy,
        'orderby'      => $orderby,
        'hierarchical' => $hierarchical,
        'hide_empty'   => $empty,
        'include'      => $cat_ids
    );
    $categories = get_categories( $args );


    usort($categories, function ($a, $b) use ($cat_ids) {
        return ((int) array_search($a->term_id, $cat_ids)) - ((int) array_search($b->term_id , $cat_ids));
    });

    foreach ($categories as $category): 
    
        // Category image
        $thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true ); 
        $image = wp_get_attachment_url( $thumbnail_id ); 

        // Category products
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'product_cat'    => $category->slug,
            'meta_query' => array(
                'relation' => 'OR',
                array(
                   'key'=>'ba_order',
                   'compare' => 'EXISTS'         
                ),
                array(
                    'key'=>'ba_order',
                    'compare' => 'NOT EXISTS'         
                )
            ),
            'order'          => 'ASC',
            'orderby'        => 'meta_value_num',
        );
        $products = get_posts( $args );

    ?>
        <div class="ba-category-row">

            <div class="ba-category-row-heading">
                <img src="<?php echo $image; ?>" />
                <h2><?php echo $category->name; ?></h2>
                <div class="ba-category-row-icon">
                    <i class="awb-icon-minus"></i>
                    <i class="awb-icon-plus"></i>
                </div>
            </div>

            <div class="ba-category-row-content">
                
                <div class="ba-product-headings">
                    <p>Product</p>
                    <p>Price</p>
                </div>

                <?php foreach ( $products as $product ):

                    $title = get_the_title( $product );
                    $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->ID ) );
                    $_product = wc_get_product( $product->ID );
                    $prices = array();

                    if( !$_product->is_type('variable') ){
                        $price = '$' . get_post_meta( $product->ID, 'wholesale_customer_wholesale_price', true );
                    }
                    else {

                        // Get size variations
                        $variations = $_product->get_available_variations();
                        $variation_data = array();


                        foreach($variations as $variation) {
                            $size = isset( $variation['attributes']['attribute_pa_size'] ) ? $variation['attributes']['attribute_pa_size'] : $variation['attributes']['attribute_pa_sizes'];
                            $var_price = get_post_meta( $variation['variation_id'], 'wholesale_customer_wholesale_price', true );

                            // Variation data for modal
                            $data = array(
                                'size' => $size,
                                'price' => floatval( $var_price ),
                                'id' => $variation['variation_id']
                            );

                            array_push($variation_data, $data);

                            // Get min and max price for display
                            array_push( $prices, floatval( $var_price ) );

                        }

                        // Price range string
                        $min = min($prices);
                        $max = max($prices);

                        $price = '$' . $min . ' - $' . $max;

                    }

                ?>
                    <div class="ba-product-row<?php echo $_product->is_type('variable') ? ' variable-product' : ''; ?>"
                    data-id="<?php echo $product->ID; ?>" <?php echo $_product->is_type('variable') ? 'data-variations="' . htmlspecialchars(json_encode($variation_data), ENT_QUOTES, 'UTF-8') . '"' : ''; ?>>
                        <h3 class="product-title">
                            <img src="<?php echo $image[0]; ?>" />
                            <?php echo $title; ?>
                        </h3>
                        <p class="product-price"><?php echo $price; ?></p>
                        <button class="select-sizes">Select <?php echo $_product->is_type('variable') ? 'Sizes' : 'Quantity'; ?></button>
                    </div>


                <?php endforeach; ?>
            </div>

        </div>

    <?php endforeach; ?>

</div>

<?php else: ?>

    <p>Sorry, you must be a wholesale customer to view this form.</p>

<?php endif; ?>