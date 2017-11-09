<?php

namespace PhraseBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Template()
     * @Route("/", name="phrase_random")
     */
    public function indexAction()
    {
        $randomPhrase = $this->container->get('app.random.phrase')->getPhrase();

        return array(
            'phrase' => $randomPhrase
        );
    }
}
