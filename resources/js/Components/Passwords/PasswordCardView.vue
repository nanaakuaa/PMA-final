<template>
    <div class="password-cards">
        <div v-if="passwords.length === 0" class="empty">
            <p>No passwords found. <button @click="$emit('create')" class="btn-link">Create one</button></p>
        </div>

        <div v-else class="cards-grid">
            <div
                v-for="password in passwords"
                :key="password.id"
                class="password-card"
                @click="$emit('select', password)"
            >
                <div class="card-header">
                    <h3>{{ password.title }}</h3>
                    <div class="card-actions">
                        <button
                            @click.stop="$emit('edit', password)"
                            class="action-btn"
                            title="Edit"
                        >
                            ✏️
                        </button>
                        <button
                            @click.stop="$emit('delete', password)"
                            class="action-btn danger"
                            title="Delete"
                        >
                            🗑️
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div v-if="password.folder" class="card-item">
                        <span class="label">Folder:</span>
                        <span class="value">📁 {{ password.folder.name }}</span>
                    </div>

                    <div v-if="password.username" class="card-item">
                        <span class="label">Username:</span>
                        <span class="value">{{ password.username }}</span>
                    </div>

                    <div v-if="password.url" class="card-item">
                        <span class="label">URL:</span>
                        <a :href="password.url" target="_blank" class="value url">
                            {{ password.url }}
                        </a>
                    </div>
                </div>

                <div class="card-footer">
                    <small>Created by {{ password.creator?.name || 'Unknown' }}</small>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    passwords: {
        type: Array,
        default: () => []
    }
});

defineEmits(['create', 'select', 'edit', 'delete']);
</script>

<style scoped>
.password-cards {
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
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.password-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.password-card:hover {
    box-shadow: 0 6px 16px rgba(255, 122, 0, 0.15);
    border-color: #ff7a00;
    transform: translateY(-2px);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #fff7ed;
}

.card-header h3 {
    margin: 0;
    color: #2c3e50;
    font-size: 1.1rem;
    word-break: break-word;
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

.action-btn:hover {
    opacity: 1;
}

.action-btn.danger:hover {
    color: #e74c3c;
}

.card-body {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.card-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #999;
    text-transform: uppercase;
}

.value {
    font-size: 0.9rem;
    color: #555;
    word-break: break-word;
}

.value.url {
    color: #3498db;
    text-decoration: none;
}

.value.url:hover {
    text-decoration: underline;
}

.card-footer {
    padding-top: 1rem;
    border-top: 1px solid #f0f0f0;
    font-size: 0.8rem;
    color: #999;
}
</style>
