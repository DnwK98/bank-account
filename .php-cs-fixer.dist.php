<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->exclude('var');
$config = new PhpCsFixer\Config();

return $config
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR1' => true,
        '@PSR2' => true,
        '@PSR12' => true,
        'no_useless_return' => true,
        'no_useless_else' => true,
        'declare_strict_types' => true,
        'no_superfluous_phpdoc_tags' => false,
        'global_namespace_import' => false,
        'phpdoc_align' => false,
        'phpdoc_to_comment' => false,
        'cast_spaces' => false,
        'concat_space' => false,
    ])
;
