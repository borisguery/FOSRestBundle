<?php

/*
 * This file is part of the FOSRestBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\RestBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * This listener handles ensures that for specific formats AccessDeniedExceptions
 * will return a 403 regardless of how the firewall is configured
 *
 * @author Lukas Kahwe Smith <smith@pooteeweet.org>
 * @author Boris Guéry <guery.b@gmail.com>
 */
class AccessDeniedListener extends ExceptionListener
{
    /**
     * @param GetResponseForExceptionEvent $event The event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        static $handling;

        if (true === $handling) {
            return false;
        }

        $handling = true;

        $exception = $event->getException();
        if (!$exception instanceof AccessDeniedException) {
            return;
        }

        $exception = new AccessDeniedHttpException("You don't have the necessary permissions", $exception);
        $event->setException($exception);

        return parent::onKernelException($event);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => array('onKernelException', 5),
        );
    }
}
