<?php
/**
 * Production error routing.
 *
 * Every channel at ERROR and above is written to stderr as one JSON object per
 * line. WikiProcessor attaches reqId — nginx supplies it per request as the
 * UNIQUE_ID FastCGI param — so an application error joins to the access-log
 * line carrying the same id. Under CLI it attaches cli_argv instead, naming the
 * runJobs.php invocation behind a job failure.
 *
 * $wgDebugLogGroups and $wgDBerrorLog are inert once the SPI is Monolog: both
 * are read only by LegacyLogger. Routing belongs here, not there.
 */

use MediaWiki\Logger\Monolog\WikiProcessor;
use MediaWiki\Logger\MonologSpi;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

$wgMWLoggerDefaultSpi = [
    'class' => MonologSpi::class,
    'args' => [ [
        'loggers' => [
            '@default' => [
                'processors' => [ 'wiki', 'psr' ],
                'handlers' => [ 'stderr' ],
            ],
        ],
        'processors' => [
            'wiki' => [ 'class' => WikiProcessor::class ],
            'psr' => [ 'class' => PsrLogMessageProcessor::class ],
        ],
        'handlers' => [
            'stderr' => [
                'class' => StreamHandler::class,
                'args' => [ 'php://stderr', Logger::ERROR ],
                'formatter' => 'json',
            ],
        ],
        'formatters' => [
            'json' => [
                'class' => JsonFormatter::class,
                // batchMode, appendNewline, ignoreEmptyContextAndExtra, includeStacktraces
                'args' => [ JsonFormatter::BATCH_MODE_JSON, true, false, true ],
            ],
        ],
    ] ],
];
