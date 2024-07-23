<?php 
function filter_posts() {
    $post_type = $_POST['post_type'];
    $post_slug = $_POST['slug'];
    $post_taxonomy = $_POST['taxonomy'];

    // Set default arguments
    $args = [
        'post_type'      => $post_type,
        'posts_per_page' => -1,
        'orderby'        => 'publish_date',
        'post_status'    => 'publish',
        'order'          => 'DESC',
    ];


    // Add taxonomy-specific arguments
    if (!empty($post_taxonomy) && isset($post_slug)) {
        $args['tax_query'] = [
            [
                'taxonomy' => $post_taxonomy,
                'field'    => 'slug',
                'terms'    => $post_slug,
            ],
        ];
    }

     // Limit the number of posts to 9 for the reset button
    if (isset($_POST['reset']) && $_POST['reset'] === 'true') {
        $args['posts_per_page'] = 9;
    }

    // Make a new WP_Query with the $args array
    $ajaxposts = new WP_Query($args);
    $response = '';

    if ($ajaxposts->have_posts()) {
        while ($ajaxposts->have_posts()) : $ajaxposts->the_post();
            ob_start(); // Start output buffering
            
            if ($post_type == 'testimonials') {
                get_template_part('lib/parts/testimonial-card');
            } else {
                get_template_part('lib/parts/post-card');
            }

            $response .= ob_get_clean(); // Store the output buffer content
        endwhile;
    } else {
        $response = '<p class="none-found"><strong>Whoops, nothing matched your filter criteria.
                      Please try adjusting your selections and filtering again.</strong></p>';
    }

    echo $response;
    wp_reset_postdata(); // Reset post data to prevent conflicts
    exit;
}

add_action('wp_ajax_filter_posts', 'filter_posts');
add_action('wp_ajax_nopriv_filter_posts', 'filter_posts');
