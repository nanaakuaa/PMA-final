<template>
    <div class="folder-cards">
        <div v-if="folders.length === 0" class="empty">
            <p>No folders found. <button @click="$emit('create')" class="btn-link">Create one</button></p>
        </div>

        <div v-else class="cards-grid">
            <div
                v-for="folder in folders"
                :key="folder.id"
                class="folder-card"
                @click="$emit('select', folder)"
            >
                <div class="card-header">
                    <div class="folder-icon">📁</div>
                    <div class="folder-info">
                        <h3>{{ folder.name }}</h3>
                        <span class="password-count">{{ folder.passwords_count }} password{{ folder.passwords_count !== 1 ? 's' : '' }}</span>
                    </div>
                    <div class="card-actions">
                        <button
                            @click.stop="$emit('edit', folder)"
                            class="action-btn"
                            title="Edit"
                        >
                            ✏️
                        </button>
                        <button
                            @click.stop="$emit('delete', folder)"
                            class="action-btn danger"
                            title="Delete"
                            :disabled="folder.passwords_count > 0"
                            :title="folder.passwords_count > 0 ? 'Cannot delete folder with passwords' : 'Delete'"
                        >
                            🗑️
                        </button>
                    </div>
                </div>

                <div v-if="folder.description" class="card-body">
                    <p>{{ folder.description }}</p>
                </div>

                <div class="card-footer">
                    <small>Created {{ formatDate(folder.created_at) }}</small>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    folders: {
        type: Array,
        default: () => []
    }
});

defineEmits(['create', 'select', 'edit', 'delete']);

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};
</script>

<style scoped>
.folder-cards {
    padding: 1rem 0;
}

.empty {
    text-align: center;
    padding: 3rem;
    color: #666;
}

.btn-link {
    background: none;
    border: none;
    color: #ff7a00;
    cursor: pointer;
    text-decoration: underline;
    font-size: 1rem;
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
}

.folder-card {
    background: white;
    border: 2px solid #fff7ed;
    border-radius: 8px;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.folder-card:hover {
    box-shadow: 0 6px 16px rgba(255, 122, 0, 0.15);
    border-color: #ff7a00;
    transform: translateY(-2px);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
}

.folder-icon {
    font-size: 2.5rem;
    flex-shrink: 0;
}

.folder-info {
    flex: 1;
}

.folder-info h3 {
    margin: 0 0 0.25rem 0;
    color: #2c3e50;
    font-size: 1.1rem;
    word-break: break-word;
}

.password-count {
    display: block;
    font-size: 0.85rem;
    color: #ff7a00;
    font-weight: 600;
}

.card-actions {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0.25rem;
    opacity: 0.6;
    transition: opacity 0.2s;
}

.action-btn:hover:not(:disabled) {
    opacity: 1;
}

.action-btn:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.action-btn.danger:hover {
    color: #e74c3c;
}

.card-body {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.card-body p {
    margin: 0;
    word-break: break-word;
}

.card-footer {
    padding-top: 1rem;
    border-top: 1px solid #f0f0f0;
    font-size: 0.8rem;
    color: #999;
}
</style>
