=== SMF Group Members ===
Contributors: Michael Stock
Tags: SMF, group, display, members, avatar, SMF 2
Requires at least: 3.1.4
Tested up to: 3.2.1
Stable tag: 0.4.1

New admin screen with group dropdown! This widget displays all members from a SMF forum. It can display only the names or name + avatar image. Supports SMF connection on different servers/domains.

== Description ==
This widget displays all members from a SMF forum. Depending on your settings, it displays only the name or name + avatar image, if user didn't set an avatar in the SMF forum, it displays a generic image you can specify (see the example image below).
Support: http://www.longu.de/_smfgm_redirect.php

The plugin adds a seperate configuration menu in the WP admin panel (called "SMF Groups"). You need to add the following data:

1. Add the database connection details to your Simple Machine Forum database.
1. Enter ID of the SMF member group you want to display in the widget.
1. Modify the HTML code for the widget.
1. If you want to display member avatars (from SMF), you NEED to modify the /attachments/.htaccess file.

Each field has an example what it should include.

The field "Limit" determines how many mambers are listed in total.
Dummy image is optional. Needs to be a http:// URL. If you set it, it uses this image if a user has no image set in his SMF profile.

Important:
SMF 2.0 adds a .htaccess file at "/forum/attachments/.htaccess". This file blocks all requests from different domains. So if you want to use this widget to display a member group from a SMF forum from a different server/domain, you NEED to modify this .htaccess file (the needed steps are explained in the admin section of this widget).

Feedback needed!
This is a very early version of the plugin. If you like it, it can be improved a lot, for example:

* Links from each avatar to SMF profiles
* Links from each avatar to other profiles (like FB)
* More options for templating
* Tag option to displays one member or all members inside a blog entry/article
* Several predefined output settings
* Better caching/performance (though it should be suitable for your needs already)
* ...

== Installation ==

Upload the ZIP file in your WP admin section, add the needed data and you're good to go.

== Frequently Asked Questions ==

= Something is not working... =

You get support at http://www.longu.de/_smfgm_redirect.php .

== Screenshots ==

1. Example of the widget

== Changelog ==

= 0.4.1 =
* Improved admin screen, bug fix.

= 0.4 =
* Member group ID is now selected more easy (using a dropdown)

= 0.3.1 =
* Fixed problem with admin/user access rights for some users

= 0.3 =
* First public version, works fine
* Minimal caching
* No option validation, but if it's not set correct, you're data is NOT harmed, so all is fine.

== Upgrade Notice ==


