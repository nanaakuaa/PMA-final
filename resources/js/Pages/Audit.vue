<template>
    <AuthenticatedLayout title="Audit Log">
        <div class="audit-page">
            <AuditLogTable
                :logs="auditData.data || []"
                :loading="loading"
            />

            <!-- Pagination -->
            <Pagination
                v-if="auditData.total > 0"
                :pagination="auditData"
                :per-page="perPage"
                @page-change="handlePageChange"
                @per-page-change="handlePerPageChange"
            />
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';
import AuditLogTable from '../Components/Audit/AuditLogTable.vue';
import Pagination from '../Components/Common/Pagination.vue';

const auditData = ref({ data: [], current_page: 1, last_page: 1, total: 0, from: 0, to: 0 });
const loading = ref(false);
const currentPage = ref(1);
const perPage = ref(50);

const loadLogs = async () => {
    loading.value = true;
    try {
        console.log('Loading logs - page:', currentPage.value, 'perPage:', perPage.value);
        const response = await axios.get('/api/audit-logs', {
            params: {
                page: currentPage.value,
                per_page: perPage.value
            }
        });
        console.log('Logs loaded:', response.data);
        auditData.value = response.data;
    } catch (error) {
        console.error('Failed to load audit logs:', error);
        alert('Failed to load audit logs: ' + (error.response?.data?.message || error.message));
    } finally {
        loading.value = false;
    }
};

const handlePageChange = (page) => {
    console.log('Page changed to:', page);
    currentPage.value = page;
    loadLogs();
};

const handlePerPageChange = (value) => {
    console.log('Per page changed to:', value);
    perPage.value = value;
    currentPage.value = 1;
    loadLogs();
};

onMounted(() => {
    loadLogs();
});
</script>

<style scoped>
.audit-page {
    max-width: 1400px;
}
</style>
