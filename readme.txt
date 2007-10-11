=== eMob Email Obfuscator ===
Contributors: billifer
Donate link: 
Tags: spam, email, mail, address, addresses, hide, JavaScript
Requires at least: 2.1
Tested up to: 2.4-bleeding
Stable tag: 1.0

Authors and commenters type their email addresses in the usual format, but eMob makes it difficult for spammers to detect and harvest them.

== Description ==

This plugin sits snugly inside your WordPress installation and silently but effectively makes it very difficult for spambots to harvest email addresses from your WordPress-powered blog. This is accomplished with a combination of WordPress filter hooks and a little bit of JavaScript.

After installation, visitors to the site will see email addresses as one of two formats:

1. A fake email address with a message telling them to hover the mouse over the address to see the actual one in a human-readable format;

2. If the browser is JavaScript-enabled, all email addresses appearing on your
blog will appear to the casual visitor as normal, valid, and correct
addresses, but spambots will have difficulty reading these addresses because
execution of JavaScript is required to see the real address in this format.

The plugin is active in both the content section _and_ the comments section: if a visitor inadvertently (or intentionally) posts a valid email address in a comment, it will be protected by this plugin as well.

== Installation ==

Unzip the file into your wp-content/plugins directory. In your wp-admin screen, activate the plugin. Easy as pie!

== Planned enhancements ==

* Pick your own mumbo-jumbo format! Do you prefer `user at example dot com` or `user (at) example /dot\ com` or `u-s-e-r@NOSPAM.e+x+a+m+p+l+e~com`? With the next version, you can decide for yourself.
* Turn on/off obfuscation in the comments section.
* Make automatic address linking a customizable option.

== Acknowledgements ==

The idea for this plugin came from Allan Odgaard, the developer of the **best text editor on the planet** -- [TextMate](http://www.macromates.com/) -- who had incorporated this functionality into TextMate but expressed a desire on his blog for a WordPress plugin to accomplish this task.