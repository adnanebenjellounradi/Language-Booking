<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use DateTime;

#[ODM\Document(collection: 'bookings')]
#[ODM\UniqueIndex(keys: ['sessionId' => 'asc', 'userId' => 'asc'])]
class Booking
{
    #[ODM\Id]
    private string $id;

    #[ODM\Field(type: 'string')]
    private string $sessionId;

    #[ODM\Field(type: 'string')]
    private string $userId;

    #[ODM\Field(type: 'date')]
    private DateTime $bookingDate;

    public function getId(): string { return $this->id; }
    
    public function getSessionId(): string { return $this->sessionId; }
    public function setSessionId(string $sessionId): self { $this->sessionId = $sessionId; return $this; }
    
    public function getUserId(): string { return $this->userId; }
    public function setUserId(string $userId): self { $this->userId = $userId; return $this; }
    
    public function getBookingDate(): DateTime { return $this->bookingDate; }
    public function setBookingDate(DateTime $bookingDate): self 
    { 
        $this->bookingDate = $bookingDate;
        return $this;
    }
}