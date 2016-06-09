<?php

namespace Tests\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class DefaultControllerTest extends AbstractControllerTest
{
    public function testIndex()
    {
        $this->isSuccessful(Request::METHOD_GET, '/');
    }
}
