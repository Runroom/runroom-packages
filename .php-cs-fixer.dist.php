<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$header = <<<'HEADER'
This file is part of the Runroom package.

(c) Runroom <runroom@runroom.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
HEADER;

$finder = Finder::create()
    ->exclude(['var'])
    ->in(__DIR__);

$config = new Config();

$config->setParallelConfig(ParallelConfigFactory::detect());
$config->setRules([
    '@PSR12' => true,
    '@PSR12:risky' => true,
    '@Symfony' => true,
    '@Symfony:risky' => true,
    'combine_consecutive_issets' => true,
    'combine_consecutive_unsets' => true,
    'concat_space' => ['spacing' => 'one'],
    'declare_strict_types' => true,
    'global_namespace_import' => ['import_classes' => false, 'import_constants' => false, 'import_functions' => false],
    'header_comment' => ['header' => $header],
    'list_syntax' => ['syntax' => 'short'],
    'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
    'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
    'no_useless_else' => true,
    'no_useless_return' => true,
    'ordered_class_elements' => true,
    'ordered_imports' => ['sort_algorithm' => 'alpha', 'imports_order' => ['class', 'function', 'const']],
    'phpdoc_order' => ['order' => ['var', 'param', 'throws', 'return', 'phpstan-var', 'psalm-var', 'phpstan-param', 'psalm-param', 'phpstan-return', 'psalm-return']],
    'phpdoc_separation' => ['groups' => [
        ['phpstan-template', 'phpstan-template-covariant', 'phpstan-extends', 'phpstan-implements', 'phpstan-var', 'psalm-var', 'phpstan-param', 'psalm-param', 'phpstan-return', 'psalm-return'],
        ['psalm-suppress', 'phpstan-ignore-next-line'],
        ['Assert\\*'],
        ['ODM\\*'],
        ['ORM\\*'],
    ]],
    'php_unit_strict' => true,
    'php_unit_test_case_static_method_calls' => true,
    'phpdoc_to_comment' => ['ignored_tags' => ['psalm-suppress', 'phpstan-var', 'phpstan-ignore-next-line', 'todo', 'return']],
    'single_line_empty_body' => true,
    'single_line_throw' => false,
    'static_lambda' => true,
    'strict_comparison' => true,
    'strict_param' => true,
    'void_return' => false,
])
->setRiskyAllowed(true)
->setFinder($finder);

return $config;
