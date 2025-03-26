<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\MarkdownRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class MarkdownExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown', [MarkdownRuntime::class, 'markdownToHtml']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('markdown', [MarkdownRuntime::class, 'markdownToHtml']),
        ];
    }
}
