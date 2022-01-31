<?php

namespace App\Util;

final class Message
{
    public const GENERAL_INTERNAL_ERROR = 'GeneralInternalError';
    public const INCORRECT_DATA         = 'IncorrectData';
    public const METHOD_NOT_ALLOWED     = 'MethodNotAllowed';
    public const NOT_FOUND              = 'NotFound';
    public const SUCCESS                = 'Success';
    public const UNKNOWN_ERROR          = 'UnknownError';

    /**
     * Message constructor.
     */
    private function __construct()
    {
    }
}
