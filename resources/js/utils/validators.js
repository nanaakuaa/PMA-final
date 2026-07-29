// Validation utilities
export const validateEmail = (email) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
};

export const validateUrl = (url) => {
    try {
        new URL(url);
        return true;
    } catch {
        return false;
    }
};

export const validatePasswordStrength = (password) => {
    let strength = 0;
    const checks = {
        length: password.length >= 8,
        lowercase: /[a-z]/.test(password),
        uppercase: /[A-Z]/.test(password),
        numbers: /[0-9]/.test(password),
        symbols: /[^a-zA-Z0-9]/.test(password)
    };

    strength += checks.length ? 20 : 0;
    strength += checks.lowercase ? 20 : 0;
    strength += checks.uppercase ? 20 : 0;
    strength += checks.numbers ? 20 : 0;
    strength += checks.symbols ? 20 : 0;

    if (password.length >= 12) strength += 10;
    if (password.length >= 16) strength += 10;

    return {
        strength: Math.min(100, strength),
        checks
    };
};

export const required = (value) => {
    if (!value || (typeof value === 'string' && !value.trim())) {
        return 'This field is required';
    }
    return null;
};

export const minLength = (min) => (value) => {
    if (value && value.length < min) {
        return `Minimum length is ${min} characters`;
    }
    return null;
};

export const maxLength = (max) => (value) => {
    if (value && value.length > max) {
        return `Maximum length is ${max} characters`;
    }
    return null;
};
