<?php
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Groomershop_SwatchTable',
    isset($file) && realpath($file) == __FILE__ ? dirname($file) : __DIR__
);
