<?php
  $EM_CONF[$_EXTKEY] = [
    'title' => 'Embed Assets',
    'description' => 'Fluid viewhelpers for embed and minified CSS/JS',
    'category' => 'fe',
    'version' => '1.4.0',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'author' => 'Tanel Põld',
    'author_email' => 'tanel@brightside.ee',
    'author_company' => 'Brightside OÜ',
    'constraints' => [
      'depends' => [
        'typo3' => '11.5.0-14.99.99',
      ],
    ],
  ];
