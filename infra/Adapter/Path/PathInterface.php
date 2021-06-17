<?php


namespace App\Adapter\Path;
use App\Adapter\Builder\Vector;

interface PathInterface
{
    public function setCursor($cursor);
    /**
     * @param Vector $vector
     */
    public function move(Vector $vector);

    public function back();
}