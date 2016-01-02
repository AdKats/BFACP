<?php

class BaseTestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $unitTesting = true;

        $testEnvironment = 'testing';

        putenv('DB_DEFAULT=circle');

        return require __DIR__ . '/../../bootstrap/start.php';
    }
}
