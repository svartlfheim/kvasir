<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude(['var', 'vendor', 'public', 'tools', 'config', 'bin'])
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@PSR12' => true,
        // 'strict_param' => false,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'ordered_imports' => true,
        'ordered_traits' => true,
        'void_return' => true,

    ])
    ->setFinder($finder)
;