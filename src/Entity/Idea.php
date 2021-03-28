<?php

namespace App\Entity;

use App\Repository\IdeaRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=IdeaRepository::class)
 */
class Idea
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Vous devez donner un titre à votre idée")
     * @Assert\Length(max=255, maxMessage="Le titre ne peut dépasser 255 caractères")
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @Assert\NotBlank(message="Vous devez donner une description à votre idée")
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $author;

    /**
     * @ORM\Column(type="integer")
     */
    private $thumbUp = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $thumbDown = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $note = 0;


    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="idea", cascade="remove")
     */
    private $votes;

    public function __construct()
    {
        $this->votes = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return mixed
     */
    public function getThumbUp()
    {
        return $this->thumbUp;
    }

    /**
     * @param mixed $thumbUp
     */
    public function setThumbUp($thumbUp): void
    {
        $this->thumbUp = $thumbUp;
    }

    /**
     * @return mixed
     */
    public function getThumbDown()
    {
        return $this->thumbDown;
    }

    /**
     * @param mixed $thumbDown
     */
    public function setThumbDown($thumbDown): void
    {
        $this->thumbDown = $thumbDown;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note): void
    {
        $this->note = $note;
    }

    /**
     * @return PersistentCollection
     */
    public function getVotes(): PersistentCollection
    {
        return $this->votes;
    }

    /**
     * @param PersistentCollection $votes
     */
    public function setVotes(PersistentCollection $votes): void
    {
        $this->votes = $votes;
    }




}
