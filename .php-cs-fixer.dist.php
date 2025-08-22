<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude('vendor');

return (new Config())
    ->setRiskyAllowed(false)
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['default' => 'single_space'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
        'no_whitespace_in_blank_line' => true,
        'no_extra_blank_lines' => ['tokens' => ['curly_brace_block']],
        'braces' => [
            'position_after_functions_and_oop_constructs' => 'next'
        ], 
    ])
    ->setFinder($finder);
