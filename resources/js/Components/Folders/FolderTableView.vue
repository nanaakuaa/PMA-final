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
    background: rgba(15, 23, 42, 0.92);
    border-radius: 1.5rem;
    overflow-x: auto;
    overflow-y: visible;
    box-shadow: 0 18px 50px rgba(0, 0, 0, 0.2);
position: relative;
}

.loading {
    padding: 2rem;
    text-align: center;
    color: #cbd5e1;
}

.empty-state {
    padding: 2rem;
    text-align: center;
    color: #94a3b8;
}

.folder-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
}

.folder-table thead {
    background: rgba(15, 23, 42, 0.96);
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.folder-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #e2e8f0;
}

.folder-table tbody tr {
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    cursor: pointer;
    transition: background-color 0.2s;
}

.folder-table tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.04);
}

.folder-table tbody tr:last-child {
    border-bottom: none;
}

.folder-table td {
    padding: 1rem;
    color: #cbd5e1;
}

.name-cell {
    font-weight: 600;
    color: #f8fafc;
}

.folder-icon {
    margin-right: 0.5rem;
}

.description-cell {
    color: #94a3b8;
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
    background: rgba(249, 115, 22, 0.18);
    color: #fbbf24;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 600;
}

.date-cell,
.user-cell {
    font-size: 0.9rem;
    color: #94a3b8;
}

.actions-cell {
    position: relative;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-btn {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #f8fafc;
    border-radius: 999px;
    cursor: pointer;
    font-size: 1.25rem;
    padding: 0.25rem 0.5rem;
    opacity: 0.85;
    transition: opacity 0.2s, transform 0.2s;
}

.dropdown-btn:hover {
    opacity: 1;
    transform: translateY(-1px);
}

.dropdown-menu {
    position: absolute;
    top:calc(100% + 6px);
    right: 0;
    background: rgba(15, 23, 42, 0.98);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 1rem;
    box-shadow: 0 16px 48px rgba(0, 0, 0, 0.25);
    z-index: 99999;
    min-width: 180px;
}

.dropdown-item {
    display: block;
    width: 100%;
    padding: 0.85rem 1rem;
    border: none;
    background: transparent;
    text-align: left;
    cursor: pointer;
    font-size: 0.95rem;
    color: #e2e8f0;
    transition: background-color 0.2s;
}

.dropdown-item:first-child {
    border-radius: 1rem 1rem 0 0;
}

.dropdown-item:last-child {
    border-radius: 0 0 1rem 1rem;
}

.dropdown-item:hover:not(:disabled) {
    background-color: rgba(255, 255, 255, 0.06);
}

.dropdown-item:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.dropdown-item.danger {
    color: #fca5a5;
}

.dropdown-item.danger:hover:not(:disabled) {
    background-color: rgba(248, 113, 113, 0.15);
}
</style>
