<?php

namespace App\Http\Middleware;
use Session;
use App;
use Closure;
use Config;

class SetLanguage
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
        $segLang = $request->lang;
        if($segLang != "404") {
            $langs =  array('pt', 'en');
            if(isset($_COOKIE['lang']) && in_array($segLang, $langs)) {
                if($_COOKIE['lang'] != $segLang) {
                    $this->set_cookie($segLang);
                }
                else {
                    $this->set_session($_COOKIE['lang']);
                }
            }
            else {
                if(Session::get('lang') == Null) {
                    $this->set_cookie(Config::get('app.fallback_locale'));
                }
                else {
                    $this->set_cookie(Session::get('lang'));
                }
                return redirect($this->redi_current_url($request->segments(), Session::get('lang')));
            }
        }
        return $next($request);



        // ALTERNATIVE METHOD

        /*$segLang = $request->lang;
        $mainLang = Config::get('app.fallback_locale');
        $langs =  array($mainLang);
        if(in_array($segLang, $langs)) {
            if(isset($_COOKIE['lang'])) {
                if($_COOKIE['lang'] != $segLang) {
                    $this->set_cookie($segLang);
                }
                else {
                    $this->set_session($_COOKIE['lang']);
                }
            }
            else {
                $this->set_cookie($segLang);
            }
        }
        else {
            if(Session::get('lang') == Null) {
                if(isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $langs)) {
                    $this->set_session($_COOKIE['lang']);
                    return redirect($this->redi_current_url($request->segments(), $_COOKIE['lang']));
                }
                $this->set_cookie($mainLang);
                return redirect($this->redi_current_url($request->segments(), $mainLang));
            }
            else if($segLang != Session::get('lang')) {
                $this->set_cookie(Session::get('lang'));
                return redirect($this->redi_current_url($request->segments(), Session::get('lang')));
            }
        }
        return $next($request);*/
    }

    private function set_cookie($sessLang) {
        setcookie("lang", $sessLang, time()+3600*24*30, '/');
        $this->set_session($sessLang);
    }

    private function set_session($sessLang) {
        Session::set("lang", $sessLang);
        $lang = strtolower($sessLang);
        App::setLocale($lang);
    }

    private function redi_current_url($segments, $lang) {
        $countSegments = count($segments);
        $redirectUrl = $lang;
        for($i = 1; $i < $countSegments; $i++) {
            $redirectUrl .= '/' .$segments[$i];
        }
        return $redirectUrl;
    }

}
