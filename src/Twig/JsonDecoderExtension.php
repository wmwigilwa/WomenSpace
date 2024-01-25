<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class JsonDecoderExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('json_decoder', [$this, 'decodeString']),
        ];
    }

    public function decodeString($content): string
    {
       $data = json_decode($content);


       if (is_array($data))
            return implode(', ', $data);
       return $content;
    }
}
