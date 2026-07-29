<template>
    <div class="notifications-container">
        <div class="notifications-header">
            <h3>Notifications</h3>
            <button
                v-if="unreadCount > 0"
                @click="markAllAsRead"
                class="mark-all-btn"
            >
                Mark all as read
            </button>
        </div>

        <div v-if="notifications.length === 0" class="no-notifications">
            <p>No notifications</p>
        </div>

        <div v-else class="notifications-list">
            <div
                v-for="notification in notifications"
                :key="notification.id"
                :class="['notification-item', { unread: !notification.is_read }]"
            >
                <div class="notification-content">
                    <div class="notification-message">
                        <strong>{{ notification.triggered_by_user?.name }}</strong>
                        <span v-if="notification.action === 'created'" class="action-text">created</span>
                        <span v-else-if="notification.action === 'updated'" class="action-text">updated</span>
                        <span v-else-if="notification.action === 'deleted'" class="action-text">deleted</span>
                        <span class="password-title">{{ notification.password?.title }}</span>
                    </div>
                    <div class="notification-time">
                        {{ formatTime(notification.created_at) }}
                    </div>
                </div>
                <div class="notification-actions">
                    <button
                        v-if="!notification.is_read"
                        @click="markAsRead(notification.id)"
                        class="read-btn"
                        title="Mark as read"
                    >
                        ✓
                    </button>
                    <button
                        @click="deleteNotification(notification.id)"
                        class="delete-btn"
                        title="Delete notification"
                    >
                        ✕
                    </button>
                </div>
            </div>
        </div>

        <div v-if="notifications.length > 0" class="notifications-footer">
            <button
                v-if="hasMore"
                @click="loadMore"
                class="load-more-btn"
            >
                Load more
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const notifications = ref([]);
const unreadCount = ref(0);
const currentPage = ref(1);
const hasMore = ref(false);
const perPage = ref(15);
const loading = ref(false);

const loadNotifications = async () => {
    if (loading.value) return;

    loading.value = true;
    try {
        const response = await axios.get('/api/notifications', {
            params: {
                page: currentPage.value,
                per_page: perPage.value
            }
        });

        if (currentPage.value === 1) {
            notifications.value = response.data.data;
        } else {
            notifications.value.push(...response.data.data);
        }

        hasMore.value = response.data.current_page < response.data.last_page;
    } catch (error) {
        console.error('Failed to load notifications:', error);
    } finally {
        loading.value = false;
    }
};

const loadUnreadCount = async () => {
    try {
        const response = await axios.get('/api/notifications/unread');
        unreadCount.value = response.data.count;
    } catch (error) {
        console.error('Failed to load unread count:', error);
    }
};

const markAsRead = async (notificationId) => {
    try {
        await axios.post(`/api/notifications/${notificationId}/read`);

        // Update the notification in the list
        const notification = notifications.value.find(n => n.id === notificationId);
        if (notification) {
            notification.is_read = true;
        }

        unreadCount.value = Math.max(0, unreadCount.value - 1);
    } catch (error) {
        console.error('Failed to mark notification as read:', error);
    }
};

const markAllAsRead = async () => {
    try {
        await axios.post('/api/notifications/read-all');

        notifications.value.forEach(n => {
            n.is_read = true;
        });

        unreadCount.value = 0;
    } catch (error) {
        console.error('Failed to mark all notifications as read:', error);
    }
};

const deleteNotification = async (notificationId) => {
    try {
        await axios.delete(`/api/notifications/${notificationId}`);

        notifications.value = notifications.value.filter(n => n.id !== notificationId);
    } catch (error) {
        console.error('Failed to delete notification:', error);
    }
};

const loadMore = () => {
    currentPage.value++;
    loadNotifications();
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
    loadNotifications();
    loadUnreadCount();
});
</script>

<style scoped>
.notifications-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    max-width: 600px;
}

.notifications-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    border-bottom: 1px solid #e0e0e0;
}

.notifications-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.mark-all-btn {
    background: none;
    border: none;
    color: #0066cc;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
}

.mark-all-btn:hover {
    text-decoration: underline;
}

.no-notifications {
    padding: 32px 16px;
    text-align: center;
    color: #999;
}

.notifications-list {
    max-height: 500px;
    overflow-y: auto;
}

.notification-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s;
}

.notification-item:hover {
    background-color: #f9f9f9;
}

.notification-item.unread {
    background-color: #f0f7ff;
}

.notification-content {
    flex: 1;
}

.notification-message {
    font-size: 14px;
    margin-bottom: 4px;
}

.action-text {
    color: #666;
}

.password-title {
    font-weight: 500;
    color: #1f2933;
}

.notification-time {
    font-size: 12px;
    color: #999;
}

.notification-actions {
    display: flex;
    gap: 8px;
    margin-left: 12px;
}

.read-btn,
.delete-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 16px;
    padding: 4px 8px;
    color: #999;
    transition: color 0.2s;
}

.read-btn:hover {
    color: #0066cc;
}

.delete-btn:hover {
    color: #cc0000;
}

.notifications-footer {
    padding: 12px 16px;
    text-align: center;
    border-top: 1px solid #f0f0f0;
}

.load-more-btn {
    background: #f0f0f0;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    color: #666;
}

.load-more-btn:hover {
    background: #e0e0e0;
}
</style>
