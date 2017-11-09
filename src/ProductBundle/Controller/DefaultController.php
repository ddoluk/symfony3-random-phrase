<?php

namespace ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use ProductBundle\Entity\Product;
use ProductBundle\Form\ProductType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="product")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pdf = $em->getRepository('ProductBundle:Product')->findAll();

        return $this->render('ProductBundle:Default:index.html.twig',array(
            'pdf' => $pdf
        ));
    }

    /**
     * @Route("/new", name="product_new")
     */

    public function newAction(Request $request)
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $file stores the uploaded PDF file
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */

            $em = $this->getDoctrine()->getManager();

            $file = $product->getBrochure();

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('brochures_directory'),
                $fileName
            );

            // Update the 'brochure' property to store the PDF file name
            // instead of its contents
            $product->setBrochure($fileName);

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product');
        }

        return $this->render('ProductBundle:Default:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
