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
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.password-card {
    background: rgba(15, 23, 42, 0.92);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 1.5rem;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
}

.password-card:hover {
    box-shadow: 0 24px 70px rgba(0, 0, 0, 0.3);
    transform: translateY(-2px);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.card-header h3 {
    margin: 0;
    color: #f8fafc;
    font-size: 1.1rem;
    word-break: break-word;
}

.card-actions {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    background: linear-gradient(135deg, rgba(251, 191, 36, 0.22), rgba(249, 115, 22, 0.22));
    border: 1px solid rgba(251, 191, 36, 0.38);
    color: #fef3c7;
    border-radius: 999px;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0.4rem 0.55rem;
    opacity: 1;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    box-shadow: 0 6px 16px rgba(249, 115, 22, 0.12);
}

.action-btn:hover {
    transform: translateY(-1px);
    background: linear-gradient(135deg, rgba(251, 191, 36, 0.35), rgba(249, 115, 22, 0.3));
    box-shadow: 0 8px 20px rgba(249, 115, 22, 0.2);
}

.action-btn.danger:hover {
    color: #fecdd3;
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
    color: #94a3b8;
    text-transform: uppercase;
}

.value {
    font-size: 0.9rem;
    color: #e2e8f0;
    word-break: break-word;
}

.value.url {
    color: #60a5fa;
    text-decoration: none;
}

.value.url:hover {
    text-decoration: underline;
}

.card-footer {
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    font-size: 0.8rem;
    color: #94a3b8;
}
</style>
