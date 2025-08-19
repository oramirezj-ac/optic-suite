/**
 * Form Validation Module
 * Ejemplo de mejora: Validación client-side robusta
 */

class FormValidator {
    constructor(formSelector, options = {}) {
        this.form = document.querySelector(formSelector);
        this.options = {
            realTimeValidation: true,
            showErrorMessages: true,
            errorClass: 'is-invalid',
            successClass: 'is-valid',
            ...options
        };
        
        this.rules = {};
        this.messages = {};
        
        if (this.form) {
            this.init();
        }
    }

    /**
     * Initialize validator
     */
    init() {
        this.setupEventListeners();
        this.setupDefaultMessages();
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Form submission
        this.form.addEventListener('submit', (e) => {
            if (!this.validateForm()) {
                e.preventDefault();
                this.focusFirstError();
            }
        });

        // Real-time validation
        if (this.options.realTimeValidation) {
            this.form.addEventListener('blur', (e) => {
                if (e.target.matches('input, select, textarea')) {
                    this.validateField(e.target);
                }
            }, true);

            this.form.addEventListener('input', (e) => {
                if (e.target.matches('input, select, textarea')) {
                    // Clear error on input to provide immediate feedback
                    if (e.target.classList.contains(this.options.errorClass)) {
                        this.clearFieldError(e.target);
                    }
                }
            });
        }
    }

    /**
     * Setup default error messages
     */
    setupDefaultMessages() {
        this.defaultMessages = {
            required: 'Este campo es requerido',
            email: 'Ingresa un email válido',
            min: 'Debe tener al menos {0} caracteres',
            max: 'No debe exceder {0} caracteres',
            numeric: 'Debe ser un número válido',
            date: 'Debe ser una fecha válida',
            alpha: 'Solo se permiten letras',
            alphanumeric: 'Solo se permiten letras y números',
            phone: 'Debe ser un teléfono válido'
        };
    }

    /**
     * Add validation rules for a field
     */
    rules(fieldName, rules) {
        this.rules[fieldName] = rules;
        return this;
    }

    /**
     * Add custom messages for a field
     */
    messages(fieldName, messages) {
        this.messages[fieldName] = messages;
        return this;
    }

    /**
     * Validate entire form
     */
    validateForm() {
        let isValid = true;
        const fields = this.form.querySelectorAll('input, select, textarea');
        
        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    /**
     * Validate single field
     */
    validateField(field) {
        const fieldName = field.name;
        const fieldRules = this.rules[fieldName];
        
        if (!fieldRules) {
            return true;
        }

        const value = field.value.trim();
        
        // Clear previous errors
        this.clearFieldError(field);
        
        // Apply each rule
        for (const rule of fieldRules) {
            const result = this.applyRule(value, rule, field);
            if (!result.valid) {
                this.showFieldError(field, result.message);
                return false;
            }
        }

        this.showFieldSuccess(field);
        return true;
    }

    /**
     * Apply a single validation rule
     */
    applyRule(value, rule, field) {
        const [ruleName, ruleParam] = rule.split(':');
        const fieldName = field.name;

        switch (ruleName) {
            case 'required':
                if (!value) {
                    return {
                        valid: false,
                        message: this.getMessage(fieldName, 'required')
                    };
                }
                break;

            case 'email':
                if (value && !this.isValidEmail(value)) {
                    return {
                        valid: false,
                        message: this.getMessage(fieldName, 'email')
                    };
                }
                break;

            case 'min':
                if (value && value.length < parseInt(ruleParam)) {
                    return {
                        valid: false,
                        message: this.getMessage(fieldName, 'min').replace('{0}', ruleParam)
                    };
                }
                break;

            case 'max':
                if (value && value.length > parseInt(ruleParam)) {
                    return {
                        valid: false,
                        message: this.getMessage(fieldName, 'max').replace('{0}', ruleParam)
                    };
                }
                break;

            case 'numeric':
                if (value && !this.isNumeric(value)) {
                    return {
                        valid: false,
                        message: this.getMessage(fieldName, 'numeric')
                    };
                }
                break;

            case 'date':
                if (value && !this.isValidDate(value)) {
                    return {
                        valid: false,
                        message: this.getMessage(fieldName, 'date')
                    };
                }
                break;

            case 'alpha':
                if (value && !this.isAlpha(value)) {
                    return {
                        valid: false,
                        message: this.getMessage(fieldName, 'alpha')
                    };
                }
                break;

            case 'phone':
                if (value && !this.isValidPhone(value)) {
                    return {
                        valid: false,
                        message: this.getMessage(fieldName, 'phone')
                    };
                }
                break;
        }

        return { valid: true };
    }

    /**
     * Get error message for field and rule
     */
    getMessage(fieldName, ruleName) {
        return this.messages[fieldName]?.[ruleName] || 
               this.defaultMessages[ruleName] || 
               'Campo inválido';
    }

    /**
     * Show field error
     */
    showFieldError(field, message) {
        field.classList.remove(this.options.successClass);
        field.classList.add(this.options.errorClass);
        
        if (this.options.showErrorMessages) {
            this.showErrorMessage(field, message);
        }

        // Emit custom event
        field.dispatchEvent(new CustomEvent('validation:error', {
            detail: { message }
        }));
    }

    /**
     * Show field success
     */
    showFieldSuccess(field) {
        field.classList.remove(this.options.errorClass);
        field.classList.add(this.options.successClass);
        
        this.hideErrorMessage(field);

        // Emit custom event
        field.dispatchEvent(new CustomEvent('validation:success'));
    }

    /**
     * Clear field error
     */
    clearFieldError(field) {
        field.classList.remove(this.options.errorClass, this.options.successClass);
        this.hideErrorMessage(field);
    }

    /**
     * Show error message
     */
    showErrorMessage(field, message) {
        let errorDiv = field.parentNode.querySelector('.error-message');
        
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            field.parentNode.appendChild(errorDiv);
        }
        
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }

    /**
     * Hide error message
     */
    hideErrorMessage(field) {
        const errorDiv = field.parentNode.querySelector('.error-message');
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    }

    /**
     * Focus first field with error
     */
    focusFirstError() {
        const firstError = this.form.querySelector(`.${this.options.errorClass}`);
        if (firstError) {
            firstError.focus();
        }
    }

    // Validation helper methods
    isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    isNumeric(value) {
        return !isNaN(value) && !isNaN(parseFloat(value));
    }

    isValidDate(dateString) {
        const date = new Date(dateString);
        return date instanceof Date && !isNaN(date);
    }

    isAlpha(value) {
        const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
        return regex.test(value);
    }

    isValidPhone(phone) {
        const regex = /^[\d\s\-\+\(\)]{10,}$/;
        return regex.test(phone);
    }
}

// Export for use in other modules
window.FormValidator = FormValidator;