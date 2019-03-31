<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Entity\User;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function getPostsForUser(?User $user)
    {
        if (is_null($user)) {
            return [];
        }

        $subscriptionIDs = array_map(
            function ($item) {
                /** @var User $item */
                return $item->getId();
            },
            $user->getSubscriptions()->toArray()
        );

        return $this->createQueryBuilder('p')
            ->where('p.author in (:authors)')
            ->setParameter('authors', $subscriptionIDs)
            ->orderBy('p.publish_date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
