<?php

namespace App\Events;

use Symfony\Component\EventDispatcher\Event;
use App\Entity\User;

class SubscribeEvent extends Event
{
    public const NAME = 'user.subscribe';

    protected $subscriber;

    protected $subscribed;

    protected $notifyText;

    public function __construct(User $subscriber, User $subscribed, string $notifyText)
    {
        $this->subscriber = $subscriber;
        $this->subscribed = $subscribed;
        $this->notifyText = $notifyText;
    }

    public function getSubscriber()
    {
        return $this->subscriber;
    }

    public function getSubscribed()
    {
        return $this->subscribed;
    }

    public function getNotifyText()
    {
        return $this->notifyText;
    }
}