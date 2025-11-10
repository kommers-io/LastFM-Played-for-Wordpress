=== Last.FM Recently Played for WordPress ===
Contributors: kommers
Tags: last.fm, scrobble, music, widget, recently played
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 8.0
Stable tag: trunk
License: MIT
License URI: https://opensource.org/licenses/MIT

Display your recently played tracks from Last.FM in a clean, responsive widget with caching and modern security standards.

== Description ==

Last.FM Recently Played is a modern, secure WordPress plugin that displays your recently played tracks from Last.FM in a beautiful widget.

**Key Features:**

* 🎵 Display recent Last.FM scrobbles in a widget, shortcode, or template function
* 📝 **Shortcode**: Use `[lastfm_tracks user="username"]` in posts/pages
* 🎨 **Template Function**: Use `lastfm_display_tracks()` in theme files
* 🔒 Secure API key storage in WordPress settings
* ⚡ Built-in caching for optimal performance
* 📱 Responsive design with mobile support
* 🎨 Clean, customizable CSS
* 🛡️ Modern security standards (proper escaping, validation, nonces)
* 🚀 PHP 8.0+ compatible with strict types
* 🌐 Translation ready with proper text domains
* ♿ Accessible markup with proper attributes

**Requirements:**

* A Last.FM account with scrobbling enabled
* A Last.FM API key is optional - plugin includes a default shared key, but getting your own is recommended for better performance

**Privacy & Security:**

