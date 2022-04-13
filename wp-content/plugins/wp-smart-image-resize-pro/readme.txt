=== Smart Image Resize PRO ===
Contributors: nlemsieh
Tags: WooCommerce product image resize, fix image crop, resize, image, picture resize, image crop, image resize without cropping, image resize, resize thumbnails, resize images in WooCommerce
Requires at least: 4.0
Tested up to: 5.7
WC requires at least: 3.0
WC tested up to: 5.4
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
Requires PHP: 5.6
Stable tag: 1.5.4


Make WooCommerce products images the same size and uniform without cropping.

== Description ==

Make WooCommerce products images the same size and uniform without cropping.

> Zero configuration.
> No more manual image editing and photo resizing.

### Features
- Resize images to any size.
- Remove unwanted whitespace from around image.
- Set a custom background color of the emerging area.
- Compress images to reduce file size.
- Select which sizes to generate.
- Convert to JPG format
- Use WebP Image

### Usage

SIR doesn't require any configuration. Just enable it under WooCommerce > Smart Image Resize, and you're ready to start uploading your images!

[Visit the guide](http://sirplugin.com/guide.html) to learn more.


 == Frequently Asked Questions ==

= How can I regenerate thumbnails I already added to the media library? =

[Visit the guide](https://sirplugin.com/guide.html#regenerating-thumbnails)

= I get an error when I upload an image =

Make sure PHP extension `fileinfo` is enabled.

== Screenshots ==

1. Before and after using the plugin.
2. Settings page.

== Changelog ==

= 1.5.4 =

* Fix an issue with some themes not loading the correct image size.

= 1.5.3 =

* Stability improvement

= 1.5.2 =

* Fix thumbnail overwriten by WordPress when original image and thumbnail dimensions are identical
* Fix an issue with Flatsome using full size image instead of woocommerce_single for lazy load.
* Ignore sizes with 9999 dimension (unlimited height/width).
* Improve WebP availability detection.

= 1.5.1 =

* Use Imagick as default when available.
* Fix Avada not serving correct thumbnails on non-WooCommerce pages.
* Improve the user experience of the settings page. 


= 1.5.0 =

* Filter processed images in the media library toolbar
* Add filter `wp_sir_serve_webp_images`
* Improve Whitespace trimming tool  


= 1.4.10 =

* Declare compatibility with WooCommerce (v5.2)


= 1.4.9 =

* Use GD extension by default to process large images.


= 1.4.8 =

* Fixed an issue with some images in CMYK color.

= 1.4.7 =

* Fixed an issue with PNG-JPG conversion conflict
* Added support for WCFM plugin.
* Declared compatibility with WooCommerce (v5.0)
* Stability improvement


= 1.4.6.1 =

* Declared compatibility with WooCommerce (v4.9).

= 1.4.6 =

* Added tolerance level setting to trim away colors that differ slightly from pure white.
* Improved unwanted/old thumbnails clean up.

= 1.4.5 =

* Stability improvement.

= 1.4.4 =

* Improved bulk-resizing using Regenerate Thumbnails plugin.
* Stability improvement.

= 1.4.3.2 =

* Disabled WooCommerce thumbnails regeneration in the background to prevent reverting changes.

= 1.4.3.1 =

* Moved the license activation form to the plugin settings page under the "Manage License" tab.

= 1.4.3 =
* Fixed a minor issue with JPG images quality when compression is set to 0%.
* Stability improvement.

= 1.4.2.7 =
* Fixed an issue with UTF-8 encoded file names.

= 1.4.2.6 =

* Improved compatibility with WC product import tool.

= 1.4.2.5 =

* Fixed an issue when uploading non-image files occured in the previous update.

= 1.4.2.4 =

* Added abilitiy to activate multiple WP installations under the same domain.

= 1.4.2.3 =

* Turned off cache busting by default.

= 1.4.2.2 =

* Fixed WebP images not loading in some non-woocommerce pages.

= 1.4.2.1 =

* Fixed trimming issue for some image profiles (Imagick).
* Added an option to specify trimmed image border.

= 1.4.2 =

* Fixed an issue with WebP images used in Open Graph image (og:image)
* Improved resizing performances
* Stability improvement

= 1.4.1 =

* Fixed a bug with WebP not installed on server.
* Fixed an issue with front-end Media Library.

= 1.4.0 =

* Added support for category images.
* Ability to decide whether to resize an image being uploaded directly from the Media Library uploader.
* Support for WooCommerce Rest API
* Developers can use the boolean parameter `_processable_image` to upload requests to automatically process images.
* Added filter `wp_sir_maybe_upscale` to prevent small images upscale.
* Process image attachment with valid parent ID.
* Improved whitespace trimming by using Imagick.
* Fixed a tiny bug with compression only works for converted PNG-to-JPG images.
* Fixed an issue with srcset attribute caused non-adjusted images to load.
* Fixed an issue with trimmed images stretched when zoomed on the product page. 
* Improved support for bulk-import products.
* Improved processing performances with Imagick.

= 1.3.9 =

* Fix compatibility issue with Dokan vendor upload interface.
* Performances improvement.

= 1.3.8 =

 * Added compatibility with WP 5.4
 * Added support for WP Smush.
 * Added support for Dokan.
 * Stability improvement.

= 1.3.7 =

 * Stability improvement.

= 1.3.6 =

 * Fix a minor issue with image parent type detection.
 * Added a new filter `wp_sir_regeneratable_post_status` to change regeneratable product status. Default: `publish`

= 1.3.5 =

 * Regenerate thumbnails speed improvement.


= 1.3.4 =

 * Stability improvement

= 1.3.3 =

 * fixed a minor issue with settings page.

= 1.3.2 =
 * Added thumbnails regeneration steps under "Regenerate Thumbnails" tab.

= 1.3.1 =
 * Fixed a minor bug in Regenerate Thumbnails tool.

= 1.3 =
 * Added a built-in tool to regenerate thumbnails.
 * woocommerce_single size is now selected by default.
 * Stability improvement.

= 1.2.4 =
 * Fix srcset images not loaded when WebP is enabled.
 
= 1.2.3 =
 * Set GD driver as default.
 * Stability improvement.

= 1.2.2 =
 * Prevent black background when converting transparent PNG to JPG.
 * Fixed random issue that causes WebP images fail to load.
 * Disabled license notice.
 * Stability improvement.

= 1.2.1 =

* Added settings links
* Fix minor bug with WebP

= 1.2.0 =

* Added Whitespace Trimming feature.
* Various improvements. 

= 1.1.12 =

* Fixed crash when Fileinfo extension is disabled. 

= 1.1.11 =

* Added support for Jetpack. 

= 1.1.10 =

* Fixed conflict with some plugins. 

= 1.1.9 =

* Prevent dynamic resize in WooCommerce.

= 1.1.8 =

* Handle WebP not installed.

= 1.1.7 =

* Fixed mbstring polyfill conflict with WP `mb_strlen` function

= 1.1.6 =
* Added polyfill for PHP mbstring extension

= 1.1.5 =
* Force square image when height is set to auto.

= 1.1.4 =
* Fixed empty sizes list 

= 1.1.3 =
* Fixed empty sizes list 

= 1.1.2 =

* Added settings improvements
* Added processed images notice.

= 1.1.1 =

* Added fileinfo and PHP version notices
* Improved settings page experience.

= 1.1.0 =

Initial release of Smart Image Resize Pro

 == Upgrade Notice ==
 
  = 1.5.0 =

* Now you can filter processed images in the media library toolbar.

= 1.5.2 =

* This update is recommended for all users.