<?php

namespace App\Object;


use JMS\Serializer\Annotation\Type;


class ApiErrorResponse
{
    /**
     * Error code
     *
     * @var code
     */
    private $code;

    /**
     * Error message
     *
     * @var message
     */
    private $message;


    public function __construct($code, $message = "")
    {
        $this->message = $message;
        $this->code = $code;
    }


    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }


}
