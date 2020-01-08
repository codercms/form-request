<?php

declare(strict_types=1);

namespace Codercms\FormRequest\Tests;

use Codercms\FormRequest\RequestNormalizer;
use Codercms\FormRequest\ValueNormalizer;
use Symfony\Component\Validator\Constraints as Assert;
use PHPUnit\Framework\TestCase;

class RequestNormalizerTest extends TestCase
{
    private RequestNormalizer $requestNormalizer;

    public function setUp(): void
    {
        parent::setUp();

        $this->requestNormalizer = new RequestNormalizer(new ValueNormalizer());
    }

    public function testBool(): void
    {
        $data = [
            'norm_bool_int_true' => 1,
            'norm_bool_int_false' => 0,
            'norm_bool_str_true_1' => '1',
            'norm_bool_str_false_1' => '0',
            'norm_bool_str_true_2' => 'true',
            'norm_bool_str_false_2' => 'false',
            'wrong_bool_int' => 11,
            'wrong_bool_str' => 'true1'
        ];

        $boolRule = $this->getBoolRule();
        $rules = new Assert\Collection(
            [
                'norm_bool_int_true' => $boolRule,
                'norm_bool_int_false' => $boolRule,
                'norm_bool_str_true_1' => $boolRule,
                'norm_bool_str_false_1' => $boolRule,
                'norm_bool_str_true_2' => $boolRule,
                'norm_bool_str_false_2' => $boolRule,
                'wrong_bool_int' => $boolRule,
                'wrong_bool_str' => $boolRule,
            ]
        );

        $normalized = $this->requestNormalizer->normalize($data, $rules);

        $this->assertEquals(true, $normalized['norm_bool_int_true']);
        $this->assertEquals(false, $normalized['norm_bool_int_false']);
        $this->assertEquals(true, $normalized['norm_bool_str_true_1']);
        $this->assertEquals(false, $normalized['norm_bool_str_false_1']);
        $this->assertEquals(true, $normalized['norm_bool_str_true_2']);
        $this->assertEquals(false, $normalized['norm_bool_str_false_2']);
        $this->assertEquals($data['wrong_bool_int'], $normalized['wrong_bool_int']);
        $this->assertEquals($data['wrong_bool_str'], $normalized['wrong_bool_str']);
    }

    public function testInt(): void
    {
        $data = [
            'norm_int_bool_true' => true,
            'norm_int_bool_false' => false,
            'norm_int_str_1' => '1',
            'norm_int_str_0' => '0',
            'norm_int_str_01' => '01',
            'norm_int_str_float' => '1.55',
            'norm_int_float' => 1.55,
            'wrong_int_str_1' => 'ab12',
            'wrong_int_str_2' => 'true1',
        ];

        $intRule = $this->getIntRule();
        $rules = new Assert\Collection(
            [
                'norm_int_bool_true' => $intRule,
                'norm_int_bool_false' => $intRule,
                'norm_int_str_1' => $intRule,
                'norm_int_str_0' => $intRule,
                'norm_int_str_01' => $intRule,
                'norm_int_str_float' => $intRule,
                'norm_int_float' => $intRule,
                'wrong_int_str_1' => $intRule,
                'wrong_int_str_2' => $intRule,
            ]
        );

        $normalized = $this->requestNormalizer->normalize($data, $rules);

        $this->assertEquals(1, $normalized['norm_int_bool_true']);
        $this->assertEquals(0, $normalized['norm_int_bool_false']);
        $this->assertEquals(1, $normalized['norm_int_str_1']);
        $this->assertEquals(0, $normalized['norm_int_str_0']);
        $this->assertEquals(1, $normalized['norm_int_str_01']);
        $this->assertEquals(1, $normalized['norm_int_str_float']);
        $this->assertEquals(1, $normalized['norm_int_float']);
        $this->assertEquals($data['wrong_int_str_1'], $normalized['wrong_int_str_1']);
        $this->assertEquals($data['wrong_int_str_2'], $normalized['wrong_int_str_2']);
    }

