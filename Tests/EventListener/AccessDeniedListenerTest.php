<?php

/*
 * This file is part of the FOSRestBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\RestBundle\Tests\EventListener;

use FOS\RestBundle\EventListener\AccessDeniedListener;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * AccessDeniedListenerTest
 *
 * @author Boris Guéry <guery.b@gmail.com>
 */
class AccessDeniedListenerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Symfony\Component\HttpFoundation\Request')) {
            $this->markTestSkipped('The "HttpFoundation" component is not available');
        }

        if (!class_exists('Symfony\Component\Security\Core\Exception\AccessDeniedException')) {
            $this->markTestSkipped('The "Security" component is not available');
        }
    }

    public function testAccessDeniedExceptionIsConvertedToAnAccessDeniedHttpException()
    {
        $request = new Request();
        $exception = new AccessDeniedException();
        $event = new GetResponseForExceptionEvent(new TestKernel(), $request, 'foo', $exception);

        $listener = new AccessDeniedListener('foo');
        $listener->onKernelException($event);
        $this->assertInstanceOf('Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException', $event->getException());
    }

    public function testCommonExceptionsAreBypassed()
    {
        $request = new Request();
        $exception = new \Exception('foo');
        $event = new GetResponseForExceptionEvent(new TestKernel(), $request, 'foo', $exception);

        $listener = new AccessDeniedListener('foo');
        $listener->onKernelException($event);
        $this->assertSame($exception, $event->getException());
    }
}

class TestKernel implements HttpKernelInterface
{
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        return new Response('foo');
    }
}
