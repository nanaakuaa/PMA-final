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
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
}

.list-header {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.search-input {
    flex: 1;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.search-input:focus {
    outline: none;
    border-color: #ff7a00;
    box-shadow: 0 0 0 3px rgba(255, 122, 0, 0.1);
}

.btn {
    padding: 0.75rem 1.5rem;
    background: #ff7a00;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    white-space: nowrap;
}

.btn:hover {
    background: #f97316;
}
</style>
