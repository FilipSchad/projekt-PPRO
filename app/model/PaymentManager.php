<?php

namespace App\Model;

use Nette\Application\UI;
use App\Model\Entities\Payment;

/*
 * Payment management
 */

class PaymentManager
{
    const PAYMENT_ENTITY = '\App\Model\Entities\Payment';
    
    public function __construct(\Kdyby\Doctrine\EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getPayments()
    {
        return $this->em->getRepository($this::PAYMENT_ENTITY)->findBy(array());
    }
    
    public function getPaymentById($id)
    {
        return $this->em->find($this::PAYMENT_ENTITY, $id);
    }
}
