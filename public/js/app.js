/**
 * Main Application JavaScript - Improved Structure
 * Ejemplo de mejora: Organización modular y mejores prácticas JS
 */

// Application namespace
const OpticSuite = {
    // Configuration
    config: {
        apiEndpoint: '/api',
        debugMode: false
    },

    // Modules
    modules: {},

    // Initialize application
    init() {
        console.log('🚀 Initializing Optic-Suite Application...');
        
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.onDOMReady());
        } else {
            this.onDOMReady();
        }
    },

    // DOM ready handler
    onDOMReady() {
        this.initializeModules();
        this.setupGlobalEventListeners();
        this.setupComponentInstances();
        
        console.log('✅ Application initialized successfully');
    },

    // Initialize all modules
    initializeModules() {
        // Date calculator module
        this.modules.dateCalculator = new DateCalculator();
        
        // Form validation for patient forms
        if (document.querySelector('#patient-form')) {
            this.setupPatientFormValidation();
        }
        
        // Search functionality
        if (document.querySelector('.search-form')) {
            this.modules.search = new SearchHandler();
        }
        
        // Table enhancements
        if (document.querySelector('.tabla')) {
            this.modules.tableEnhancer = new TableEnhancer();
        }
    },

    // Setup global event listeners
    setupGlobalEventListeners() {
        // Global error handler
        window.addEventListener('error', (e) => {
            console.error('Global error:', e.error);
            this.showNotification('Ha ocurrido un error inesperado', 'error');
        });

        // Unhandled promise rejections
        window.addEventListener('unhandledrejection', (e) => {
            console.error('Unhandled promise rejection:', e.reason);
            this.showNotification('Error en la operación', 'error');
        });

        // Navigation confirmation for unsaved changes
        this.setupNavigationGuard();
    },

    // Setup component instances
    setupComponentInstances() {
        // Initialize tooltips
        this.initializeTooltips();
        
        // Initialize modals
        this.initializeModals();
        
        // Initialize dropdowns
        this.initializeDropdowns();
    },

    // Setup patient form validation
    setupPatientFormValidation() {
        const validator = new FormValidator('#patient-form', {
            realTimeValidation: true,
            showErrorMessages: true
        });

        validator
            .rules('nombres', ['required', 'min:2', 'max:100', 'alpha'])
            .rules('apellido_paterno', ['required', 'min:2', 'max:100', 'alpha'])
            .rules('apellido_materno', ['max:100', 'alpha'])
            .rules('email', ['email'])
            .rules('telefono', ['phone'])
            .rules('fecha_nacimiento', ['date']);

        this.modules.patientValidator = validator;
    },

    // Navigation guard for unsaved changes
    setupNavigationGuard() {
        let hasUnsavedChanges = false;

        // Track form changes
        document.addEventListener('input', (e) => {
            if (e.target.matches('form input, form select, form textarea')) {
                hasUnsavedChanges = true;
            }
        });

        // Reset on successful form submission
        document.addEventListener('submit', () => {
            hasUnsavedChanges = false;
        });

        // Warn before leaving page
        window.addEventListener('beforeunload', (e) => {
            if (hasUnsavedChanges) {
                e.preventDefault();
                e.returnValue = 'Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?';
                return e.returnValue;
            }
        });
    },

    // Show notification
    showNotification(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `notification notification--${type}`;
        notification.innerHTML = `
            <span>${message}</span>
            <button type="button" class="notification__close">&times;</button>
        `;

        // Add to page
        document.body.appendChild(notification);

        // Remove after duration
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, duration);

        // Remove on click
        notification.querySelector('.notification__close').addEventListener('click', () => {
            notification.remove();
        });
    },

    // Initialize tooltips
    initializeTooltips() {
        const tooltips = document.querySelectorAll('[data-tooltip]');
        tooltips.forEach(element => {
            new Tooltip(element);
        });
    },

    // Initialize modals
    initializeModals() {
        const modalTriggers = document.querySelectorAll('[data-modal-target]');
        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const modalId = trigger.dataset.modalTarget;
                const modal = document.querySelector(modalId);
                if (modal) {
                    this.showModal(modal);
                }
            });
        });
    },

    // Show modal
    showModal(modal) {
        modal.classList.add('modal--active');
        document.body.classList.add('modal-open');

        // Close on backdrop click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                this.hideModal(modal);
            }
        });

        // Close on escape key
        const escapeHandler = (e) => {
            if (e.key === 'Escape') {
                this.hideModal(modal);
                document.removeEventListener('keydown', escapeHandler);
            }
        };
        document.addEventListener('keydown', escapeHandler);
    },

    // Hide modal
    hideModal(modal) {
        modal.classList.remove('modal--active');
        document.body.classList.remove('modal-open');
    },

    // Initialize dropdowns
    initializeDropdowns() {
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(dropdown => {
            new DropdownHandler(dropdown);
        });
    }
};

/**
 * Date Calculator Class - Improved version
 */
class DateCalculator {
    constructor() {
        this.button = document.getElementById('btn_calcular_fecha');
        this.ageInput = document.getElementById('edad_historica');
        this.yearInput = document.getElementById('ano_expediente');
        this.birthDateInput = document.getElementById('fecha_nacimiento');

        if (this.button) {
            this.init();
        }
    }

    init() {
        this.button.addEventListener('click', () => this.calculate());
        
        // Real-time calculation on input change
        [this.ageInput, this.yearInput].forEach(input => {
            if (input) {
                input.addEventListener('input', () => this.calculateIfValid());
            }
        });
    }

