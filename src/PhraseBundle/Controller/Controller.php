<?php

namespace PhraseBundle\Controller;


use PhraseBundle\Entity\Phrase;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

class Controller extends BaseController
{
    public function enforceOwnerSecurity(Phrase $phrase)
    {
        $user = $this->getUser();

        if ($user != $phrase->getOwner()) {
            throw $this->createAccessDeniedException('You are not the owner!!!');
        }
    }

    public function getSecurityContext()
    {
        return $this->container->get('security.authorization_checker');
    }
}
