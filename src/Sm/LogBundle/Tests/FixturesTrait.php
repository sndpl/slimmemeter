<?php

namespace Sm\LogBundle\Tests;

trait FixturesTrait
{
    protected function getFixture($fileName)
    {
        return file_get_contents(__DIR__ . '/../Resources/fixtures/' . $fileName);
    }
}
