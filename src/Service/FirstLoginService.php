<?php

namespace App\Service;

use App\Entity\Historique;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FirstLoginService {
    private UserPasswordHasherInterface $uph;

    public function __construct(UserPasswordHasherInterface $uph) {
        $this->uph = $uph;
    }

    public function checkDefaultPassword(Participant $user) : bool {
        $passwordDefault = strtolower($user->getNom() . $user->getPrenom()) . "@SORTIR2023";
        return $this->uph->isPasswordValid($user, $passwordDefault);
    }
}