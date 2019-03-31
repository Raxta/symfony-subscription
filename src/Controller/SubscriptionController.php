<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use App\Entity\User;
use App\Events\SubscribeEvent;
use App\Events\Subscribers\SubscriptionSubscriber;

class SubscriptionController extends AbstractController
{
    protected $eventDispatcher;

    public function __construct(\Swift_Mailer $mailer)
    {
        $subscriber = new SubscriptionSubscriber($mailer);
        $dispatcher = new EventDispatcher();
        $this->eventDispatcher = $dispatcher;
        $this->eventDispatcher->addSubscriber($subscriber);
    }

    /** @Route("/subscribe/{userId}", name="subscribe") */
    public function subscribe(int $userId)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        /** @var User $subsribedUser */
        $subsribedUser = $userRepository->find($userId);
        if (!is_null($currentUser) && !is_null($subsribedUser)) {
            $currentUser->addSubscription($subsribedUser);
            $em->persist($currentUser);
            $em->flush();

            $notifyText = $this->renderView(
                'emails/subscription.html.twig',
                [
                    'subscriber' => $currentUser,
                    'subscribed' => $subsribedUser
                ]
            );
            $subscribeEvent = new SubscribeEvent(
                $currentUser,
                $subsribedUser,
                $notifyText
            );
            $this->eventDispatcher->dispatch(SubscribeEvent::NAME, $subscribeEvent);
        }

        return $this->redirectToRoute('user_list');
    }

    /** @Route("/unsubscribe/{userId}", name="unsubscribe") */
    public function unsubscribe(int $userId)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        /** @var User $unsubsribedUser */
        $unsubsribedUser = $userRepository->find($userId);
        if (!is_null($currentUser) && !is_null($unsubsribedUser)) {
            $currentUser->removeSubscription($unsubsribedUser);
            $em->persist($currentUser);
            $em->flush();
        }

        return $this->redirectToRoute('user_list');
    }
}