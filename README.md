# Display Posts Multisite

Extends Display Posts to display posts from other sites in a WordPress multisite network. All you do is change the shortcode itself to `display-posts-multisite` add a `blog_id` parameter to the shortcode with an integer that matches the blog id of the site you want to display posts from. That's it. Everything else works exactly the same Display Posts.

## Example

```[display-posts-multisite category="news" posts_per_page="5" blog_id="42" no_posts_message="No news is good news."]```
