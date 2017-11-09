<?php

namespace PhraseBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use PhraseBundle\Entity\Phrase;
use PhraseBundle\Form\PhraseType;

class PhraseController extends Controller
{
    /**
     * @Route("/phrase", name="phrase_list")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $criteria = ($this->getSecurityContext()->isGranted('ROLE_ADMIN'))
            ? array()
            : array('owner' => $this->getUser());

        $phrases = $em->getRepository('PhraseBundle:Phrase')->findBy($criteria, array('created'=>'DESC'));

        return $this->render('PhraseBundle:Phrase:index.html.twig', array(
            'phrases' => $phrases,
        ));
    }

    /**
     * @Route("/phrase/new", name="phrase_new")
     */
    public function newAction()
    {
        $phrase = new Phrase();

        $form = $this->createCreateForm($phrase);

        return $this->render('PhraseBundle:Phrase:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/phrase/create", name="phrase_create")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $phrase = new Phrase();
        $form = $this->createCreateForm($phrase);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $owner = $this->getUser();
            $phrase->setOwner($owner);

            $em = $this->getDoctrine()->getManager();
            $em->persist($phrase);
            $em->flush();

            $this->addFlash('notice', 'Phrase was add!');

            return $this->redirectToRoute('phrase_show', array('id' => $phrase->getId()));
        }

        $this->addFlash('notice', 'Something went wrong');

        return $this->redirectToRoute('phrase_new');
    }

    /**
     * @Route("/phrase/{id}/show", name="phrase_show")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $showPhrase = $em->getRepository('PhraseBundle:Phrase')->find($id);

        return $this->render('PhraseBundle:Phrase:show.html.twig', array(
            'phrase' => $showPhrase,
        ));
    }

    /**
     * @Route("/phrase/{id}/edit", name="phrase_edit")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $phrase = $em->getRepository('PhraseBundle:Phrase')->find($id);

        $this->enforceOwnerSecurity($phrase);

        $form = $this->createEditForm($phrase);

        return $this->render('PhraseBundle:Phrase:edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/phrase/{id}/update", name="phrase_update")
     * @Method({"POST","PUT"})
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $phrase = $em->getRepository('PhraseBundle:Phrase')->find($id);

        $this->enforceOwnerSecurity($phrase);

        $form = $this->createEditForm($phrase);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em->flush();

            $this->addFlash('notice', 'Phrase was update');

            return $this->redirectToRoute('phrase_show', array('id' => $phrase->getId()));
        }

        $this->addFlash('notice', 'Something went wrong');

        return $this->redirectToRoute('phrase_edit');
    }

    /**
     * @Route("/phrase/{id}/delete", name="phrase_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $phrase = $em->getRepository('PhraseBundle:Phrase')->find($id);

        $this->enforceOwnerSecurity($phrase);

        $em->remove($phrase);
        $em->flush();

        return $this->redirectToRoute('phrase_list');
    }

    private function createCreateForm($phrase)
    {
        $form = $this->createForm(PhraseType::class, $phrase, array(
            'action' => $this->generateUrl('phrase_create'),
        ));

        $form->add('submit', SubmitType::class, array(
            'label' => 'Create'
        ));

        return $form;
    }

    private function createEditForm($phrase)
    {
        $form = $this->createForm(PhraseType::class, $phrase, array(
            'action' => $this->generateUrl('phrase_update', array('id' => $phrase->getId()))
        ));

        $form->add('submit', SubmitType::class, array(
            'label' => 'Update'
        ));

        return $form;
    }
}
