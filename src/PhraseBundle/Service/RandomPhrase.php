<?php

namespace PhraseBundle\Service;

use Doctrine\ORM\EntityManager;

class RandomPhrase
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getPhrase()
    {
        $randomPhrase = $this->em->getRepository('PhraseBundle:Phrase')->getRandomPhrase();

        return reset($randomPhrase);
    }
}