* Your API key is stored securely in WordPress options
* All API requests use HTTPS
* Output is properly sanitized to prevent XSS attacks
* Transient caching reduces API calls and improves performance

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/lastfm-played-wp/` or install via the WordPress admin panel
2. Activate the plugin through the 'Plugins' menu in WordPress
3. (Optional) Go to Settings > Last.FM Settings to configure your own API key for better performance

**Using the Widget:**
1. Go to Appearance > Widgets
2. Add the "Last.FM Recently Played" widget to your sidebar
3. Configure with your Last.FM username and preferences

**Using Shortcode:**
Add `[lastfm_tracks user="your_username"]` to any post or page

**Using Template Function:**
Add `<?php lastfm_display_tracks('your_username'); ?>` to your theme files

**Getting Your Own API Key (Optional but Recommended):**

The plugin works out of the box with a default shared API key. However, for better performance and to avoid potential rate limits, we recommend getting your own free API key:

1. Visit https://www.last.fm/api/account/create
2. Fill in the application form (use your website URL)
3. Copy your API Key
4. Paste it in Settings > Last.FM Settings

== Frequently Asked Questions ==

= Do I need my own API key? =

No! The plugin works immediately with a default shared API key. However, getting your own free API key is recommended for:
* Better performance
* Higher rate limits
* Avoiding potential throttling from shared key usage

Visit https://www.last.fm/api/account/create to create a free API account. You'll receive an API key immediately after registration.

= How often does the widget update? =

The widget caches user data for 1 hour and track data for 5 minutes to improve performance and respect Last.FM's API rate limits. When a track is currently playing, it will show "Now playing..." in real-time.

= How do I use the shortcode? =

Add this to any post or page:
`[lastfm_tracks user="your_username"]`

Optional parameters:
* `count` - Number of tracks (default: 5, max: 50)
* `showuser` - Show user info (default: true)

Examples:
* `[lastfm_tracks user="johndoe" count="10"]`
* `[lastfm_tracks user="johndoe" count="3" showuser="false"]`

= How do I use the template function? =

Add this to your theme files (header.php, sidebar.php, etc.):

`<?php if ( function_exists( 'lastfm_display_tracks' ) ) {
    lastfm_display_tracks( 'your_username', 5, true );
} ?>`

Parameters:
1. Username (required)
2. Number of tracks (optional, default: 5)
3. Show user info (optional, default: false)

= Can I hide the user profile information? =

Yes! In the widget settings, simply uncheck "Show user profile information" to display only the track list without the user's profile picture, name, and play count.

For shortcode: Use `showuser="false"`
For template function: Set third parameter to `false`

= Can I customize the styling? =

Yes! The plugin uses standard CSS classes that you can override in your theme's CSS:
* `.lastfm-row` - Container for rows
* `.lastfm-user` - User info section
* `.lastfm-tracklist` - Track list items
* `.lastfm-col-*` - Column classes

= Is this plugin secure? =

Yes! Version 1.0.0 includes major security improvements:
* API keys stored securely in WordPress options (not hardcoded)
* All output properly escaped to prevent XSS
* Input validation and sanitization
* Uses wp_remote_get() instead of insecure file functions
* HTTPS-only API calls
* Nonce verification for settings

= Does this work with PHP 8.x? =

Yes! The plugin is fully compatible with PHP 8.0+ and uses modern PHP features like strict types and type hints.

= How do I report bugs or request features? =

Please use the GitHub issue tracker:
https://github.com/kommers-io/LastFM-Played-for-Wordpress/issues

== Screenshots ==

1. Widget displaying recent tracks in the sidebar
2. Settings page for configuring your Last.FM API key
3. Widget configuration panel

== Changelog ==

= 1.1.0 - 2025-11-10 =
**New Feature: Shortcode and Template Function Support**

* Added: `[lastfm_tracks]` shortcode for use in posts and pages
* Added: `lastfm_display_tracks()` template function for theme integration
* Added: `lastfm_get_tracks()` helper function that returns HTML
* Feature request fulfilled from 2016 - display tracks without widgets
* All functions support custom track count and show/hide user options

= 1.0.1 - 2025-11-10 =
**Critical fix for widget compatibility**

* Fixed: Restored original widget ID to prevent widgets from being removed on update
* This fixes the issue where widgets disappeared after updating from 0.99.x to 1.0.0

= 1.0.0 - 2025-11-10 =
**Major security and modernization update**

* **Easy Setup:**
  - Includes default API key - works immediately after installation
  - Optional custom API key support for better performance
  - Settings page for easy configuration
* **New Features:**
  - Option to hide user profile information (show tracks only)
* **Security fixes:**
  - API key management with secure settings page
  - Changed all API calls from HTTP to HTTPS
  - Added proper output escaping to prevent XSS vulnerabilities
  - Replaced @simplexml_load_file with secure wp_remote_get()
  - Added input validation and sanitization
  - Implemented nonce verification for settings
* **Performance improvements:**
  - Added transient caching (1 hour for user data, 5 minutes for tracks)
  - Reduced API calls significantly
* **Modern standards:**
  - Updated to PHP 8.0+ with strict types and type hints
  - Updated to WordPress 6.0+ standards
  - Proper text domain for translations
  - Added proper error handling
  - Removed deprecated code
* **UI/UX improvements:**
  - Modern, polished design with gradient backgrounds
  - Smooth hover effects and transitions
  - Card-based layout with shadows and borders
  - Dark mode support for modern browsers
  - Staggered fade-in animations for tracks
  - System font stack for better performance
  - Last.FM brand colors (#d51007)
  - Added admin settings page with proper capability checks
  - Added settings link on plugins page
  - Better error messages for administrators
  - Improved responsive design
  - Added loading="lazy" for images
* **Code quality:**
  - Follows WordPress Coding Standards
  - Comprehensive PHPDoc comments
  - Modern CSS (removed vendor prefixes)
  - Added uninstall.php for proper cleanup

= 0.99.2 =
* Previous release

== Upgrade Notice ==

= 1.0.1 =
Critical fix: Restores widget compatibility. If you updated to 1.0.0 and lost your widgets, please update to 1.0.1 and re-add them.

= 1.0.0 =
Major security and modernization update! The plugin now works immediately with a default API key. For optimal performance, visit Settings > Last.FM Settings to add your own free API key (optional but recommended).

== Third-Party Services ==

This plugin connects to the Last.FM API to retrieve your music listening data:
* Service: Last.FM Web Services API
* API Documentation: https://www.last.fm/api
* Terms of Service: https://www.last.fm/api/tos
* Privacy Policy: https://www.last.fm/legal/privacy

Data sent to Last.FM: Your Last.FM username (configured in widget settings)
Data received: Your user profile information and recently played tracks
