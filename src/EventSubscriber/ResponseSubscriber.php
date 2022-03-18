<?php

namespace Smile\PerfreporterBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => [
                ['addPerfreporter', 0],
            ],
        ];
    }

    public function addPerfreporter(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $response->headers->set('X-Header-Set-By', 'Perfs Reporter');
    }
}