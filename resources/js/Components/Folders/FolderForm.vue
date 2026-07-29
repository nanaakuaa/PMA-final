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
    background: white;
    border-radius: 8px;
    padding: 2rem;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
}

.folder-form h2 {
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

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #ff7a00;
    box-shadow: 0 0 0 3px rgba(255, 122, 0, 0.1);
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
