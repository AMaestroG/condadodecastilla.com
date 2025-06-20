<?php

function ensure_session_started()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}
