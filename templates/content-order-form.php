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
        $cat_args = array(
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
            'order' => 'ASC',
            'orderby' => 'meta_value_num',
        );

    ?>
        <div class="ba-category-row" data-id="<?php echo $category->term_id; ?>" data-name="<?php echo $category->name; ?>">

            <div class="ba-category-row-heading">
                <img src="<?php echo $image; ?>" />
                <h2><?php echo $category->name; ?></h2>
                <div class="ba-category-row-icon">
                    <i class="awb-icon-minus"></i>
                    <i class="awb-icon-plus"></i>
                </div>
            </div>

            <div class="ba-category-row-content">
                <svg version="1.1" class="loader" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                    width="40px" height="40px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
                <path fill="#fdbf48" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">
                    <animateTransform attributeType="xml"
                    attributeName="transform"
                    type="rotate"
                    from="0 25 25"
                    to="360 25 25"
                    dur="0.6s"
                    repeatCount="indefinite"/>
                    </path>
                </svg>
            </div>

        </div>

    <?php endforeach; ?>

</div>

<?php else: ?>

    <p>Sorry, you must be a wholesale customer to view this form.</p>

<?php endif; ?>