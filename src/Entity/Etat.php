<?php

namespace App\Entity;

enum Etat: string {
    case CREEE = 'Créée';
    case OUVERTE = 'Ouverte';
    case CLOTUREE = 'Clôturée';
    case ENCOURS = 'En cours';
    case TERMINEE = 'Terminée';
    case ANNULEE = 'Annulée';
}
