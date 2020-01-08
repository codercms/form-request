<?php

declare(strict_types=1);

namespace Codercms\FormRequest;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use RuntimeException;

class ValidationException extends RuntimeException
{
    private ConstraintViolationListInterface $violationList;

    public function __construct(ConstraintViolationListInterface $violationList)
    {
        parent::__construct('Validation error');

        $this->violationList = $violationList;
    }

    public function getViolationList(): ConstraintViolationListInterface
    {
        return $this->violationList;
    }
}