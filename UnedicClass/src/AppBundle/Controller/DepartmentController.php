<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class DepartmentController extends Controller
{
	/**
     * @Route("/formCreateDp", name="formCreateDp")
     */
    public function formCreateDp(Request $request)
    {
        $department = new Department();

        $form = $this->createFormBuilder($department)
        ->add('name', TextType::class)
        ->add('capacity', IntegerType::class)
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($department);
            $entityManager->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('department/formCreateDp.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}