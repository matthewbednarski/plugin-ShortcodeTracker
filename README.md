# ShortcodeTracker Plugin

| Branch | Status |
| --- | --- |
| Master | [![Build Status](https://travis-ci.org/mgazdzik/plugin-ShortcodeTracker.svg?branch=master)](https://travis-ci.org/mgazdzik/plugin-ShortcodeTracker) |
| Develop | [![Build Status](https://travis-ci.org/mgazdzik/plugin-ShortcodeTracker.svg?branch=develop)](https://travis-ci.org/mgazdzik/plugin-ShortcodeTracker) |

## Description

Plugin allows to turn Piwik instance into URL Shortener.

Basic features:

* easily create shortcode from any page you track in Piwik (integration with Actions report UI),
* create shortcode for any custom URL you want,
* perform redirects using your Piwik instance,
* get usage statistics for shortcodes handled by your instance
    * get best performing URL's on websites you track,
    * external URLs redirect statistics,
* see which URLs are being shortened and visited most often - also for external URLs not tracked in your Piwik.

Goodness coming:

* for redirect performance improvement, store your shortcodes in storage like Memcache or Redis,
* attributing shortcode redirects with actual visits on your page,
* more advanced reports,

Before using, please read content in [`Setup`](https://github.com/mgazdzik/plugin-ShortcodeTracker#setup) section 
as it contains steps required to make plugin work with your Piwik instance!

## Usage

After correctly setting up this plugin (please see section below), you are ready for shortening your Urls.

There is one new section in top reporting menu called "Shortcodes".

This view gives you possibility to shorten any URL you want and operate with shortcode retrieved.

Additionally this plugin integrates with Page URL's report - hover over URL you want to shorten and click scissors icon.

This will call popup with appropriate shortcode, so you don't need to manually shorten any URL you already track with your
Piwik instance.

Enjoy!

## Setup

### Webserver
Besides of functional Piwik instance with this plugin enabled you will also need special configuration for your webserver.

It's purpose is to redirect any short url hitting your server to proper API method doing the magic.

Below you can find example configurations

* [for NGINX webserver vhost](docs/nginx_config.md)
* [for Apache2 webserver .htaccess file](docs/apache_config.md)

**Please be aware that in your case this configuration may be different, so please contact your system/webserver
admin for advisory!**


### Plugin

Before you can start shortening your URLs you need to perform following steps:

* go to Administration -> Plugins,
* find "ShortcodeTracker" plugin and click `enable`,

After you confirm that plugin has been enabled:
* go to Administration -> Plugin Settings,
* go to ShortcodeTracker section,
* fill in Shortener URL input,
* if you want to track external sites, you need to decide to which Piwik page those actions will be attributed (see
External redirects tracking section below),
* click 'save',
* **additionally you have to make Shortener URL a trusted host for Piwik by entering it in settings section**,

This is necessary to perform, as otherwise you will not be able to generate shortened URLs or use them with Piwik.

### External redirects tracking

It is possible to also track redirect actions for external URLs (i.e. which URL doesn't match any page tracked within
your Piwik instance). However, it is required to decide to which site this traffic will be attributed to.

It is recommended to create a separate Website in Piwik instance only dedicated to this traffic, so that other websites
reports won't be affected by redirect events.

To select which site should collect redirects:

* go to `Plugin Settings` section,
* from dropdown you can select site for external redirects,
* alternatively you can select not to track external redirects by setting `Do not collect external shortcode redirects`,
* click save

## Changelog

* 0.6.2
    * Sort out mistakenly pushed tag


* 0.6.0
    * Shortcode usage report added link to shortened page to for easier recognition of what is being shortened and used most,
    * Display summarized report displaying which URLs were visited most via shortcode redirects,


* 0.5.0
    * Add statistics collection for redirects to pages not tracked with Piwik (external pages)
         * collect redirect statistics into Site you choose in interface,
         * aggregate and display report for external shortcodes in separate view


* 0.4.5
    * fix README formating for sake of Plugin market
    
    
* 0.4.4
    * add license to plugin.json
    
    
* 0.4.3
    * fix plugin.json structure for Plugin market


* 0.4.2
    * Added missing changelog


* 0.4.0
    * Piwik Plugin market release


* 0.3.0
    * Tuned travis build file
    * Mark Shortcodes as internal during creating
    * Track custom event with "redirect" category upon each redirect for internal Shortcode
    * Secure API methods from anonymous user usage
    * Add shortcode report for internally tracked URLs:
        * Create new visit during redirect (store referrer)
        * Add Shortcode usage report based on Custom Events plugin API


* 0.2.0
    * added Travis build badges for master and develop branches
    * fixed existing unit tests
    * slight refactor in terms of class naming
    * added integration test for API methods


* 0.1.0
    * API allowing to create and retrieve shortcodes,
    * basic storage in MySQL, but possible to add other caching layers - for ex. Memcache, Redis,
    * unit tests covering core logic,
    * redirect API method that will preform appropriate redirects for incoming shortcode requests,
    * basic setup guide involving Apache2 and Nginx configs,
    * settings section allowing user to configure Shortener base URL (which may and should be different than Piwik instance)

## Backlog



* Migrate plugin to work with Piwik 2.15 LTS version,
* Add advanced report for each shortcode
    * stitch every redirect event with following action,
    * add new referrer type (shortcode),
    * aggregate statistics,
    * add segment for referrer,
* Refactor plugin so it's possible to cover Model.php with tests,
* Add queue system for tracking redirect events to improve performance of redirect feature,
* Add integration test for redirect tracking,
* Add support for at least one caching system (redis/memcache),
* Improve HTML elements designs/styles,
* Throw exception/signal in UI in case Shortener URL is not changed,
* Introduce Shortener base URL validation (in Settings section),
* introduce value object to store Shortcode,
* handle case when given idsite has multiple domains assigned (currently it's only for main domain URL),


## Support

Please direct any feedback regarding plugin to Github repository issue tracker available at
[https://github.com/mgazdzik/plugin-ShortcodeTracker/issues](https://github.com/mgazdzik/plugin-ShortcodeTracker/issues).


## Credits
Scissors icon visible in Actions report is originating from
[https://icons8.com/](https://icons8.com/).
