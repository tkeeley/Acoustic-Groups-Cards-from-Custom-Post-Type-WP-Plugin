<?php
/*
Plugin Name: Acoustic Artists/Bands
Description: Custom post type for Acoustic Artists/Bands with band cards.
Version: 1.0
Author: Cup O Code
License: GPL2
*/


// Register the Our Bands custom post type
function acoustic_register_post_type() {
    $labels = array(
        'name' => 'Acoustic',
        'singular_name' => 'Acoustic',
        // ... add more labels if needed
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'supports' => array('title', 'thumbnail'),
        'menu_icon' => 'dashicons-format-audio', // Set the menu icon to a music note
        'has_archive' => true,
        'rewrite' => array('slug' => 'acoustic'),
        'hierarchical' => false,
        'show_in_rest' => true,
        'orderby' => 'title', // Order posts alphabetically by title
        'order' => 'ASC'
    );

    register_post_type('acoustic', $args);
}
add_action('init', 'acoustic_register_post_type');

function acoustic_modify_post_type_loop($query) {
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('acoustic')) {
        $query->set('orderby', 'meta_value title');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
            'relation' => 'OR',
            array(
                'key' => 'is_last_post',
                'value' => '1',
                'compare' => 'NOT EXISTS',
            ),
            array(
                'key' => 'is_last_post',
                'value' => '1',
                'compare' => '!=',
            ),
        ));
    }
}
add_action('pre_get_posts', 'acoustic_modify_post_type_loop');

function acoustic_display_image_crop_notice() {
    global $pagenow, $post_type;

    // Check if we are on the 'post-new.php' screen for the 'acoustic' post type
    if ($pagenow === 'post-new.php' && $post_type === 'acoustic') {
        echo '<div class="notice notice-info" style="font-weight: bold; font-size: 16px;"><p>***Please make sure all images are cropped square for the best fit***</p></div>';
    }
}
add_action('admin_notices', 'acoustic_display_image_crop_notice');


// Add custom meta box for band details
function acoustic_add_meta_box() {
    add_meta_box(
        'acoustic-details',
        'Acoustic Details',
        'acoustic_meta_box_callback',
        'acoustic',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'acoustic_add_meta_box');

//Add custom box to always show last CTA card
function acoustic_add_custom_meta_box() {
    add_meta_box(
        'last_post_meta_box',
        'Last Post',
        'acoustic_render_last_post_meta_box',
        'acoustic',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'acoustic_add_custom_meta_box');

function acoustic_render_last_post_meta_box($post) {
    $is_last_post = get_post_meta($post->ID, 'is_last_post', true);
    ?>
    <label for="is_last_post">
        <input type="checkbox" id="is_last_post" name="is_last_post" value="1" <?php checked($is_last_post,'1'); ?>>
        Display this post last
    </label>
    <?php
}

function acoustic_save_last_post($post_id) {
    if (array_key_exists('is_last_post', $_POST)) {
        update_post_meta($post_id, 'is_last_post', $_POST['is_last_post']);
    } else {
        delete_post_meta($post_id, 'is_last_post');
    }
}
add_action('save_post', 'acoustic_save_last_post');



// Callback function for the band details meta box
function acoustic_meta_box_callback($post) {
    wp_nonce_field(basename(__FILE__), 'acoustic_nonce');
    $acoustic_website = get_post_meta($post->ID, 'acoustic_website', true);
    ?>
    <p>
        <label for="band_website">Band Website:</label>
        <input type="text" name="acoustic_website" id="acoustic_website" value="<?php echo esc_attr($acoustic_website); ?>">
    </p>

    <?php
}

// Save band details meta box data
function acoustic_save_meta_box_data($post_id) {
    if (!isset($_POST['acoustic_nonce']) || !wp_verify_nonce($_POST['acoustic_nonce'], basename(__FILE__))) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST['post_type']) && 'acoustic' === $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    if (isset($_POST['acoustic_website'])) {
        update_post_meta($post_id, 'acoustic_website', sanitize_text_field($_POST['acoustic_website']));
    }
}
add_action('save_post', 'acoustic_save_meta_box_data');

// Shortcode to display band cards
function acoustic_shortcode($atts) {
    $atts = shortcode_atts(array(
        'count' => -1,
    ), $atts, 'acoustic');

    $args = array(
        'post_type' => 'acoustic',
        'posts_per_page' => $atts['count'],
        'orderby' => 'title',
        'order' => 'ASC',
    );

    $acoustic = new WP_Query($args);

    ob_start();
    if ($acoustic->have_posts()) {
        ?>
        <div class="acoustic-wrapper">
            <?php while ($acoustic->have_posts()) : $acoustic->the_post(); ?>
                <div class="acoustic-card">
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php echo esc_url(get_post_meta(get_the_ID(), 'acoustic_website', true)); ?>" target="_blank">
                            <?php the_post_thumbnail(); ?>
                        </a>
                    <?php endif; ?>
                    <p class="acoustic-name"><?php the_title(); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
        <?php
    } else {
        echo 'No bands found.';
    }

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('acoustic', 'acoustic_shortcode');


// Remove main WordPress editor box
function acoustic_remove_editor() {
    remove_post_type_support('acoustic', 'editor');
}
add_action('init', 'acoustic_remove_editor');

// Add CSS
function acoustic_add_styles() {
    ?>
    <style>
	.acoustic-wrapper {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    padding: 0 1em;
    box-sizing: border-box;
}

.acoustic-card {
    /*border: 1px solid #ddd;*/
    padding-bottom: 10px;
    /*margin: 0.5em;*/
    /*border-radius: 10px;*/
    margin-bottom: 2.5em;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
    background-color: #231155;
    box-sizing: border-box;
    line-height: inherit;
    text-align: left;
    max-width: 400px; /* Set a maximum width for the cards */
     /* Flexbox properties for responsiveness */
    flex-grow: 0;
    flex-shrink: 1;
}

.acoustic-card:hover {
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    transform: translateY(-5px);
}

.acoustic-card > * {
    margin: 0;
}

.acoustic-card img {
    width: 95%;
    max-width: 100%;
    height: auto;
    display: block;
    margin-bottom: 0.5em;
    object-fit: cover;
    top: -20px;
    position: relative;
    left: 20px;
    /*padding: 5px;*/
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.75);
}

.acoustic-name {
    font-weight: 700;
    text-align: center;
    font-family: 'Avenir Next', Arial, sans-serif;
    color: #fff;
}

    </style>
    <?php
}
add_action('wp_head', 'acoustic_add_styles');
