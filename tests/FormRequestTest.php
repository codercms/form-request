<?php

declare(strict_types=1);

namespace Codercms\FormRequest\Tests;

use Codercms\FormRequest\FormRequest;
use Codercms\FormRequest\FormRequestInterface;
use Codercms\FormRequest\RequestNormalizer;
use Codercms\FormRequest\ValidationException;
use Codercms\FormRequest\ValueNormalizer;
use Symfony\Component\Validator\Constraints as Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class FormRequestTest extends TestCase
{
    private FormRequestInterface $formRequest;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpFormRequest();
    }

    public function testValidationSuccessful(): void
    {
        $data = [
            'books' => [
                [
                    'name' => 'some',
                    'author_id' => '228',
                    'price' => '322.42',
                    'is_active' => 'true',
                ]
            ]
        ];
        $expected = [
            'books' => [
                [
                    'name' => 'some',
                    'author_id' => 228,
                    'price' => 322.42,
                    'is_active' => true,
                ]
            ]
        ];

        $normalized = $this->formRequest->handle($data);

        $this->assertEquals($expected, $normalized);
    }

    public function testValidationError(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            'books' => [
                [
                    'author_id' => 'hey',
                    'price' => 'abra',
                    'is_active' => 228,
                ]
            ]
        ];

        $this->formRequest->handle($data);
    }

    private function setUpFormRequest(): void
    {
        $this->formRequest = new class extends FormRequest {
            public function __construct()
            {
                $requestNormalizer = new RequestNormalizer(new ValueNormalizer());
                $validator = Validation::createValidator();

                parent::__construct($requestNormalizer, $validator);
            }

            protected function setupRules(): Assert\Collection
            {
                return new Assert\Collection(
                    [
                        'books' => [
                            new Assert\Type('array'),
                            new Assert\Count(1),
                            new Assert\All(
                                new Assert\Collection(
                                    [
                                        'name' => new Assert\Type('string'),
                                        'author_id' => new Assert\Type('int'),
                                        'price' => [
                                            new Assert\GreaterThan(1.0),
                                            new Assert\Type('float'),
                                        ],
                                        'is_active' => new Assert\Type('bool'),
                                    ]
                                )
                            )
                        ]
                    ]
                );
            }
        };
    }
}
