<?php

namespace Npds\Npds;


Class Npds 
{

    /**
     * Représente le chemin de base de l'application.
     *
     * @var string
     */
    private string $basePath;

    
    /**
     * Constructeur de la classe.
     *
     * Initialise le chemin de base en appelant la méthode {@see setBasePath()}.
     *
     * @param string|null $basePath Chemin de base personnalisé (facultatif).
     *                              Si null, la constante BASEPATH est utilisée.
     */
    public function __construct(?string $basePath = null) 
    { 
        $this->setBasePath($basePath); 
    }

    /**
     * Définit le chemin de base de l'application.
     *
     * Si aucun chemin n'est fourni, utilise la constante BASEPATH.
     *
     * @param string|null $basePath Chemin de base personnalisé ou null.
     * @return void
     */
    public function setBasePath(?string $basePath): void
    {
        $this->basePath = rtrim($basePath ?: BASEPATH, '/\\');
    }

    /**
     * Retourne le chemin de base de l'application.
     *
     * @return string Le chemin de base.
     */
    public function basePath(): string
    {
        return $this->basePath;
    }

}