<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Student
 *
 * @ORM\Table(name="student")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StudentRepository")
 */
class Student
{
    /**
     * @ORM\ManyToOne(targetEntity="Department", inversedBy="students")
     * @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     * @Assert\NotBlank(message="La classe  est obligatoire")
     *
     */
    private $department;

    /**
     * @var int
     *
     * @ORM\Column(name="NumEtud", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="AppBundle\Doctrine\RandomIdGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="FirstName", type="string", length=25)
     *
     * @Assert\NotBlank(message="Le prénom est obligatoire")
     *
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="LastName", type="string", length=25)
     *
     * @Assert\NotBlank(message="Le nom est obligatoire")
     *
     */
    private $lastName;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Student
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Student
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set department
     *
     * @param integer $department_id
     *
     * @return Student
     */
    public function setDepartment($department_id)
    {
        $this->department = $department_id;

        return $this;
    }

    /**
     * Get department
     *
     * @return integer
     */
    public function getDepartment()
    {
        return $this->department;
    }
}

