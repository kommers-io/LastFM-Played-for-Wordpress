# Last.FM Recently Played for WordPress

**Display your recently played tracks from Last.FM in a clean, responsive widget with modern security standards.**

[![WordPress Plugin](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)](https://wordpress.org/plugins/lastfm-played-for-wp/)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-purple.svg)](https://php.net)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)

## Features

* 🎵 Display recent Last.FM scrobbles in a widget
* 🔒 Secure API key storage in WordPress settings
* ⚡ Built-in caching for optimal performance (1 hour for user data, 5 minutes for tracks)
* 📱 Responsive design with mobile support
* 🎨 **Modern, polished design** with:
  - Card-based layout with smooth shadows
  - Gradient backgrounds
  - Hover effects and transitions
  - Dark mode support
  - Staggered fade-in animations
  - Last.FM brand colors
* 🛡️ Modern security standards (proper escaping, validation, nonces)
* 🚀 PHP 8.0+ compatible with strict types and type hints
* 🌐 Translation ready with proper text domains
* ♿ Accessible markup with proper ARIA attributes

## Requirements

- WordPress 6.0 or higher
- PHP 8.0 or higher
- A Last.FM account with scrobbling enabled
- A Last.FM API key (free) - [Get one here](https://www.last.fm/api/account/create)

## Installation

1. Upload the plugin files to `/wp-content/plugins/lastfm-played-wp/` or install via the WordPress admin panel
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Settings > Last.FM Settings** and enter your API key
4. Go to **Appearance > Widgets** and add the "Last.FM Recently Played" widget to your sidebar
5. Configure the widget with your Last.FM username and preferences

### Getting Your API Key

1. Visit https://www.last.fm/api/account/create
2. Fill in the application form (use your website URL)
3. Copy your API Key
4. Paste it in **Settings > Last.FM Settings**

## Security Improvements in v1.0.0

Version 1.0.0 includes major security enhancements:

- ✅ **API Key Security**: Moved from hardcoded to secure settings page
- ✅ **HTTPS Only**: All API calls now use HTTPS instead of HTTP
- ✅ **XSS Prevention**: Proper output escaping for all user-generated content
- ✅ **Secure HTTP Requests**: Using `wp_remote_get()` instead of `simplexml_load_file()`
- ✅ **Input Validation**: All inputs sanitized and validated
- ✅ **Nonce Verification**: CSRF protection for settings
- ✅ **Capability Checks**: Proper permission checks for admin functions

## Development

### Changelog

See [readme.txt](trunk/readme.txt) for full changelog.

**v1.0.0 (2025-11-10)**
- Major security and modernization update
- Moved API key to settings page
- Implemented HTTPS and proper escaping
- Added transient caching
- PHP 8.0+ compatibility with type hints
- WordPress 6.7 compatibility

### Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Issues & Support

Please use the [GitHub issue tracker](https://github.com/kommers-io/LastFM-Played-for-Wordpress/issues) for bug reports and feature requests.

## Legal & Compliance

**Important:** This plugin is not affiliated with, endorsed by, or sponsored by Last.FM or CBS Interactive. Last.FM is a registered trademark of Last.FM Limited.

By using this plugin, you agree to comply with:
- [Last.FM Terms of Service](https://www.last.fm/legal/terms)
- [Last.FM API Terms of Service](https://www.last.fm/api/tos)
- [Last.FM Privacy Policy](https://www.last.fm/legal/privacy)

### Third-Party Data

This plugin connects to the Last.FM API to retrieve your music listening data:
- **Service**: Last.FM Web Services API
- **Data Sent**: Your Last.FM username (configured in widget settings)
- **Data Received**: Your user profile information and recently played tracks
- **API Documentation**: https://www.last.fm/api

## License

MIT License - Copyright (c) 2017-2025 KOMMERS GmbH

See [LICENSE](LICENSE) file for details.

## Credits

Developed and maintained by [KOMMERS GmbH](https://kommers.io)

---

**Official Plugin**: https://wordpress.org/plugins/lastfm-played-for-wp/
