<?php

namespace GlobalHelpers\Extensions;

use Michelf\MarkdownExtra;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ExtensionTwig extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown', [$this, 'markdownParser'], ['is_safe'=>true]),
        ];
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('assets', [$this, 'asset']),
        ];
    }

  
    /**
     * @author ASSANE DIONE <atpro0290@gmail.com>
     */
    public function markdownParser($value): string
    {
        return MarkdownExtra::defaultTransform($value);
    }

    /**
     * @author ASSANE DIONE <atpro0290@gmail.com>
     * @param string $link {{ l'url du fichier }}
     * @return string
     */
    public function asset(string $link): string
    {
            return asset($link);
    }

   
}
