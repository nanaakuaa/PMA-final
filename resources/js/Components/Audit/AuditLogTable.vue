<template>
    <div class="audit-log-table">
        <div class="table-header">
            <h2>Audit Log</h2>
            <div class="filters">
                <select v-model="filterAction" class="filter-select">
                    <option value="">All Actions</option>
                    <option value="password_created">Created</option>
                    <option value="password_viewed">Viewed</option>
                    <option value="password_updated">Updated</option>
                    <option value="password_deleted">Deleted</option>
                    <option value="password_shared">Shared</option>
                </select>
            </div>
        </div>

        <div v-if="loading" class="loading">Loading...</div>

        <div v-else-if="filteredLogs.length === 0" class="empty-state">
            <p>No audit logs found</p>
        </div>

        <div v-else class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Resource</th>
                        <th>IP Address</th>
                        <th>Date & Time</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="log in filteredLogs" :key="log.id">
                        <td>
                            <div class="user-cell">
                                <span class="user-name">{{ log.user?.name || 'Unknown' }}</span>
                                <span class="user-email">{{ log.user?.email || '' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="action-badge" :class="getActionClass(log.action)">
                                {{ formatAction(log.action) }}
                            </span>
                        </td>
                        <td>
                            <div class="resource-info">
                                <span class="resource-type">{{ formatModelType(log.model_type) }}</span>
                                <span v-if="log.model_id" class="resource-id">#{{ log.model_id }}</span>
                            </div>
                        </td>
                        <td>{{ log.ip_address }}</td>
                        <td>{{ formatDate(log.created_at) }}</td>
                        <td>
                            <button
                                v-if="log.metadata"
                                @click="showDetails(log)"
                                class="details-btn"
                            >
                                View
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="logs.length > 0" class="pagination">
            <!-- Pagination component is now used in Audit.vue -->
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    logs: {
        type: Array,
        default: () => []
    },
    loading: {
        type: Boolean,
        default: false
    }
});

defineEmits([]);

const filterAction = ref('');

const filteredLogs = computed(() => {
    if (!filterAction.value) return props.logs;
    return props.logs.filter(log => log.action === filterAction.value);
});

const getActionClass = (action) => {
    if (action.includes('created')) return 'action-create';
    if (action.includes('updated')) return 'action-update';
    if (action.includes('deleted')) return 'action-delete';
    if (action.includes('viewed')) return 'action-view';
    if (action.includes('shared')) return 'action-share';
    return 'action-default';
};

const formatAction = (action) => {
    return action
        .replace(/_/g, ' ')
        .replace(/\b\w/g, l => l.toUpperCase());
};

const formatModelType = (type) => {
    if (!type) return '-';
    return type.split('\\').pop();
};

const formatDate = (date) => {
    return new Date(date).toLocaleString();
};

const showDetails = (log) => {
    const metadata = log.metadata || {};
    let message = 'Audit Log Details:\n\n';

    message += `User: ${log.user?.name || 'Unknown'} (${log.user?.email || 'N/A'})\n`;
    message += `Action: ${formatAction(log.action)}\n`;
    message += `Resource: ${formatModelType(log.model_type)}`;
    if (log.model_id) message += ` #${log.model_id}`;
    message += `\nIP Address: ${log.ip_address}\n`;
    message += `Date: ${formatDate(log.created_at)}\n`;
    message += `User Agent: ${log.user_agent || 'N/A'}\n\n`;

    if (Object.keys(metadata).length > 0) {
        message += 'Metadata:\n';
        message += JSON.stringify(metadata, null, 2);
    } else {
        message += 'No additional metadata available.';
    }

    alert(message);
};
</script>

<style scoped>
.audit-log-table {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.table-header h2 {
    margin: 0;
    color: #2c3e50;
}

.filters {
    display: flex;
    gap: 1rem;
}

.filter-select {
    padding: 0.5rem 1rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.table-container {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #f8f9fa;
    padding: 0.75rem;
    text-align: left;
    font-weight: 600;
    color: #666;
    border-bottom: 2px solid #e0e0e0;
}

td {
    padding: 0.75rem;
    border-bottom: 1px solid #f0f0f0;
}

.action-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.user-cell {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.user-name {
    font-weight: 600;
    color: #c2410c;
}

.user-email {
    font-size: 0.75rem;
    color: #999;
}

.action-create {
    background: #fff7ed;
    color: #c2410c;
    border: 1px solid #fdba74;
}

.action-update {
    background: #d1ecf1;
    color: #0c5460;
}

.action-delete {
    background: #f8d7da;
    color: #721c24;
}

.action-view {
    background: #e2e3e5;
    color: #383d41;
}

.action-share {
    background: #fff3cd;
    color: #856404;
}

.action-default {
    background: #f0f0f0;
    color: #666;
}

.resource-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.resource-type {
    font-weight: 600;
}

.resource-id {
    font-size: 0.75rem;
    color: #999;
}

.details-btn {
    padding: 0.25rem 0.75rem;
    background: #3498db;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.875rem;
}

.details-btn:hover {
    background: #2980b9;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 1.5rem;
}

.page-info {
    color: #666;
}

.loading, .empty-state {
    text-align: center;
    padding: 3rem;
    color: #666;
}
</style>
