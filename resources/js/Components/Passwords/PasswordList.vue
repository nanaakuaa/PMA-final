<template>
    <div class="password-list">
        <div class="list-header">
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Search passwords..."
                class="search-input"
            />
            <button @click="$emit('create')" class="btn">
                + New Password
            </button>
        </div>

        <PasswordTableView
            :passwords="passwords"
            :loading="loading"
            :searchQuery="searchQuery"
            @select="$emit('select', $event)"
            @edit="$emit('edit', $event)"
            @delete="$emit('delete', $event)"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import PasswordTableView from './PasswordTableView.vue';

const props = defineProps({
    passwords: {
        type: Array,
        default: () => []
    },
    loading: {
        type: Boolean,
        default: false
    }
});

defineEmits(['create', 'select', 'edit', 'delete']);

const searchQuery = ref('');
</script>

<style scoped>
.password-list {
    background: rgba(15, 23, 42, 0.9);
    border-radius: 1.5rem;
    padding: 1.5rem;
}

.list-header {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.search-input {
    flex: 1;
    padding: 0.9rem 1rem;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 999px;
    font-size: 1rem;
    background: rgba(15, 23, 42, 0.7);
    color: #f8fafc;
}

.search-input::placeholder {
    color: rgba(248, 250, 252, 0.6);
}

.search-input:focus {
    outline: none;
    border-color: rgba(249, 115, 22, 0.8);
    box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.14);
}

.btn {
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #fbbf24, #f97316);
    color: #08090a;
    border: none;
    border-radius: 999px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    white-space: nowrap;
}

.btn:hover {
    background: linear-gradient(135deg, #f59e0b, #ea580c);
}
</style>
