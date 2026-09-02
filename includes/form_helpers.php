<?php
/** Shared helpers for form endpoints. */

function json_out(array $data): void
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

function clean_val($s): string
{
    return trim((string) $s);
}

/** Name: letters, spaces, . ' - only; at least 2 chars; no digits. */
function valid_name(string $s): bool
{
    $s = trim($s);
    return mb_strlen($s) >= 2 && (bool) preg_match("/^[A-Za-z][A-Za-z .'\\-]*$/", $s);
}

function valid_email(string $s): bool
{
    return (bool) filter_var(trim($s), FILTER_VALIDATE_EMAIL);
}

/** Exactly 10 digits. */
function valid_phone10(string $s): bool
{
    return (bool) preg_match('/^\d{10}$/', trim($s));
}

function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '';
}
