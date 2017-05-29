<?php

declare(strict_types=1);

// PHP-CS-Fixer v2.3.2
return PhpCsFixer\Config::create()
    ->setCacheFile(__DIR__ . '/var/cache/.php-cs-fixer.cache')
    ->setRiskyAllowed(true)
    ->setRules([
        'array_syntax'                                => [
            'syntax' => 'short',
        ],
        'binary_operator_spaces'                      => [
            'align_double_arrow' => true,
            'align_equals'       => true,
        ],
        'blank_line_after_namespace'                  => true,
        'blank_line_after_opening_tag'                => true,
        'blank_line_before_return'                    => true,
        'braces'                                      => [
            'allow_single_line_closure'                   => true,
            'position_after_functions_and_oop_constructs' => 'next',
        ],
        'cast_spaces'                                 => true,
        'class_definition'                            => [
            'multiLineExtendsEachSingleLine' => true,
            'singleLine'                     => true,
        ],
        'concat_space'                                => [
            'spacing' => 'one',
        ],
        'declare_equal_normalize'                     => [
            'space' => 'none',
        ],
        'declare_strict_types'                        => true, // forcing strict types will stop non strict code from working
        'dir_constant'                                => true, // risky when the function "dirname()" is overridden
        'elseif'                                      => true,
        'encoding'                                    => true,
        'ereg_to_preg'                                => true, // risky if the "ereg" function is overridden
        'full_opening_tag'                            => true,
        'function_declaration'                        => [
            'closure_function_spacing' => 'one',
        ],
        'function_to_constant'                        => true,  // risky when any of the configured functions to replace are overridden.
        'function_typehint_space'                     => true,
        'general_phpdoc_annotation_remove'            => [
            'annotations' => [
                'author',
                'category',
                'copyright',
                'version',
            ],
        ],
        'hash_to_slash_comment'                       => true,
        'heredoc_to_nowdoc'                           => true,
        'include'                                     => true,
        'indentation_type'                            => true,
        'is_null'                                     => [ // risky when the function "is_null()" is overridden.
            'use_yoda_style' => true,
        ],
        'line_ending'                                 => true,
        'linebreak_after_opening_tag'                 => true,
        'list_syntax'                                 => [
            'syntax' => 'long',
        ],
        'lowercase_cast'                              => true,
        'lowercase_constants'                         => true,
        'lowercase_keywords'                          => true,
        'magic_constant_casing'                       => true,
        'mb_str_functions'                            => true, // risky when any of the functions are overridden.
        'method_argument_space'                       => [
            'keep_multiple_spaces_after_comma' => true,
        ],
        'method_separation'                           => true,
        'modernize_types_casting'                     => true, // risky if any of the functions "intval", "floatval", "doubleval", "strval" or "boolval" are overridden.
        'native_function_casing'                      => true,
        'new_with_braces'                             => true,
        'no_alias_functions'                          => true, // risky when any of the alias functions are overridden.
        'no_blank_lines_after_class_opening'          => true,
        'no_blank_lines_after_phpdoc'                 => true,
        'no_closing_tag'                              => true,
        'no_empty_comment'                            => true,
        'no_empty_phpdoc'                             => true,
        'no_empty_statement'                          => true,
        'no_extra_consecutive_blank_lines'            => [
            'tokens' => [
                'continue',
                'curly_brace_block',
                'extra',
                'parenthesis_brace_block',
                'return',
                'square_brace_block',
                'throw',
                'use',
                'use_trait',
            ],
        ],
        'no_leading_import_slash'                     => true,
        'no_leading_namespace_whitespace'             => true,
        'no_mixed_echo_print'                         => [
            'use' => 'echo',
        ],
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_multiline_whitespace_before_semicolons'   => true,
        'no_php4_constructor'                         => true, // risky when old style constructor being fixed is overridden or overrides parent one
        'no_short_bool_cast'                          => true,
        'no_singleline_whitespace_before_semicolons'  => true,
        'no_spaces_after_function_name'               => true,
        'no_spaces_around_offset'                     => [
            'positions' => [
                'inside',
                'outside',
            ],
        ],
        'no_spaces_inside_parenthesis'                => true,
        'no_trailing_comma_in_list_call'              => true,
        'no_trailing_comma_in_singleline_array'       => true,
        'no_trailing_whitespace'                      => true,
        'no_trailing_whitespace_in_comment'           => true,
        'no_unneeded_control_parentheses'             => [
            'statements' => [
                'break',
                'clone',
                'continue',
                'echo_print',
                'return',
                'switch_case',
                'yield',
            ],
        ],
        'no_unreachable_default_argument_value'       => true, // modifies the signature of functions; therefore risky when using systems (such as some Symfony components) that rely on those (for example through reflection)
        'no_unused_imports'                           => true,
        'no_useless_else'                             => true,
        'no_useless_return'                           => true,
        'no_whitespace_before_comma_in_array'         => true,
        'no_whitespace_in_blank_line'                 => true,
        'non_printable_character'                     => true, // risky when strings contain intended invisible characters
        'normalize_index_brace'                       => true,
        'object_operator_without_whitespace'          => true,
        'ordered_class_elements'                      => true,
        'ordered_imports'                             => [
            'sortAlgorithm' => 'alpha',
        ],
        'php_unit_construct'                          => true, // fixer could be risky if one is overriding PHPUnit's native methods
        'php_unit_dedicate_assert'                    => true, // fixer could be risky if one is overriding PHPUnit's native methods
        'php_unit_fqcn_annotation'                    => true,
        'php_unit_strict'                             => true, // risky when any of the functions are overridden
        'php_unit_test_class_requires_covers'         => true,
        'phpdoc_align'                                => true,
        'phpdoc_annotation_without_dot'               => true,
        'phpdoc_indent'                               => true,
        'phpdoc_inline_tag'                           => true,
        'phpdoc_no_access'                            => true,
        'phpdoc_no_alias_tag'                         => true,
        'phpdoc_no_empty_return'                      => true,
        'phpdoc_no_package'                           => true,
        'phpdoc_no_useless_inheritdoc'                => true,
        'phpdoc_order'                                => true,
        'phpdoc_return_self_reference'                => true,
        'phpdoc_scalar'                               => true,
        'phpdoc_separation'                           => true,
        'phpdoc_single_line_var_spacing'              => true,
        'phpdoc_summary'                              => false,
        'phpdoc_to_comment'                           => true,
        'phpdoc_trim'                                 => true,
        'phpdoc_types'                                => true,
        'phpdoc_var_without_name'                     => true,
        'pow_to_exponentiation'                       => true, // risky when the function "pow()" is overridden
        'pre_increment'                               => true,
        'protected_to_private'                        => true,
        'psr4'                                        => true, // this fixer may change you class name, which will break the code that is depended on old name
        'random_api_migration'                        => true, // risky when the configured functions are overridden
        'return_type_declaration'                     => [
            'space_before' => 'one',
        ],
        'self_accessor'                               => true,
        'semicolon_after_instruction'                 => true,
        'short_scalar_cast'                           => true,
        'silenced_deprecation_error'                  => true, // silencing of deprecation errors might cause changes to code behaviour
        'single_blank_line_at_eof'                    => true,
        'single_blank_line_before_namespace'          => true,
        'single_class_element_per_statement'          => [
            'elements' => [
                'const',
                'property',
            ],
        ],
        'single_import_per_statement'                 => true,
        'single_line_after_imports'                   => true,
        'single_quote'                                => true,
        'space_after_semicolon'                       => true,
        'standardize_not_equals'                      => true,
        'strict_comparison'                           => true, // changing comparisons to strict might change code behavior
        'strict_param'                                => true, // risky when the fixed function is overridden or if the code relies on non-strict usage
        'switch_case_semicolon_to_colon'              => true,
        'switch_case_space'                           => true,
        'ternary_operator_spaces'                     => true,
        'ternary_to_null_coalescing'                  => true,
        'trailing_comma_in_multiline_array'           => true,
        'trim_array_spaces'                           => true,
        'unary_operator_spaces'                       => false,
        'visibility_required'                         => [
            'elements' => [
                'const',
                'property',
                'method',
            ],
        ],
        'whitespace_after_comma_in_array'             => true,
    ]);
