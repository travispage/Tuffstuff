=== SiteTree ===
Contributors: _luigi
Plugin URI: http://sitetreeplugin.com
Tags: admin, archive page, archives,  cache, caching, categories, dynamic, generator, google, google sitemap, html5, html, html sitemap, image sitemap, list, meta box, mu, multisite, pages, ping, posts, plugin, post, robots, screen options, search engines, seo, site map, sitemap, tags, user-friendly, wordpress, xml, xml sitemap
Requires at least: 3.3
Tested up to: 3.6
Stable tag: 1.5.3
License: GPL v2.0

A lightweight and user-friendly tool to enhance your WordPress site with feature-loaded Google Sitemap and Archive Page (or HTML5 Compliant Site Map).

== Description ==

SiteTree is a lightweight and user-friendly tool that lets you add to your WordPress site a human-readable Google (Image) Sitemap for search engines and/or a customisable Archive Page (or HTML5 Compliant Site Map) for your visitors in no time.

To learn more, visit [SiteTreePlugin.com](http://sitetreeplugin.com/)

= What's New in SiteTree 1.5 =
* Automatic rescheduling of failed pings.
* User Interface control to cancel a scheduled ping.
* Exclude categories and tags from the Google Sitemap.
* Exclude tags from the Archive Page.
* Option to add to the Robots.txt file created by WordPress the permalink to the Google Sitemap.
* Customise the filename of the Google Sitemap.
* Improved the web caching.
* Dashboard improved with various visual and textual feedback.

= Features Overview =
* Human readable Google Sitemap with images support, automatic ping, web caching, ...
* Customisable Archive Page (or user-friendly Site Map) with one click activation.
* Support for all the WordPress built-in content types.
* Meta-settings available in the New/Edit screen for in-depth customisation.
* Multisite compatible.
* Integrated caching and [WP Super Cache](http://wordpress.org/extend/plugins/wp-super-cache/) support.
* Both the Archive and the Sitemap are automatically refreshed only when needed.
* WordPress-like administration area with a Dashboard to easily interact with the plugin.
* Developed with an eye on performance.
* … and many other.

== Installation ==

1. Upload the folder `sitetree` to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. The plugin will guide you through the setup process.

= Uninstalling SiteTree =
The only safe way to completely uninstal the plugin is to follow the steps below. *Notice that all of your settings and saved data will be deleted permanently!*

1. Go to the 'Plugins' screen.
1. Click 'Deactivate'.
1. Once deactivated, click 'Delete'.

== Screenshots ==

1. SiteTree Dashboard
1. Ping info with a control to cancel scheduled pings.
1. Meta-settings available in the New/Edit screen of a post or page.
1. Google Sitemap with images support generated for SiteTreePlugin.com
1. A Site Map styled by the Twenty Eleven theme.
1. Another example of Site Map styled by the Twenty Eleven theme and a few additional lines of CSS.

== Translations ==

* German (Thomas Meesters)
* Italian
* Russian (Oleg Vlasov)
* Swedish (Joakim Lindskog)

More information on how to translate SiteTree in the [contribute page](http://sitetreeplugin.com/contribute/#translate) on SiteTreePlugin.com

== Changelog ==

[Version History](http://sitetreeplugin.com/version-history/)

= 1.5.3 =
* Improved compatibility with WordPress 3.6
* Fixed a conflict with the 'Custom Post Types' feature.
* From now on, the Google Sitemap is served as an `application/xml` document.

= 1.5.2 =
* New: Swedish translation courtesy of Joakim Lindskog.
* Partially updated the italian localisation.
* Enhancement: the number of posts and the number of comments shown in the Archive Page have been tagged with a `<span>`.
* Enhancement: the Google Sitemap is now rebuilt if an attachment is updated or deleted.
* Fix: only some of the posts and the pages excluded from the Sitemap were listed in the Robots.txt file created by WordPress.

= 1.5.1 =
* Fix: the list of excluded content was migrated only partially during the upgrade process.
* Fix: while upgrading from version 1.3 or later, the plugin excluded some posts and pages from both the Google Sitemap and the Archive Page even though they hadn't been flagged as *excluded content*.

= 1.5 =
* New: exclude categories and tags from the Google Sitemap.
* New: exclude tags from the Archive Page.
* New: option to add to the Robots.txt file created by WordPress the permalink to the Google Sitemap.
* New: customise the filename of the Google Sitemap.
* New: setting to limit the number of posts in the Archive Page.
* New: German translation courtesy of Thomas Meesters.
* New: added a UI control to cancel scheduled pings.
* New: added some more links in the Dashboard and some information about the plugin accessible through a modal window (requires javascript).
* Enhancement: improved web caching.
* Enhancement: added the number of images to the stats displayed in Dashboard.
* Enhancement: now, you'll get notified if the Sitemap (or the Archive) reaches its allowed (or set) limit.
* Enhancement: failed pings are automatically rescheduled.
* Enhancement: much more feedback about the state of the pings.
* Update: the settings to list the images in the Google Sitemap have been grouped into one.
* Update: the length of image titles has been limited to 70 characters, the one of image captions to 160.
* Update: renamed the "HTML5 Sitemap" as "Archive Page".
* Update: for a matter of performance, the Google Sitemap has been limited to 10000 URLs for now.
* Security fix: the setting to exclude authors from the Archive Page accepted any given value.
* Security fix: the direct access to some files caused a fatal error.
* Security fix: some subfolders in the plugin bundle were browsable on servers that allow the directory listing.
* Fix: a database error generated while uninstalling the plugin.
* Fix: if an excluded post/page was trashed or permanently deleted, it was listed anyway in the robots.txt file.
* Fix: the detection of the archive page could fail in some conditions.
* Fix: the rebuild process didn't fire if bulk posts/pages were moved to the trash.
* Fix: various problems with the functionality to limit the content in the Archive page.
* Fix: a security warning showed up on Multisite environments whenever a post/page was published or updated.
* Fix: some unescaped characters led to localisation issues.
* … and many other bug fixes and enhancements.

== Upgrade Notice ==

= 1.5.3 =
Bug fixes and improved compatibility with WordPress 3.6