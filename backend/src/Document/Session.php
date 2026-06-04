<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use DateTime;

#[ODM\Document(collection: 'sessions')]
class Session
{
    #[ODM\Id]
    private string $id;

    #[ODM\Field(type: 'string')]
    private string $language;

    #[ODM\Field(type: 'date')]
    private DateTime $date;

    #[ODM\Field(type: 'string')]
    private string $time;

    #[ODM\Field(type: 'string')]
    private string $location;

    #[ODM\Field(type: 'int')]
    private int $availableSeats;

    // Getters
    public function getId(): string { return $this->id; }
    public function getLanguage(): string { return $this->language; }
    public function getDate(): DateTime { return $this->date; }
    public function getTime(): string { return $this->time; }
    public function getLocation(): string { return $this->location; }
    public function getAvailableSeats(): int { return $this->availableSeats; }
    
    // Setters
    public function setLanguage(string $language): self { $this->language = $language; return $this; }
    public function setDate(DateTime $date): self { $this->date = $date; return $this; }
    public function setTime(string $time): self { $this->time = $time; return $this; }
    public function setLocation(string $location): self { $this->location = $location; return $this; }
    public function setAvailableSeats(int $availableSeats): self { $this->availableSeats = $availableSeats; return $this; }
}