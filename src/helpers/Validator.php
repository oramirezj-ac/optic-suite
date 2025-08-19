<?php

/**
 * Validation helper class
 * Ejemplo de mejora: Validación centralizada y segura
 */
class Validator
{
    private $errors = [];
    private $data = [];

    /**
     * Validate a set of data against rules
     * 
     * @param array $data Data to validate
     * @param array $rules Validation rules
     * @return bool
     */
    public function validate(array $data, array $rules): bool
    {
        $this->data = $data;
        $this->errors = [];

        foreach ($rules as $field => $ruleSet) {
            $fieldRules = explode('|', $ruleSet);
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get first error for a field
     */
    public function getError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Apply a single validation rule
     */
    private function applyRule(string $field, $value, string $rule): void
    {
        $ruleParts = explode(':', $rule);
        $ruleName = $ruleParts[0];
        $ruleParams = $ruleParts[1] ?? null;

        switch ($ruleName) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, "El campo {$field} es requerido");
                }
                break;

            case 'min':
                if (!empty($value) && strlen($value) < (int)$ruleParams) {
                    $this->addError($field, "El campo {$field} debe tener al menos {$ruleParams} caracteres");
                }
                break;

            case 'max':
                if (!empty($value) && strlen($value) > (int)$ruleParams) {
                    $this->addError($field, "El campo {$field} no debe exceder {$ruleParams} caracteres");
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "El campo {$field} debe ser un email válido");
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "El campo {$field} debe ser numérico");
                }
                break;

            case 'date':
                if (!empty($value)) {
                    $date = DateTime::createFromFormat('Y-m-d', $value);
                    if (!$date || $date->format('Y-m-d') !== $value) {
                        $this->addError($field, "El campo {$field} debe ser una fecha válida (YYYY-MM-DD)");
                    }
                }
                break;

            case 'alpha':
                if (!empty($value) && !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $value)) {
                    $this->addError($field, "El campo {$field} solo debe contener letras");
                }
                break;
        }
    }

    /**
     * Add an error for a field
     */
    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Sanitize input data
     */
    public static function sanitize(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = trim(filter_var($value, FILTER_SANITIZE_STRING));
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Escape output for safe HTML display
     */
    public static function escape($value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}