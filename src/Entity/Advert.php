<?php

namespace App\Entity;

use App\Repository\AdvertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass=AdvertRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *     fields={"title"},
 *     message="Ce titre a déjà été choisi, veuillez choisir un autre."
 * )
 */
class Advert
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $published = true;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="adverts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToOne(targetEntity=Image::class, cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="adverts")
     * @Assert\Valid()
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity=AdvertSkill::class, mappedBy="advert", orphanRemoval=true)
     * @Assert\Valid()
     */
    private $advertSkills;

    /**
     * @ORM\OneToMany(targetEntity=Application::class, mappedBy="advert", orphanRemoval=true)
     * @Assert\Valid()
     */
    private $applications;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbApplications = 0;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=View::class, mappedBy="advert", orphanRemoval=true)
     * @Assert\Valid()
     */
    private $views;

    /**
     * @Assert\Callback
     * @param ExecutionContextInterface $context
     */
    public function isContentValid(ExecutionContextInterface $context): void
    {
        $forbiddenWords = ['démotivation', 'abandon'];

        // On vérifie que le contenu ne contient pas l'un des mots
        if (preg_match('#'.implode('|', $forbiddenWords).'#', $this->getContent())) {
            // La règle est violée, on définit l'erreur
            $context
                ->buildViolation('Contenu invalide car il contient un mot interdit (démotivation ou abandon).') // message
                ->atPath('content')  // attribut de l'objet qui est violé
                ->addViolation() // ceci déclenche l'erreur
            ;
        }
    }

    public function increaseApplication(): void
    {
        $this->nbApplications++;
    }

    public function decreaseApplication(): void
    {
        $this->nbApplications--;
    }

    /**
     * Advert constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->applications = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->advertSkills = new ArrayCollection();
        $this->views = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->title. ' ' . $this->content;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function updateDate(): void
    {
        $this->setUpdatedAt(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|Application[]
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications[] = $application;
            $application->setAdvert($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->contains($application)) {
            $this->applications->removeElement($application);
            // set the owning side to null (unless already changed)
            if ($application->getAdvert() === $this) {
                $application->setAdvert(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    /**
     * @return Collection|AdvertSkill[]
     */
    public function getAdvertSkills(): Collection
    {
        return $this->advertSkills;
    }

    public function addAdvertSkill(AdvertSkill $advertSkill): self
    {
        if (!$this->advertSkills->contains($advertSkill)) {
            $this->advertSkills[] = $advertSkill;
            $advertSkill->setAdvert($this);
        }

        return $this;
    }

    public function removeAdvertSkill(AdvertSkill $advertSkill): self
    {
        if ($this->advertSkills->contains($advertSkill)) {
            $this->advertSkills->removeElement($advertSkill);
            // set the owning side to null (unless already changed)
            if ($advertSkill->getAdvert() === $this) {
                $advertSkill->setAdvert(null);
            }
        }

        return $this;
    }

    public function getNbApplications(): ?int
    {
        return $this->nbApplications;
    }

    public function setNbApplications(int $nbApplications): self
    {
        $this->nbApplications = $nbApplications;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|View[]
     */
    public function getViews(): Collection
    {
        return $this->views;
    }

    public function addView(View $view): self
    {
        if (!$this->views->contains($view)) {
            $this->views[] = $view;
            $view->setAdvert($this);
        }

        return $this;
    }

    public function removeView(View $view): self
    {
        if ($this->views->contains($view)) {
            $this->views->removeElement($view);
            // set the owning side to null (unless already changed)
            if ($view->getAdvert() === $this) {
                $view->setAdvert(null);
            }
        }

        return $this;
    }

}
