<?php

declare(strict_types=1);

namespace Codercms\FormRequest;

use Symfony\Component\Validator\Constraints as Assert;

use function is_array;
use function count;

class RequestNormalizer implements RequestNormalizerInterface
{
    private ValueNormalizer $valueNormalizer;

    public function __construct(ValueNormalizer $valueNormalizer)
    {
        $this->valueNormalizer = $valueNormalizer;
    }

    public function normalize(array $data, Assert\Collection $collection): array
    {
        foreach ($collection->fields as $field => $constraint) {
            if (!isset($data[$field])) {
                continue;
            }

            if ($constraint instanceof Assert\Existence) {
                $data[$field] = $this->normalizeDataByExistence($data[$field], $constraint);
            } elseif ($constraint instanceof Assert\All && is_array($data)) {
                $data = $this->normalizeDataByAll($data, $constraint);
            }
        }

        return $data;
    }

    protected function normalizeDataByExistence($data, Assert\Existence $existence)
    {
        foreach ($existence->constraints as $constraint) {
            if ($constraint instanceof Assert\Type) {
                $data = $this->normalizeValueByType($data, $constraint);
            } elseif (is_array($data)) {
                if ($constraint instanceof Assert\Collection) {
                    $data = $this->normalize($data, $constraint);
                } elseif ($constraint instanceof Assert\All) {
                    $data = $this->normalizeDataByAll($data, $constraint);
                }
            }
        }

        return $data;
    }

    protected function normalizeDataByAll(array $data, Assert\All $existence): array
    {
        foreach ($existence->constraints as $constraint) {
            if ($constraint instanceof Assert\Collection) {
                foreach ($data as $key => $value) {
                    if (is_array($value)) {
                        $data[$key] = $this->normalize($value, $constraint);
                    }
                }
            } elseif ($constraint instanceof Assert\Type) {
                foreach ($data as $key => $value) {
                    $data[$key] = $this->normalizeValueByType($value, $constraint);
                }
            }
        }

        return $data;
    }

    protected function normalizeValueByType($value, Assert\Type $constraint)
    {
        if (is_array($constraint->type)) {
            if (count($constraint->type) > 1) {
                return $value;
            }

            $type = $constraint->type[0];
        } else {
            $type = $constraint->type;
        }

        if ($type === 'bool' || $type === 'boolean') {
            return $this->valueNormalizer->toBool($value);
        }

        if ($type === 'int' || $type === 'integer') {
            return $this->valueNormalizer->toInt($value);
        }

        if ($type === 'float' || $type === 'numeric') {
            return $this->valueNormalizer->toFloat($value);
        }

        return $value;
    }
}
