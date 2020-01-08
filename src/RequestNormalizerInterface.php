<?php

declare(strict_types=1);

namespace Codercms\FormRequest;

use Symfony\Component\Validator\Constraints as Assert;

interface RequestNormalizerInterface
{
    public function normalize(array $data, Assert\Collection $collection);
}