<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6a9db6f85b0dce0225e42595ef7cdae8
{
    public static $prefixLengthsPsr4 = array (
        'J' => 
        array (
            'JustCloudflareCacheManagement\\' => 30,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'JustCloudflareCacheManagement\\' => 
        array (
            0 => __DIR__ . '/../..' . '/source',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6a9db6f85b0dce0225e42595ef7cdae8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6a9db6f85b0dce0225e42595ef7cdae8::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6a9db6f85b0dce0225e42595ef7cdae8::$classMap;

        }, null, ClassLoader::class);
    }
}
