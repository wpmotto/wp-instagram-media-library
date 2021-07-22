=== Social Media Downloader ===
Contributors: mottodesignstudio
Tags: instagram, media library, feed, social, media downloader
Donate link: https://motto.ca
Requires at least: 4.8
Tested up to: 5.8
Requires PHP: 7.2
Stable tag: 1.2
License: GPLv2
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html

Download images from public social media accounts to your WordPress image library. This is a great way to embed Instagram posts on your site without it breaking in the future. 

== Description ==

There are many Instagram feed plugins for WordPress available. Unfortunately, Instagram would rather users not embed their media feeds on external websites. Therefore there isn't any official open API to do so and the unoffocial APIs are very error prone. Even if an API is working today, it will likely fail in the near future. 

That's why this plugin doesn't embed external images. Instead it'll sync your media library with Instagram by downloading them when available. If the API eventually breaks, the only side-effect is an out-of-date but still working instagram feed; as opposed to other plugins which will produce broken feeds while the authors work to update code to work with the breaking API changes. 

== Installation ==
- Install and activate the plugin
- In Settings > Media > Social Media Library, enter and save the account username you want to download from
- Use the included shortcode to output your most recent images `[igml posts_per_page="5"]`
    - Attributes map directly to `WP_Query` arguments.
    - Use `link="social"` or `link="attachment"` to link the image.
- Currently, requests from your server may occasionally get blocked and so it's best to use a proxy. [Signup here](https://rapidapi.com/restyler/api/instagram40) and enter your API key.

== Changelog ==

= 1.2 =
* Added option to run sync immediately. 
* Added url ink to the shortcode.

= 1.1 =
* Moved social network meta from post content to attachment meta. 
* Added filter in Media Library to view by network.

= 1.0 =
* First version. 