<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use UserBundle\Form\UserType;

class UserController extends Controller
{
    /**
     * @Route("/user", name="user_list")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('UserBundle:User')->findAll();

        return $this->render('UserBundle:User:index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * @Route("/user/{id}/show",name="user_show")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('UserBundle:User')->find($id);

        return $this->render('UserBundle:User:show.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * @Route("/user/new", name="user_new")
     */
    public function newAction(Request $request)
    {
        $user = new User();

        $form = $this->createCreateForm($user);

        return $this->render('UserBundle:User:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/user/create", name="user_create")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $user = new User();

        $form = $this->createCreateForm($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('notice', 'User was add');

            return $this->redirectToRoute('user_show', array('id' => $user->getId()));
        }

        $this->addFlash('notice', 'Something went wrong.');

        return $this->redirectToRoute('user_new');
    }

    /**
     * @Route("/user/{id}/edit", name="user_edit")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('UserBundle:User')->find($id);

        $form = $this->createUpdateForm($user);

        return $this->render('UserBundle:User:edit.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));
    }

    /**
     * @Route("/user/{id}/update", name="user_update")
     * @Method({"POST","PUT"})
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('UserBundle:User')->find($id);

        $form = $this->createUpdateForm($user);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em->flush();

            $this->addFlash('notice', 'User was update.');

            return $this->redirectToRoute('user_show', array('id' => $user->getId()));
        }

        $this->addFlash('notice', 'Something went wrong.');

        return $this->redirectToRoute('user_edit');
    }

    /**
     * @Route("/user/{id}/delete", name="user_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('UserBundle:User')->find($id);

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('user_list');
    }

    private function createCreateForm($user)
    {
        $form = $this->createForm(UserType::class, $user, array(
            'action' => $this->generateUrl('user_create')
        ));

        $form->add('submit', SubmitType::class, array(
            'label' => 'Create'
        ));

        return $form;
    }

    private function createUpdateForm($user)
    {
        $form = $this->createForm(UserType::class, $user, array(
            'action' => $this->generateUrl('user_update', array('id' => $user->getId()))
        ));

        $form->add('submit', SubmitType::class, array(
            'label' => 'Update'
        ));

        return $form;
    }

}
