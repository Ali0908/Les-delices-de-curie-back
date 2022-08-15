<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IngredientRepository::class)
 */
class Ingredient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups("api_recipe_add")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=RecipeIngredient::class, mappedBy="unit", orphanRemoval=true)
     */
    private $unit;

    public function __construct()
    {
        $this->unit = new ArrayCollection();
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
     * @return Collection<int, RecipeIngredient>
     */
    public function getUnit(): Collection
    {
        return $this->unit;
    }

    public function addUnit(RecipeIngredient $unit): self
    {
        if (!$this->unit->contains($unit)) {
            $this->unit[] = $unit;
            $unit->setUnit($this);
        }

        return $this;
    }

    public function removeUnit(RecipeIngredient $unit): self
    {
        if ($this->unit->removeElement($unit)) {
            // set the owning side to null (unless already changed)
            if ($unit->getUnit() === $this) {
                $unit->setUnit(null);
            }
        }

        return $this;
    }
}
