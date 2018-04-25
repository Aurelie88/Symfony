<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectRepository")
 */
class Project
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="typeProjet", type="string", length=255)
     */
    private $typeProjet;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var bool
     *
     * @ORM\Column(name="delivery", type="boolean")
     */
    private $delivery;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TimeTracking", mappedBy="project")
     */
    private $timeTrackings;

    public function __construct()
    {
        $this->timeTrackings = new ArrayCollection();
    }

    public function addTimeTracking(\AppBundle\Entity\TimeTracking $timeTracking){
        $this->timeTrackings[]=$timeTracking;
    }

    public function removeProduct(\AppBundle\Entity\TimeTracking $timeTracking){
        $this->timeTrackings->removeElement($timeTracking);
    }

    public function getTimeTrackings(){
        return $this->timeTrackings;
    }
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
     * Set titre
     *
     * @param string $titre
     *
     * @return Project
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set typeProjet
     *
     * @param string $typeProjet
     *
     * @return Project
     */
    public function setTypeProjet($typeProjet)
    {
        $this->typeProjet = $typeProjet;

        return $this;
    }

    /**
     * Get typeProjet
     *
     * @return string
     */
    public function getTypeProjet()
    {
        return $this->typeProjet;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Project
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Project
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set delivery
     *
     * @param boolean $delivery
     *
     * @return Project
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;

        return $this;
    }

    /**
     * Get delivery
     *
     * @return bool
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * Set timeTracking
     *
     */
    public function setTimeTracking($timeTracking)
    {
        $this->timeTracking = $timeTracking;

        return $this;
    }

    /**
     * Get timeTracking
     *
     * @return bool
     */
    public function getTimeTracking()
    {
        return $this->timeTracking;
    }

    public function __toString() {
        return $this->titre;
    }
}

