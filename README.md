# FormRequest
A lightweight FormRequest implementation with data normalization.
Based on symfony/validator.

Comparision:
1. Laravel FormRequest - faster about 3.8 times
2. Symfony Form - faster about 9.7 times

Usage (look at tests for more examples):
```php
<?php

declare(strict_types=1);

use Codercms\FormRequest\RequestNormalizer;
use Codercms\FormRequest\ValueNormalizer;
use Codercms\FormRequest\ValidationException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class TestFormRequest extends \Codercms\FormRequest\FormRequest
{
    protected function setupRules(): Assert\Collection
    {
        return new Assert\Collection(
            [
                'is_active' => new Assert\Optional(
                    new Assert\Type('bool')
                ),
            ]
        );
    }
}

$formRequest = new TestFormRequest(
    new RequestNormalizer(new ValueNormalizer()), 
    Validation::createValidator()
);

$data = [/* your incoming data here */];

try {
    $normalizedData = $formRequest->handle($data);
} catch (ValidationException $e) {
    $errors = $e->getViolationList();
    // handle errors here
}
```
