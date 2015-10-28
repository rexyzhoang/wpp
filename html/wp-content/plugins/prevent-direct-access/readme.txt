=== Prevent Direct Access Plugin ===
Contributors: BuildWPS, duonghung1269, gaupoit
Donate link: N/A
Tags: protect, files, photos, images, pdf, 301, 302, plugin, redirect, nofollow,  404, prevent
Requires at least: 4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tested up to: 1.0
Stable tag: 1.0

A simple way to prevent search engines and the public from indexing and accessing your files without user authentication.

== Description ==
**Current Version 1.0**


This plugin has these functionalities:

= PROTECT UPLOADED FILES =
Prevent Direct Access is designed to protect your website files such as images, pdf and video uploaded via Wordpress Media or post.


Once protected, they cannot be accessed directly on the server. An error message will appear if users attempt to read and download those files.

= COPY PRIVATE URL TO CLIPBOARD =
Once a file is checked to be protected, the plugin will generate a private URL containing random string for users to access the file. This private URL is the only way to access that protected file.
Users can copy that private URL to clipboard (to paste on their browsers or email) by clicking on the “Copy URL” button.


= FILE RESTRICTIONS =
Currently, this plugin restricts the maximum number of protected files to 3 for the free version. The premium version will offer unlimited protected files and some other features. Contact us at hello@buildwps.com for the premium version.

**PLEASE NOTE:**  This plugin is not compatible with WordPress versions less than 3.7. Requires PHP 5.2+.

= TROUBLESHOOTING: =
We will update troubleshootings later.

== Installation ==

1. Upload `prevent-direct-access` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Protect your files by going to ‘Media’, under list view


== Frequently Asked Questions ==
** SEE A LIST OF MORE UP TO DATE FAQS IN THE PLUGIN MENU ITSELF ** 

= Why nothing happens after I activate the plugin? =
First, this plugin only supports Apache server at the moment. 
Second, the plugin needs to flush some mod_rewrite rule to your website’s .htaccess file to prevent direct access to your files on the server. So in order for the plugin to work properly, you must make that .htaccess file (located on your website root folder) writable.

= Why do I see the popup box that says I can protect only 3 files? =
The free version of this plugin offers protection to 3 files only. Contact us at hello@buildwps.com for the premium version, which offer unlimited protected files and other premium features.

== Screenshots ==
1. How to activate plugin
2. Go to media to protect your files
3. Choose list view mode for the plugin’s options to show
4. How to protect your files 
5. Copy private url to clipboard
6. How to unprotect your files
7. A warning message is displayed when you try to protect more than 3 files

== Changelog ==
= TODO =
* Support nginx server
* Track private URL hits
* Allow changing private URL

== Upgrade Notice ==
N/A
