=== Debug Bar Rewrite Rules ===

[[[[
* Contributors: butuzov
* Donate Link: http://wordpress.org
* Tags: permalinks, rewrite rules, tests, testing, debug, debug bar
* Requires at least: 3.4
* Tested up to: 5.7.0
* Stable tag: 0.6.5
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html


Debug Bar Rewrite Rules adds a new panel to Debug Bar that displays information about WordPress Rewrites Rules (if used).

== Description ==

Debug Bar Rewrite Rules adds information about Rewrite Rules (changed via filters) to a new panel in the Debug Bar. This plugin is an extension for [Debug Bar](http://wordpress.org/extend/plugins/debug-bar/), but it is also can work in standalone mode (as admin tools page). Note: this plugin not able to track `add_rewrite_rule` function calls, for a reason this function is untraceable.

Once installed, you will have access to the following information:

* Number of existing rewrite rules
* List of rewrite rules
* List of available filter hooks that can affect rewrite rules.
* List of filters that affects rewrite rules.
* Ability to search in rules with highlighting matches.
* Ability to test url and see what rules can be applied to it.
* Ability to flush rules directly from debug bar panel/tools page.

== Screenshots ==

1. Testing url for matches - show  matched rules and actual matches
2. Searching in rules list alongside with filtering and highlighting occurrences
3. Interface of Rewrite Rules Inspector without Debug Bar
== Changelog ==

= 0.6.5 =
* [bugfix] - php8.0 compatible call_user_func_array calls.
= 0.6 =
* [general] - way assets appear on a page changed.
* [bug] - Fixed: warning on private static var
* [bug] - Fixed: admin page    https://github.com/butuzov/Debug-Bar-Rewrite-Rules/issues/2
* [bug] - Fixed: domain field  https://github.com/butuzov/Debug-Bar-Rewrite-Rules/issues/3
* [general] - added Makefile to simplify development

= 0.5 =
* [minor changes] - Localization Changes.
* [improvement] - New Icon (for wordpress.org) and tags.
* [code refactoring] - Minor code changes.

= 0.4 =
* [improvement] - Added track for PHP `__invoke` methods (callable objects)
* [bugfix] - Added fix for plugin loaded via symlinks
* [code refactoring] - Code of PHP and JS refactored.

= 0.3 =
* [ui] UI Change - Domain input box width calculated with JS
* [bugfix] - e.preventDefault()
* [bugfix] - Double check for empty array in filters UI

= 0.2 =
* Code refactored from version 0.1

= 0.1 =
* Non Public Release
