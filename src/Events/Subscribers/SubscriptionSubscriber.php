<?php

namespace App\Events\Subscribers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Events\SubscribeEvent;
use Twig\Compiler;

class SubscriptionSubscriber implements EventSubscriberInterface
{
    protected $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            SubscribeEvent::NAME => 'onSubscribeEvent'
        ];
    }

    public function onSubscribeEvent(SubscribeEvent $event)
    {
        $subscribed = $event->getSubscribed();
        $notifyText = $event->getNotifyText();

        $toEmail = $subscribed->getEmail();

        $message = (new \Swift_Message('Subscription'))
            ->setFrom('no-reply@example.com')
            ->setTo($toEmail)
            ->setBody(
                $notifyText,
                'text/html'
            );

        $this->mailer->send($message);
        
        return true;
    }
}

