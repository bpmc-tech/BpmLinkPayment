<?php
namespace Plugin\BpmLinkPayment\Repository;

use Eccube\Repository\AbstractRepository;
use Plugin\BpmLinkPayment\Entity\PaymentStatus;
// use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Persistence\ManagerRegistry;

class PaymentStatusRepository extends AbstractRepository
{
    // public function __construct(RegistryInterface $registry)
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentStatus::class);
    }
}