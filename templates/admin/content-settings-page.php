<?php if ( isset( $_GET['success'] ) && $_GET['success'] == 'yes' ): ?>
    <div class="notice notice-success">
        <p>Categories saved.</p>
    </div>
<?php endif; ?>

<div class="ba-admin-page-wrap">
    <h1>Wholesale Order Form Settings</h1>

    <?php

    $taxonomy     = 'product_cat';
    $orderby      = 'name'; 
    $hierarchical = 1;
    $empty        = 0;

    $args = array(
            'taxonomy'     => $taxonomy,
            'orderby'      => $orderby,
            'hierarchical' => $hierarchical,
            'hide_empty'   => $empty,
            'parent'       => 0
    );
    $categories = get_categories( $args );
    
    $selected_categories_json = str_replace( '\\', '', get_option( 'ba_categories' ) );
    $selected_categories = json_decode( $selected_categories_json, true );

    if ( !is_array( $selected_categories ) ) {
        $selected_categories = array();
    }

    ?>

    <form class="ba-admin-form ba-category-order-form" method="POST" action="<?php echo admin_url( 'admin.php' ); ?>">
        <div class="ba-admin-row">

            <div class="ba-admin-col">

                <h2>Select Categories</h2>

                <div class="ba-admin-checkboxes">
                    <?php foreach($categories as $category): ?>
                        <div class="ba-admin-checkbox-wrap">
                            <input type="checkbox" id="<?php echo $category->term_id; ?>" name="<?php echo $category->term_id; ?>" 
                            value="<?php echo $category->name; ?>" <?php echo array_key_exists($category->name, $selected_categories) ? 'checked' : ''; ?>>
                            <label for="<?php echo $category->term_id; ?>"><?php echo $category->name; ?></label>
                        </div>

                        <?php

                        $args = array(
                            'taxonomy' => 'product_cat',
                            'parent' => $category->term_id,
                            'empty' => 0,
                            'hierarchical' => 1
                        );

                        $children = get_categories( $args );

                        foreach ($children as $child): ?>
                            <div class="ba-admin-checkbox-wrap child">
                                <input type="checkbox" id="<?php echo $child->term_id; ?>" name="<?php echo $child->term_id; ?>" 
                                value="<?php echo $child->name; ?>" <?php echo array_key_exists($child->name, $selected_categories) ? 'checked' : ''; ?>>
                                <label for="<?php echo $child->term_id; ?>"><?php echo $child->name; ?></label>
                            </div>

                            <?php 

                            $args = array(
                                'taxonomy' => 'product_cat',
                                'parent' => $child->term_id,
                                'empty' => 0,
                                'hierarchical' => 1
                            );

                            $grand_children = get_categories( $args );

                            foreach ($grand_children as $grand_child): ?>
                                <div class="ba-admin-checkbox-wrap grand-child">
                                    <input type="checkbox" id="<?php echo $grand_child->term_id; ?>" name="<?php echo $grand_child->term_id; ?>" 
                                    value="<?php echo $grand_child->name; ?>" <?php echo array_key_exists($grand_child->name, $selected_categories) ? 'checked' : ''; ?>>
                                    <label for="<?php echo $grand_child->term_id; ?>"><?php echo $grand_child->name; ?></label>
                                </div>
                            <?php endforeach; ?>

                        <?php endforeach; ?>

                    <?php endforeach; ?>
                </div>

            </div>

            <div class="ba-admin-col">

                <h2>Sort Categories</h2>

                <ul class="ba-admin-sortable">
                    <?php foreach ( $selected_categories as $key => $value ): ?>
                        <li data-id="<?php echo $value; ?>"><?php echo $key; ?></li>
                    <?php endforeach; ?>
                </ul>
                <p class="helper">Click and drag to sort</p>

            </div>

        </div>

        <input type="hidden" name="ordered_cats" id="orderedCats" value="<?php echo $selected_categories_json; ?>" />
        <input type="hidden" name="action" value="ba_save_settings" />
        <button type="submit">Save Categories</button>
    </form>
</div>