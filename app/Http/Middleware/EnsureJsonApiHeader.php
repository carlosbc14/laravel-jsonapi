<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EnsureJsonApiHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('Accept') !== 'application/vnd.api+json') {
            throw new HttpException(406, __('Not Acceptable'));
        }

        if ($request->isMethod('POST') || $request->isMethod('PATCH')) {
            if ($request->header('Content-Type') !== 'application/vnd.api+json') {
                throw new HttpException(415, __('Unsupported Media Type'));
            }
        }

        $response = $next($request);
        $response->headers->set('Content-Type', 'application/vnd.api+json');

        return $response;
    }
}
