<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class CensuratorService
{
    // V1 : weak
    // const CENSORED_WORDS = ['canaille', 'méchant', 'idiot', 'stupide', 'con'];

    // V2 : ContainerBagInterface strong get words from file data/word_censured.txt
    public function __construct(private readonly ContainerBagInterface $containerBag) {}
    // V3 : Injection depuis le fichier de configuration
    // public function __construct(private $wordsCensuredDirectory) {}

    public function censor(string $text): string
    {
        // V2 : ContainerBagInterface strong get words from file data/word_censured.txt
        $CENSORED_WORDS = file_get_contents($this->containerBag->get('app.words_censured_directory'));
        
        // V3 : Injection depuis le fichier de configuration
        // $CENSORED_WORDS = file_get_contents($this->wordsCensuredDirectory);

        $CENSORED_WORDS = explode(",", $CENSORED_WORDS);
        foreach ($CENSORED_WORDS as $word) {
            $text = str_ireplace($word, str_repeat('*', mb_strlen($word)), $text);
        }
        return $text;
    }
}
