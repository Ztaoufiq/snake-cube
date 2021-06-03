<?php

namespace App\Port\Path;

use App\Port\Path\PathInterface;
use App\Port\Builder\Vector;
use App\Exception\PathException\MaximumPathException;

class MaximumPath implements PathInterface
{
    private $dimension;
    private $path;
    private $flags;
    private $initCursor;
    private $cursor;

    public function __construct(array $dimension)
    {
        $this->dimension = $dimension;
        $this->path = [];
        $this->flags = $this->createVolumeFlags($this->dimension);
        $this->initCursor = null;
    }

    /**
     * @return array
     */
    public function getPath(): array
    {
        return $this->path;
    }

    protected function createVolumeFlags(array $dimension): array
    {
        $volumeFlags = [];
        for($i = 0; $i < $dimension[0]; $i++) {
            $volumeFlags[] = array_chunk(array_fill(0, $dimension[0] * $dimension[0], false), $dimension[0]);
        }
        return $volumeFlags;
    }

    public function setCursor($cursor): void
    {
        if (count($this->path) !== 0) {
            throw new MaximumPathException("Changing cursor cannot happen in the middle of a path calculation");
        }
        if ($this->initCursor !== null) {
            $this->setFlag($this->initCursor, false);
        }
        $this->initCursor = $cursor;
        $this->cursor = $this->initCursor;
        $this->setFlag($this->cursor, true);
    }
    protected function getFlag($cursor): bool
    {
        $tmp = $this->flags;
        for ($i = 0, $iMax = count($cursor); $i < $iMax; $i++) {
            $tmp = $tmp[$cursor[$i]];
        }
        return $tmp;

    }
    protected function setFlag($cursor, $value): void
    {
        if (count($cursor) === 3) {
            $this->flags[$cursor[0]][$cursor[1]][$cursor[2]] = $value;
        }
    }

    /**
     * @param Vector $vector
     * @return bool
     */
    public function canMove(Vector $vector): bool
    {
        $cursorPositionValue = $this->cursor[$vector->getPosition()];
        $newValue = $cursorPositionValue + $vector->getValue();
        if ($newValue < 0 || $newValue >= $this->dimension[$vector->getPosition()]) {
            return false;
        }
        $futureCursor = $this->cursor;
        $sign = $vector->getValue() < 0 ? -1 : 1 ;
        for ($i = 0; $i < ($sign * $vector->getValue()); $i++) {
            $futureCursor[$vector->getPosition()] += $sign;
            if ($this->getFlag($futureCursor)) {
                return false;
            }
        }
        return true;
    }
    /**
     * @param Vector $vector
     */
    public function move(Vector $vector): void
    {
        array_push($this->path, $vector);
        $sign = $vector->getValue() < 0 ? -1 : 1 ;
        for ($i = 0; $i < ($sign * $vector->getValue()); $i++) {
            $this->cursor[$vector->getPosition()] += $sign;
            $this->setFlag($this->cursor, true);
        }
    }
    public function back(): void
    {
        $vector = array_pop($this->path);
        $sign = $vector->getValue() < 0 ? -1 : 1 ;
        for ($i = 0; $i < ($sign * $vector->getValue()); $i++) {
            $this->setFlag($this->cursor, false);
            $this->cursor[$vector->getPosition()] += -$sign;
        }
    }
}
