<template>
    <div class="folder-form">
        <h2>{{ isEdit ? 'Edit Folder' : 'Create New Folder' }}</h2>

        <form @submit.prevent="handleSubmit">
            <div class="form-group">
                <label for="name">Folder Name *</label>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    placeholder="e.g., Work Accounts"
                />
                <span v-if="errors.name" class="error">{{ errors.name }}</span>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea
                    id="description"
                    v-model="form.description"
                    rows="3"
                    placeholder="Optional description for this folder..."
                ></textarea>
                <span v-if="errors.description" class="error">{{ errors.description }}</span>
            </div>

            <div class="form-actions">
                <button type="button" @click="$emit('cancel')" class="btn btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn" :disabled="submitting">
                    {{ submitting ? 'Saving...' : 'Save Folder' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';

const props = defineProps({
    folder: {
        type: Object,
        default: null
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

const emit = defineEmits(['submit', 'cancel']);

const isEdit = ref(!!props.folder);

const form = reactive({
    name: props.folder?.name || '',
    description: props.folder?.description || ''
});

watch(() => props.folder, (newFolder) => {
    if (newFolder) {
        form.name = newFolder.name;
        form.description = newFolder.description;
    }
});

const handleSubmit = () => {
    emit('submit', { ...form });
};
</script>

<style scoped>
.folder-form {
    background: rgba(15, 23, 42, 0.94);
    border-radius: 1.5rem;
    padding: 2rem;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
}

.folder-form h2 {
    margin-bottom: 1.5rem;
    color: #f8fafc;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #94a3b8;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.9rem 1rem;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 1rem;
    font-size: 1rem;
    background: rgba(15, 23, 42, 0.75);
    color: #f8fafc;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: rgba(249, 115, 22, 0.8);
    box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.14);
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
    border-radius: 999px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    transition: all 0.2s;
}

.btn:not(.btn-secondary) {
    background: linear-gradient(135deg, #fbbf24, #f97316);
    color: #08090a;
}

.btn:not(.btn-secondary):hover:not(:disabled) {
    background: linear-gradient(135deg, #f59e0b, #ea580c);
}

.btn.btn-secondary {
    background: rgba(255, 255, 255, 0.08);
    color: #f8fafc;
    border: 1px solid rgba(255, 255, 255, 0.12);
}

.btn.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.14);
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.error {
    color: #fca5a5;
    font-size: 0.875rem;
    display: block;
    margin-top: 0.25rem;
}
</style>
