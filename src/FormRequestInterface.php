<?php

declare(strict_types=1);

namespace Codercms\FormRequest;

interface FormRequestInterface
{
    public function handle(array $data): array;
}