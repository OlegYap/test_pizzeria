<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Prometheus\CollectorRegistry;
use Prometheus\Counter;
use Prometheus\Histogram;

class MetricsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    private Histogram $requestDuration;
    private Counter   $requestTotal;
    private Counter   $requestErrors;

    public function __construct(CollectorRegistry $registry)
    {
        $this->requestDuration = $registry->getOrRegisterHistogram(
            'http', 'request_duration_seconds',
            'Сколько занимает обработка HTTP-запроса',
            ['route', 'method', 'status'],
            [0.005, 0.01, 0.025, 0.05, 0.1, 0.25, 0.5, 1, 2, 4]
        );

        $this->requestTotal = $registry->getOrRegisterCounter(
            'http', 'requests_total',
            'Количество HTTP-запросов',
            ['route', 'method', 'status']
        );

        $this->requestErrors = $registry->getOrRegisterCounter(
            'http', 'request_errors_total',
            'Ошибки (status >= 500)',
            ['route', 'method', 'status']
        );
    }

    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);

        try {
            $response = $next($request);
        } catch (\Throwable $e) {
            $response = response('Internal server error', 500);
            throw $e;
        } finally {
            $status = $response->getStatusCode() ?? 500;
            $route  = optional($request->route())->getName() ?? 'undefined';

            $labels = [$route, $request->getMethod(), (string)$status];

            $this->requestDuration->observe(microtime(true) - $start, $labels);
            $this->requestTotal->inc($labels);

            if ($status >= 500) {
                $this->requestErrors->inc($labels);
            }
        }

        return $response;
    }
}
