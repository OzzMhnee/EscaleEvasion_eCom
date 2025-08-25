<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function searchEngine(string $query): array
    {
        $qb = $this->createQueryBuilder("p"); // Création d'un QueryBuilder pour l'entité Product (alias 'p')

        $words = preg_split('/\s+/', trim($query)); // On découpe la requête en mots séparés par des espaces

        $i = 0;

        foreach ($words as $word) {// Pour chaque mot saisi par l'utilisateur
            $param = 'word' . $i; // On crée un nom de paramètre unique, nécessaire pour le l'ajout aux valeurs keys/valeurs de $qb
            $qb->andWhere("(p.name LIKE :$param OR p.description LIKE :$param)"); // On ajoute LA condition : le nom OU la description doit contenir ce mot
            $qb->setParameter($param, '%' . $word . '%'); //  % : les jokers sont là pour la recherche partielle
            $i++;
        }

        // On exécute la requête et on retourne les résultats
        return $qb->getQuery()->getResult();
    }

    public function findAvailableBetweenDatesQuery($startDate, $endDate)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->andWhere('p.isAvailable = true')
            ->andWhere('NOT EXISTS (
                SELECT 1 FROM App\Entity\Reservation r
                WHERE r.product = p
                  AND r.status IN (:statuses)
                  AND r.startDate < :endDate
                  AND r.endDate > :startDate
            )')
            ->setParameter('startDate', new \DateTime($startDate))
            ->setParameter('endDate', new \DateTime($endDate))
            ->setParameter('statuses', ['en attente', 'confirmée']);
        return $qb->getQuery();
    }
    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
