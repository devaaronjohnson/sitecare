=== LH Wayback Machine ===
Contributors:      shawfactor
Donate link:       https://lhero.org/portfolio/lh-wayback-machine/
Tags:              archive, post, content, wayback, machine
Requires at least: 4.5
Tested up to:      5.5
Stable tag:        trunk
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Automatically creates Wayback Machine snapshots of site, including archives

== Description ==

LH Wayback Machine integrates your website with the [Internet Archive](https://archive.org/web/) to create easy-to-view snapshots of your site over time, giving you a fully navigable visual history of the changes you've made.

The plugin gives you some handy tools to easily trigger and view snapshots:

* Automatically creates a Wayback Machine snapshot when you update your content.
* Automatically creates a Wayback Machine snapshot of archived content.


LH Wayback Machines automated functionality works for the following content types:

* Posts
* Pages
* Custom Post Types
* Categories
* Tags
* Custom Taxonomies


This means that whenever you edit/save one of these content types, a snapshot of the corresponding front-end page will be archived via the Wayback Machine. As you update your content, the Wayback Machine will automatically keep a visual history of your changes. 

**Like this plugin? Please consider [leaving a 5-star review](https://wordpress.org/support/view/plugin-reviews/lh-wayback-machine/).**

**Love this plugin or want to help the LocalHero Project? Please consider [making a donation](https://lhero.org/portfolio/lh-wayback-machine/).**


== Frequently Asked Questions ==

= Will this handle old content? =

Yes it will, in fact this is the main advantage of this plugin. This plugin will detect old content that has not been archived and create a queue to send it to the wayback machine

= Why did you write this plugin? =
Because I liked Mickey Kay's Archiver plugin but I wanted something that did not need configuration and runs in the background.

= How does this plugin work? =
When the plugin is activated a queue of old content is created and this is sent off the wayback machine periodically via cron. Any change to your content is also detected, so new or modified content is also queued to be sent to the archive.

= How can I see if this plugin is working? =
The plugin creates a column in the posts, pages, and taxonomies screens with the date the last time the wayback machine was pinged. By clicking on this date you can view the most recently archived content.

= What is something does not work?  =

LH Wayback Machine, and all [https://lhero.org](LocalHero) plugins are made to WordPress standards. Therefore they should work with all well coded plugins and themes. However not all plugins and themes are well coded (and this includes many popular ones). 

If something does not work properly, firstly deactivate ALL other plugins and switch to one of the themes that come with core, e.g. twentyfirteen, twentysixteen etc.

If the problem persists pleasse leave a post in the support forum: [https://wordpress.org/support/plugin/lh-wayback-machine/](https://wordpress.org/support/plugin/lh-wayback-machine/) . I look there regularly and resolve most queries.

= What if I need a feature that is not in the plugin?  =

Please contact me for custom work and enhancements here: [https://shawfactor.com/contact/](https://shawfactor.com/contact/)



== Installation ==

1. Upload the entire `lh-wayback-machine` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. That is it (there is no need for configuration)


== Changelog ==

= 1.00 March 25, 2017 =
* Initial release

= 1.01 July 29, 2017 =
* Added class check

= 1.02 July 29, 2019 =
* Added taxonomy support and also a column to track performance

= 1.03 October 10, 2020 =
* Hide column by default