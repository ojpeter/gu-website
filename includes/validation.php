<?php

/**
 * Shared server-side validator — CLAUDE.md Rule 4. Every form (public and
 * admin) goes through this; never a scattered hand-rolled if-check.
 *
 * Usage:
 *   $v = new Validator($_POST);
 *   $v->required('name')->maxLength('name', 150);
 *   $v->required('email')->email('email');
 *   if ($v->fails()) { $errors = $v->errors(); }
 */
class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    private function value(string $field): mixed
    {
        return $this->data[$field] ?? null;
    }

    private function fail(string $field, string $message): static
    {
        $this->errors[$field][] = $message;
        return $this;
    }

    public function required(string $field, string $label = ''): static
    {
        $value = $this->value($field);
        if ($value === null || (is_string($value) && trim($value) === '')) {
            $this->fail($field, ($label ?: $field) . ' is required.');
        }
        return $this;
    }

    public function email(string $field): static
    {
        $value = $this->value($field);
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->fail($field, 'Enter a valid email address.');
        }
        return $this;
    }

    public function maxLength(string $field, int $max): static
    {
        $value = $this->value($field);
        if (is_string($value) && mb_strlen($value) > $max) {
            $this->fail($field, "Must be {$max} characters or fewer.");
        }
        return $this;
    }

    public function minLength(string $field, int $min): static
    {
        $value = $this->value($field);
        if (is_string($value) && mb_strlen(trim($value)) < $min) {
            $this->fail($field, "Must be at least {$min} characters.");
        }
        return $this;
    }

    public function date(string $field, string $format = 'Y-m-d'): static
    {
        $value = $this->value($field);
        if ($value !== null && $value !== '') {
            $d = DateTime::createFromFormat($format, $value);
            if (!$d || $d->format($format) !== $value) {
                $this->fail($field, 'Enter a valid date.');
            }
        }
        return $this;
    }

    public function dateAfterOrEqual(string $field, string $otherField, string $format = 'Y-m-d'): static
    {
        $value = $this->value($field);
        $other = $this->value($otherField);
        if ($value && $other) {
            $d1 = DateTime::createFromFormat($format, $value);
            $d2 = DateTime::createFromFormat($format, $other);
            if ($d1 && $d2 && $d1 < $d2) {
                $this->fail($field, 'Must be on or after the start date.');
            }
        }
        return $this;
    }

    public function isNatural(string $field): static
    {
        $value = $this->value($field);
        if ($value !== null && $value !== '' && (!is_numeric($value) || (int) $value < 0)) {
            $this->fail($field, 'Must be a non-negative number.');
        }
        return $this;
    }

    public function between(string $field, float $min, float $max): static
    {
        $value = $this->value($field);
        if ($value !== null && $value !== '' && is_numeric($value)) {
            if ((float) $value < $min || (float) $value > $max) {
                $this->fail($field, "Must be between {$min} and {$max}.");
            }
        }
        return $this;
    }

    public function inList(string $field, array $allowed): static
    {
        $value = $this->value($field);
        if ($value !== null && $value !== '' && !in_array($value, $allowed, true)) {
            $this->fail($field, 'Invalid selection.');
        }
        return $this;
    }

    public function pattern(string $field, string $regex, string $message = 'Invalid format.'): static
    {
        $value = $this->value($field);
        if ($value !== null && $value !== '' && !preg_match($regex, (string) $value)) {
            $this->fail($field, $message);
        }
        return $this;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}

/**
 * File upload validator — extension whitelist, max size, and REAL
 * server-side MIME re-check via finfo (never trust the client-reported
 * type). CLAUDE.md Rule 4/5.
 *
 * @param array $file  A single $_FILES[...] entry.
 * @param array $allowedExtensions e.g. ['jpg','jpeg','png','webp','pdf']
 * @param array $allowedMimeTypes e.g. ['image/jpeg','image/png','application/pdf']
 * @return string|null Error message, or null if valid.
 */
function validate_upload(array $file, array $allowedExtensions, array $allowedMimeTypes, int $maxBytes = 5 * 1024 * 1024): ?string
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return 'No file was uploaded.';
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return 'Upload failed. Please try again.';
    }
    if ($file['size'] > $maxBytes) {
        return 'File is too large.';
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExtensions, true)) {
        return 'File type not allowed.';
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $realMime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($realMime, $allowedMimeTypes, true)) {
        return 'File content does not match an allowed type.';
    }

    return null;
}
