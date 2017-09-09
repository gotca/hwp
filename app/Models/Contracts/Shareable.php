<?php

namespace App\Models\Contracts;


interface Shareable
{

    const SQUARE = 'square';
    const RECTANGLE = 'rectangle';

    public function isShareable();

    public function getShareableUrl();
}