    calculate() {
        const age = parseInt(this.ageInput?.value);
        const year = parseInt(this.yearInput?.value);

        if (!this.isValidInput(age, year)) {
            OpticSuite.showNotification(
                'Por favor, ingresa la edad y el año del expediente válidos.',
                'warning'
            );
            return;
        }

        const birthYear = year - age;
        const currentYear = new Date().getFullYear();

        // Validate calculated birth year
        if (birthYear < 1900 || birthYear > currentYear) {
            OpticSuite.showNotification(
                'La fecha calculada no es válida. Verifica los datos ingresados.',
                'error'
            );
            return;
        }

        // Set calculated date
        const calculatedDate = `${birthYear}-01-01`;
        if (this.birthDateInput) {
            this.birthDateInput.value = calculatedDate;
            
            // Trigger change event for validation
            this.birthDateInput.dispatchEvent(new Event('change'));
            
            OpticSuite.showNotification(
                `Fecha de nacimiento calculada: ${birthYear}`,
                'success'
            );
        }
    }

    calculateIfValid() {
        const age = parseInt(this.ageInput?.value);
        const year = parseInt(this.yearInput?.value);

        if (this.isValidInput(age, year)) {
            this.calculate();
        }
    }

    isValidInput(age, year) {
        return !isNaN(age) && !isNaN(year) && 
               age > 0 && age < 150 && 
               year > 1900 && year <= new Date().getFullYear();
    }
}

/**
 * Search Handler Class
 */
class SearchHandler {
    constructor() {
        this.searchForm = document.querySelector('.search-form');
        this.searchInput = document.querySelector('input[name="q"]');
        
        if (this.searchForm && this.searchInput) {
            this.init();
        }
    }

    init() {
        // Debounced search
        let searchTimeout;
        this.searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.performSearch(e.target.value);
            }, 300);
        });

        // Clear search
        const clearButton = document.createElement('button');
        clearButton.type = 'button';
        clearButton.className = 'search-clear';
        clearButton.innerHTML = '&times;';
        clearButton.addEventListener('click', () => this.clearSearch());
        
        this.searchInput.parentNode.appendChild(clearButton);
    }

    performSearch(query) {
        if (query.length >= 2) {
            // Here you could implement AJAX search
            console.log('Searching for:', query);
        }
    }

    clearSearch() {
        this.searchInput.value = '';
        this.searchInput.focus();
        // Redirect to clear results
        window.location.href = this.searchForm.action;
    }
}

/**
 * Table Enhancer Class
 */
class TableEnhancer {
    constructor() {
        this.tables = document.querySelectorAll('.tabla');
        this.init();
    }

    init() {
        this.tables.forEach(table => {
            this.enhanceTable(table);
        });
    }

    enhanceTable(table) {
        // Add responsive wrapper
        if (!table.parentNode.classList.contains('table-responsive')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }

        // Add sorting capabilities
        this.addSorting(table);
        
        // Add row highlighting
        this.addRowHighlighting(table);
    }

    addSorting(table) {
        const headers = table.querySelectorAll('th');
        headers.forEach((header, index) => {
            if (!header.classList.contains('no-sort')) {
                header.style.cursor = 'pointer';
                header.addEventListener('click', () => {
                    this.sortTable(table, index);
                });
            }
        });
    }

    addRowHighlighting(table) {
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            row.addEventListener('mouseenter', () => {
                row.classList.add('row-highlighted');
            });
            row.addEventListener('mouseleave', () => {
                row.classList.remove('row-highlighted');
            });
        });
    }

    sortTable(table, columnIndex) {
        // Implementation for table sorting
        console.log('Sorting table by column:', columnIndex);
    }
}

/**
 * Simple Tooltip Class
 */
class Tooltip {
    constructor(element) {
        this.element = element;
        this.tooltip = null;
        this.init();
    }

    init() {
        this.element.addEventListener('mouseenter', () => this.show());
        this.element.addEventListener('mouseleave', () => this.hide());
    }

    show() {
        const text = this.element.dataset.tooltip;
        if (!text) return;

        this.tooltip = document.createElement('div');
        this.tooltip.className = 'tooltip';
        this.tooltip.textContent = text;
        document.body.appendChild(this.tooltip);

        // Position tooltip
        const rect = this.element.getBoundingClientRect();
        this.tooltip.style.left = `${rect.left + rect.width / 2}px`;
        this.tooltip.style.top = `${rect.top - this.tooltip.offsetHeight - 5}px`;
    }

    hide() {
        if (this.tooltip) {
            this.tooltip.remove();
            this.tooltip = null;
        }
    }
}

/**
 * Dropdown Handler Class
 */
class DropdownHandler {
    constructor(dropdown) {
        this.dropdown = dropdown;
        this.trigger = dropdown.querySelector('.dropdown__trigger');
        this.menu = dropdown.querySelector('.dropdown__menu');
        
        if (this.trigger && this.menu) {
            this.init();
        }
    }

    init() {
        this.trigger.addEventListener('click', (e) => {
            e.preventDefault();
            this.toggle();
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!this.dropdown.contains(e.target)) {
                this.close();
            }
        });
    }

    toggle() {
        this.dropdown.classList.toggle('dropdown--open');
    }

    close() {
        this.dropdown.classList.remove('dropdown--open');
    }
}

// Initialize application when script loads
OpticSuite.init();