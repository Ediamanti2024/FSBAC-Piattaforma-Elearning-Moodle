<?php

defined('MOODLE_INTERNAL') || die();

$observers = [
    // Creazione di un nuovo utente
    [
        'eventname' => '\core\event\user_created',
        'callback' => 'local_fsbac_social_observer::user_created',
    ],
    
    // Iscrizione di un utente a un corso
    [
        'eventname' => '\core\event\user_enrolment_created',
        'callback' => 'local_fsbac_social_observer::user_enrolled',
    ],

    // Autenticazione utente in piattaforma
    [   
        'eventname' => '\core\event\user_loggedin',
        'callback' => 'local_fsbac_social_observer::user_loggedin',
    ],
];
