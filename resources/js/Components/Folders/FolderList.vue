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
    background: white;
    border-radius: 8px;
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
    color: #2c3e50;
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
