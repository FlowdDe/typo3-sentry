# TYPO3 Sentry integration

This extension sends error messages from TYPO3 CMS to Sentry event logging
system. Administrators need to configure Sentry URL after the extension
installation in the extension properties. There is also an option to disable
TYPO3 error handler if you want to handle errors only through Sentry.

# 404 Error handling
To enable 404 error handling and have this extension report those events to Sentry
you need to set the `pageNotFound_handling` variable like so:

```php
$GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFound_handling'] = 'USER_FUNCTION:'.\DmitryDulepov\Sentry\ErrorHandlers\SentryPageNotFoundHandler::class.'->handle';
```

this would break the normal 404 handling in place so set the value you had there before to

```
$GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFound_handling_original'] 
```

The `SentryPageNotFoundHandler` will just pass down the value of `pageNotFound_handling_original`
in the normal TYPO3 404 flow after it sent a message to Sentry.

## License

The extension is licensed under GPL v2 (just like TYPO3 CMS).

The extension includes a Raven-PHP module from Sentry. See its license in
lib/raven-php/LICENSE.

## Contact

Author: Dmitry Dulepov
E-mail: dmitry.dulepov@gmail.com