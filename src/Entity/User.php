<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $display_name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="author_id", orphanRemoval=true)
     */
    private $posts;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="subscriptors")
     */
    private $subscriptions;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="subscriptions")
     */
    private $subscriptors;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->subscription = new ArrayCollection();
        $this->subscriptors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getDisplayName(): ?string
    {
        return $this->display_name;
    }

    public function setDisplayName(string $display_name): self
    {
        $this->display_name = $display_name;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthorId($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getAuthorId() === $this) {
                $post->setAuthorId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(self $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions[] = $subscription;
        }

        return $this;
    }

    public function removeSubscription(self $subscription): self
    {
        if ($this->subscriptions->contains($subscription)) {
            $this->subscriptions->removeElement($subscription);
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getSubscriptors(): Collection
    {
        return $this->subscriptors;
    }

    public function addSubscriptor(self $subscriptor): self
    {
        if (!$this->subscriptors->contains($subscriptor)) {
            $this->subscriptors[] = $subscriptor;
            $subscriptor->addSubscription($this);
        }

        return $this;
    }

    public function removeSubscriptor(self $subscriptor): self
    {
        if ($this->subscriptors->contains($subscriptor)) {
            $this->subscriptors->removeElement($subscriptor);
            $subscriptor->removeSubscription($this);
        }

        return $this;
    }
}
