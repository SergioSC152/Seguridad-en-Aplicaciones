<?php
namespace App\Http\Middleware;
use App\Services\SecurityAuditService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class AuditWorkspace
{
    public function handle(Request $request,Closure $next): Response
    {
        $actor=$request->user()?->id;
        try { $response=$next($request); }
        catch (\Throwable $exception) {
            $status=method_exists($exception,'getStatusCode')?$exception->getStatusCode():500;
            if($exception instanceof \Illuminate\Validation\ValidationException)$status=$request->expectsJson()?422:302;
            if($exception instanceof \Illuminate\Auth\Access\AuthorizationException)$status=403;
            app(SecurityAuditService::class)->record('request.failed','warning',$actor,$status);
            throw $exception;
        }
        if (!$request->isMethod('GET') || $response->getStatusCode()>=400) app(SecurityAuditService::class)->record('request.'.$request->method(),$response->getStatusCode()>=400?'warning':'info',$actor,$response->getStatusCode());
        return $response;
    }
}
