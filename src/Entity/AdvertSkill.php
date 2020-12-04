<?php

namespace App\Entity;

use App\Repository\AdvertSkillRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AdvertSkillRepository::class)
 */
class AdvertSkill
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
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity=Advert::class, inversedBy="advertSkills")
     * @ORM\JoinColumn(nullable=false)
     */
    private $advert;

    /**
     * @ORM\ManyToOne(targetEntity=Skill::class, inversedBy="advertSkills")
     * @ORM\JoinColumn(nullable=false)
     */
    private $skill;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getAdvert(): ?Advert
    {
        return $this->advert;
    }

    public function setAdvert(?Advert $advert): self
    {
        $this->advert = $advert;

        return $this;
    }

    public function getSkill(): ?Skill
    {
        return $this->skill;
    }

    public function setSkill(?Skill $skill): self
    {
        $this->skill = $skill;

        return $this;
    }
}
