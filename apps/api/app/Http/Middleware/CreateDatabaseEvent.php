<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

// @TODO: This whole class is suppose to be a Laravel package
class CreateDatabaseEvent
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasHeader('x-refodev-session-id') || !$request->hasHeader('x-refodev-session-secret-token')) {
            return $next($request);
        }

        $sessionId = $request->header('x-refodev-session-id');
        $sessionSecret = $request->header('x-refodev-session-secret-token');

        DB::listen(function (QueryExecuted $query) use ($sessionSecret, $sessionId) {
            $sql = $query->sql;
            $bindings = $query->bindings;
            $executionTimeInMilliseconds = $query->time;

            foreach ($bindings as $binding) {
                $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
                $sql = preg_replace('/\?/', $value, $sql, 1);
            }

            // @todo: uncomment this when transferring this to a package. This is creating an infinite loop
//            $url = sprintf('http://localhost/sessions/%s/events', $sessionId);
//            $response = Http::withHeaders(['x-session-secret-token' => $sessionSecret])
//                ->post($url, [
//                    'sql' => $sql,
//                    'executionTimeInMilliseconds' => $executionTimeInMilliseconds,
//                ]);
        });

        return $next($request);
    }
}
