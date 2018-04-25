<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Job
 *
 * @ORM\Table(name="job")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JobRepository")
 */
class Job
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
     * @ORM\Column(name="nomMetier", type="string", length=255)
     */
    private $nomMetier;


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
     * Set nomMetier
     *
     * @param string $nomMetier
     *
     * @return Job
     */
    public function setNomMetier($nomMetier)
    {
        $this->nomMetier = $nomMetier;

        return $this;
    }

    /**
     * Get nomMetier
     *
     * @return string
     */
    public function getNomMetier()
    {
        return $this->nomMetier;
    }

    public function __toString() {
        return $this->nomMetier;
    }
}

