<?php

namespace App\DataFixtures;

use App\Document\Session;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Persistence\ObjectManager;
use DateTime;

class SessionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $sessions = [
            ['language' => 'English', 'date' => '2025-05-10', 'time' => '10:00', 'location' => 'Paris Center', 'seats' => 20],
            ['language' => 'Spanish', 'date' => '2025-05-11', 'time' => '14:00', 'location' => 'Lyon', 'seats' => 15],
            ['language' => 'French', 'date' => '2025-05-12', 'time' => '09:00', 'location' => 'Marseille', 'seats' => 25],
            ['language' => 'German', 'date' => '2025-05-13', 'time' => '11:00', 'location' => 'Paris Center', 'seats' => 10],
            ['language' => 'Italian', 'date' => '2025-05-14', 'time' => '15:00', 'location' => 'Bordeaux', 'seats' => 30],
            ['language' => 'Chinese', 'date' => '2025-05-15', 'time' => '13:00', 'location' => 'Paris Center', 'seats' => 18],
            ['language' => 'Japanese', 'date' => '2025-05-16', 'time' => '16:00', 'location' => 'Lyon', 'seats' => 12],
        ];

        foreach ($sessions as $data) {
            $session = new Session();
            $session->setLanguage($data['language']);
            $session->setDate(new DateTime($data['date']));
            $session->setTime($data['time']);
            $session->setLocation($data['location']);
            $session->setAvailableSeats($data['seats']);
            
            $manager->persist($session);
        }

        $manager->flush();
        
        echo "Created " . count($sessions) . " sessions\n";
    }
}