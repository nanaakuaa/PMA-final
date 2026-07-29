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
    background: rgba(15, 23, 42, 0.94);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 1.5rem;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 18px 50px rgba(0,0,0,0.18);
}

.password-card:hover {
    box-shadow: 0 24px 70px rgba(0,0,0,0.28);
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
    color: #f8fafc;
}

.card-actions {
    display: flex;
    gap: 0.5rem;
}

.icon-btn {
    background: linear-gradient(135deg, rgba(251, 191, 36, 0.22), rgba(249, 115, 22, 0.22));
    border: 1px solid rgba(251, 191, 36, 0.38);
    color: #fef3c7;
    border-radius: 999px;
    cursor: pointer;
    font-size: 1rem;
    padding: 0.4rem 0.55rem;
    opacity: 1;
    transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    box-shadow: 0 6px 16px rgba(249, 115, 22, 0.12);
}

.icon-btn:hover {
    transform: translateY(-1px);
    background: linear-gradient(135deg, rgba(251, 191, 36, 0.35), rgba(249, 115, 22, 0.3));
    box-shadow: 0 8px 20px rgba(249, 115, 22, 0.2);
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
    color: #94a3b8;
    margin-right: 0.5rem;
}

.value {
    color: #e2e8f0;
}

.link {
    color: #60a5fa;
    text-decoration: none;
}

.link:hover {
    text-decoration: underline;
}

.folder-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: rgba(249, 115, 22, 0.18);
    border-radius: 999px;
    font-size: 0.75rem;
    margin-top: 0.5rem;
    color: #fbbf24;
}

.card-footer {
    padding-top: 0.75rem;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.date {
    font-size: 0.75rem;
    color: #94a3b8;
}

.user-info {
    font-size: 0.75rem;
    color: #fbbf24;
    font-weight: 500;
}
</style>
