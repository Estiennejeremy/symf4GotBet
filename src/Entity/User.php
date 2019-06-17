<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Captcha\Bundle\CaptchaBundle\Validator\Constraints as CaptchaAssert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"mail"}, message="There is already an account with this mail")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $score = 0;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reponse", mappedBy="user")
     */
    private $reponses;

    /**
     * @ORM\Column(type="binary", nullable=true)
     */
    private $ajouer;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $jouer;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $jouerBonnus;

    /**
    * @ORM\ManyToOne(targetEntity="App\Entity\Personnage")
     * @ORM\JoinColumn(nullable=true)
    */
    private $personnage;
    
    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

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

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getUsername() {
        return $this->mail;
    }

    public function getSalt() {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getRoles() {
        if (empty($this->roles)) {
            return ['ROLE_USER'];
        }
        return $this->roles;
    }

    function addRole($role) {
        $this->roles[] = $role;
    }

    public function eraseCredentials() {

    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->nom,
            $this->prenom,
            $this->mail,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->nom,
            $this->prenom,
            $this->mail,
            $this->password,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized, array('allowed_classes' => false));
    }

    /**
     * @return Collection|Reponse[]
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(Reponse $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses[] = $reponse;
            $reponse->setUser($this);
        }

        return $this;
    }

    public function removeReponse(Reponse $reponse): self
    {
        if ($this->reponses->contains($reponse)) {
            $this->reponses->removeElement($reponse);
            // set the owning side to null (unless already changed)
            if ($reponse->getUser() === $this) {
                $reponse->setUser(null);
            }
        }

        return $this;
    }

    public function getAjouer()
    {
        return $this->ajouer;
    }

    public function setAjouer($ajouer): self
    {
        $this->ajouer = $ajouer;

        return $this;
    }

    public function getJouer(): ?bool
    {
        return $this->jouer;
    }

    public function setJouer(bool $jouer): self
    {
        $this->jouer = $jouer;

        return $this;
    }
  /**
   * @CaptchaAssert\ValidCaptcha(
   *      message = "Le captcha est invalide." 
   * )
   */
  protected $captchaCode;

  public function getCaptchaCode()
  {
    return $this->captchaCode;
  }

  public function setCaptchaCode($captchaCode)
  {
    $this->captchaCode = $captchaCode;
  }

  public function getJouerBonnus(): ?bool
  {
      return $this->jouerBonnus;
  }

  public function setJouerBonnus(?bool $jouerBonnus): self
  {
      $this->jouerBonnus = $jouerBonnus;

      return $this;
  }

  public function getPersonnage(): ?Personnage
  {
      return $this->personnage;
  }

  public function setPersonnage(?Personnage $personnage): self
  {
      $this->personnage = $personnage;

      return $this;
  }
}
