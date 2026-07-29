<template>
    <div class="notification-badge-container">
        <button @click="isOpen = !isOpen" class="notification-bell">
            🔔
            <span v-if="unreadCount > 0" class="badge-count">{{ unreadCount > 99 ? '99+' : unreadCount }}</span>
        </button>

        <div v-if="isOpen" class="notification-dropdown">
            <div class="dropdown-header">
                <h4>Notifications</h4>
                <button @click="isOpen = false" class="close-btn">✕</button>
            </div>

            <div v-if="recentNotifications.length === 0" class="empty-state">
                <p>No new notifications</p>
            </div>

            <div v-else class="recent-notifications">
                <div
                    v-for="notification in recentNotifications"
                    :key="notification.id"
                    :class="['notification-preview', { unread: !notification.is_read }]"
                >
                    <div class="notification-content">
                        <strong>{{ notification.triggered_by_user?.name }}</strong>
                        <span v-if="notification.action === 'created'" class="action">created</span>
                        <span v-else-if="notification.action === 'updated'" class="action">updated</span>
                        <span v-else-if="notification.action === 'deleted'" class="action">deleted</span>
                        <span class="password-title">{{ notification.password?.title }}</span>
                    </div>
                    <div class="notification-time">{{ formatTime(notification.created_at) }}</div>
                </div>
            </div>

            <div class="dropdown-footer">
                <a href="/settings" class="view-all-link">View all notifications</a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const isOpen = ref(false);
const unreadCount = ref(0);
const recentNotifications = ref([]);
const pollInterval = ref(null);

const loadUnreadNotifications = async () => {
    try {
        const response = await axios.get('/api/notifications/unread');
        unreadCount.value = response.data.count;
        recentNotifications.value = response.data.notifications;
    } catch (error) {
        console.error('Failed to load notifications:', error);
    }
};

const formatTime = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins}m ago`;
    if (diffHours < 24) return `${diffHours}h ago`;
    if (diffDays < 7) return `${diffDays}d ago`;

    return date.toLocaleDateString();
};

onMounted(() => {
    loadUnreadNotifications();

    // Poll for new notifications every 30 seconds
    pollInterval.value = setInterval(loadUnreadNotifications, 30000);
});

onUnmounted(() => {
    if (pollInterval.value) {
        clearInterval(pollInterval.value);
    }
});
</script>

<style scoped>
.notification-badge-container {
    position: relative;
}

.notification-bell {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    position: relative;
    padding: 0;
    display: flex;
    align-items: center;
}

.badge-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ff4444;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

.notification-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    width: 350px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    margin-top: 8px;
}

.dropdown-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid #e0e0e0;
}

.dropdown-header h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.close-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;
    color: #999;
}

.empty-state {
    padding: 24px 16px;
    text-align: center;
    color: #999;
}

.recent-notifications {
    max-height: 300px;
    overflow-y: auto;
}

.notification-preview {
    padding: 12px 16px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: background-color 0.2s;
}

.notification-preview:hover {
    background-color: #f9f9f9;
}

.notification-preview.unread {
    background-color: #f0f7ff;
}

.notification-content {
    font-size: 13px;
    line-height: 1.4;
    margin-bottom: 4px;
}

.notification-content strong {
    color: #1f2933;
}

.action {
    color: #666;
    margin: 0 3px;
}

.password-title {
    font-weight: 500;
    color: #1f2933;
}

.notification-time {
    font-size: 11px;
    color: #999;
}

.dropdown-footer {
    padding: 12px 16px;
    border-top: 1px solid #e0e0e0;
    text-align: center;
}

.view-all-link {
    color: #0066cc;
    text-decoration: none;
    font-size: 14px;
}

.view-all-link:hover {
    text-decoration: underline;
}
</style>
