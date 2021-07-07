=== Social Media Library ===
Contributors: mottodesignstudio
Tags: instagram, media library, feed, social
Donate link: https://motto.ca
Requires at least: 4.8
Tested up to: 5.7
Requires PHP: 7.2
Stable tag: 1.0.1
License: GPLv2
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html

This plugin allows you to save images from a public Instagram account to your WordPress library.

== Description ==
There are many Instagram feed plugins for WordPress available. Unfortunately, Instagram would rather users not embed their media feeds on external websites. Therefore there isn\'t any official open API to do so and the unoffocial APIs are very error prone. Even if an API is working today, it will likely fail in the near future. 

That\'s why this plugin doesn\'t embed external images. Instead it\'ll sync your media library with Instagram by downloading them when available. If the API eventually breaks, the only side-effect is an out-of-date but still working instagram feed; as opposed to other plugins which will produce broken feeds while the authors work to update code to work with the breaking API changes. 

== Installation ==
- Install and activate the plugin
- In Settings > Media > Social Media Library, enter and save the account username you want to download from
- Use the included shortcode to output your most recent images `[igml posts_per_page=\"5\"]`

== Changelog ==

= 1.0 =
* First version. 