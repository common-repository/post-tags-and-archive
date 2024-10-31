=== Post Tags and Archives ===
Contributors: oxpal, Thomas Schmall
Donate link: http://www.oxpal.com/main.php?o=dev_wordpress_pta
Plugin URI: http://www.oxpal.com/main.php?o=dev_wordpress_pta
Tags: tag cloud, tags, cloud, archive, archives, seo, post, posts, page, pages, wordpress, list posts, shortcode
Requires at least: 2.3
Tested up to: 5.6
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display the Tag Cloud or the Archive directly in posts and pages, and change their look in the options.

== Description ==
Tag clouds and Archives are useful for visitors, but also for search engines to find and index what topics your blog relates to. "Post Tags and Archives" makes it easy to add Tag Clouds or Archives to posts and pages via shortcodes.
Before, you could only put it in the sidebar or widgets. That could hurt the search ranking for relevant terms and crowds the sidebar. So one either had to truncate the cloud and archive - or get rid of them (or use some more complicated trickery).
Now you can just drop them in your posts and change the look easily via the options.

== Installation == 
1. Download the zip file and extract the contents.
2. Upload the 'post-tags-and-archives' folder to your plugins directory (wp-content/plugins/).
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. See 'Settings -> Post Tags & Archives'
5. To display the Tag Cloud, add `[POSTTAGS]` in the post or page content. 
6. For the archive use `[POSTARCHIVES]`  (Notice Plural in both cases!)
7. Enjoy!

(For the mad tweakers, you can also add the php code `<?php pta_posttags(); ?>` or  `<?php pta_postarchives(); ?>` to your templates to show the cloud/archive. I guess it's basically the same as the default WordPress feature, just with easier options)

== Frequently Asked Questions ==

= How can I style the Cloud/Archive? =
The cloud is enclosed in `<div class="pta-posttags">`, and the archive in `<div class="pta-postarchives">`,so that you can use CSS for changing the look.
For example  `text-transform: lowercase;` will give it a calmer look.


= Will it help with SEO and the google bot? =
Having the archive or cloud fully in your sidebar hurts your search ranking, since you have lots of links on your page. Less (but more relevant) links is better. Putting the archives and cloud on a separate page or post is better, as it allows bots to crawl - and interested readers to see all your content in a nice format.
Don't add it repeatedly and redundantly though. Make sure it's useful for your visitors, then it's typically also welcome by google.

= Is there widget support? =
Not at the moment. Since there is the Wordpress internal TagCloud widget. If it's requested often, I'll add it.

== Options ==
Should be self-explanatory.

The advanced features: Please be careful with the HTML parts. 
The archive-format setting "options for dropdown-menu" is for people who want to setup forms. You have to add a proper form yourself - with select/post and all. Then this feature will add all archives (in the type you chose) as <option>


== Screenshots ==
1. This shows the tag cloud in action
2. Shows two versions of the archive in action
3. Shows how to add the shortcode in posts/pages
4. Shows the options page
5. Command for PHP integration

== Changelog ==

= 1.1.1 =
* (Mar 2013) has_cap - fixed for current wordpress version

= 1.1 = 
* (Feb 2013) added php call feature, lots of tweaking

= 1.0 = 
* (2012) added archive feature

= 0.1 = 
* (2011) first tag cloud

== TODO == 
* add products tag support
Probably like this:
 $args = array(
        'number' => 15,
        'taxonomy' => 'product_tag'
    );
    return $args;

== Other notes ==
Theoretically functional: from WP 1.5 (most features from: 2.5. "separator"-option from 2.9, "Tags-Order" for tags feature only from 3.5)

