<?php
/**
 * Server-side validation helpers
 */

class Validator
{
    private array $errors = [];
    private array $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (!isset($this->data[$field]) || trim($this->data[$field]) === '') {
            $this->errors[$field] = "{$label} is required.";
        }
        return $this;
    }

    public function email(string $field, string $label = 'Email'): self
    {
        if (!empty($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "{$label} must be a valid email address.";
        }
        return $this;
    }

    public function min(string $field, int $min, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (!empty($this->data[$field]) && strlen($this->data[$field]) < $min) {
            $this->errors[$field] = "{$label} must be at least {$min} characters.";
        }
        return $this;
    }

    public function max(string $field, int $max, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (!empty($this->data[$field]) && strlen($this->data[$field]) > $max) {
            $this->errors[$field] = "{$label} must not exceed {$max} characters.";
        }
        return $this;
    }

    public function numeric(string $field, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (!empty($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field] = "{$label} must be a number.";
        }
        return $this;
    }

    public function min_value(string $field, float $min, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (isset($this->data[$field]) && $this->data[$field] !== '' && (float)$this->data[$field] < $min) {
            $this->errors[$field] = "{$label} must be at least {$min}.";
        }
        return $this;
    }

    public function in(string $field, array $allowed, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (!empty($this->data[$field]) && !in_array($this->data[$field], $allowed)) {
            $this->errors[$field] = "{$label} has an invalid value.";
        }
        return $this;
    }

    public function date(string $field, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (!empty($this->data[$field])) {
            $d = DateTime::createFromFormat('Y-m-d', $this->data[$field]);
            if (!$d || $d->format('Y-m-d') !== $this->data[$field]) {
                $this->errors[$field] = "{$label} must be a valid date.";
            }
        }
        return $this;
    }

    public function future_date(string $field, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (!empty($this->data[$field]) && $this->data[$field] < date('Y-m-d')) {
            $this->errors[$field] = "{$label} must be today or a future date.";
        }
        return $this;
    }

    public function confirmed(string $field, string $confirmField = null): self
    {
        $confirmField = $confirmField ?? $field . '_confirmation';
        $label = ucfirst(str_replace('_', ' ', $field));
        if (($this->data[$field] ?? '') !== ($this->data[$confirmField] ?? '')) {
            $this->errors[$field] = "{$label} confirmation does not match.";
        }
        return $this;
    }

    public function unique(string $field, string $table, ?int $excludeId = null, string $column = null): self
    {
        $column = $column ?? $field;
        $label = ucfirst(str_replace('_', ' ', $field));
        if (!empty($this->data[$field])) {
            $db = Database::getConnection();
            $sql = "SELECT id FROM {$table} WHERE {$column} = ?";
            $params = [$this->data[$field]];
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            if ($stmt->fetch()) {
                $this->errors[$field] = "{$label} is already taken.";
            }
        }
        return $this;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
