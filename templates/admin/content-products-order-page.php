<?php if ( isset( $_GET['success'] ) && $_GET['success'] == 'yes' ): ?>
    <div class="notice notice-success">
        <p>Product order saved.</p>
    </div>
<?php endif; ?>

<h1>Products Order</h1>


<form class="ba-admin-form ba-product-order-form" method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">

<?php

$args = array(
    'show_option_all' => 'Select Category',
    'taxonomy' => 'product_cat',
    'hierarchical ' => 1
);

wp_dropdown_categories( $args );

?>

    <div class="ba-admin-sortable-wrap">

        <svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
            width="25px" height="25px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
        <path fill="#2271b1" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">
            <animateTransform attributeType="xml"
            attributeName="transform"
            type="rotate"
            from="0 25 25"
            to="360 25 25"
            dur="0.6s"
            repeatCount="indefinite"/>
            </path>
        </svg>

        <ul class="ba-admin-sortable products">

        </ul>
    </div>

    <div class="ba-admin-error">
        Error fetching products.
    </div>

    <input type="hidden" id="productIDs" name="product_ids" value="">
    <input type="hidden" name="action" value="ba_save_product_order" />
    <button type="submit">Save Product Order</button>

</form>