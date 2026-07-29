<template>
    <div class="password-card" @click="$emit('click')">
        <div class="card-header">
            <h3>{{ password.title }}</h3>
            <div class="card-actions" @click.stop>
                <button @click="$emit('edit')" class="icon-btn" title="Edit">✏️</button>
                <button @click="$emit('delete')" class="icon-btn" title="Delete">🗑️</button>
            </div>
        </div>

        <div class="card-body">
            <div v-if="password.username" class="info-row">
                <span class="label">Username:</span>
                <span class="value">{{ password.username }}</span>
            </div>

            <div v-if="password.url" class="info-row">
                <span class="label">URL:</span>
                <a :href="password.url" target="_blank" @click.stop class="value link">
                    {{ truncateUrl(password.url) }}
                </a>
            </div>

            <div v-if="password.folder" class="folder-badge">
                📁 {{ password.folder.name }}
            </div>
        </div>

        <div class="card-footer">
            <span class="date">Updated {{ formatDate(password.updated_at) }}</span>
            <span v-if="password.updater" class="user-info">by {{ password.updater.name }}</span>
        </div>
    </div>
</template>

<script setup>
defineProps({
    password: {
        type: Object,
        required: true
    }
});

defineEmits(['click', 'edit', 'delete']);

const truncateUrl = (url) => {
    if (!url) return '';
    const maxLength = 30;
    return url.length > maxLength ? url.substring(0, maxLength) + '...' : url;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};
</script>

<style scoped>
.password-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1.25rem;
    cursor: pointer;
    transition: all 0.3s;
}

.password-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 1rem;
}

.card-header h3 {
    margin: 0;
    font-size: 1.125rem;
    color: #2c3e50;
}

.card-actions {
    display: flex;
    gap: 0.5rem;
}

.icon-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    padding: 0.25rem;
    opacity: 0.6;
    transition: opacity 0.2s;
}

.icon-btn:hover {
    opacity: 1;
}

.card-body {
    margin-bottom: 1rem;
}

.info-row {
    display: flex;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.label {
    color: #666;
    margin-right: 0.5rem;
}

.value {
    color: #2c3e50;
}

.link {
    color: #3498db;
    text-decoration: none;
}

.link:hover {
    text-decoration: underline;
}

.folder-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: #f0f0f0;
    border-radius: 4px;
    font-size: 0.75rem;
    margin-top: 0.5rem;
}

.card-footer {
    padding-top: 0.75rem;
    border-top: 1px solid #f0f0f0;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.date {
    font-size: 0.75rem;
    color: #999;
}

.user-info {
    font-size: 0.75rem;
    color: #f97316;
    font-weight: 500;
}
</style>
