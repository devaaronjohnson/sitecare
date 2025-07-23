<?php 

/**
 * Register the category block.
 */
function grillseeker_register_category_block()
{
    if ( ! function_exists( 'register_block_type' ) ) {
        return;
    }

    register_block_type(
        'grillseeker/category',
        array(
            'render_callback' => 'grillseeker_render_cetagory_block',
            'attributes'      => array(
                'id' => array(
                    'type' => 'string',
                ),
                'show' => array(
                    'type' => 'array',
                    'default' => array(),
                ),
            ),
        )
    );
}
add_action( 'init', 'grillseeker_register_category_block' );

/**
 * Render the category block.
 */
function grillseeker_render_cetagory_block( $attributes )
{
    $id = $attributes['id'];
    
    $classes = isset( $attributes['className'] ) ? $attributes['className'] : '';

    $show = $attributes['show'];

    $categories = grillseeker_categories( $show );

    // Turn on output buffering
    ob_start(); 

    ?>

    <div class="wp-block-grillseeker-category-block <?php echo $classes; ?>">       
        <div class="wp-block-grillseeker-category-block-content">
            <?php
                foreach ( $categories as $category ) :
            ?>
                <a href="<?php echo get_category_link( $category->term_id ); ?>">
                    <div class='grillseeker-category'>
                        <div class='grillseeker-category-icon'>
                            <img class="grillseeker-category-icon-image remove-lazy-loading" src="<?php echo $category->icon; ?>" decoding="sync" loading="nolazy" width="60" height="60" />
                        </div>
                        
                        <div class='grillseeker-category-name'>
                            <h3><?php echo $category->name; ?></h3>
                        </div>
                    </div>
                </a>
            <?php 
                endforeach;
            ?>
        </div>
    </div>

    <?php

    // Collect output
    $output = ob_get_contents(); 

    // Turn off ouput buffer
    ob_end_clean(); 

    return $output;
}
