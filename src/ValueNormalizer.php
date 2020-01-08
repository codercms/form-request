<?php

declare(strict_types=1);

namespace Codercms\FormRequest;

use function is_bool;
use function is_int;
use function is_float;
use function is_string;
use function is_numeric;

class ValueNormalizer
{
    /**
     * @param mixed $value
     * @return bool|mixed
     */
    public function toBool($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            if (0 === $value || 1 === $value) {
                return (bool)$value;
            }

            return $value;
        }

        if (is_string($value)) {
            if ('0' === $value || '1' === $value) {
                return (bool)$value;
            }

            if ('false' === $value || 'true' === $value) {
                return 'true' === $value;
            }
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @return int|mixed
     */
    public function toInt($value)
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_float($value)) {
            return (int)$value;
        }

        if (is_bool($value)) {
            return (int)$value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (int)$value;
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @return float|mixed
     */
    public function toFloat($value)
    {
        if (is_float($value)) {
            return $value;
        }

        if (is_int($value)) {
            return (float)$value;
        }

        if (is_bool($value)) {
            return (float)$value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (float)$value;
        }

        return $value;
    }
}