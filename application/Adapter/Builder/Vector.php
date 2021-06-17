<?php


namespace App\Adapter\Builder;


class Vector
{
    private $position;
    private $value;
    private $variables;

    public function __construct(int $position, int $value)
    {
        $this->position = $position;
        $this->value = $value;
        $this->variables = ['x', 'y', 'z', 't'];
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    /**
     * @param int $position
     * @return string
     */
    public function getVariable(int $position): string
    {
        if ($position < count($this->variables)) {
            return $this->variables[$position];
        }
        return 'k' . ($position - count($this->variables) + 1);
    }

    /**
     * @param int $number
     * @return int
     */
    public function getCanonical(int $number): int
    {
         return ( $number == 1 ? '' : ( $number == -1 ? '-' : strval($number) ) );
    }

}