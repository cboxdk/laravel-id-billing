<?php

declare(strict_types=1);

namespace Cbox\Id\Billing\Tests;

use Cbox\Console\Kit\ConsoleKitServiceProvider;
use Cbox\Console\Kit\Facades\Console;
use Cbox\Id\Billing\BillingServiceProvider;
use Illuminate\Support\ServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @return list<class-string<ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [ConsoleKitServiceProvider::class, BillingServiceProvider::class];
    }

    /**
     * @return array<string, class-string>
     */
    protected function getPackageAliases($app): array
    {
        return ['Console' => Console::class];
    }
}
