<?php

namespace App\Form\DataTransformer;

class CentimesTransformer implements \Symfony\Component\Form\DataTransformerInterface
{

    /**
     * @inheritDoc
     */
    public function transform(mixed $value)
    {
        if($value === null) {
            return '';
        }
        return $value / 100;
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform(mixed $value)
    {
        if($value !== null) {
            return'';
        }
        return $value * 100;
    }
}