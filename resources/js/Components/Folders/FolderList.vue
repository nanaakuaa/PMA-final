<template>
    <div class="folder-list">
        <div class="list-header">
            <h2>Folders</h2>
            <button @click="$emit('create')" class="btn">
                + New Folder
            </button>
        </div>

        <FolderTableView
            :folders="folders"
            :loading="loading"
            @select="$emit('select', $event)"
            @edit="$emit('edit', $event)"
            @delete="$emit('delete', $event)"
        />
    </div>
</template>

<script setup>
import FolderTableView from './FolderTableView.vue';

defineProps({
    folders: {
        type: Array,
        default: () => []
    },
    loading: {
        type: Boolean,
        default: false
    }
});

defineEmits(['create', 'select', 'edit', 'delete']);
</script>

<style scoped>
.folder-list {
    background: rgba(15, 23, 42, 0.9);
    border-radius: 1.5rem;
    padding: 1.5rem;
}

.list-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.list-header h2 {
    margin: 0;
    color: #f8fafc;
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
