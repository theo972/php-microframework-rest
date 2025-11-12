<?php

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__.'/src', __DIR__.'/public'])
    ->name('*.php')
    ->exclude(['vendor']);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(false)
    ->setRules([
        '@PSR12' => true,
        'binary_operator_spaces' => ['default' => 'align_single_space_minimal'],
        'no_unused_imports' => true,
        'no_trailing_whitespace' => true,
        'single_quote' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'declare_strict_types' => false,
        'phpdoc_align' => ['align' => 'vertical'],
    ])
    ->setFinder($finder);
