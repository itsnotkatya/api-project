<?php

namespace App\Exception\API;

use App\Util\Message;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class APIException extends Exception
{
    /**
     * @var array
     */
    protected array $errors;

    /**
     * BaseException constructor.
     *
     * @param array       $errors
     * @param string|null $message
     */
    public function __construct(array $errors = [], ?string $message = null)
    {
        parent::__construct($message ?? Message::UNKNOWN_ERROR, $this->getHttpCode(), null);

        $this->errors = $errors;
    }

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    /**
     * Set errors.
     *
     * @param array $errors
     *
     * @return $this
     */
    public function setErrors(array $errors): APIException
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Payload data for exception.
     *
     * @return array
     */
    public function getData(): array
    {
        return [];
    }
}
