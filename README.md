# Plugin Name: Acoustic Artists/Bands

Plugin URI:
Description: Custom post type for Acoustic Artists/Bands with band cards.
Version: 1.0
Author: Cup O Code
License: GPL2

## Overview

The Acoustic Artists/Bands plugin is a WordPress plugin that adds a custom post type called "Acoustic" to your WordPress site. It allows you to create band cards for acoustic artists/bands with their names and website links. The plugin also provides a shortcode to display the band cards on your site.

## Installation

To install the Acoustic Artists/Bands plugin, follow these steps:

1. Download the plugin ZIP file.
2. In your WordPress admin panel, navigate to "Plugins" > "Add New".
3. Click the "Upload Plugin" button and choose the downloaded ZIP file.
4. Click "Install Now" and then activate the plugin.

## Creating Band Cards

To create band cards for acoustic artists/bands with the Acoustic Artists/Bands plugin, follow these steps:

1. In the WordPress admin panel, navigate to "Acoustic" in the left sidebar menu.
2. Click "Add New" to create a new band card.
3. Enter the artist/band name and the website URL in the provided fields.
4. Set a featured image for the band card by clicking the "Set featured image" link in the right sidebar.
5. Publish or update the band card.

## Displaying Band Cards

To display the band cards on your site, you can use the `[acoustic]` shortcode. The shortcode accepts an optional attribute `count` to limit the number of band cards displayed. For example, `[acoustic count="3"]` will display the first three band cards.

You can add the shortcode to any post, page, or text widget on your site. When the page is viewed, the band cards will be rendered according to the specified count.

## Customization

The Acoustic Artists/Bands plugin provides customization options through the use of hooks and filters. You can modify the behavior and appearance of the plugin by adding your own custom code snippets.

For example, you can modify the band card template by overriding the `acoustic_shortcode` function. You can also modify the CSS styles by adding your own styles using the `wp_head` action hook.

## Support

If you encounter any issues or have questions regarding the Acoustic Artists/Bands plugin, you can reach out to the plugin author, Cup O Code.

## License

The Acoustic Artists/Bands plugin is licensed under GPL2. You are free to modify and distribute this plugin according to the terms of the GPL2 license.
