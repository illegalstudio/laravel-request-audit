<?php

namespace Illegal\RequestAudit\Http\Middleware;

use Closure;
use Illegal\RequestAudit\Models\RequestRecord;
use Illuminate\Http\Request;

class RequestRecorder
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Verifica il contenuto della richiesta
        if ($this->shouldAuditRequest($request)) {
            // Registra le informazioni relative alla richiesta
            $this->auditRequest($request);
        }

        return $next($request);
    }

    /**
     * Whether the request should be audited.
     */
    protected function shouldAuditRequest(Request $request): bool
    {
        return true;
    }

    /**
     * Record the request information.
     */
    protected function auditRequest(Request $request): void
    {
        /**
         * Record the request information.
         */
        RequestRecord::create([
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'forwarded_ip' => $request->header('X-Forwarded-For') ?? '',
            'cloudflare_ip' => $request->header('CF-Connecting-IP') ?? '',
            'headers' => $request->header(),
            'user_agent' => $request->header('User-Agent'),
            'content' => $request->getContent(),
            'body' => $request->all(),
            'server_params' => $request->server(),
        ]);
    }
}
