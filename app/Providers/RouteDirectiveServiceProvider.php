<?php

namespace App\Providers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class RouteDirectiveServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('route', function($expression) {
            return "<?php echo route($expression); ?>";
        });

        Blade::directive('routeWithProtocol', function($expression) {
            return "<?php echo ".__CLASS__."::routeWithProtocol($expression); ?>";
        });

        Blade::directive('url', function($expression) {
            return "<?php echo url($expression); ?>";
        });

        Blade::directive('active', function($expression) {
            return "<?php echo ".__CLASS__."::active($expression); ?>";
        });
        
        Blade::directive('playerLink', function($expression) {
            return "<?php echo ".__CLASS__."::playerLink($expression); ?>";
        });
    }

    public static function active($name, $className = 'active')
    {
        $active = Request::route()->getName();
        if ($name === $active) {
            return $className;
        }

        return '';
    }

    public static function routeWithProtocol($routeName, $params, $protocol)
    {
        return $protocol . route($routeName, $params);
    }
    
    static public function playerLink($player)
    {
        return '<a href="'.route('players', ['name_key'=>$player->name_key]).'">#' . $player->seasons->first()->number. ' ' . $player->first_name . ' ' .$player->last_name.'</a>';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
