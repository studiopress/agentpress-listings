=== Plugin Name ===
Contributors: nathanrice, wpmuguru, studiopress, nick_thegeek, marksabbath
Tags: real estate, agentpress, genesis, genesiswp
Requires at least: 4.0.0
Tested up to: 6.1
Stable tag: 1.3.4

This plugin adds a Listings custom post type for Real Estate agents.

== Description ==

The AgentPress Listings plugin uses custom post types, custom taxonomies, and widgets to create a listings management system.

You can use the taxonomy creation tool to create your own way of classifying listings, and use those taxonomies to allow users to search for listings.

== Installation ==

1. Upload the entire `agentpress-listings` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Begin creating listings and listing taxonomies.

== Frequently Asked Questions ==

= Is this plugin still in beta? =
No, but that doesn't necessarily mean it's perfect. Please report any bugs to us, and be sure to update whenever a new version is available.

= Is there a way to add more HTML tags accepted for the map and video textarea? =
Yes, you can use the filter agentpress_featured_listings_allowed_html.



== Changelog ==

= 1.3.4 =
* Ensure edited property details save in WordPress 5.3+.

= 1.3.3 =
* Prevent `_listing_price_sortable` meta being removed when creating new listings or saving existing ones. If listings disappeared from your archives and search results after editing them, try updating the plugin and then resaving them.

= 1.3.2 =
* Fix a bug where map and video textarea were stripping HTML tags from the content.

= 1.3.1 =
* Fix bug where taxonomies were not saving.

= 1.3.0 =
* Reorganize plugin.
* Enforce WordPress code standards.
* Introduce Circle CI.
* Fix bug where field wasn't saving.
* Fix bug where field wasn't showing.

= 1.2.7 =
* WordPress compatibility.

= 1.2.6 =
* Add plugin header i18n

= 1.2.5 =
* Fix typo

= 1.2.4 =
* PHP7 compatibility

= 1.2.3 =
* Fixed issue where existing taxonomies could not be edited
* Fixed typo in the language .pot filename

= 1.2.2 =
* Fixed issue with incorrect usage of `array_pop()`

= 1.2.1 =
* Fixed bug with listing search results using wrong template
* Fixed issue with breadcrumbs on listing search results page

= 1.2.0 =
* Update textdomain
* Prevent invalid taxonomy slugs
* Fix PHP notices and warnings
* Fix link to edit taxonomy
* Conditionally output markup in listing widget
* Use wp_get_theme() instead of get_theme_data()

= 1.1.0 =
* Fix spacing issues
* Update Listings menu icon

= 1.0.0 =
* Localized the property details labels
* Fixes typo
* Change the property details filter name
* Enable comments on listings post type
* Fix admin redirect bug
* Tag for public release

= 0.9.1 =
* Flush rewrite rules when plugin is activated, or taxonomies are created.
* Remove hard line break between dropdowns in the property search widget.
* Add button text as a widget option in the property search widget.
* Remove a rogue `</div>`.
* Move the comma to the proper place in the address output in Featured Listings.
* Hook the init function to `after_setup_theme` so filters in the child theme will work.
* Short-circuit the plugin if a Genesis child theme isn't active.
* Make the property details (label and custom field key) filterable.
* Make the loop output filterable in Featured Listings.

= 0.9.0 =
* Public beta release

= 0.1.0 =
* Initial beta release
