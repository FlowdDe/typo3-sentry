<?php

namespace DmitryDulepov\Sentry\ErrorHandlers;

use Raven_Client;

class SentryPageNotFoundHandler {

    /**
     * Log a message to Sentry and just continue with the normal flow in TSFE
     *
     * @param array $params
     * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $tsfe
     */
    public function handle($params, $tsfe) {
        $ravenClient = $GLOBALS['SENTRY_CLIENT'] ?? null;

        if($ravenClient) {
            $ravenClient->message('404 Page not found', [], Raven_Client::WARNING, false, $params);
        }

        $tsfe->pageErrorHandler($GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFound_handling_original']);
    }

}
