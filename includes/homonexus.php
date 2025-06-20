<?php

function homonexus_body_class(): string
{
    $active = isset($_COOKIE['homonexus']) && $_COOKIE['homonexus'] === 'on';
    return $active ? 'homonexus-active' : '';
}
