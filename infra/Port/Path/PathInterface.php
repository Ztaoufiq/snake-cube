<?php


namespace App\Port\Path;
use App\Port\Builder\Vector;

interface PathInterface
{
    public function setCursor($cursor);
    /**
     * @param Vector $vector
     */
    public function move(Vector $vector);

    public function back();
}