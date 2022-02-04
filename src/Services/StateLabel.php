<?php


namespace App\Services;


abstract class StateLabel
{
    public const STATE_CREATED          = 'Créée';
    public const STATE_DONE             = 'Passée';
    public const STATE_CANCELED         = 'Annulée';
    public const STATE_HISTORIZED       = 'Archivée';
    public const STATE_OPEN             = 'Ouverte';
    public const STATE_END_REGISTER     = 'Clôturée';
    public const STATE_IN_PROGRESS      = 'Activité en cours';
}