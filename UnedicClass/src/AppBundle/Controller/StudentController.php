<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Student;
use AppBundle\Entity\Department;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Nelmio\ApiDocBundle\Annotation as Doc;

class StudentController extends Controller
{
    /**
     *
     * @Doc\ApiDoc(
     *     section="Students",
     *     resource=true,
     *     description="Print a form and then when user click on submit post datas to create the student.",
     *     requirements={
     *          {
     *              "name"="request",
     *              "dataType"="Request",
     *              "description"="Data from the form."
     *          }
     *     }
     * )
     *
     * @Route("/formCreate", name="formCreate")
     */
    public function formCreate(Request $request)
    {
        $student = new Student();

        $departments = array();
        $departments = $this->getDoctrine()->getRepository(Department::class)->findAll();

        $form = $this->createFormBuilder($student)
        ->add('firstname', TextType::class)
        ->add('lastname', TextType::class)
        ->add('department', EntityType::class, array(
            'class'=>'AppBundle:Department',
            'choice_label'=>'name',
            'expanded'=>false,
            'multiple'=>false,
        ))
        ->getForm();

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $department = $this->getDoctrine()->getRepository(Department::class)->find($data->department);

            $students = $this->getDoctrine()->getRepository(Student::class)->findBy(
                array('department' => $department->id))
            ;

            if($department->capacity == count($students)) {

                $this->addFlash('warning', 'Attention la classe est pleine, inscrivez l\'élève dans une autre classe.');

                return $this->render('student/formCreate.html.twig', array(
                    'form' => $form->createView(), 'departments' => $departments,
                ));

            }
            else {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($student);
                $entityManager->flush();

                $this->addFlash('success', 'L\'élève a bien été créé.');

                return $this->render('student/formCreate.html.twig', array(
                    'form' => $form->createView(), 'departments' => $departments,
                ));
            }


        } else if($form->isSubmitted()) {
            $this->addFlash('danger', 'La saisie du formulaire comporte des erreurs, veuillez les corriger s\'il vous plaît.');

            return $this->render('student/formCreate.html.twig', array(
                'form' => $form->createView(), 'departments' => $departments,
            ));            
        }

        return $this->render('student/formCreate.html.twig', array(
            'form' => $form->createView(), 'departments' => $departments,
        ));
    }

    /**
     *
     * @Doc\ApiDoc(
     *     section="Students",
     *     resource=true,
     *     description="Get the list of students.",
     * )
     *
     * @Route("/getStudents", name="getStudents")
     *
     */
    public function getStudents()
    {

        $repository = $this->getDoctrine()->getRepository(Student::class);
        $students = $repository->findAll();
        if(count($students) > 0) {
           return $this->render('student/getStudents.html.twig', array(
            'students' => $students,
        ));
       } else {
        $this->addFlash('info', 'Aucun élève n\'a été pour le moment créé.');
        return $this->render('student/getStudents.html.twig', array(
            'students' => $students,
        ));
    }
}

    /**
     *
     * @Doc\ApiDoc(
     *     section="Students",
     *     resource=true,
     *     description="Get the detail of a student",
     *     requirements={
     *          {
     *              "name"="numetud",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="The student unique identifier."
     *          }
     *     }
     * )
     *
     * @Route("/getStudent/{numetud}", name="getStudent")
     *
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
     *
     * @Doc\ApiDoc(
     *     section="Students",
     *     resource=true,
     *     description="Delete a student",
     *     requirements={
     *          {
     *              "name"="numetud",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="The student unique identifier."
     *          }
     *     }
     * )
     *
     * @Route("/deleteStudent/{numetud}", name="deleteStudent")
     *
     */
    public function deleteStudent($numetud)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $student = $this->getDoctrine()->getRepository(Student::class)->find($numetud);

        $entityManager->remove($student);
        $entityManager->flush();

        $this->addFlash('success', 'L\'élève a bien été supprimé.');

        $students = $this->getDoctrine()->getRepository(Student::class)->findAll();

        return $this->redirectToRoute('getStudents', array(
            'students' => $students,
        ));
    }

    /**
     *
     * @Doc\ApiDoc(
     *     section="Students",
     *     resource=true,
     *     description="Edit a student",
     *     requirements={
     *          {
     *              "name"="numetud",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="The student unique identifier."
     *          }
     *     }
     * )
     *
     * @Route("/editStudent/{numetud}", name="editStudent")
     */
    public function editStudent(Request $request, $numetud)
    {

        $student = $this->getDoctrine()->getRepository(Student::class)->find($numetud);

        $form = $this->createFormBuilder($student)
        ->add('firstname', TextType::class)
        ->add('lastname', TextType::class)
        ->add('department', EntityType::class, array(
            'class'=>'AppBundle:Department',
            'choice_label'=>'name',
            'expanded'=>false,
            'multiple'=>false,
        ))
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $department = $this->getDoctrine()->getRepository(Department::class)->find($data->department);

            $students = $this->getDoctrine()->getRepository(Student::class)->findBy(
                array('department' => $department->id))
            ;

            if($department->capacity == count($students)) {

                $this->addFlash('warning', 'Attention la classe est pleine, inscrivez l\'élève dans une autre classe.');

                return $this->render('student/editStudent.html.twig', array(
                    'form' => $form->createView(), 'student' => $student,
                ));

            }
            else {

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                $this->addFlash('success', 'L\'élève a bien été edité.');

                return $this->redirectToRoute('getStudents');
            }
        } else if($form->isSubmitted()) {
            $this->addFlash('danger', 'La saisie du formulaire comporte des erreurs, veuillez les corriger s\'il vous plaît.');

            return $this->render('student/editStudent.html.twig', array(
                'form' => $form->createView(), 'student' => $student,
            ));            
        }

        return $this->render('student/editStudent.html.twig', array(
            'student' => $student, 'form' => $form->createView(),
        ));
    }
}
