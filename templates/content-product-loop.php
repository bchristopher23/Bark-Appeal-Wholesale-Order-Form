<div class="ba-product-headings">
    <p>Product</p>
    <p>Price</p>
</div>

<?php 

$products = $data->products;

foreach ( $products as $product ):

    $title = get_the_title( $product->ID );
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