<template>
    <div class="folder-table-container">
        <div v-if="loading" class="loading">Loading...</div>

        <div v-else-if="folders.length === 0" class="empty-state">
            <p>No folders found</p>
        </div>

        <table v-else class="folder-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Passwords</th>
                    <th>Created</th>
                    <th>Created By</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="folder in folders" :key="folder.id" @click="$emit('select', folder)" class="table-row">
                    <td class="name-cell">
                        <span class="folder-icon">📁</span>
                        {{ folder.name }}
                    </td>
                    <td class="description-cell">{{ folder.description || '—' }}</td>
                    <td class="password-count-cell">
                        <span class="count-badge">{{ folder.passwords_count }}</span>
                    </td>
                    <td class="date-cell">{{ formatDate(folder.created_at) }}</td>
                    <td class="user-cell">{{ folder.creator?.name || '—' }}</td>
                    <td class="date-cell">{{ formatDate(folder.updated_at) }}</td>
                    <td class="actions-cell" @click.stop>
                        <div class="dropdown">
                            <button class="dropdown-btn" @click="toggleDropdown(folder.id)">⋮</button>
                            <div v-if="openDropdown === folder.id" class="dropdown-menu" @click.stop>
                                <button @click="$emit('edit', folder); openDropdown = null" class="dropdown-item">✏️ Edit</button>
                                <button
                                    @click="$emit('delete', folder); openDropdown = null"
                                    class="dropdown-item danger"
                                    :disabled="folder.passwords_count > 0"
                                    :title="folder.passwords_count > 0 ? 'Cannot delete folder with passwords' : ''"
                                >
                                    🗑️ Delete
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    folders: {
        type: Array,
        default: () => []
    },
    loading: {
        type: Boolean,
        default: false
    }
});

defineEmits(['select', 'edit', 'delete']);

const openDropdown = ref(null);

const toggleDropdown = (id) => {
    openDropdown.value = openDropdown.value === id ? null : id;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};
</script>

<style scoped>
.folder-table-container {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.loading {
    padding: 2rem;
    text-align: center;
    color: #666;
}

.empty-state {
    padding: 2rem;
    text-align: center;
    color: #999;
}

.folder-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
}

.folder-table thead {
    background: #f5f5f5;
    border-bottom: 2px solid #ddd;
}

.folder-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #333;
}

.folder-table tbody tr {
    border-bottom: 1px solid #e0e0e0;
    cursor: pointer;
    transition: background-color 0.2s;
}

.folder-table tbody tr:hover {
    background-color: #fff7ed;
}

.folder-table tbody tr:last-child {
    border-bottom: none;
}

.folder-table td {
    padding: 1rem;
    color: #555;
}

.name-cell {
    font-weight: 600;
    color: #2c3e50;
}

.folder-icon {
    margin-right: 0.5rem;
}

.description-cell {
    color: #999;
    font-size: 0.9rem;
    max-width: 250px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.password-count-cell {
    text-align: center;
}

.count-badge {
    display: inline-block;
    background: #ff7a00;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 600;
}

.date-cell,
.user-cell {
    font-size: 0.9rem;
    color: #999;
}

.actions-cell {
    position: relative;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.25rem;
    padding: 0.25rem 0.5rem;
    opacity: 0.6;
    transition: opacity 0.2s;
}

.dropdown-btn:hover {
    opacity: 1;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    min-width: 120px;
}

.dropdown-item {
    display: block;
    width: 100%;
    padding: 0.75rem 1rem;
    border: none;
    background: none;
    text-align: left;
    cursor: pointer;
    font-size: 0.95rem;
    color: #333;
    transition: background-color 0.2s;
}

.dropdown-item:first-child {
    border-radius: 3px 3px 0 0;
}

.dropdown-item:last-child {
    border-radius: 0 0 3px 3px;
}

.dropdown-item:hover:not(:disabled) {
    background-color: #f5f5f5;
}

.dropdown-item:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.dropdown-item.danger {
    color: #dc2626;
}

.dropdown-item.danger:hover:not(:disabled) {
    background-color: #fee2e2;
}
</style>
