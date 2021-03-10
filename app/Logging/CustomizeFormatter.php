<?php
namespace App\Logging;

class CustomizeFormatter
{
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor(function ($record) {

                $record['extra']['ip'] = request()->getClientIp();
                $record['extra']['path'] = request()->path();
                $record['extra']['host_version'] = env('app_version');
                $record['extra']['fp'] = request()->header('X-APP-FP');
                $record['extra']['app_name'] = request()->header('X-APP-NAME');
                $record['extra']['app_version'] = request()->header('X-APP-VERSION');
                $record['extra']['user_id'] = request()->user()->id ?? '';
                $record['extra']['user_name'] = request()->user()->name ?? '';
                $record['extra']['user_model'] = request()->user() !== null ? get_class(request()->user()) : '';
                $record['extra']['request'] = request()->all();

                return $record;
            });
        }
    }
}
