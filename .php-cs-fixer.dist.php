<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = (new Finder())
    ->in([__DIR__ . '/src', __DIR__ . '/public', __DIR__ . '/tests'])
    ->exclude('var');

return (new Config())
    ->setRules([
        '@PhpCsFixer'     => true,
        '@PHP82Migration' => true,

        'blank_line_after_namespace'             => true,
        'single_import_per_statement'            => true,
        'single_line_after_imports'              => true,
        'no_unused_imports'                      => true,
        'multiline_whitespace_before_semicolons' => false,
        'phpdoc_to_comment'                      => false,

        'ordered_imports'         => true,
        'global_namespace_import' => true,
        'ordered_class_elements'  => [
            'order' => [
                'use_trait',
                'constant',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public_static',
                'property_public',
                'property_protected_static',
                'property_protected',
                'property_private_static',
                'property_private',
                'construct',
                'destruct',
                'magic',
                'phpunit',
                'method_public_static',
                'method_public',
                'method_protected_static',
                'method_protected',
                'method_private_static',
                'method_private',
            ],
        ],

        'php_unit_test_class_requires_covers' => false,
        'method_chaining_indentation'         => false,
        'php_unit_internal_class'             => false,

        'self_static_accessor' => true,

        'ternary_to_null_coalescing' => true,
        'binary_operator_spaces'     => [
            'default'   => 'single_space',
            'operators' => [
                '='   => 'align_single_space_minimal',
                '+='  => 'align_single_space_minimal',
                '-='  => 'align_single_space_minimal',
                '/='  => 'align_single_space_minimal',
                '*='  => 'align_single_space_minimal',
                '%='  => 'align_single_space_minimal',
                '**=' => 'align_single_space_minimal',
                '=>'  => 'align_single_space_minimal',
            ],
        ],
        'yoda_style' => [
            'equal'            => false,
            'identical'        => false,
            'less_and_greater' => null,
        ],
        'concat_space' => [
            'spacing' => 'one',
        ],
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'single_space_around_construct'           => true,
        'control_structure_braces'                => true,
        'control_structure_continuation_position' => true,
        'declare_parentheses'                     => true,
        'statement_indentation'                   => true,
        'no_multiple_statements_per_line'         => true,
        'no_extra_blank_lines'                    => true,
        'elseif'                                  => true,
        'trim_array_spaces'                       => true,

        'function_declaration'          => true,
        'no_spaces_after_function_name' => true,
        'spaces_inside_parentheses'     => true,

        'cast_spaces'                 => true,
        'encoding'                    => true,
        'full_opening_tag'            => true,
        'linebreak_after_opening_tag' => true,
        'no_closing_tag'              => true,
        'indentation_type'            => true,
        'line_ending'                 => true,
        'single_blank_line_at_eof'    => false,
        'no_trailing_whitespace'      => true,
        'lowercase_keywords'          => true,
        'no_whitespace_in_blank_line' => true,
        'echo_tag_syntax'             => true,

        'doctrine_annotation_braces'           => false,
        'doctrine_annotation_array_assignment' => [
            'operator' => '=',
        ],

        'align_multiline_comment' => [
            'comment_type' => 'all_multiline',
        ],

        'no_superfluous_phpdoc_tags'          => false,
        'phpdoc_order'                        => true,
        'phpdoc_separation'                   => true,
        'phpdoc_var_without_name'             => false,
        'phpdoc_no_empty_return'              => false,
        'phpdoc_var_annotation_correct_order' => true,
        'phpdoc_types_order'                  => [
            'sort_algorithm'  => 'none',
            'null_adjustment' => 'always_last',
        ],

        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'case',
                'continue',
                'declare',
                'default',
                'do',
                'exit',
                'for',
                'foreach',
                'goto',
                'if',
                'include',
                'include_once',
                'require',
                'require_once',
                'return',
                'switch',
                'throw',
                'try',
                'while',
                'yield',
            ],
        ],

        'nullable_type_declaration_for_default_null_value' => true,

        'phpdoc_add_missing_param_annotation' => [
            'only_untyped' => false,
        ],
    ])
    ->setFinder($finder);
