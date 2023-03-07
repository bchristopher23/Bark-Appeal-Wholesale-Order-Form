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
                
                <!-- If Starter Packs, display sub categories with products -->
                <?php if ( $category->name === 'Starter Packs' ):

                    $args = array(
                        'taxonomy'     => $taxonomy,
                        'orderby'      => 'ID',
                        'hierarchical' => $hierarchical,
                        'hide_empty'   => true,
                        'parent' => $category->term_id
                    );

                    $sub_cats = get_categories( $args );

                    foreach( $sub_cats as $cat ): ?>

                    <h3 class="ba-sub-cat-heading"><?php echo $cat->name; ?></h3>

                    <?php
                    $cat_args['product_cat'] = $cat->slug;
                    $products = get_posts( $cat_args );

                    $templates = new Custom_Template_Loader;

                    $templates
                    ->set_template_data( array('products' => $products) )
                    ->get_template_part( 'content', 'product-loop' );

                    endforeach; ?>

                <?php else:

                // Not Starter Packs, get products

                // Category products
                $products = get_posts( $cat_args );

                $templates = new Custom_Template_Loader;

                $templates
                ->set_template_data( array('products' => $products) )
                ->get_template_part( 'content', 'product-loop' );

                endif; ?>

            </div>

        </div>

    <?php endforeach; ?>

</div>

<?php else: ?>

    <p>Sorry, you must be a wholesale customer to view this form.</p>

<?php endif; ?>