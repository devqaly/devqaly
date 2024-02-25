<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use tests\Support\UsesSubscriptionTrait;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Boot the testing helper traits.
     *
     */
    protected function setUpTraits(): void
    {
        $uses = parent::setUpTraits();

        if (isset($uses[UsesSubscriptionTrait::class])) {
            $this->setupProperties();
        }
    }
}
