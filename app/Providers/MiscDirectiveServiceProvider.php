<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

/**
 * Class MiscDirectiveServiceProvider
 * @package App\Providers
 */
class MiscDirectiveServiceProvider extends ServiceProvider
{

    /**
     * Houses the variables for the forimplode/implode directives
     * @var array
     */
    static protected $implodes = [];
    static protected $implodeStacks = [];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Add our date directives to Blade
        $self = $this;
        $partial = function($func) use ($self) {
            return function($date) use ($func, $self) {
                return $self->outputPhp($func, $date);
            };
        };
        
        Blade::directive('ordinal', $partial('ordinal'));
        Blade::directive('number', $partial('number'));
        Blade::directive('numberOrNothing', $partial('numberOrNothing'));

        Blade::directive('forimplode', $partial('forImplode'));
        Blade::directive('endforimplode', $partial('endForImplode'));
        Blade::directive('implode', $partial('implode'));
    }

    /**
     * Returns a string of PHP code to use for the directive
     *
     * @param $d
     * @param $format
     * @return string
     */
    public function outputPhp($func, $expression)
    {
        debug($func, $expression);
        return "<?php echo ".__CLASS__."::$func($expression); ?>";
    }


    /**
     * Takes a cardinal number (1) and returns ordinal (1st)
     *
     * @param $int
     * @return string
     */
    static public function ordinal($int)
    {
        $s = ["th","st","nd","rd"];
        $v = $int%100;
        $keys = [($v-20)%10, $v, 0];
        foreach($keys as $k){
            if (array_key_exists($k, $s)) {
                return $v . $s[$k];
            }
        }

        return $v;
    }

    /**
     * Shortcut for number format
     *
     * @param $number
     * @param int $decimals = 0
     * @param string $decimalPoint = .
     * @param string $thousandsSeperator = ,
     * @return string
     */
    static public function number($number, $decimals = 0, $decimalPoint = '.', $thousandsSeperator = ',')
    {
        return number_format($number, $decimals, $decimalPoint, $thousandsSeperator);
    }

    static public function numberOrNothing($number, $decimals = 0, $decimalPoint = '.', $thousandsSeperator = ',')
    {
        if ($number) {
            return self::number($number, $decimals, $decimalPoint, $thousandsSeperator);
        } else {
            return '';
        }
    }

    // just needed to satisfy the provider
    public function register()
    {
    }
}
