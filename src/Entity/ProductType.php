<?php

namespace App\Entity;

enum ProductType: string {

    case FOOD = 'Food';
    case BEVERAGE = 'Beverage';
    case TEXTILE = 'Textile';
    case CHEMICAL = 'Chemical';
}

