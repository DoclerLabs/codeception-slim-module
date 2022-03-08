<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/test')
    ->exclude('support/_generated')
    ->name('*.php');

$config = new PhpCsFixer\Config();
return $config->setRules(
        [
            '@PSR2'                                     => true,
            'array_syntax'                              => ['syntax' => 'short'],
            'binary_operator_spaces'                    => [
                'default' => 'align_single_space_minimal',
            ],
            'blank_line_before_statement'               => [
                'statements' => ['break', 'continue', 'return', 'try'],
            ],
            'braces'                                    => [
                'allow_single_line_closure' => true,
            ],
            'class_attributes_separation'               => [
                'elements' => ['const' => 'one', 'method' => 'one', 'property' => 'one', 'trait_import' => 'none'],
            ],
            'declare_strict_types'                      => true,
            'no_alternative_syntax'                     => true,
            'no_leading_import_slash'                   => true,
            'multiline_whitespace_before_semicolons'    => [
                'strategy' => 'no_multi_line'
            ],
            'echo_tag_syntax'                           => [
                'format' => 'long'
            ],
            'no_spaces_inside_parenthesis'              => true,
            'no_useless_else'                           => true,
            'not_operator_with_space'                   => false,
            'not_operator_with_successor_space'         => false,
            'ordered_imports'                           => true,
            'phpdoc_add_missing_param_annotation'       => true,
            'phpdoc_align'                              => [
                'align' => 'vertical',
            ],
            'phpdoc_indent'                             => true,
            'phpdoc_no_package'                         => true,
            'phpdoc_order'                              => true,
            'phpdoc_scalar'                             => [
                'types' => ['boolean', 'double', 'integer', 'real', 'str'],
            ],
            'phpdoc_separation'                         => true,
            'phpdoc_single_line_var_spacing'            => true,
            'phpdoc_to_comment'                         => true,
            'phpdoc_trim'                               => true,
            'phpdoc_var_without_name'                   => true,
            'return_type_declaration'                   => [
                'space_before' => 'none',
            ],
            'single_quote'                              => true,
            'ternary_operator_spaces'                   => true,
            'trailing_comma_in_multiline'               => [
                'elements' => ['arrays']
            ],
            'trim_array_spaces'                         => true,
            'single_line_after_imports'                 => true,
            'unary_operator_spaces'                     => true,
            'visibility_required'                       => true,
            'yoda_style'                                => false,
        ]
    )
    ->setRiskyAllowed(true)
    ->setIndent('    ')
    ->setUsingCache(false)
    ->setFinder($finder);
