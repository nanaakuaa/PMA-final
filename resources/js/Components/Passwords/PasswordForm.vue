<template>
    <div class="password-form">
        <h2>{{ isEdit ? 'Edit Password' : 'Create New Password' }}</h2>

        <form @submit.prevent="handleSubmit">
            <div class="form-group">
                <label for="title">Title *</label>
                <input
                    id="title"
                    v-model="form.title"
                    type="text"
                    required
                    placeholder="e.g., Gmail Account"
                />
                <span v-if="errors.title" class="error">{{ errors.title }}</span>
            </div>

            <div class="form-group">
                <label for="folder_id">Folder</label>
                <select id="folder_id" v-model="form.folder_id">
                    <option :value="null">No Folder</option>
                    <option v-for="folder in folders" :key="folder.id" :value="folder.id">
                        {{ folder.name }}
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="username">Username / Email</label>
                <input
                    id="username"
                    v-model="form.username"
                    type="text"
                    autocomplete="off"
                    placeholder="username or email"
                />
            </div>

            <div class="form-group">
                <label for="password">Password *</label>
                <div class="password-input-group">
                    <input
                        id="password"
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        required
                        autocomplete="new-password"
                        placeholder="Enter password"
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="toggle-password"
                    >
                        {{ showPassword ? '🙈' : '👁️' }}
                    </button>
                    <button
                        type="button"
                        @click="$emit('generate')"
                        class="btn-generate"
                    >
                        Generate
                    </button>
                </div>
                <span v-if="errors.password" class="error">{{ errors.password }}</span>
            </div>

            <div class="form-group">
                <label for="url">Website URL</label>
                <input
                    id="url"
                    v-model="form.url"
                    type="url"
                    placeholder="https://example.com"
                />
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea
                    id="notes"
                    v-model="form.notes"
                    rows="4"
                    placeholder="Additional notes..."
                ></textarea>
            </div>

            <div class="form-group">
                <label>
                    <input v-model="form.is_company_wide" type="checkbox" />
                    Visible to all company users
                </label>
            </div>

            <div v-if="!form.is_company_wide" class="form-group">
                <label for="department_id">Restrict to Department</label>
                <select id="department_id" v-model="form.department_id">
                    <option :value="null">Select Department</option>
                    <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                        {{ dept.name }}
                    </option>
                </select>
            </div>

            <div class="form-actions">
                <button type="button" @click="$emit('cancel')" class="btn btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn" :disabled="submitting">
                    {{ submitting ? 'Saving...' : 'Save Password' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';

const props = defineProps({
    password: {
        type: Object,
        default: null
    },
    folders: {
        type: Array,
        default: () => []
    },
    departments: {
        type: Array,
        default: () => []
    },
    errors: {
        type: Object,
        default: () => ({})
    },
    submitting: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['submit', 'cancel', 'generate']);

const isEdit = ref(!!props.password);
const showPassword = ref(false);

const form = reactive({
    title: props.password?.title || '',
    folder_id: props.password?.folder_id || null,
    username: props.password?.username || '',
    password: '',
    url: props.password?.url || '',
    notes: props.password?.notes || '',
    department_id: props.password?.department_id || null,
    is_company_wide: props.password?.is_company_wide ?? true
});

watch(() => props.password, (newPassword) => {
    if (newPassword) {
        form.title = newPassword.title;
        form.folder_id = newPassword.folder_id;
        form.username = newPassword.username;
        form.password = newPassword.password || '';
        form.url = newPassword.url;
        form.notes = newPassword.notes;
        form.department_id = newPassword.department_id;
        form.is_company_wide = newPassword.is_company_wide ?? true;
    }
});

const handleSubmit = () => {
    emit('submit', { ...form });
};

const setPassword = (newPassword) => {
    form.password = newPassword;
};

defineExpose({
    setPassword
});
</script>

<style scoped>
.password-form {
    background: white;
    border-radius: 8px;
    padding: 2rem;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
}

.password-form h2 {
    margin-bottom: 1.5rem;
    color: #2c3e50;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #333;
}

.form-group label input[type="checkbox"] {
    display: inline-block;
    margin-right: 0.5rem;
    vertical-align: middle;
    width: auto;
    padding: 0;
    margin-bottom: 0;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #ff7a00;
    box-shadow: 0 0 0 3px rgba(255, 122, 0, 0.1);
}

.password-input-group {
    display: flex;
    gap: 0.5rem;
}

.password-input-group input {
    flex: 1;
}

.toggle-password {
    padding: 0.75rem;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.2s;
}

.toggle-password:hover {
    background: #f5f5f5;
}

.btn-generate {
    padding: 0.75rem 1rem;
    border: 1px solid #ff7a00;
    background: white;
    color: #ff7a00;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 500;
}

.btn-generate:hover {
    background: #ff7a00;
    color: white;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    transition: all 0.2s;
}

.btn:not(.btn-secondary) {
    background: #ff7a00;
    color: white;
}

.btn:not(.btn-secondary):hover:not(:disabled) {
    background: #f97316;
}

.btn.btn-secondary {
    background: #e0e0e0;
    color: #333;
}

.btn.btn-secondary:hover {
    background: #d0d0d0;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.error {
    color: #dc2626;
    font-size: 0.875rem;
    display: block;
    margin-top: 0.25rem;
}
</style>
