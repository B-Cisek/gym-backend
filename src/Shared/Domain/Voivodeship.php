<?php

declare(strict_types=1);

namespace App\Shared\Domain;

enum Voivodeship: string
{
    case DOLNOSLASKIE = 'dolnoslaskie';
    case KUJAWSKO_POMORSKIE = 'kujawsko-pomorskie';
    case LUBELSKIE = 'lubelskie';
    case LUBUSKIE = 'lubuskie';
    case LODZKIE = 'lodzkie';
    case MALOPOLSKIE = 'malopolskie';
    case MAZOWIECKIE = 'mazowieckie';
    case OPOLSKIE = 'opolskie';
    case PODKARPACKIE = 'podkarpackie';
    case PODLASKIE = 'podlaskie';
    case POMORSKIE = 'pomorskie';
    case SLASKIE = 'slaskie';
    case SWIETOKRZYSKIE = 'swietokrzyskie';
    case WARMINSKO_MAZURSKIE = 'warminsko-mazurskie';
    case WIELKOPOLSKIE = 'wielkopolskie';
    case ZACHODNIOPOMORSKIE = 'zachodniopomorskie';

    public function label(): string
    {
        return match ($this) {
            self::DOLNOSLASKIE => 'Dolnośląskie',
            self::KUJAWSKO_POMORSKIE => 'Kujawsko-Pomorskie',
            self::LUBELSKIE => 'Lubelskie',
            self::LUBUSKIE => 'Lubuskie',
            self::LODZKIE => 'Łódzkie',
            self::MALOPOLSKIE => 'Małopolskie',
            self::MAZOWIECKIE => 'Mazowieckie',
            self::OPOLSKIE => 'Opolskie',
            self::PODKARPACKIE => 'Podkarpackie',
            self::PODLASKIE => 'Podlaskie',
            self::POMORSKIE => 'Pomorskie',
            self::SLASKIE => 'Śląskie',
            self::SWIETOKRZYSKIE => 'Świętokrzyskie',
            self::WARMINSKO_MAZURSKIE => 'Warmińsko-Mazurskie',
            self::WIELKOPOLSKIE => 'Wielkopolskie',
            self::ZACHODNIOPOMORSKIE => 'Zachodniopomorskie',
        };
    }
}
