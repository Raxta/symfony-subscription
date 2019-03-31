<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Entity\Post;
use App\Form\NewPostFormType;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {
        $currentUser = $this->getUser();

        $post = new Post();
        $newPostForm = $this->createForm(NewPostFormType::class, $post);
        $newPostForm->handleRequest($request);

        if (!is_null($currentUser) && $newPostForm->isSubmitted() && $newPostForm->isValid()) {
            $post->setPublishDate(new \DateTime());
            $post->setAuthorId($currentUser);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            unset($post);
            unset($newPostForm);
            $post = new Post();
            $newPostForm = $this->createForm(NewPostFormType::class, $post);
        }

        $postsRepository = $this->getDoctrine()->getRepository(Post::class);
        $posts = $postsRepository->getPostsForUser($currentUser);

        return $this->render(
            'index.html.twig',
            [
                'posts' => $posts,
                'newPostForm' => $newPostForm->createView()
            ]
        );
    }

    /**
     * @Route("/users", name="user_list")
     */
    public function userList()
    {
        $currentUser = $this->getUser();
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $users = $userRepository->getAnotherUsers($currentUser ? $currentUser->getId() : null);
        $subscriptionIDs = array_map(
            function ($item) {
                return $item->getId();
            },
            $currentUser->getSubscriptions()->toArray()
        );

        return $this->render(
            'users.html.twig',
            [
                'users' => $users,
                'subscriptions' => $subscriptionIDs
            ]
        );
    }
}

