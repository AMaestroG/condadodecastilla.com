<?php
require_once __DIR__ . '/_header.html';
// Load AI assistant drawer so it is available on every page
echo file_get_contents(__DIR__ . '/fragments/header/ai-drawer.html');
// Load fixed right toolbar
echo file_get_contents(__DIR__ . '/fragments/toolbars/fixed-right-toolbar.html');

