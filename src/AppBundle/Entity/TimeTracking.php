<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TimeTracking
 *
 * @ORM\Table(name="time_tracking")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TimeTrackingRepository")
 */
class TimeTracking
{
    public function __construct()
    {
        $this->createAt = new \DateTime();
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Employee", inversedBy="time_tracking")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idEmployee;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project", inversedBy="time_tracking")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idProject;

    /**
     * @var int
     *
     * @ORM\Column(name="nbJour", type="integer")
     */
    private $nbJour;

    /**
     *@ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createAt;

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
     * Set idEmployee
     *
     * @param integer $idEmployee
     *
     * @return TimeTracking
     */
    public function setIdEmployee($idEmployee)
    {
        $this->idEmployee = $idEmployee;

        return $this;
    }

    /**
     * Get idEmployee
     *
     * @return int
     */
    public function getIdEmployee()
    {
        return $this->idEmployee;
    }

    /**
     * Set idProject
     *
     * @param integer $idProject
     *
     * @return TimeTracking
     */
    public function setIdProject($idProject)
    {
        $this->idProject = $idProject;

        return $this;
    }

    /**
     * Get idProject
     *
     * @return int
     */
    public function getIdProject()
    {
        return $this->idProject;
    }

    /**
     * Set nbJour
     *
     * @param integer $nbJour
     *
     * @return TimeTracking
     */
    public function setNbJour($nbJour)
    {
        $this->nbJour = $nbJour;

        return $this;
    }

    /**
     * Get nbJour
     *
     * @return int
     */
    public function getNbJour()
    {
        return $this->nbJour;
    }

    public function __toString() {
        return $this->nbJour." jour";
    }
}

