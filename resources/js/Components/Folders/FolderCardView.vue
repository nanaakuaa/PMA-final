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
    color: #94a3b8;
}

.btn-link {
    background: none;
    border: none;
    color: #fbbf24;
    cursor: pointer;
    text-decoration: underline;
    font-size: 1rem;
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    overflow: visible;

}

.folder-card {
    background: rgba(15, 23, 42, 0.92);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 1.5rem;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
    overflow: visible;
    position: relative;


}

.folder-card:hover {
    box-shadow: 0 24px 70px rgba(0, 0, 0, 0.3);
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
    color: #f8fafc;
    font-size: 1.1rem;
    word-break: break-word;
}

.password-count {
    display: block;
    font-size: 0.85rem;
    color: #fbbf24;
    font-weight: 600;
}

.card-actions {
    position: relative;
    display: flex;
    gap: 0.5rem;

}

.action-btn {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #f8fafc;
    border-radius: 999px;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0.35rem 0.5rem;
    opacity: 0.85;
    transition: opacity 0.2s, transform 0.2s;
}

.action-btn:hover:not(:disabled) {
    opacity: 1;
    transform: translateY(-1px);
}

.action-btn:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.action-btn.danger:hover {
    color: #fb7185;
}

.card-body {
    color: #cbd5e1;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    line-height: 1.6;
}

.card-body p {
    margin: 0;
    word-break: break-word;
}

.card-footer {
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    font-size: 0.8rem;
    color: #94a3b8;
}
</style>
