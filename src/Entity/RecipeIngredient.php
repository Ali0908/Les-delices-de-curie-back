<?php

namespace App\Entity;

use App\Repository\RecipeIngredientRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecipeIngredientRepository::class)
 */
class RecipeIngredient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Recipe::class, inversedBy="quantity")
     * @ORM\JoinColumn(nullable=false)
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity=Ingredient::class, inversedBy="unit")
     * @ORM\JoinColumn(nullable=false)
     */
    private $unit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?Recipe
    {
        return $this->quantity;
    }

    public function setQuantity(?Recipe $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnit(): ?Ingredient
    {
        return $this->unit;
    }

    public function setUnit(?Ingredient $unit): self
    {
        $this->unit = $unit;

        return $this;
    }
}
