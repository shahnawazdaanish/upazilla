<?php

namespace App\Logging;

use Monolog\Formatter\JsonFormatter as BaseJsonFormatter;

/**
 * Class JsonFormatter
 *
 * @package App\Components\Log\Formatter
 * @author Miguel Borges <miguelborges@miguelborges.com>
 */
class JsonFormatter extends BaseJsonFormatter
{
    const APPLICATION = 'bKashOnDelivery';

    /**
     * {@inheritdoc}
     */
    public function format(array $record) : string
    {
        $record = [
            'time' => $record['datetime']->format('Y-m-d H:i:s'),
            'application' => self::APPLICATION,
            'host' => request()->server('SERVER_ADDR'),
            'remote-addrress' => request()->server('REMOTE_ADDR'),
            'level' => $record['level_name'],
            'message' => $record['message']
        ];

        if (!empty($record['extra'])) {
            $record['extra']['path'] = request()->path();
            $record['extra']['fp'] = request()->header('X-APP-FP');
            $record['payload']['extra'] = $record['extra'];
        }

        if (!empty($record['context'])) {
            $record['payload']['context'] = $record['context'];
        }

        $json = $this->toJson($this->normalize($record), true) . ($this->appendNewline ? "\n" : '');

        return $json;
    }

}
