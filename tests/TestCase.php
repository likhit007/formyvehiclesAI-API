<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     */
    public function createApplication()
    {
        $cachedConfig = Application::inferBasePath().'/bootstrap/cache/config.php';
        if (file_exists($cachedConfig)) {
            @unlink($cachedConfig);
        }

        $cachedRoutes = Application::inferBasePath().'/bootstrap/cache/routes-v7.php';
        if (file_exists($cachedRoutes)) {
            @unlink($cachedRoutes);
        }

        $app = parent::createApplication();

        // Enforce safety: tests must NEVER run against the development/production database
        $connection = $app['config']->get('database.default');
        $database = $app['config']->get("database.connections.{$connection}.database");

        if ($database === 'vehicles_ai') {
            throw new RuntimeException(
                'Safety protection triggered: Tests are configured to run against the main database [vehicles_ai]. Tests must run against [vehicles_ai_testing]. Aborting to protect your database.'
            );
        }

        return $app;
    }
}
