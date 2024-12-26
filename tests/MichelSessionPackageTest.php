<?php

namespace Test\PhpDevCommunity\Session;

use PhpDevCommunity\Session\Michel\Package\MichelSessionPackage;
use PhpDevCommunity\Session\Storage\NativeSessionStorage;
use PhpDevCommunity\Session\Storage\SessionStorageInterface;
use PhpDevCommunity\UniTester\TestCase;
use Psr\Container\ContainerInterface;
use RuntimeException;

final class MichelSessionPackageTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        session_destroy();
        foreach (glob( __DIR__ . '/var/session/*') as $file) {
            unlink($file);
        }
    }

    protected function execute(): void
    {
        $container = new class implements ContainerInterface {
            private array $definitions = [];
            private array $values = [];

            public function __construct()
            {
                $package = new MichelSessionPackage();
                $definitions = $package->getDefinitions();
                $parameters = $package->getParameters();
                $this->definitions = $definitions + $parameters + [
                        ContainerInterface::class => $this,
                        'michel.project_dir' => __DIR__,
                        'session.save_path' => 'var/session',
                    ];
            }

            public function get(string $id)
            {
                if (!$this->has($id)) {
                    throw new RuntimeException('Unknown definition: ' . $id);
                }

                if (isset($this->values[$id])) {
                    return $this->values[$id];
                }

                $value = $this->definitions[$id];
                if (is_callable($value)) {
                    $value = $value($this);
                }

                $this->values[$id] = $value;
                return $value;
            }

            public function has(string $id): bool
            {
                return isset($this->definitions[$id]);
            }
        };

        $sessionStorage = $container->get(SessionStorageInterface::class);
        $this->assertInstanceOf(NativeSessionStorage::class, $sessionStorage);

        $sessionStorage->put('username', 'myName');
        $this->assertTrue($sessionStorage->has('username'));
        $this->assertTrue(!empty(glob( __DIR__ . '/var/session/*')));
    }
}
