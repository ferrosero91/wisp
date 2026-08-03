/**
 * INTERNET SYSTEM - UI Enhancements
 * Mejoras de UX, validación de formularios y efectos visuales
 */

(function() {
    'use strict';

    // ============ INICIALIZACIÓN ============
    document.addEventListener('DOMContentLoaded', function() {
        initFormValidation();
        initRippleEffect();
        initSmoothScroll();
        initTooltips();
        initAnimations();
        initCounterAnimations();
        initPasswordStrength();
    });

    // ============ VALIDACIÓN DE FORMULARIOS ============
    function initFormValidation() {
        // Agregar validación en tiempo real a todos los formularios
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                // Validar al perder el foco
                input.addEventListener('blur', function() {
                    validateField(this);
                });
                
                // Limpiar errores al escribir
                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        validateField(this);
                    }
                });
            });
            
            // Validar al enviar
            form.addEventListener('submit', function(e) {
                if (!validateForm(this)) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        });
    }

    function validateField(field) {
        const value = field.value.trim();
        const type = field.type;
        const required = field.hasAttribute('required');
        const minLength = field.getAttribute('minlength');
        const maxLength = field.getAttribute('maxlength');
        const pattern = field.getAttribute('pattern');
        
        // Limpiar estado anterior
        clearFieldError(field);
        
        // Campo requerido vacío
        if (required && value === '') {
            setFieldError(field, 'Este campo es obligatorio');
            return false;
        }
        
        // Validar email
        if (type === 'email' && value !== '') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                setFieldError(field, 'Ingrese un correo electrónico válido');
                return false;
            }
        }
        
        // Validar longitud mínima
        if (minLength && value.length < parseInt(minLength)) {
            setFieldError(field, `Mínimo ${minLength} caracteres`);
            return false;
        }
        
        // Validar longitud máxima
        if (maxLength && value.length > parseInt(maxLength)) {
            setFieldError(field, `Máximo ${maxLength} caracteres`);
            return false;
        }
        
        // Validar patrón
        if (pattern && value !== '') {
            const regex = new RegExp(pattern);
            if (!regex.test(value)) {
                setFieldError(field, 'Formato inválido');
                return false;
            }
        }
        
        // Validar número
        if (type === 'number' && value !== '') {
            const min = field.getAttribute('min');
            const max = field.getAttribute('max');
            const numValue = parseFloat(value);
            
            if (isNaN(numValue)) {
                setFieldError(field, 'Ingrese un número válido');
                return false;
            }
            if (min && numValue < parseFloat(min)) {
                setFieldError(field, `El valor mínimo es ${min}`);
                return false;
            }
            if (max && numValue > parseFloat(max)) {
                setFieldError(field, `El valor máximo es ${max}`);
                return false;
            }
        }
        
        // Campo válido
        setFieldSuccess(field);
        return true;
    }

    function validateForm(form) {
        const inputs = form.querySelectorAll('input, select, textarea');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            // Scroll al primer error
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
            
            // Mostrar mensaje de error
            showToast('Por favor, corrija los errores en el formulario', 'error');
        }
        
        return isValid;
    }

    function setFieldError(field, message) {
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');
        
        // Crear mensaje de error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        errorDiv.style.cssText = 'display:block;color:#ff5b57;font-size:12px;margin-top:4px;animation:fadeIn 0.3s ease';
        
        // Insertar después del campo
        field.parentNode.insertBefore(errorDiv, field.nextSibling);
        
        // Agregar icono de error
        addFieldIcon(field, 'error');
    }

    function setFieldSuccess(field) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        
        // Agregar icono de éxito
        addFieldIcon(field, 'success');
    }

    function clearFieldError(field) {
        field.classList.remove('is-invalid', 'is-valid');
        
        // Eliminar mensajes de error
        const errorDiv = field.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
        
        // Eliminar iconos
        const icons = field.parentNode.querySelectorAll('.field-icon-status');
        icons.forEach(icon => icon.remove());
    }

    function addFieldIcon(field, type) {
        // Eliminar icono existente
        const existingIcon = field.parentNode.querySelector('.field-icon-status');
        if (existingIcon) {
            existingIcon.remove();
        }
        
        // Crear nuevo icono
        const icon = document.createElement('i');
        icon.className = 'field-icon-status';
        icon.style.cssText = 'position:absolute;right:10px;top:50%;transform:translateY(-50%);font-size:16px;pointer-events:none;transition:all 0.3s ease';
        
        if (type === 'error') {
            icon.className += ' fas fa-exclamation-circle';
            icon.style.color = '#ff5b57';
        } else {
            icon.className += ' fas fa-check-circle';
            icon.style.color = '#00acac';
        }
        
        // Hacer el contenedor relativo si no lo es
        const parent = field.parentNode;
        if (getComputedStyle(parent).position === 'static') {
            parent.style.position = 'relative';
        }
        
        parent.appendChild(icon);
        
        // Animación de entrada
        icon.style.opacity = '0';
        icon.style.transform = 'translateY(-50%) scale(0.5)';
        requestAnimationFrame(() => {
            icon.style.opacity = '1';
            icon.style.transform = 'translateY(-50%) scale(1)';
        });
    }

    // ============ EFECTO RIPPLE EN BOTONES ============
    function initRippleEffect() {
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn');
            if (!btn) return;
            
            const ripple = document.createElement('span');
            ripple.className = 'ripple';
            
            const rect = btn.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.4);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
            `;
            
            btn.style.position = 'relative';
            btn.style.overflow = 'hidden';
            btn.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    }

    // ============ SMOOTH SCROLL ============
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;
                
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // ============ TOOLTIPS MEJORADOS ============
    function initTooltips() {
        // Los tooltips ya se inicializan con Bootstrap
        // Agregar animación de entrada
        $(document).on('show.bs.tooltip', function(e) {
            const tooltip = $(e.target).data('bs.tooltip');
            if (tooltip && tooltip.$tip) {
                tooltip.$tip.css('animation', 'fadeIn 0.2s ease');
            }
        });
    }

    // ============ ANIMACIONES DE ENTRADA ============
    function initAnimations() {
        // Intersection Observer para animaciones al hacer scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        // Observar paneles y widgets
        document.querySelectorAll('.panel, .widget, .card').forEach(el => {
            el.classList.add('animate-ready');
            observer.observe(el);
        });
    }

    // ============ ANIMACIÓN DE CONTADORES ============
    function initCounterAnimations() {
        const counters = document.querySelectorAll('.stats-info h4');
        
        counters.forEach(counter => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounter(counter);
                        observer.unobserve(counter);
                    }
                });
            }, { threshold: 0.5 });
            
            observer.observe(counter);
        });
    }

    function animateCounter(element) {
        const text = element.textContent;
        const hasSymbol = text.includes('$') || text.includes('%');
        const number = parseFloat(text.replace(/[^0-9.-]/g, ''));
        
        if (isNaN(number)) return;
        
        const duration = 1000;
        const steps = 30;
        const increment = number / steps;
        let current = 0;
        let step = 0;
        
        const timer = setInterval(() => {
            step++;
            current += increment;
            
            if (step >= steps) {
                current = number;
                clearInterval(timer);
            }
            
            if (hasSymbol) {
                const symbol = text.match(/[$%]/)[0];
                element.textContent = symbol + ' ' + Math.round(current).toLocaleString();
            } else {
                element.textContent = Math.round(current).toLocaleString();
            }
        }, duration / steps);
    }

    // ============ INDICADOR DE FORTALEZA DE CONTRASEÑA ============
    function initPasswordStrength() {
        const passwordInputs = document.querySelectorAll('input[type="password"]');
        
        passwordInputs.forEach(input => {
            // No agregar si ya tiene indicador
            if (input.dataset.strength) return;
            
            const strengthDiv = document.createElement('div');
            strengthDiv.className = 'password-strength';
            strengthDiv.style.cssText = 'margin-top:8px;display:none';
            strengthDiv.innerHTML = `
                <div class="strength-bar" style="height:4px;background:#e0e7ef;border-radius:4px;overflow:hidden">
                    <div class="strength-fill" style="height:100%;width:0;transition:all 0.3s ease;border-radius:4px"></div>
                </div>
                <div class="strength-text" style="font-size:11px;margin-top:4px;color:#7a8ba0"></div>
            `;
            
            input.parentNode.insertBefore(strengthDiv, input.nextSibling);
            
            input.addEventListener('input', function() {
                const strength = calculatePasswordStrength(this.value);
                updateStrengthIndicator(strengthDiv, strength);
            });
        });
    }

    function calculatePasswordStrength(password) {
        let score = 0;
        
        if (password.length === 0) return { score: 0, level: 'none', text: '' };
        if (password.length >= 8) score += 1;
        if (password.length >= 12) score += 1;
        if (/[a-z]/.test(password)) score += 1;
        if (/[A-Z]/.test(password)) score += 1;
        if (/[0-9]/.test(password)) score += 1;
        if (/[^a-zA-Z0-9]/.test(password)) score += 1;
        
        const levels = [
            { min: 0, level: 'weak', text: 'Débil', color: '#ff5b57' },
            { min: 2, level: 'fair', text: 'Regular', color: '#f59c1a' },
            { min: 4, level: 'good', text: 'Buena', color: '#348fe2' },
            { min: 5, level: 'strong', text: 'Fuerte', color: '#00acac' }
        ];
        
        const level = levels.reverse().find(l => score >= l.min) || levels[0];
        return { score, ...level };
    }

    function updateStrengthIndicator(container, strength) {
        const fill = container.querySelector('.strength-fill');
        const text = container.querySelector('.strength-text');
        
        if (strength.score === 0) {
            container.style.display = 'none';
            return;
        }
        
        container.style.display = 'block';
        fill.style.width = (strength.score / 6 * 100) + '%';
        fill.style.background = strength.color;
        text.textContent = strength.text;
        text.style.color = strength.color;
    }

    // ============ TOAST NOTIFICATIONS ============
    function showToast(message, type = 'info', duration = 3000) {
        // Crear contenedor si no existe
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.style.cssText = 'position:fixed;top:20px;right:20px;z-index:10000;display:flex;flex-direction:column;gap:10px';
            document.body.appendChild(container);
        }
        
        // Iconos por tipo
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };
        
        // Colores por tipo
        const colors = {
            success: { bg: 'linear-gradient(135deg, #00acac, #14b58c)', border: '#00acac' },
            error: { bg: 'linear-gradient(135deg, #ff5b57, #ff7269)', border: '#ff5b57' },
            warning: { bg: 'linear-gradient(135deg, #f59c1a, #f8b500)', border: '#f59c1a' },
            info: { bg: 'linear-gradient(135deg, #348fe2, #49b6d6)', border: '#348fe2' }
        };
        
        // Crear toast
        const toast = document.createElement('div');
        toast.style.cssText = `
            background: ${colors[type].bg};
            color: white;
            padding: 14px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 300px;
            max-width: 400px;
            animation: slideInRight 0.4s ease-out;
            cursor: pointer;
            transition: all 0.3s ease;
        `;
        
        toast.innerHTML = `
            <i class="${icons[type]}" style="font-size:20px"></i>
            <span style="flex:1;font-size:14px;font-weight:500">${message}</span>
            <i class="fas fa-times" style="opacity:0.7;cursor:pointer" onclick="this.parentElement.remove()"></i>
        `;
        
        // Hover effect
        toast.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(-5px)';
            this.style.boxShadow = '0 15px 40px rgba(0,0,0,0.25)';
        });
        
        toast.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
            this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.2)';
        });
        
        container.appendChild(toast);
        
        // Auto-remove
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease-in forwards';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    // ============ CONFIRMACIONES MODERNAS ============
    function showConfirm(options) {
        return new Promise((resolve) => {
            const defaults = {
                title: '¿Estás seguro?',
                message: '',
                confirmText: 'Confirmar',
                cancelText: 'Cancelar',
                type: 'warning',
                icon: 'fas fa-question-circle'
            };
            
            const config = { ...defaults, ...options };
            
            // Crear modal
            const modal = document.createElement('div');
            modal.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(15,28,46,0.6);backdrop-filter:blur(8px);z-index:10000;display:flex;align-items:center;justify-content:center;animation:fadeIn 0.2s ease';
            
            const colors = {
                warning: '#f59c1a',
                danger: '#ff5b57',
                info: '#348fe2',
                success: '#00acac'
            };
            
            modal.innerHTML = `
                <div style="background:white;border-radius:16px;padding:32px;max-width:400px;width:90%;box-shadow:0 25px 80px rgba(0,0,0,0.3);animation:bounceIn 0.4s ease;text-align:center">
                    <div style="width:64px;height:64px;border-radius:50%;background:${colors[config.type]}15;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
                        <i class="${config.icon}" style="font-size:28px;color:${colors[config.type]}"></i>
                    </div>
                    <h3 style="font-size:18px;font-weight:700;color:#2d3a48;margin-bottom:12px">${config.title}</h3>
                    <p style="font-size:14px;color:#7a8ba0;margin-bottom:24px;line-height:1.5">${config.message}</p>
                    <div style="display:flex;gap:12px;justify-content:center">
                        <button class="btn-cancel" style="padding:10px 24px;border-radius:10px;border:1px solid #e4e9f0;background:white;color:#556477;font-weight:600;cursor:pointer;transition:all 0.2s ease">${config.cancelText}</button>
                        <button class="btn-confirm" style="padding:10px 24px;border-radius:10px;border:none;background:${colors[config.type]};color:white;font-weight:600;cursor:pointer;transition:all 0.2s ease;box-shadow:0 4px 12px ${colors[config.type]}40">${config.confirmText}</button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Eventos
            modal.querySelector('.btn-cancel').addEventListener('click', () => {
                modal.style.animation = 'fadeOut 0.2s ease forwards';
                setTimeout(() => {
                    modal.remove();
                    resolve(false);
                }, 200);
            });
            
            modal.querySelector('.btn-confirm').addEventListener('click', () => {
                modal.style.animation = 'fadeOut 0.2s ease forwards';
                setTimeout(() => {
                    modal.remove();
                    resolve(true);
                }, 200);
            });
            
            // Cerrar con Escape
            document.addEventListener('keydown', function handler(e) {
                if (e.key === 'Escape') {
                    modal.remove();
                    resolve(false);
                    document.removeEventListener('keydown', handler);
                }
            });
            
            // Hover effects
            const cancelBtn = modal.querySelector('.btn-cancel');
            const confirmBtn = modal.querySelector('.btn-confirm');
            
            cancelBtn.addEventListener('mouseenter', function() {
                this.style.background = '#f8f9fa';
                this.style.borderColor = '#c3cdd9';
            });
            cancelBtn.addEventListener('mouseleave', function() {
                this.style.background = 'white';
                this.style.borderColor = '#e4e9f0';
            });
            
            confirmBtn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = `0 6px 20px ${colors[config.type]}50`;
            });
            confirmBtn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = `0 4px 12px ${colors[config.type]}40`;
            });
        });
    }

    // ============ EXPORTAR FUNCIONES ============
    window.UIEnhancements = {
        showToast,
        showConfirm,
        validateForm,
        validateField
    };

    // Agregar estilos de animación
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        .animate-ready {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .animate-ready.animate-in {
            opacity: 1;
            transform: translateY(0);
        }
        .is-invalid {
            border-color: #ff5b57 !important;
            box-shadow: 0 0 0 3px rgba(255,91,87,0.15) !important;
        }
        .is-valid {
            border-color: #00acac !important;
            box-shadow: 0 0 0 3px rgba(0,172,172,0.15) !important;
        }
    `;
    document.head.appendChild(style);

})();
