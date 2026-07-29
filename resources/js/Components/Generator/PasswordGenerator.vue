<template>
    <div class="password-generator">
        <h2>Password Generator</h2>

        <div class="generated-password">
            <input
                v-model="generatedPassword"
                type="text"
                readonly
                placeholder="Generated password will appear here"
                class="password-display"
            />
            <button @click="copyPassword" class="copy-btn" :disabled="!generatedPassword">
                {{ copied ? '✓ Copied' : '📋 Copy' }}
            </button>
        </div>

        <div class="generator-options">
            <div class="form-group">
                <label for="length">
                    Password Length: <strong>{{ options.length }}</strong>
                </label>
                <input
                    id="length"
                    v-model.number="options.length"
                    type="range"
                    min="8"
                    max="64"
                    class="range-slider"
                />
            </div>

            <div class="checkbox-group">
                <label class="checkbox-label">
                    <input v-model="options.includeUppercase" type="checkbox" />
                    <span>Uppercase Letters (A-Z)</span>
                </label>

                <label class="checkbox-label">
                    <input v-model="options.includeLowercase" type="checkbox" />
                    <span>Lowercase Letters (a-z)</span>
                </label>

                <label class="checkbox-label">
                    <input v-model="options.includeNumbers" type="checkbox" />
                    <span>Numbers (0-9)</span>
                </label>

                <label class="checkbox-label">
                    <input v-model="options.includeSymbols" type="checkbox" />
                    <span>Symbols (!@#$%^&*)</span>
                </label>
            </div>

            <div class="strength-meter">
                <label>Password Strength</label>
                <div class="strength-bar">
                    <div
                        class="strength-fill"
                        :class="strengthClass"
                        :style="{ width: strength + '%' }"
                    ></div>
                </div>
                <span class="strength-label">{{ strengthLabel }}</span>
            </div>
        </div>

        <div class="generator-actions">
            <button @click="generatePassword" class="btn">
                Generate Password
            </button>
            <button
                v-if="generatedPassword"
                @click="$emit('use', generatedPassword)"
                class="btn btn-secondary"
            >
                Use This Password
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue';

defineEmits(['use']);

const generatedPassword = ref('');
const copied = ref(false);

const options = reactive({
    length: 16,
    includeUppercase: true,
    includeLowercase: true,
    includeNumbers: true,
    includeSymbols: true
});

const strength = ref(0);

const strengthClass = computed(() => {
    if (strength.value >= 80) return 'strong';
    if (strength.value >= 60) return 'good';
    if (strength.value >= 40) return 'medium';
    return 'weak';
});

const strengthLabel = computed(() => {
    if (strength.value >= 80) return 'Very Strong';
    if (strength.value >= 60) return 'Strong';
    if (strength.value >= 40) return 'Medium';
    return 'Weak';
});

const generatePassword = () => {
    let characters = '';

    if (options.includeLowercase) characters += 'abcdefghijklmnopqrstuvwxyz';
    if (options.includeUppercase) characters += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if (options.includeNumbers) characters += '0123456789';
    if (options.includeSymbols) characters += '!@#$%^&*()-_=+[]{}|;:,.<>?';

    if (!characters) {
        alert('Please select at least one character type');
        return;
    }

    let password = '';
    for (let i = 0; i < options.length; i++) {
        password += characters.charAt(Math.floor(Math.random() * characters.length));
    }

    generatedPassword.value = password;
    calculateStrength(password);
};

const calculateStrength = (password) => {
    let score = 0;

    if (password.length >= 8) score += 20;
    if (password.length >= 12) score += 10;
    if (password.length >= 16) score += 10;

    if (/[a-z]/.test(password)) score += 15;
    if (/[A-Z]/.test(password)) score += 15;
    if (/[0-9]/.test(password)) score += 15;
    if (/[^a-zA-Z0-9]/.test(password)) score += 15;

    strength.value = Math.min(100, score);
};

const copyPassword = async () => {
    try {
        await navigator.clipboard.writeText(generatedPassword.value);
        copied.value = true;
        setTimeout(() => copied.value = false, 2000);
    } catch (error) {
        console.error('Failed to copy:', error);
    }
};

watch(options, () => {
    if (generatedPassword.value) {
        generatePassword();
    }
}, { deep: true });

// Generate initial password
generatePassword();
</script>

<style scoped>
.password-generator {
    background: white;
    border-radius: 8px;
    padding: 2rem;
    max-width: 600px;
}

.password-generator h2 {
    margin-bottom: 1.5rem;
    color: #2c3e50;
}

.generated-password {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
}

.password-display {
    flex: 1;
    padding: 1rem;
    font-family: monospace;
    font-size: 1.125rem;
    border: 2px solid #3498db;
    border-radius: 4px;
    background: #f8f9fa;
}

.copy-btn {
    padding: 1rem 1.5rem;
    background: #3498db;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.2s;
}

.copy-btn:hover:not(:disabled) {
    background: #2980b9;
}

.copy-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.generator-options {
    margin-bottom: 2rem;
}

.range-slider {
    width: 100%;
    margin-top: 0.5rem;
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin: 1.5rem 0;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.checkbox-label input {
    width: auto;
    margin: 0;
}

.strength-meter {
    margin-top: 1.5rem;
}

.strength-meter label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #666;
}

.strength-bar {
    height: 10px;
    background: #e0e0e0;
    border-radius: 5px;
    overflow: hidden;
}

.strength-fill {
    height: 100%;
    transition: all 0.3s;
}

.strength-fill.weak {
    background: #e74c3c;
}

.strength-fill.medium {
    background: #f39c12;
}

.strength-fill.good {
    background: #3498db;
}

.strength-fill.strong {
    background: #27ae60;
}

.strength-label {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: #666;
}

.generator-actions {
    display: flex;
    gap: 1rem;
}
</style>
