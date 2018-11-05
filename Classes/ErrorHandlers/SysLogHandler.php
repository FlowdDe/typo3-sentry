<?php

namespace DmitryDulepov\Sentry\ErrorHandlers;

use Raven_Client;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SysLogHandler {

    /**
     * Log messages from GeneralUtility::sysLog() to sentry
     *
     * @param array $params
     * @param mixed $fakeThis
     */
    public function sysLog($params, $fakeThis) {
        if (!empty($params['initLog'])) {
            return;
        }

        $ravenClient = $GLOBALS['SENTRY_CLIENT'] ?? null;

        if($ravenClient instanceof Raven_Client) {
            $ravenClient->captureMessage(
                $params['msg'],
                [],
                [
                    'level' => $this->mapSysLogSeverityToSentrySeverity($params['severity'])
                ],
                $params['backTrace']
            );
        }
    }

    protected function mapSysLogSeverityToSentrySeverity($sysLogSeverity)
    {
        $severity = Raven_Client::ERROR;
        switch ($sysLogSeverity) {
            case GeneralUtility::SYSLOG_SEVERITY_INFO:
                $severity = Raven_Client::INFO;
                break;
            case GeneralUtility::SYSLOG_SEVERITY_NOTICE:
                $severity = Raven_Client::DEBUG;
                break;
            case GeneralUtility::SYSLOG_SEVERITY_WARNING:
                $severity = Raven_Client::WARNING;
                break;
            case GeneralUtility::SYSLOG_SEVERITY_ERROR:
                $severity = Raven_Client::ERROR;
                break;
            case GeneralUtility::SYSLOG_SEVERITY_FATAL:
                $severity = Raven_Client::FATAL;
                break;
        }
        return $severity;
    }
}
