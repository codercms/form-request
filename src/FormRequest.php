<?php

declare(strict_types=1);

namespace Codercms\FormRequest;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class FormRequest implements FormRequestInterface
{
    protected RequestNormalizerInterface $requestNormalizer;
    protected Assert\Collection $rules;
    protected ValidatorInterface $validator;

    public function __construct(RequestNormalizerInterface $requestNormalizer, ValidatorInterface $validator)
    {
        $this->requestNormalizer = $requestNormalizer;
        $this->validator = $validator;
        $this->rules = $this->setupRules();
    }

    public function handle(array $data): array
    {
        $normData = $this->requestNormalizer->normalize($data, $this->rules);
        $errors = $this->validator->validate($normData, $this->rules);
        if ($errors->count()) {
            throw new ValidationException($errors);
        }

        return $normData;
    }

    abstract protected function setupRules(): Assert\Collection;

    public function getRules(): Assert\Collection
    {
        return $this->rules;
    }
}
