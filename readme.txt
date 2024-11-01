=== Universal WP Lead Tracking ===
Contributors: inboundhorizons
Tags: lead, tracking, email, contact, form
Requires at least: 3.3
Requires PHP: 5.4
License: MIT
Tested up to: 6.5
Stable tag: 1.0.5

Adds lead tracking information to e-mails coming from Contact Form 7, Gravity Forms, Ninja Forms, or Elementor PRO form submissions. It also allows you to send form submission events to Google Analytics (Universal and GA4) in order to track form submissions there. Simply add the `[tracking-info]` shortcode at the bottom of your form emails to get started. 

== Description ==

Universal WP Lead Tracking gives you important information about users from contact form submissions on your Wordpress site and allows you to instantly set up Google Analytics events to send conversion data when someone submits a form on your website.

When you receive an e-mail from a contact form on your website, you will instantly see data on a user's landing page on your site, the page that they filled out the contact form on, the referring source that sent them to your website, their location IP (country-level) and browser/platform used. 

This information is added by simply putting the `[tracking-info]` shortcode in the email being sent to you when a user submits a form on your website. Just paste the `[tracking-info]` shortcode in your e-mail template, and every time you receive a lead from your website, you will be able to see information from the user that submitted the form. 

= The Specific Information This Plugin Provides =

The tracking info specifically includes the Form Page URL, Original Referrer, Landing Page, User IP, and Browser. What do these specific items mean?

* Form Page URL: This is the page on your website where the user filled out your form.
* Original Referrer: This is where the user came to your website from such as Google or Facebook.
* Landing Page: This is the page on your website that the user first came to from an outside source.
* User IP: This is the IP address of the user that sent a form submission and includes a country location. 
* Browser: This tells you what browser a user was on when they sent a form submission.

= This Tracking Code Works With Most Major Form Plugins =

This shortcode tracking method will work with forms on Contact Form 7, Gravity Forms, Ninja Forms, or Elementor PRO. Because it is shortcode based, you can just drop in the code to the e-mail submission template on any of these major form plugins. 

If you are sending your website e-mail submissions via an HTML based output on one of these plugins, we have that covered too. Just add html="true" to your shortcode like so: `[tracking-info html="true"]`

= This Plugin Sends Form Events To Google Analytics =

As an added feature, you can track any form submission on your website as a Google Analytics event if you have Google Analytics set up on your website. 

This happens automatically when you check the box for the Analytics event you want on the plugin settings section. We currently support ga{} events for Universal Analytics and gtag{} events for Google Analytics 4. 

This will show up in your Google Analytics view as an action called "submit" with an event category called "Contact Form" that you can then set as a lead source in your conversion preferences in Google Analytics

For more plugins, help, or information about this plugin please visit [www.inboundhorizons.com](https://www.inboundhorizons.com/product/wp-universal-lead-tracking-free-wordpress-plugin/ "Inbound Horizons - Universal WP Lead Tracking").

== Screenshots ==

1. This is the view of the plugin admin panel options you will see in Wordpress
2. This is an example of where you will enter the tracking shortcode on a web form. This is an example from a Contact Form 7 mail form sent to the Wordpress site administrator after a user submits a form on the site.
3. This is an example of the tracking information you will receive at the bottom of a response email when a user submits a form on your site.

== Changelog ==

= 1.0.5 - 2024-04-03 =
* UPDATE: Confirmed compatibility with WordPress v6.5.
* UPDATE: Updated IP 2 Location database to latest release of April 2024.

= 1.0.4 - 2024-04-01 =
* UPDATE: Bumped version.

= 1.0.3.4 - 2024-03-27 =
* FIX: Fixed small bugs with "if" checks.
* UPDATE: Updated IP2Location database.

= 1.0.3.2 - 2023-09-29 =
* FIX: Fixed small bug causing error in background.
* UPDATE: Updated IP2Location database.

= 1.0.3.1 - 2023-02-01 =
* TWEAK: Cleaned up some code and clarified wording on settings page.
* ADD: Added IP lookup functionality to find country based on IP address. IPv6 requires the GNU Multiple Precision (GMP) extension to be installed.

= 1.0.3 - 2023-01-25 =
* TWEAK: Changed some wording on admin page and edited readme.txt

= 1.0.2 - 2022-11-04 =
* TWEAK: Tested with WordPress v6.1

= 1.0.1.3 - 2022-10-28 =
* FIX: Escaped HTML and Scripts

= 1.0.1.2 - 2022-10-27 =
* TWEAK: Updated readme