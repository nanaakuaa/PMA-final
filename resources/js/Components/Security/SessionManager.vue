<template>
    <div class="session-manager">
        <div class="sessions-header">
            <h3>Active Sessions</h3>
            <p>Manage your active sessions and revoke access from other devices</p>
        </div>

        <div v-if="loading" class="loading">Loading sessions...</div>

        <div v-else-if="sessions.length === 0" class="empty">
            <p>No active sessions found</p>
        </div>

        <div v-else class="sessions-list">
            <div v-for="session in sessions" :key="session.id" class="session-item">
                <div class="session-info">
                    <div class="session-header">
                        <span class="device-name">
                            {{ session.browser || 'Unknown Browser' }}
                            <span v-if="session.is_current" class="current-badge">Current</span>
                        </span>
                    </div>
                    <div class="session-details">
                        <span class="detail">{{ session.device_type || 'Unknown Device' }}</span>
                        <span class="detail">{{ session.ip_address }}</span>
                        <span class="detail">{{ formatDate(session.last_activity) }}</span>
                    </div>
                </div>

                <button
                    v-if="!session.is_current"
                    @click="revokeSession(session)"
                    :disabled="revoking === session.id"
                    class="btn-revoke"
                >
                    {{ revoking === session.id ? 'Revoking...' : 'Revoke' }}
                </button>
            </div>
        </div>

        <div v-if="sessions.length > 1" class="revoke-all-section">
            <button @click="showRevokeAllForm = true" class="btn btn-danger">
                Revoke All Other Sessions
            </button>

            <div v-if="showRevokeAllForm" class="revoke-all-form">
                <p>Enter your password to revoke all other sessions:</p>
                <div class="form-group">
                    <input v-model="revokeAllPassword" type="password" placeholder="Password" />
                </div>
                <div class="form-actions">
                    <button @click="showRevokeAllForm = false" class="btn btn-secondary">Cancel</button>
                    <button @click="revokeAllSessions" :disabled="revoking" class="btn btn-danger">
                        {{ revoking ? 'Revoking...' : 'Revoke All' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';

const sessions = ref([]);
const loading = ref(false);
const revoking = ref(null);
const showRevokeAllForm = ref(false);
const revokeAllPassword = ref('');
let trackingInterval = null;

const loadSessions = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/sessions');
        sessions.value = response.data;
    } catch (error) {
        console.error('Failed to load sessions:', error);
    } finally {
        loading.value = false;
    }
};

const trackActivity = async () => {
    try {
        await axios.post('/api/sessions/track');
    } catch (error) {
        console.error('Failed to track activity:', error);
    }
};

const revokeSession = async (session) => {
    revoking.value = session.id;
    try {
        await axios.delete(`/api/sessions/${session.id}`);
        sessions.value = sessions.value.filter(s => s.id !== session.id);
        alert('Session revoked');
    } catch (error) {
        console.error('Failed to revoke session:', error);
        alert('Failed to revoke session');
    } finally {
        revoking.value = null;
    }
};

const revokeAllSessions = async () => {
    revoking.value = true;
    try {
        await axios.post('/api/sessions/revoke-all', {
            password: revokeAllPassword.value,
        });
        showRevokeAllForm.value = false;
        revokeAllPassword.value = '';
        await loadSessions();
        alert('All other sessions revoked');
    } catch (error) {
        console.error('Failed to revoke sessions:', error);
        alert('Failed to revoke sessions. Invalid password.');
    } finally {
        revoking.value = null;
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleString();
};

onMounted(() => {
    loadSessions();
    trackingInterval = setInterval(trackActivity, 60000); // Track every minute
});

onBeforeUnmount(() => {
    if (trackingInterval) clearInterval(trackingInterval);
});
</script>

<style scoped>
.session-manager {
    max-width: 700px;
    margin: 0 auto;
    background: white;
    padding: 2rem;
    border-radius: 8px;
}

.sessions-header {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e0e0e0;
}

.sessions-header h3 {
    margin: 0 0 0.5rem 0;
    color: #2c3e50;
}

.sessions-header p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.loading,
.empty {
    text-align: center;
    padding: 2rem;
    color: #666;
}

.sessions-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
}

.session-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 4px;
    border-left: 4px solid #ff7a00;
}

.session-info {
    flex: 1;
}

.session-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.device-name {
    font-weight: 600;
    color: #2c3e50;
}

.current-badge {
    background: #fff7ed;
    color: #c2410c;
    padding: 0.25rem 0.5rem;
    border-radius: 3px;
    font-size: 0.75rem;
    font-weight: 600;
}

.session-details {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.detail {
    font-size: 0.875rem;
    color: #666;
}

.btn-revoke {
    padding: 0.5rem 1rem;
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.875rem;
    transition: background 0.2s;
}

.btn-revoke:hover:not(:disabled) {
    background: #c0392b;
}

.btn-revoke:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.revoke-all-section {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e0e0e0;
}

.revoke-all-form {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    padding: 1rem;
    border-radius: 4px;
    margin-top: 1rem;
}

.form-group {
    margin: 1rem 0;
}

.form-group input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1rem;
}
</style>
