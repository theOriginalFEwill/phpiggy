<?php

declare(strict_types=1);

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;

class MatchRule implements RuleInterface
{
    public function validate(array $data, string $field, array $params): bool
    {
        $field1 = $data[$field];
        $field2 = $data[$params[0]];

        return $field1 === $field2;
    }

    public function getMessage(array $data, string $field, array $params): string
    {
        return "Does not match {$params[0]} field";
    }
}
