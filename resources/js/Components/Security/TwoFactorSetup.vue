<template>
    <div class="twofa-setup">
        <div v-if="!twoFaEnabled" class="setup-form">
            <h3>Enable Two-Factor Authentication</h3>
            <p>Scan this QR code with your authenticator app:</p>

            <div v-if="qrCode" class="qr-code-container">
                <img :src="qrCode" alt="QR Code" class="qr-code" />
            </div>

            <div v-if="secret" class="secret-backup">
                <p><strong>Manual Entry Key:</strong></p>
                <code>{{ secret }}</code>
                <button @click="copySecret" class="copy-btn">Copy</button>
            </div>

            <div class="form-group">
                <label for="verify-code">Enter 6-digit code from your app:</label>
                <input
                    id="verify-code"
                    v-model="verificationCode"
                    type="text"
                    maxlength="6"
                    placeholder="000000"
                />
            </div>

            <div class="form-actions">
                <button @click="generateSetup" :disabled="loading" class="btn btn-secondary">
                    {{ loading ? 'Generating...' : 'Generate Setup' }}
                </button>
                <button @click="enableTwoFa" :disabled="loading || !verificationCode" class="btn">
                    {{ loading ? 'Enabling...' : 'Verify & Enable' }}
                </button>
            </div>

            <div v-if="recoveryCodes" class="recovery-codes">
                <h4>Save Your Recovery Codes</h4>
                <p>Store these codes in a safe place. You can use them to access your account if you lose access to your authenticator app.</p>
                <div class="codes-list">
                    <code v-for="(code, idx) in recoveryCodes" :key="idx">{{ code }}</code>
                </div>
                <button @click="downloadCodes" class="btn btn-secondary">Download Codes</button>
            </div>
        </div>

        <div v-else class="twofa-enabled">
            <h3>Two-Factor Authentication Enabled</h3>
            <p class="success-message">✓ Your account is protected with 2FA</p>
            <button @click="showDisableForm = true" class="btn btn-danger">Disable 2FA</button>

            <div v-if="showDisableForm" class="disable-form">
                <p>Enter your password to disable 2FA:</p>
                <div class="form-group">
                    <input v-model="disablePassword" type="password" placeholder="Password" />
                </div>
                <div class="form-actions">
                    <button @click="showDisableForm = false" class="btn btn-secondary">Cancel</button>
                    <button @click="disableTwoFa" :disabled="loading" class="btn btn-danger">Disable</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const twoFaEnabled = ref(false);
const qrCode = ref('');
const secret = ref('');
const verificationCode = ref('');
const loading = ref(false);
const recoveryCodes = ref(null);
const showDisableForm = ref(false);
const disablePassword = ref('');

const checkStatus = async () => {
    try {
        const response = await axios.get('/api/2fa/status');
        twoFaEnabled.value = response.data.is_enabled;
    } catch (error) {
        console.error('Failed to check 2FA status:', error);
    }
};

const generateSetup = async () => {
    loading.value = true;
    try {
        const response = await axios.post('/api/2fa/generate-setup');
        qrCode.value = response.data.qr_code;
        secret.value = response.data.secret;
        verificationCode.value = '';
        recoveryCodes.value = null;
    } catch (error) {
        console.error('Failed to generate setup:', error);
        alert('Failed to generate 2FA setup');
    } finally {
        loading.value = false;
    }
};

const enableTwoFa = async () => {
    if (!verificationCode.value || verificationCode.value.length !== 6) {
        alert('Please enter a valid 6-digit code');
        return;
    }

    loading.value = true;
    try {
        const response = await axios.post('/api/2fa/enable', {
            secret: secret.value,
            code: verificationCode.value,
        });
        recoveryCodes.value = response.data.recovery_codes;
        twoFaEnabled.value = true;
        alert('2FA enabled successfully!');
    } catch (error) {
        console.error('Failed to enable 2FA:', error);
        alert('Failed to verify code. Please try again.');
    } finally {
        loading.value = false;
    }
};

const disableTwoFa = async () => {
    loading.value = true;
    try {
        await axios.post('/api/2fa/disable', {
            password: disablePassword.value,
        });
        twoFaEnabled.value = false;
        showDisableForm.value = false;
        disablePassword.value = '';
        alert('2FA disabled successfully');
    } catch (error) {
        console.error('Failed to disable 2FA:', error);
        alert('Failed to disable 2FA. Invalid password.');
    } finally {
        loading.value = false;
    }
};

const copySecret = async () => {
    try {
        await navigator.clipboard.writeText(secret.value);
        alert('Secret copied to clipboard');
    } catch (error) {
        console.error('Failed to copy:', error);
    }
};

const downloadCodes = () => {
    const text = recoveryCodes.value.join('\n');
    const element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
    element.setAttribute('download', 'recovery-codes.txt');
    element.style.display = 'none';
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
};

onMounted(() => {
    checkStatus();
});
</script>

<style scoped>
.twofa-setup {
    max-width: 600px;
    margin: 0 auto;
}

.setup-form,
.twofa-enabled {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.qr-code-container {
    text-align: center;
    margin: 1.5rem 0;
    padding: 1rem;
    background: #f5f5f5;
    border-radius: 4px;
}

.qr-code {
    max-width: 300px;
    height: auto;
}

.secret-backup {
    background: #fff7ed;
    border: 1px solid #fdba74;
    padding: 1rem;
    border-radius: 4px;
    margin: 1rem 0;
}

.secret-backup code {
    display: block;
    word-break: break-all;
    margin: 0.5rem 0;
    font-family: 'Courier New', monospace;
}

.recovery-codes {
    background: #d1ecf1;
    border: 1px solid #b6e4eb;
    padding: 1.5rem;
    border-radius: 4px;
    margin-top: 1.5rem;
}

.codes-list {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
    margin: 1rem 0;
}

.codes-list code {
    background: white;
    padding: 0.5rem;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    text-align: center;
}

.success-message {
    color: #0c5460;
    padding: 0.75rem;
    background: #d1ecf1;
    border-radius: 4px;
    margin: 1rem 0;
}

.disable-form {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    padding: 1rem;
    border-radius: 4px;
    margin-top: 1rem;
}

.form-group {
    margin: 1rem 0;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.form-group input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    justify-content: flex-end;
}

.copy-btn {
    margin-top: 0.5rem;
    padding: 0.5rem 1rem;
    background: #ff7a00;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.875rem;
}

.copy-btn:hover {
    background: #e56a00;
}
</style>
