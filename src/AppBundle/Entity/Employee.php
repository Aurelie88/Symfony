<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Employee
 *
 * @ORM\Table(name="employee")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmployeeRepository")
 */
class Employee
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
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom;

    /**
     * @var string
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Job", cascade={"persist", "merge"})
     */
    private $metier;

    /**
     * @var float
     * @ORM\Column(name="cout", type="float")
     */
    private $cout;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="embauche", type="datetime")
     */
    private $embauche;

    /**
     * @var bool
     *
     * @ORM\Column(name="archive", type="boolean")
     */
    private $archive;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TimeTracking", mappedBy="employee")
     */
    private $timeTrackings;

    public function __construct()
    {
        $this->timeTrackings = new ArrayCollection();
    }

    public function addTimeTracking(\AppBundle\Entity\TimeTracking $timeTracking){
        $this->timeTrackings[]=$timeTracking;

        return $this;
    }

    public function removeTimeTracking(\AppBundle\Entity\TimeTracking $timeTracking){
        $this->timeTrackings->removeElement($timeTracking);
    }

    public function getTimeTrackings()
    {
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
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Employee
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Employee
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Employee
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set metier
     *
     * @param string $metier
     *
     * @return Employee
     */
    public function setMetier($metier)
    {
        $this->metier = $metier;

        return $this;
    }

    /**
     * Get metier
     *
     * @return string
     */
    public function getMetier()
    {
        return $this->metier;
    }

    /**
     * Set cout
     *
     * @param float $cout
     *
     * @return Employee
     */
    public function setCout($cout)
    {
        $this->cout = $cout;

        return $this;
    }

    /**
     * Get cout
     *
     * @return float
     */
    public function getCout()
    {
        return $this->cout;
    }

    /**
     * Set embauche
     *
     * @param \DateTime $embauche
     *
     * @return Employee
     */
    public function setEmbauche($embauche)
    {
        $this->embauche = $embauche;

        return $this;
    }

    /**
     * Get embauche
     *
     * @return \DateTime
     */
    public function getEmbauche()
    {
        return $this->embauche;
    }

    /**
 * Set archive
 *
 * @param boolean $delivery
 *
 * @return Project
 */
    public function setArchive($archive)
    {
        $this->archive = $archive;

        return $this;
    }

    /**
     * Get archive
     *
     * @return bool
     */
    public function getArchive()
    {
        return $this->archive;
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

    public function __toString() {
        return strtoupper($this->nom)." ".$this->prenom;
    }

}

