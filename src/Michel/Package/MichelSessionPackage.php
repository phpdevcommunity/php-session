<?php

namespace PhpDevCommunity\Session\Michel\Package;

use PhpDevCommunity\Michel\Package\PackageInterface;
use PhpDevCommunity\Session\Storage\NativeSessionStorage;
use PhpDevCommunity\Session\Storage\SessionStorageInterface;
use Psr\Container\ContainerInterface;

final class MichelSessionPackage implements PackageInterface
{

    public function getDefinitions(): array
    {
        return [
            SessionStorageInterface::class => static function (ContainerInterface $container) {
                $pathSession = $container->get('session.save_path');
                if (($pathSession[0] ?? '') !== '/') {
                    $pathSession = $container->get('michel.project_dir') . DIRECTORY_SEPARATOR . $pathSession;
                }

                return new NativeSessionStorage([
                    'save_path' => $pathSession,
                    'cookie_lifetime' => $container->get('session.cookie_lifetime'),
                    'gc_maxlifetime' => $container->get('session.gc_maxlifetime'),
                    'cookie_secure' => $container->get('session.cookie_secure'),
                    'cookie_httponly' => $container->get('session.cookie_httponly'),
                    'use_strict_mode' => $container->get('session.use_strict_mode'),
                    'use_only_cookies' => $container->get('session.use_only_cookies'),
                    'sid_length' => $container->get('session.sid_length'),
                    'sid_bits_per_character' => $container->get('session.sid_bits_per_character'),
                    'cookie_samesite' => $container->get('session.cookie_samesite'),
                ]);
            }
        ];
    }

    public function getParameters(): array
    {
        return [
            'session.save_path' => getenv('SESSION_SAVE_PATH') ?: 'var/session', // Default path for session storage
            'session.cookie_lifetime' => self::getEnv('SESSION_COOKIE_LIFETIME') ?? 86400, // Cookie lifetime (24 hours)
            'session.gc_maxlifetime' => self::getEnv('SESSION_GC_MAXLIFETIME') ?? 604800, // Server-side session lifetime (7 days)
            'session.cookie_secure' => self::getEnv('SESSION_COOKIE_SECURE') === true, // Cookie is only transmitted via HTTPS
            'session.cookie_httponly' => self::getEnv('SESSION_COOKIE_HTTPONLY') === true, // Prevents JavaScript access to the cookie
            'session.use_strict_mode' => self::getEnv('SESSION_USE_STRICT_MODE') === true, // Rejects invalid SIDs
            'session.use_only_cookies' => self::getEnv('SESSION_USE_ONLY_COOKIES') === true, // Prevents using SIDs in the URL
            'session.sid_length' => self::getEnv('SESSION_SID_LENGTH') ?? 64, // Secure SID length
            'session.sid_bits_per_character' => self::getEnv('SESSION_SID_BITS_PER_CHARACTER') ?? 6, // Bits per character (6 for maximum security)
            'session.cookie_samesite' => self::getEnv('SESSION_COOKIE_SAMESITE') ?? 'Strict', // Protection against CSRF attacks
        ];
    }

    private static  function getEnv(string $name)
    {
        return $_ENV[$name] ?? null;
    }

    public function getRoutes(): array
    {
        return [];
    }

    public function getListeners(): array
    {
        return [];
    }

    public function getCommands(): array
    {
        return [];
    }
}
