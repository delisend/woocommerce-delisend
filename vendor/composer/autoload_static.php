<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9e04d3c136ecb470ee085343daf86ebe
{
    public static $files = array (
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
        '6e3fae29631ef280660b3cdad06f25a8' => __DIR__ . '/..' . '/symfony/deprecation-contracts/function.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        'a4a119a56e50fbb293281d9a48007e0e' => __DIR__ . '/..' . '/symfony/polyfill-php80/bootstrap.php',
        'e39a8b23c42d4e1452234d762b03835a' => __DIR__ . '/..' . '/ramsey/uuid/src/functions.php',
        '667aeda72477189d0494fecd327c3641' => __DIR__ . '/..' . '/symfony/var-dumper/Resources/functions/dump.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Php80\\' => 23,
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Component\\VarDumper\\' => 28,
        ),
        'R' => 
        array (
            'Ramsey\\Uuid\\' => 12,
            'Ramsey\\Collection\\' => 18,
        ),
        'P' => 
        array (
            'Psr\\SimpleCache\\' => 16,
            'Psr\\Log\\' => 8,
            'Psr\\Http\\Message\\' => 17,
            'Psr\\Http\\Client\\' => 16,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
        ),
        'D' => 
        array (
            'DelisendApi\\' => 12,
        ),
        'B' => 
        array (
            'Brick\\Math\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Php80\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-php80',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Component\\VarDumper\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/var-dumper',
        ),
        'Ramsey\\Uuid\\' => 
        array (
            0 => __DIR__ . '/..' . '/ramsey/uuid/src',
        ),
        'Ramsey\\Collection\\' => 
        array (
            0 => __DIR__ . '/..' . '/ramsey/collection/src',
        ),
        'Psr\\SimpleCache\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/simple-cache/src',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/src',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-factory/src',
            1 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'Psr\\Http\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-client/src',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
        'DelisendApi\\' => 
        array (
            0 => __DIR__ . '/..' . '/delisend/delisend-php-api/src',
        ),
        'Brick\\Math\\' => 
        array (
            0 => __DIR__ . '/..' . '/brick/math/src',
        ),
    );

    public static $classMap = array (
        'Attribute' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Attribute.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Delisend\\WC\\Lib\\Screens\\Abstract_Settings_Screen' => __DIR__ . '/../..' . '/src/lib/screens/Abstract_Settings_Screen.php',
        'Delisend\\WC\\Lib\\Screens\\Connection' => __DIR__ . '/../..' . '/src/lib/screens/Connection.php',
        'Delisend\\WC\\Lib\\WC_Delisend_Api' => __DIR__ . '/../..' . '/src/lib/WC_Delisend_Api.php',
        'Delisend\\WC\\Lib\\WC_Delisend_Connection' => __DIR__ . '/../..' . '/src/lib/WC_Delisend_Connection.php',
        'Delisend\\WC\\Lib\\WC_Delisend_Definitions' => __DIR__ . '/../..' . '/src/lib/WC_Delisend_Definitions.php',
        'Delisend\\WC\\Lib\\WC_Delisend_Endpoint' => __DIR__ . '/../..' . '/src/lib/WC_Delisend_Endpoint.php',
        'Delisend\\WC\\Lib\\WC_Delisend_Error' => __DIR__ . '/../..' . '/src/lib/WC_Delisend_Error.php',
        'Delisend\\WC\\Lib\\WC_Delisend_Exception' => __DIR__ . '/../..' . '/src/lib/WC_Delisend_Exception.php',
        'Delisend\\WC\\Lib\\WC_Delisend_Helper' => __DIR__ . '/../..' . '/src/lib/WC_Delisend_Helper.php',
        'Delisend\\WC\\Lib\\WC_Delisend_Install' => __DIR__ . '/../..' . '/src/lib/WC_Delisend_Install.php',
        'Delisend\\WC\\Lib\\WC_Delisend_Message' => __DIR__ . '/../..' . '/src/lib/WC_Delisend_Message.php',
        'Delisend\\WC\\Lib\\WC_Delisend_Notice' => __DIR__ . '/../..' . '/src/lib/WC_Delisend_Notice.php',
        'Delisend\\WC\\Lib\\WC_Delisend_Order' => __DIR__ . '/../..' . '/src/lib/WC_Delisend_Order.php',
        'Delisend\\WC\\Lib\\WC_Delisend_Plugin' => __DIR__ . '/../..' . '/src/lib/WC_Delisend_Plugin.php',
        'Delisend\\WC\\Lib\\WC_Delisend_Rating' => __DIR__ . '/../..' . '/src/lib/WC_Delisend_Rating.php',
        'Delisend\\WC\\Lib\\WC_Delisend_Settings' => __DIR__ . '/../..' . '/src/lib/WC_Delisend_Settings.php',
        'Delisend\\WC\\Lib\\WC_Delisend_Shipping' => __DIR__ . '/../..' . '/src/lib/WC_Delisend_Shipping.php',
        'Delisend\\WC\\Lib\\WC_Delisend_Utils' => __DIR__ . '/../..' . '/src/lib/WC_Delisend_Utils.php',
        'Delisend\\WC\\WC_Delisend_Loader' => __DIR__ . '/../..' . '/src/WC_Delisend_Loader.php',
        'PhpToken' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/PhpToken.php',
        'Stringable' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Stringable.php',
        'UnhandledMatchError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/UnhandledMatchError.php',
        'ValueError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/ValueError.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9e04d3c136ecb470ee085343daf86ebe::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9e04d3c136ecb470ee085343daf86ebe::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit9e04d3c136ecb470ee085343daf86ebe::$classMap;

        }, null, ClassLoader::class);
    }
}
