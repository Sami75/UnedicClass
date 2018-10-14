<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\Student;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation as Doc;


class DepartmentController extends FosRestController
{

    /**
     *
     * @Doc\ApiDoc(
     *     section="Departments",
     *     resource=true,
     *     description="Print a form and then when user click on submit post datas to create the department.",
     *     requirements={
     *          {
     *              "name"="request",
     *              "dataType"="Request",
     *              "description"="Data from the form."
     *          }
     *     }
     * )
     *
     * @Route("/formCreateDp", name="formCreateDp")
     *
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

            $this->addFlash('success', 'La classe a bien été créée.');

            return $this->redirectToRoute('homepage');
        }
        else if($form->isSubmitted()){

            $this->addFlash('danger', 'La saisie du formulaire comporte des erreurs, veuillez les corriger s\'il vous plaît.');

            return $this->render('department/formCreateDp.html.twig', array(
                'form' => $form->createView(),
            )); 
        }          

        return $this->render('department/formCreateDp.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     *
     * @Doc\ApiDoc(
     *     section="Departments",
     *     resource=true,
     *     description="Get the list of departments.",
     *     statusCodes={
     *         200="Returned when there are departments.",
     *         404="Returned when there is any departments."
     *     }
     * )
     *
     * @Rest\View
     *
     */
    public function getDepartmentsAction()
    {
        $repository = $this->getDoctrine()->getRepository(Department::class);
        $departments = $repository->findAll();

        if(count($departments) > 0) {
            $view = $this->view($departments, 200)
            ->setTemplate("department/getDepartments.html.twig")
            ->setTemplateVar("departments")
            ;

            return $this->handleView($view);
        }
        else {
            $this->addFlash('info', 'Aucune classe n\'a été pour le moment créée. <a href="{{ path(formCreateDp) }}">Cliquez ici pour en créer</a>');
            $view = $this->view($departments, 404)
            ->setTemplate("department/getDepartments.html.twig")
            ->setTemplateVar("departments")
            ;

            return $this->handleView($view);
        }
    }

    /**
     *
     * @Doc\ApiDoc(
     *     section="Departments",
     *     resource=true,
     *     description="Get the list of students that own department.",
     *     requirements={
     *          {
     *              "name"="numclasse",
     *              "dataType"="integer",
     *              "requirement"="\d+",
     *              "description"="The department unique identifier."
     *          }
     *     },
     *     statusCodes={
     *         200="Returned when there are students in the department",
     *         404="Returned when there are any students in the department"
     *     }
     * )
     *
     * @Rest\View
     *
     */
    public function getDepartmentAction($numclasse)
    {
        $repository = $this->getDoctrine()->getRepository(Student::class);
        $students = $repository->findBy(
            array('department' => $numclasse),
            array('lastName' => 'asc')
        );

        if(count($students) > 0) {
            $view = $this->view($students, 200)
            ->setTemplate("department/getDepartment.html.twig")
            ->setTemplateVar("students")
            ;

            return $this->handleView($view);
        }
        else {
            $department = $this->getDoctrine()->getRepository(Department::class)->find($numclasse);

            $view = $this->view($department, 404)
            ->setTemplate("department/noStudents.html.twig")
            ->setTemplateVar("department")
            ;

            return $this->handleView($view);
        }
    }       
}