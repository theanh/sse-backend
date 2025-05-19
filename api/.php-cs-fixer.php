<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/app')
    ->in(__DIR__.'/tests')
    ->in(__DIR__.'/database')
    ->in(__DIR__.'/routes');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'ordered_imports' => false,
        'single_quote' => true,
        'blank_line_after_namespace' => true,
        'no_extra_blank_lines' => true,
        'no_whitespace_in_blank_line' => true,
        'no_trailing_whitespace' => true,
        'declare_strict_types' => false,
    ])
    ->setFinder($finder);
