<?php
defined('TYPO3_MODE') or die();

(function($extKey) {

    $extConf = @unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sentry']);
    if(is_array($extConf) && isset($extConf['sentryDSN'])) {

        // Set error handler
        $GLOBALS['SENTRY_CLIENT'] = new Raven_Client($extConf['sentryDSN']);
        $ravenErrorHandler = new Raven_ErrorHandler($GLOBALS['SENTRY_CLIENT']);

        $errorMask = $GLOBALS['TYPO3_CONF_VARS']['SYS']['errorHandlerErrors'];

        // Register handlers in case if we do not have to report to TYPO3. Otherwise we need to register those handlers first!
        if(!$extConf['passErrorsToTypo3']) {
            $ravenErrorHandler->registerErrorHandler(false, $errorMask);
            $ravenErrorHandler->registerExceptionHandler(false);
        }

        // Make sure that TYPO3 does not override our handler
        \DmitryDulepov\Sentry\ErrorHandlers\SentryErrorHandler::initialize($ravenErrorHandler, $errorMask);
        \DmitryDulepov\Sentry\ErrorHandlers\SentryExceptionHandler::initialize($ravenErrorHandler);

        if(version_compare(TYPO3_branch, '7.0', '>=')) {
            \DmitryDulepov\Sentry\ErrorHandlers\SentryExceptionHandlerFrontend::initialize($ravenErrorHandler);
        }

        if (empty($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['systemLog'])) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['systemLog'] = [];
        }
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['systemLog']['tx_sentry'] =  \DmitryDulepov\Sentry\ErrorHandlers\SysLogHandler::class . '->sysLog';

        // Register test plugin
        if(is_array($extConf) && isset($extConf['enableTestPlugin']) && $extConf['enableTestPlugin']) {
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('DmitryDulepov.sentry', 'ErrorHandlerTest', array('ErrorHandlerTest' => 'index,phpWarning,phpError,phpException'), array('ErrorHandlerTest' => 'index,phpWarning,phpError,phpException'));
        }
        unset($extConf);

        // Fix TYPO3 7.0 hard-coded FE exception handler
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Frontend\\ContentObject\\Exception\\ProductionExceptionHandler'] = array(
            'className' => 'DmitryDulepov\\Sentry\\ErrorHandlers\\SentryExceptionHandlerFrontend',
        );
    }
})($_EXTKEY);
