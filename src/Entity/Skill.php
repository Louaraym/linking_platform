<?php

namespace App\Entity;

use App\Repository\SkillRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SkillRepository::class)
 * @UniqueEntity(
 *     fields={"name"},
 *     message="Ce nom a déjà été choisi, veuillez choisir un autre."
 * )
 */
class Skill
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=AdvertSkill::class, mappedBy="skill", orphanRemoval=true)
     */
    private $advertSkills;

    public function __construct()
    {
        $this->advertSkills = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
            $advertSkill->setSkill($this);
        }

        return $this;
    }

    public function removeAdvertSkill(AdvertSkill $advertSkill): self
    {
        if ($this->advertSkills->contains($advertSkill)) {
            $this->advertSkills->removeElement($advertSkill);
            // set the owning side to null (unless already changed)
            if ($advertSkill->getSkill() === $this) {
                $advertSkill->setSkill(null);
            }
        }

        return $this;
    }
}
