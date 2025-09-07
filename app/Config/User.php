<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuration des options utilisateurs
    |--------------------------------------------------------------------------
    |
    | Cette section gère tous les paramètres relatifs aux comptes utilisateurs,
    | à l'inscription, à la visibilité, aux permissions et à l'interaction
    | avec le contenu du site.
    |
    */

    // Activer les avatars ? (1=Oui 0=Non)
    'smilies' => 1,

    // Taille maximale pour les avatars uploadés en pixels (largeur*hauteur)
    'avatar_size' => '80*100',

    // Activer l'inscription courte (sans ICQ, MSN, etc.) ? (1=Oui 0=Non)
    'short_user' => 1,

    // Rendre la liste des membres privée (1=Oui) ou publique (0=Non)
    'member_list' => 1,

    // Autoriser la création automatique de nouveaux utilisateurs (envoi d'email et connexion autorisée)
    'autoRegUser' => 1,

    // Activer les courts avis (type 'livre d'or') ? (1=Oui, 0=Non)
    'short_review' => 0,

    // Autoriser les membres à s'abonner aux sujets ? (1=Oui, 0=Non)
    'subscribe' => 1,

    // Autoriser les membres à se rendre invisibles pour les autres membres ? (1=Oui, 0=Non)
    'member_invisible' => 0,

    // Permettre de fermer l'inscription des nouveaux membres ? (1=Oui, 0=Non)
    'CloseRegUser' => 0,

    // Autoriser l'utilisateur à choisir son mot de passe ? (1=Oui, 0=Non)
    'memberpass' => 1,

    // Nom par défaut des utilisateurs anonymes
    'anonymous' => 'Visiteur',

    // Nombre d'utilisateurs affichés sur la page de la liste des membres
    'show_user' => 20,

    // Autoriser les anonymes à poster des commentaires ? (1=Oui 0=Non)
    'anonpost' => 1,

    // Nombre maximum de commentaires par utilisateur sur 24h
    'troll_limit' => 5,

    // Activer la modération des commentaires ? (1=Oui, 0=Non)
    'moderate' => 1,

    // Autoriser uniquement les modérateurs et admins à publier des news ? (1=Oui, 0=Non)
    'mod_admin_news' => 0,

    // Ne pas enregistrer les visites des admins dans les statistiques ? (1=Oui => ne pas enregistrer, 0=Non => enregistrer)
    'not_admin_count' => 1,

];
