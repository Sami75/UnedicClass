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

        $form = $this->createFormBuilder($student)
        ->add('firstname', TextType::class)
        ->add('lastname', TextType::class)
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('student/formCreate.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/getStudents", name="getStudents")
     */
    public function getStudents()
    {

        $repository = $this->getDoctrine()->getRepository(Student::class);
        $students = $repository->findAll();
        return $this->render('student/getStudents.html.twig', array(
            'students' => $students,
        ));
    }

    /**
     * @Route("/getStudent/{numetud}", name="getStudent")
     */
    public function getStudent($numetud)
    {

        $repository = $this->getDoctrine()->getRepository(Student::class);
        $student = $repository->find($numetud);
        return $this->render('student/getStudent.html.twig', array(
            'student' => $student,
        ));
    }

    /**
     * @Route("/deleteStudent/{numetud}", name="deleteStudent")
     */
    public function deleteStudent($numetud)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $student = $this->getDoctrine()->getRepository(Student::class)->find($numetud);

        $entityManager->remove($student);
        $entityManager->flush();

        $students = $this->getDoctrine()->getRepository(Student::class)->findAll();

        return $this->redirectToRoute('getStudents', array(
            'students' => $students,
        ));
    }

    /**
     * @Route("/editStudent/{numetud}", name="editStudent")
     */
    public function editStudent(Request $request, $numetud)
    {

        $student = $this->getDoctrine()->getRepository(Student::class)->find($numetud);

        $form = $this->createFormBuilder($student)
        ->add('firstname', TextType::class)
        ->add('lastname', TextType::class)
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('getStudents');
        }

        return $this->render('student/editStudent.html.twig', array(
            'student' => $student, 'form' => $form->createView(),
        ));
    }
}
