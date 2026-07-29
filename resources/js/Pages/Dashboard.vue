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
            </div>

            <div class="card mt-4">
                <h2 class="mb-3">Recent Personal Passwords</h2>
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
    background: rgba(15, 23, 42, 0.88);
    padding: 1.5rem;
    border-radius: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 22px 50px rgba(0, 0, 0, 0.18);
}

.stat-card h3 {
    margin: 0 0 0.75rem 0;
    color: #94a3b8;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #fde047;
    margin: 0;
}

.card {
    margin-top: 1.5rem;
}

.password-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem;
    background: rgba(15, 23, 42, 0.72);
    margin-bottom: 0.75rem;
    border-radius: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.password-info {
    flex: 1;
}

.password-info strong {
    color: #f8fafc;
    font-size: 1rem;
}

.text-muted {
    color: #94a3b8;
    font-size: 0.9rem;
    margin: 0.35rem 0 0 0;
}

.creator-info {
    color: #cbd5e1;
    font-size: 0.78rem;
    font-weight: 600;
    margin: 0.35rem 0 0 0;
}
</style>
