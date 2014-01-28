=== Plugin Name ===
Contributors: nathanrice, wpmuguru, studiopress
Tags: real estate, agentpress, genesis, genesiswp
Requires at least: 3.2
Tested up to: 3.2.1
Stable tag: 0.9.1

This plugin plugin creates a listings management system for AgentPress child themes, from StudioPress.

== Description ==

The AgentPress Listings plugin uses custom post types, custom taxonomies, and widgets to create a listings management system for AgentPress child themes.

You can use the taxonomy creation tool to create your own way of categorizing listings, and use those taxonomies to allow users to search for listings.

== Installation ==

1. Upload the entire `agentpress-listings` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Begin creating listings and listing taxonomies.

== Frequently Asked Questions ==

= Is this plugin still in beta? =
Yes. The AgentPress Listings plugin is still under active development. Expect periodic updates as we approach a 1.0 release.


== Changelog ==

= 0.1.0 =
* Initial beta release

= 0.9.0 =
* Public beta release

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