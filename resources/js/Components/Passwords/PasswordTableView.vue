<template>
    <div class="password-table-container">
        <div v-if="loading" class="loading">Loading...</div>

        <div v-else-if="filteredPasswords.length === 0" class="empty-state">
            <p>No passwords found</p>
        </div>

        <table v-else class="password-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Username</th>
                    <th>URL</th>
                    <th>Folder</th>
                    <th>Last Updated</th>
                    <th>Updated By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="password in filteredPasswords" :key="password.id" @click="$emit('select', password)" class="table-row">
                    <td class="title-cell">{{ password.title }}</td>
                    <td class="username-cell">{{ password.username || '—' }}</td>
                    <td class="url-cell">
                        <a v-if="password.url" :href="password.url" target="_blank" class="url-link">
                            {{ truncateUrl(password.url) }}
                        </a>
                        <span v-else>—</span>
                    </td>
                    <td class="folder-cell">
                        <span v-if="password.folder" class="folder-badge">
                            📁 {{ password.folder.name }}
                        </span>
                        <span v-else>—</span>
                    </td>
                    <td class="date-cell">{{ formatDate(password.updated_at) }}</td>
                    <td class="user-cell">{{ password.updater?.name || '—' }}</td>
                    <td class="actions-cell" @click.stop>
                        <div class="dropdown">
                            <button class="dropdown-btn" @click="toggleDropdown(password.id)">⋮</button>
                            <div v-if="openDropdown === password.id" class="dropdown-menu" @click.stop>
                                <button @click="$emit('edit', password); openDropdown = null" class="dropdown-item">✏️ Edit</button>
                                <button @click="$emit('delete', password); openDropdown = null" class="dropdown-item danger">🗑️ Delete</button>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    passwords: {
        type: Array,
        default: () => []
    },
    loading: {
        type: Boolean,
        default: false
    },
    searchQuery: {
        type: String,
        default: ''
    }
});

defineEmits(['select', 'edit', 'delete']);

const openDropdown = ref(null);

const toggleDropdown = (id) => {
    openDropdown.value = openDropdown.value === id ? null : id;
};

const filteredPasswords = computed(() => {
    if (!props.searchQuery.trim()) return props.passwords;
    const query = props.searchQuery.toLowerCase();
    return props.passwords.filter(password =>
        password.title.toLowerCase().includes(query) ||
        password.username?.toLowerCase().includes(query) ||
        password.url?.toLowerCase().includes(query)
    );
});

const truncateUrl = (url) => {
    if (!url) return '';
    const maxLength = 40;
    return url.length > maxLength ? url.substring(0, maxLength) + '...' : url;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};
</script>

<style scoped>
.password-table-container {
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

.password-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
}

.password-table thead {
    background: #f5f5f5;
    border-bottom: 2px solid #ddd;
}

.password-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #333;
}

.password-table tbody tr {
    border-bottom: 1px solid #e0e0e0;
    cursor: pointer;
    transition: background-color 0.2s;
}

.password-table tbody tr:hover {
    background-color: #fff7ed;
}

.password-table tbody tr:last-child {
    border-bottom: none;
}

.password-table td {
    padding: 1rem;
    color: #555;
}

.title-cell {
    font-weight: 600;
    color: #2c3e50;
}

.username-cell {
    font-family: monospace;
    font-size: 0.9rem;
    color: #666;
}

.url-cell {
    max-width: 200px;
    overflow: hidden;
}

.url-link {
    color: #ff7a00;
    text-decoration: none;
}

.url-link:hover {
    text-decoration: underline;
}

.folder-badge {
    display: inline-block;
    background: #fff7ed;
    color: #ff7a00;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.9rem;
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

.dropdown-item:hover {
    background-color: #f5f5f5;
}

.dropdown-item.danger {
    color: #dc2626;
}

.dropdown-item.danger:hover {
    background-color: #fee2e2;
}
</style>
