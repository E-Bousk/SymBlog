<?php

namespace App\Repository;

use App\Entity\Articles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Articles|null find($id, $lockMode = null, $lockVersion = null)
 * @method Articles|null findOneBy(array $criteria, array $orderBy = null)
 * @method Articles[]    findAll()
 * @method Articles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticlesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articles::class);
    }

    /**
     * Retourne une liste d'articles pour l'API
     * 
     * @return array 
     */
    public function apiFindAll(): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.id', 'a.title', 'a.content', 'a.featured_image', 'a.created_at')
            ->orderBy('a.created_at', 'DESC')
        ;

        $query = $qb->getQuery();

        return $query->execute();
    }
}
