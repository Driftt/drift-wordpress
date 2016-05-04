# Drift WordPress Integration

[![Build Status](https://travis-ci.org/driftt/drift-wordpress.svg?branch=master)](https://travis-ci.org/driftt/drift-wordpress)

# Compatibility

Requires PHP 5.3 or higher.

# Local Testing

Running tests requires [phpunit](https://phpunit.de/).

```php
DRIFT_PLUGIN_TEST=1 phpunit
```

# Installation

1. Create and configure an account at [Drift](http://www.drift.com).
2. Add this plugin to WordPress and enable it.
3. Visit https://app.driftt.com/settings/configure and copy the code chunk
4. Visit Drift Settings on your WordPress site and paste the code chunk into the textarea. Submit the settings form.

# Users

If a `$current_user` is present, we use their email as an identifier in the widget.
Otherwise the widget operates in [anonymous mode](https://www.drift.com/live-chat) and you can require that a user submit their email address. This allows you to talk with anonymous visitors on your WordPress site.
