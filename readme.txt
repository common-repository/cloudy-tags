=== Cloudy Tags ===
Contributors: someblogsite
Donate link: http://www.someblogsite.com/donate.php
Tags: tags, tag cloud, widget, shortcode
Requires at least: 2.8
Tested up to: 2.8
Stable tag: trunk

Displays a tag cloud with a cloudy look.

== Description ==

There are a variety of tag cloud plugins, many of them just tweaks of the basic WordPress tag cloud code.  This plugin is no different.

Well, it is different in that it provides one new way to display the tags: cloudily.

The usual format is for tags with a smaller post count to have a smaller font size and larger counts a larger font size.  Cloudy Tags still allows that, but it adds the property of cloudiness.  So tags with smaller post counts are cloudier and larger counts are clearer or sharper.

The plugin page is at [Some Blog Site](http://www.someblogsite.com/web-stuff/cloudy-tags/).

== Installation ==
1. Upload `cloudy-tags` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Use the Appearance -> Widgets menu to select and configure the Cloudy Tags sidebar widget
4. Use the Settings -> Cloudy Tags menu to configure the Cloudy Tags non-widget tag cloud.
4. Add `[cloudytags]` to your posts or pages.
5. Add 'non_widget_cloudy_tags();' to your theme code somewhere.

== Changelog ==
General information about this plugin at [Some Blog Site](http://www.someblogsite.com/web-stuff/cloudy-tags/).

= 1.0 =
* Initial release of widget and non-widget (shortcode).


== Frequently Asked Questions ==

= Where are the clouds?  My tags look just like normal text. =

The default settings should produce some cloudy text.  I use the term "cloudy", but you may perceive it as blurry or murky.

If the text has no blur, then the problem might be your web browser.  The cloud is really just a text shadow.  Older browsers do not support the CSS property 'text-shadow'.  This is supported by recent versions of the major browsers.  If you are using an older version of Firefox or Opera or Safari, you should upgrade.  If you are using Internet Explorer, you should switch to something else.

Also, it could be the colors you are using.  It is hard to see light shadows on a dark background.  Personal preferences may vary, but it is generally better to have a lighter background and a darker shadow color.

= I changed some of the options but the tag cloud doesn't look any different.  What's going on? =

There are two sets of options because there are two types of Cloudy Tags clouds: widget and non-widget.

If you changed the options under the Appearances - Widgets menu, then only the tag cloud in the widget sidebar will be affected.  I

If you changed the options under the Settings - Couldy Tags menu, then only the tag clouds in pages and posts (using shortcode) will be affected.

You can use one type or the other or both.

= What's a shortcode? =

That's a nice way to call plugins from within WordPress.  You don't need to modify any PHP source code; you just type one simple phrase (`[cloudytags]` in this case) in your page or post and the tag cloud will magically appear there once it is published.

= I don't like the cloudy look.  I just want a normal tag cloud but with different colors. =

That's not a question.

But if that is how you feel, then you should look at some other plugins.  There are plenty out there; I recommend [Configurable Tag Cloud](http://reciprocity.be/ctc/).

= I don't like that the links get underlining when I hover the mouse over the link.  Where is the option to change that? =

It's hard-coded in the PHP files.  Look for the functions get_head_widget() and get_head_non_widget().  I could have put it in the options somehow but it was not a high priority.  Maybe the next revision of Cloudy Tags will have that CSS in a better spot.


== Screenshots ==
1. Sample Output
2. Widget Configuration Form
3. Non-widget (shortcode) Configuration Form
