<?php

namespace Domain\Shape\Exception;
class InvalidCubeDataException extends \Exception
{
    //protected $message = 'Update message exception';
    //protected $code = 'Update code exception';;

    function build() {
        throw new InvalidCubeDataException('Msg excemption');
    }
}