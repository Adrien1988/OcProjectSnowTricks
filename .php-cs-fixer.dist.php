<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->notPath('vendor/')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],

        'binary_operator_spaces' => [
            'operators' => ['=>' => 'align_single_space_minimal', '=' => 'align_single_space_minimal'],
        ],
        'blank_line_after_namespace' => true,
        'blank_line_after_opening_tag' => true,
        'blank_line_before_statement' => [
            'statements' => ['return', 'break', 'continue', 'throw'],
        ],
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'phpdoc_order' => true,
        'phpdoc_trim' => true,
        'single_quote' => true,
        'no_trailing_whitespace' => true,
        'no_extra_blank_lines' => [
            'tokens' => ['extra'],
        ],
        'return_type_declaration' => ['space_before' => 'none'],
        'visibility_required' => ['elements' => ['property', 'method', 'const']],
        'no_blank_lines_after_phpdoc' => true,
    ])
    ->setFinder($finder)
;
