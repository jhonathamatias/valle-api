<?php

namespace Valle\Services;

class ValidatePostProps
{
    public static function verify(object $post, array $expectedProps): bool
    {
        $missingProps = [];

        foreach ($expectedProps as $prop) {
            if (isset($post->$prop) === false) {
                $missingProps[] = $prop;
            }
        }

        if (count($missingProps) > 0 ) {
            throw new \Exception('Você precisa informar o (' . implode(', ', $missingProps) . ')');
        }

        return true;
    }
}