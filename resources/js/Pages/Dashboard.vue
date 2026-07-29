<template>
    <AuthenticatedLayout title="Dashboard" >
        <div class="dashboard">
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Passwords</h3>
                    <p class="stat-number">{{ stats.total_passwords }}</p>
                </div>
                <div class="stat-card">
                    <h3>Total Folders</h3>
                    <p class="stat-number">{{ stats.total_folders }}</p>
                </div>
                <div class="stat-card">
                    <h3>Team Members</h3>
                    <p class="stat-number">{{ stats.total_employees }}</p>
                </div>
            </div>

            <div class="card mt-4">
                <h2 class="mb-3">Recent Company Passwords</h2>
                <div v-if="recentPasswords.length > 0">
                    <div
                        v-for="password in recentPasswords"
                        :key="password.id"
                        class="password-item"
                    >
                        <div class="password-info">
                            <strong>{{ password.title }}</strong>
                            <p class="text-muted">{{ password.username }}</p>
                            <p v-if="password.creator" class="creator-info">
                                Created by {{ password.creator.name }}
                            </p>
                        </div>
                        <Link :href="`/passwords`" class="btn btn-secondary">
                            View All
                        </Link>
                    </div>
                </div>
                <p v-else>No passwords yet. Create your first password!</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
    recentPasswords: Array,
});
</script>

<style scoped>
.dashboard {
    max-width: 1200px;

}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-card h3 {
    margin: 0 0 0.5rem 0;
    color: #666;
    font-size: 0.875rem;
    text-transform: uppercase;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #2b3743;
    margin: 0;
}

.password-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #fff7ed;
    margin-bottom: 0.5rem;
    border-radius: 4px;
    border: 1px solid #ffe0b3;
}

.password-info {
    flex: 1;
}

.text-muted {
    color: #666;
    font-size: 0.875rem;
    margin: 0.25rem 0 0 0;
}

.creator-info {
    color: #f97316;
    font-size: 0.75rem;
    font-weight: 600;
    margin: 0.25rem 0 0 0;
}
</style>
