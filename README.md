# Instagram Media Library

This WordPress plugin allows you to save images from a public Instagram user to your WordPress library.

## Why Use It?

There are many Instagram feed plugins for WordPress available. Unfortunately, Instagram would rather users not embed their media feeds on external websites. Therefore there isn't any official open API to do so and the unoffocial APIs are very error prone. Even if an API is working today, it will likely fail in the near future. 

That's why this plugin doesn't embed external images. Instead it'll sync your media library with Instagram by downloading them when available. If the API eventually breaks, the only side-effect is an out-of-date but still working instagram feed; as opposed to other plugins which will produce broken feeds while the authors work to update code to work with the breaking API changes. 

## RapidAPI

At the moment, we require a RapidAPI key. This allows us implement a proxy to avoid getting the IP banned from Instagram.

## Developers

```
$medias = new \Motto\InstagramMediaLibrary\MediaUploads( $query_args = [] );
foreach( $medias->get() as $image ) {
    echo '<a href="'. $image->social('link') .'">';
    echo $image->html();
    echo '</a>';
} 
```

## Shortcodes
- `[social_feed posts_per_page="3"]`
    - Attributes map directly to `WP_Query` arguments.
    - Use `link="social"` or `link="attachment"` to link the image.