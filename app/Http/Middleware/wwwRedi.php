<?php
namespace App\Http\Middleware;
use Closure;
class wwwRedi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        
        $url = $request->url();
        
        // www to non-www
        
        /*if(strpos($url, 'localhost') === False && strpos($url, 'www.') !== False) {
            $url = explode('www.', $url);
            $wwwURL = $url[0].$url[1];
            return redirect($wwwURL, 301);
        }*/
        
        // non-www to www

        
        if(
            strpos($url, 'localhost') === False &&
            strpos($url, 'www.') === False &&
            strpos($url, '127.0.0.1') === False &&
            strpos($url, '192.168.1') === False
        )
        {

            $url = explode('://', $url);
            $wwwURL = $url[0]. '://www.' .$url[1];
            return redirect($wwwURL, 301);
        }
        return $next($request);
    }

}