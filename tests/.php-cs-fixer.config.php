<?php

declare(strict_types=1);

return PhpCsFixer\Config::create()
    ->setUsingCache(false)
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony'                            => true,
        '@Symfony:risky'                      => true,
        'array_syntax'                        => ['syntax' => 'short'],
        'binary_operator_spaces'              => ['align_double_arrow' => true, 'align_equals' => true],
        'class_definition'                    => ['multiLineExtendsEachSingleLine' => true],
        'concat_space'                        => ['spacing' => 'one'],
        'declare_strict_types'                => true,
        'dir_constant'                        => true,
        'ereg_to_preg'                        => true,
        'linebreak_after_opening_tag'         => true,
        'mb_str_functions'                    => true,
        'modernize_types_casting'             => true,
//        'native_function_invocation'          => true,
        'no_php4_constructor'                 => true,
        'no_useless_else'                     => true,
        'ordered_class_elements'              => true,
        'ordered_imports'                     => ['sortAlgorithm' => 'alpha'],
        'php_unit_strict'                     => true,
        'php_unit_test_class_requires_covers' => true,
        'pow_to_exponentiation'               => true,
        'psr4'                                => true,
        'random_api_migration'                => true,
        'return_type_declaration'             => [ 'space_before' => 'one' ],
        'strict_comparison'                   => true,
        'strict_param'                        => true,
        'ternary_to_null_coalescing'          => true,
        'visibility_required'                 => ['elements' => ['const', 'property', 'method']],
    ]);
