<?php

return PhpCsFixer\Config::create()
    ->setUsingCache(false)
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony'                => true,
        '@Symfony:risky'          => true,
        'binary_operator_spaces'  => [ 'align_double_arrow' => true, 'align_equals' => true ],
        'class_definition'        => [ 'multiLineExtendsEachSingleLine' => true ],
        'concat_space'            => [ 'spacing' => 'one' ],
        'declare_strict_types'    => true,
        'dir_constant'            => true,
        'ereg_to_preg'            => true,
        'mb_str_functions'        => true,
        'modernize_types_casting' => true,
        'no_php4_constructor'     => true,
        'php_unit_strict'         => true,
        'psr4'                    => true,
        'return_type_declaration' => [ 'space_before' => 'one' ],
        'simplified_null_return'  => true,
        'strict_comparison'       => true,
        'strict_param'            => true,
    ])
;
