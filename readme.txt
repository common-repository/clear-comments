=== Clear comments ===
Contributors: brahmnan
Tags: comments
Requires at least: 3.9
Requires PHP: 5.3
Tested up to: 5.6
Stable tag: 1.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin replaces words from the stop list with asterisks ****

== Description ==

Sometimes site visitors use profanity in the comments. In order not to embarrass 
visitors with foul language, you can replace the forbidden words with asterisks.

Supported languages: english, russian

== Installation ==
* Upload `clear-comments` folder to the `/wp-content/plugins/` directory
* Activate the plugin through the 'Plugins' menu in WordPress
* Go to menu Settings / Clear comments / Options
* In the "Black list" box, enter stop words. Each word on a new line, example:
    man
    app    
* In the "White list" box, enter the exclusion words, example:
    woman
    apple
* Save settings
* Go to the "Test" tab
* In the Comment text box, enter a phrase to check the replacement of stop words, example:
    the woman append a picture of an apple to the application, and sent it her man
* Click Send
* The following data will be displayed below:
    - Found keywords - Found stop words will be highlighted in bold
    - Result text - How the processed comment will look like
    - Keywords list - List of found stop words

* All processed comments in which stop words were found will be displayed in the "Overvew" tab.

== Changelog ==

= 1.0.4 =
* Refactor russian translate

= 1.0.3 =
* Change of quotation marks in translation

= 1.0.2 =
* Add the Text Domain

= 1.0.1 =
* Loading the translate using action plugins_loaded