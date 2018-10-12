<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Student;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class StudentController extends Controller
{
    /**
     * @Route("/formCreate", name="formCreate")
     */
    public function formCreate(Request $request)
    {
        $student = new Student();
        $student->setFirstName('Prenom de l\'élève');
        $student->setLastName('Nom de l\'élève');

        $form = $this->createFormBuilder($student)
        ->add('firstname', TextType::class, array('label' => 'Prénom'))
        ->add('lastname', TextType::class, array('label' => 'Nom'))
        ->add('save', SubmitType::class, array('label' => 'Créer'))
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $student = $form->getData();
            $rand1=random_int(10000, 32767);
            $rand2=random_int(10000, 32767);
            $student->setNumEtud($rand1.$rand2);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('student/formCreate.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
