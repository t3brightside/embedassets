<?php
  $EM_CONF[$_EXTKEY] = [
    'title' => 'Embed Assets',
    'description' => 'Embed CSS and JS files for Fluid f:asset',
    'category' => 'fe',
    'version' => '1.0.0',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'author' => 'Tanel Põld',
    'author_email' => 'tanel@brightside.ee',
    'author_company' => 'Brightside OÜ',
    'constraints' => [
      'depends' => [
        'typo3' => '11.5.0-12.99.99',
      ],
    ],
  ];
