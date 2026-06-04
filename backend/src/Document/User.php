<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ODM\Document(collection: 'users')]
#[ODM\UniqueIndex(keys: ['email' => 'asc'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ODM\Id]
    private string $id;

    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    private string $name;

    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    #[ODM\Field(type: 'string')]
    #[Assert\NotBlank]
    private string $password;

    #[ODM\Field(type: 'collection')]
    private array $roles = ['ROLE_USER'];

    public function getId(): string 
    { 
        return $this->id; 
    }
    
    public function getName(): string 
    { 
        return $this->name; 
    }
    
    public function setName(string $name): self 
    { 
        $this->name = $name; 
        return $this; 
    }
    
    public function getEmail(): string 
    { 
        return $this->email; 
    }
    
    public function setEmail(string $email): self 
    { 
        $this->email = $email; 
        return $this; 
    }
    
    public function getPassword(): string 
    { 
        return $this->password; 
    }
    
    public function setPassword(string $password): self 
    { 
        // Le mot de passe sera hashé par le système
        $this->password = $password;
        return $this;
    }
    
    public function getRoles(): array 
    { 
        return $this->roles; 
    }
    
    public function setRoles(array $roles): self 
    { 
        $this->roles = $roles; 
        return $this; 
    }
    
    public function getSalt(): ?string 
    { 
        return null; 
    }
    
    public function getUsername(): string 
    { 
        return $this->email; 
    }
    
    public function getUserIdentifier(): string 
    { 
        return $this->email; 
    }
    
    public function eraseCredentials(): void 
    { 
        // Ne rien faire
    }
}