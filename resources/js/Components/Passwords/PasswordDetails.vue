<template>
    <div class="password-details">
        <div class="details-header">
            <h2>{{ password.title }}</h2>
            <div class="header-actions">
                <button @click="$emit('edit')" class="btn btn-secondary">Edit</button>
                <button @click="$emit('close')" class="btn-close">✕</button>
            </div>
        </div>

        <div class="details-body">
            <div v-if="password.folder" class="detail-item">
                <label>Folder</label>
                <div class="folder-badge">📁 {{ password.folder.name }}</div>
            </div>

            <div v-if="password.username" class="detail-item">
                <label>Username / Email</label>
                <div class="value-with-copy">
                    <span>{{ password.username }}</span>
                    <button @click="copyToClipboard(password.username)" class="copy-btn">
                        {{ copied === 'username' ? '✓' : '📋' }}
                    </button>
                </div>
            </div>

            <div class="detail-item">
                <label>Password</label>
                <div class="value-with-copy">
                    <span class="password-value">
                        {{ showPassword ? decryptedPassword : '••••••••••••' }}
                    </span>
                    <button @click="togglePasswordVisibility" class="copy-btn">
                        {{ showPassword ? '🙈' : '👁️' }}
                    </button>
                    <button @click="copyToClipboard(decryptedPassword)" class="copy-btn">
                        {{ copied === 'password' ? '✓' : '📋' }}
                    </button>
                </div>
            </div>

            <div v-if="password.url" class="detail-item">
                <label>Website URL</label>
                <div class="value-with-copy">
                    <a :href="password.url" target="_blank" class="url-link">
                        {{ password.url }}
                    </a>
                    <button @click="copyToClipboard(password.url)" class="copy-btn">
                        {{ copied === 'url' ? '✓' : '📋' }}
                    </button>
                </div>
            </div>

            <div v-if="password.notes" class="detail-item">
                <label>Notes</label>
                <div class="notes-content">{{ decryptedNotes }}</div>
            </div>

            <div class="detail-item">
                <label>Created</label>
                <div class="timestamp-info">
                    <span>{{ formatDate(password.created_at) }}</span>
                    <span v-if="password.creator" class="user-badge">by {{ password.creator.name }}</span>
                </div>
            </div>

            <div class="detail-item">
                <label>Last Modified</label>
                <div class="timestamp-info">
                    <span>{{ formatDate(password.updated_at) }}</span>
                    <span v-if="password.updater" class="user-badge">by {{ password.updater.name }}</span>
                </div>
            </div>
        </div>

        <div class="details-footer">
            <button @click="$emit('delete')" class="btn-danger">
                Delete Password
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    password: {
        type: Object,
        required: true
    }
});

defineEmits(['edit', 'delete', 'close']);

const showPassword = ref(false);
const decryptedPassword = ref('');
const decryptedNotes = ref('');
const copied = ref('');

const togglePasswordVisibility = async () => {
    if (!showPassword.value && !decryptedPassword.value) {
        await fetchDecryptedData();
    }
    showPassword.value = !showPassword.value;
};

const fetchDecryptedData = async () => {
    try {
        const response = await axios.get(`/api/passwords/${props.password.id}`);
        decryptedPassword.value = response.data.password;
        decryptedNotes.value = response.data.notes || '';
    } catch (error) {
        console.error('Failed to decrypt password:', error);
    }
};

const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
        copied.value = text === decryptedPassword.value ? 'password' :
                       text === props.password.username ? 'username' : 'url';
        setTimeout(() => copied.value = '', 2000);
    } catch (error) {
        console.error('Failed to copy:', error);
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleString();
};

onMounted(() => {
    // Optionally fetch decrypted data on mount
});
</script>

<style scoped>
.password-details {
    background: rgba(15, 23, 42, 0.94);
    border-radius: 1.5rem;
    padding: 2rem;
    max-width: 600px;
}

.details-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.details-header h2 {
    margin: 0;
    color: #f8fafc;
}

.header-actions {
    display: flex;
    gap: 0.75rem;
}

.btn-close {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #f8fafc;
    border-radius: 999px;
    width: 2.5rem;
    height: 2.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    cursor: pointer;
    opacity: 0.8;
    transition: opacity 0.2s, transform 0.2s;
}

.btn-close:hover {
    opacity: 1;
    transform: translateY(-1px);
}

.details-body {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.detail-item {
    display: flex;
    flex-direction: column;
}

.detail-item label {
    font-weight: 600;
    color: #94a3b8;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.value-with-copy {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.password-value {
    font-family: monospace;
    font-size: 1.125rem;
    color: #f8fafc;
}

.timestamp-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.user-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: rgba(255, 255, 255, 0.08);
    color: #facc15;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    border: 1px solid rgba(250, 204, 21, 0.2);
}

.copy-btn {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #f8fafc;
    border-radius: 999px;
    padding: 0.5rem 0.75rem;
    cursor: pointer;
    transition: background 0.2s, transform 0.2s;
}

.copy-btn:hover {
    background: rgba(255, 255, 255, 0.12);
    transform: translateY(-1px);
}

.url-link {
    color: #60a5fa;
    text-decoration: none;
}

.url-link:hover {
    text-decoration: underline;
}

.notes-content {
    background: rgba(255, 255, 255, 0.04);
    padding: 1rem;
    border-radius: 1rem;
    white-space: pre-wrap;
    color: #e2e8f0;
}

.folder-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 999px;
    color: #f8fafc;
}

.details-footer {
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.btn-danger {
    background: #ef4444;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 999px;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn-danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 32px rgba(239, 68, 68, 0.18);
}
</style>
