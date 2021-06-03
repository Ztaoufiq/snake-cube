<?php

namespace App\Port\Path;

use App\Port\Path\PathInterface;
use App\Port\Builder\Vector;
use App\Exception\PathException\IdenticalPathException;

class IdenticalPath implements PathInterface
{
    private $dimension;
    private $eqClasses;
    private $eqClassesPath;
    private $cursor;

    public function __construct(array $dimension)
    {
        $this->dimension = $dimension;
        $this->eqClasses = $this->createEqClassesFromDimensions();
        $this->eqClassesPath = [$this->eqClasses];
    }

    protected function createEqClassesFromDimensions(): array
    {
        $eqClasses = range(0, count($this->dimension) - 1);
        for ($i = 1, $iMax = count($this->dimension); $i < $iMax; $i++) {
            $value = $this->dimension[$i];
            for ($j = 0; $j <= $i; $j++) {
                if ($this->dimension[$j] === $value) {
                    $eqClasses[$i] = $j;
                    break;
                }
            }
        }
        return $eqClasses;
    }

    public function setCursor($cursor)
    {
        if (count($this->eqClassesPath) !== 1 && count($this->eqClassesPath) !== 2) {
            throw new IdenticalPathException("Changing cursor cannot happen in the middle of a path calculation");
        }
        if (count($this->eqClassesPath) === 2) {
            array_pop($this->eqClassesPath);
        }
        $this->cursor = $cursor;
        $cursorEqClasses = $this->eqClassesPath[0];
        for ($i = 0, $iMax = count($cursorEqClasses); $i < $iMax; $i++) {
            if ($cursorEqClasses[$i] !== $i && !$this->eqComparator($cursor[$cursorEqClasses[$i]], $cursor[$i], $this->dimension[$i])) {
                $oldClass = $cursorEqClasses[$i];
                $cursorEqClasses[$i] = $i;
                for ($j = $i + 1, $jMax = count($cursorEqClasses); $j < $jMax; $j++) {
                    if ($cursorEqClasses[$j] === $oldClass) {
                        $cursorEqClasses[$j] = $i;
                    }
                }
            }
        }
        $this->eqClasses = $cursorEqClasses;
        $this->eqClassesPath[] = $cursorEqClasses;
    }

    protected function eqComparator($p1, $p2, $dim)
    {
        return $p1 === $p2 or $p1 + $p2 + 1 === $dim;
    }
    public function getUsefulPoints(): \Generator
    {
        if (count($this->eqClassesPath) !== 1) {
            throw new IdenticalPathException("When computing useful points, there is only 1 eqClasses, computed from the dimensions");
        }
        foreach ($this->getUsefulPointsRec(0, array_fill(0, count($this->dimension), 0)) as $point) {
            yield $point;
        }
    }
    public function getUsefulPointsRec(int $index, array $minimums): \Generator
    {
        if ($index === count($this->dimension)) {
            yield [];
        } else {
            $eqClass = $this->eqClasses[$index];
            $minimum = $minimums[$eqClass];
            for ($i = $minimum; $i < (int)(($this->dimension[$index] + 1) / 2); $i++) {
                $minimums[$eqClass] = $i;
                foreach ($this->getUsefulPointsRec($index + 1, $minimums) as $t){
                    $t[] = $i;
                    yield $t;
                }
            }
            $minimums[$eqClass] = $minimum;
        }
    }
    /**
     * @param Vector $vector
     */
    public function move(Vector $vector): void
    {
        if ($this->eqClasses[$vector->getPosition()] !== $vector->getPosition()) {
            throw new IdenticalPathException("A move must always concern the first vector of an equivalence class");
        }
        $hasChanges = false;
        $position = $vector->getPosition();
        $newEqClasses = $this->eqClasses;
        $newEqClass = null;

        for ($i = $position + 1, $iMax = count($this->eqClasses); $i < $iMax; $i++) {
            if ($this->eqClasses[$i] === $position) {
                if (!$hasChanges) {
                    $hasChanges = true;
                    $newEqClasses = $this->eqClasses;
                }
                if($newEqClass === null) {
                    $newEqClass = $i;
                }
                $newEqClasses[$i] = $newEqClass;
            }
        }
        $this->eqClasses = $newEqClasses;
        array_push($this->eqClassesPath, $newEqClasses);
    }
    public function back(): void
    {
        array_pop($this->eqClassesPath);
        $this->eqClasses = end($this->eqClassesPath);
    }
    public function mustExplore($i): bool
    {
        return $this->eqClasses[$i] === $i;
    }
}
