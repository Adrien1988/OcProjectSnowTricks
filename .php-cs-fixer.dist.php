<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->exclude(['var', 'vendor', 'tests', 'migrations']);

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => [
            'operators' => ['=>' => 'align_single_space', '=' => 'single_space'],
        ],
        'blank_line_after_namespace' => true,
        'blank_line_after_opening_tag' => true,
        'class_attributes_separation' => [
            'elements' => ['method' => 'one'], // Une ligne avant chaque méthode.
        ],
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'phpdoc_no_empty_return' => false,
        'phpdoc_order' => true,
        'phpdoc_trim' => true,
        'phpdoc_align' => true,
        'phpdoc_summary' => false,
        'no_superfluous_phpdoc_tags' => false,
        'single_quote' => true,
        'no_trailing_whitespace' => true,
        'no_extra_blank_lines' => [
            'tokens' => ['break', 'continue', 'throw', 'use'],
        ],
        'return_type_declaration' => ['space_before' => 'none'],
        'visibility_required' => ['elements' => ['property', 'method', 'const']],
        'no_blank_lines_after_phpdoc' => false,
        'yoda_style' => false,
        'align_multiline_comment' => true,
    ])
    ->setFinder($finder);
