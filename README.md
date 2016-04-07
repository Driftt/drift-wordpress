# Drift WordPress Integration

[![Build Status](https://travis-ci.org/driftt/drift-wordpress.svg?branch=master)](https://travis-ci.org/driftt/drift-wordpress)

# Compatibility

Requires PHP 5.3 or higher.

# Local Testing

Running tests requires [phpunit](https://phpunit.de/).

```php
DRIFT_PLUGIN_TEST=1 phpunit
```

# Usage

Installing this plugin provides a new Drift settings page.
Authenticate with Drift to retrieve your app_id and secure_mode_secret.
<img src="https://raw.githubusercontent.com/drift/drift-wordpress/master/screenshots/settings_not_auth.png"/>

Once authenticated, if you have enabled [Acquire](https://www.drift.io/live-chat), the Drift widget will automatically appear on your site.

<img src="https://raw.githubusercontent.com/drift/drift-wordpress/master/screenshots/settings_auth.png"/>

# Users

If a `$current_user` is present, we use their email as an identifier in the widget.
Otherwise the widget operates in [Acquire mode](https://www.drift.com/live-chat) (if available). This allows you to talk with anonymous visitors on your WordPress site.

