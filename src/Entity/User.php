<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80)
     * @Groups("api_user_browse")
     * @Groups("api_user_read")
     *  @Groups("api_user_add")
     * @Groups("api_login")
     */
    private $user;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Groups("api_user_browse")
     * @Groups("api_user_read")
     *  @Groups("api_user_add")
     * @Groups("api_login")
     */
    private $email;



    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rate;

    /**
     * @ORM\Column(type="string", length=200)
     * @Groups("api_user_browse")
     * @Groups("api_user_read")
     *  @Groups("api_user_add")
     * @Groups("api_login")
     */
    private $password;



    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups("api_user_browse")
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity=Recipe::class, mappedBy="users")
     * @Groups("api_user_browse")
     */
    private $recipes;

    /**
     * @ORM\ManyToMany(targetEntity=Recipe::class, mappedBy="likes")
     */
    private $likes;

    /**
     * @ORM\ManyToMany(targetEntity=Recipe::class, inversedBy="favorites")
     */
    private $favorites;


    public function __construct()
    {
        $this->recipes = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->favorites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }



    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(?int $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }




    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        //? va nous permettre de reconnaitre un utilisateur authentifié d'un utilisateur anonyme
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection<int, Recipe>
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(Recipe $recipes): self
    {
        if (!$this->recipes->contains($recipes)) {
            $this->recipes[] = $recipes;
            $recipes->setUsers($this);
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipes): self
    {
        if ($this->recipes->removeElement($recipes)) {
            // set the owning side to null (unless already changed)
            if ($recipes->getUsers() === $this)
                $recipes->setUsers(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Recipe>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Recipe $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->addLike($this);
        }

        return $this;
    }

    public function removeLike(Recipe $like): self
    {
        if ($this->likes->removeElement($like)) {
            $like->removeLike($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Recipe>
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Recipe $favorite): self
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites[] = $favorite;
        }

        return $this;
    }

    public function removeFavorite(Recipe $favorite): self
    {
        $this->favorites->removeElement($favorite);

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }
}