    public function testFloat(): void
    {
        $data = [
            'norm_float_bool_true' => true,
            'norm_float_bool_false' => false,
            'norm_float_str_1' => '1',
            'norm_float_str_0' => '0',
            'norm_float_str_01' => '01',
            'norm_float_str_2' => '1.55',
            'norm_float' => 1.55,
            'wrong_float_str_1' => 'ab12',
            'wrong_float_str_2' => '1.a4',
            'wrong_float_str_3' => '1,4',
        ];

        $floatRule = $this->getFloatRule();
        $rules = new Assert\Collection(
            [
                'norm_float_bool_true' => $floatRule,
                'norm_float_bool_false' => $floatRule,
                'norm_float_str_1' => $floatRule,
                'norm_float_str_0' => $floatRule,
                'norm_float_str_01' => $floatRule,
                'norm_float_str_2' => $floatRule,
                'norm_float' => $floatRule,
                'wrong_float_str_1' => $floatRule,
                'wrong_float_str_2' => $floatRule,
                'wrong_float_str_3' => $floatRule,
            ]
        );

        $normalized = $this->requestNormalizer->normalize($data, $rules);

        $this->assertEquals(1.0, $normalized['norm_float_bool_true']);
        $this->assertEquals(0.0, $normalized['norm_float_bool_false']);
        $this->assertEquals(1.0, $normalized['norm_float_str_1']);
        $this->assertEquals(0.0, $normalized['norm_float_str_0']);
        $this->assertEquals(1.0, $normalized['norm_float_str_01']);
        $this->assertEquals(1.55, $normalized['norm_float_str_2']);
        $this->assertEquals(1.55, $normalized['norm_float']);
        $this->assertEquals($data['wrong_float_str_1'], $normalized['wrong_float_str_1']);
        $this->assertEquals($data['wrong_float_str_2'], $normalized['wrong_float_str_2']);
        $this->assertEquals($data['wrong_float_str_3'], $normalized['wrong_float_str_3']);
    }

    public function testMultipleTypes(): void
    {
        $data = [
            'some' => '1.44',
            'some2' => '1',
        ];

        $typeRule = new Assert\Optional(
            new Assert\Type(['int', 'float'])
        );

        $rule = new Assert\Collection(
            [
                'some' => $typeRule,
                'some2' => $typeRule
            ]
        );

        $normalized = $this->requestNormalizer->normalize($data, $rule);

        $this->assertEquals('1.44', $normalized['some']);
        $this->assertEquals('1', $normalized['some2']);
    }

    public function testNested(): void
    {
        $data = [
            'books' => [
                [
                    'author_id' => '2',
                    'price' => '228.5',
                    'is_active' => 'true',
                    'tags' => [
                        '0',
                        '1',
                    ]
                ]
            ],
            'authors' => [
                '1',
                '2',
                '3'
            ]
        ];

        $rule = new Assert\Collection(
            [
                'books' => [
                    new Assert\Type('array'),
                    new Assert\All(
                        new Assert\Collection(
                            [
                                'author_id' => new Assert\Type('int'),
                                'price' => new Assert\Type('float'),
                                'is_active' => new Assert\Type('bool'),
                                'tags' => new Assert\All([
                                    new Assert\Type('int'),
                                ])
                            ]
                        )
                    )
                ],
                'authors' => [
                    new Assert\All(
                        [
                            new Assert\Type('int')
                        ]
                    )
                ],
            ]
        );

        $normalized = $this->requestNormalizer->normalize($data, $rule);

        $this->assertEquals(2, $normalized['books'][0]['author_id']);
        $this->assertEquals(228.5, $normalized['books'][0]['price']);
        $this->assertEquals(true, $normalized['books'][0]['is_active']);
        $this->assertEquals([0, 1], $normalized['books'][0]['tags']);

        $this->assertEquals([1, 2, 3], $normalized['authors']);
    }

    private function getBoolRule(): Assert\Optional
    {
        return new Assert\Optional(
            new Assert\Type('bool')
        );
    }

    private function getIntRule(): Assert\Optional
    {
        return new Assert\Optional(
            new Assert\Type('int')
        );
    }

    private function getFloatRule(): Assert\Optional
    {
        return new Assert\Optional(
            new Assert\Type('numeric')
        );
    }
}