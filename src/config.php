<?php

use Dotenv\Dotenv;

$dotenv = new Dotenv(dirname(__DIR__, 1));
$dotenv->load();

// Defaulte date for São Paulo
date_default_timezone_set('America/Sao_Paulo');

// Default Language for dates
setlocale(LC_ALL, 'pt');

// Desativa cache auto do php
session_cache_limiter('');