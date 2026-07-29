<template>
    <header class="navbar">
        <div class="navbar-content">
            <div class="navbar-left">
                <h1>{{ title }}</h1>
            </div>
            <div class="navbar-right">
                <NotificationBadge />
                <span class="user-name">{{ $page.props.auth?.user?.name }}</span>
                <Link href="/logout" method="post" as="button" class="btn btn-secondary">
                    Logout
                </Link>
            </div>
        </div>
    </header>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import NotificationBadge from './NotificationBadge.vue';

defineProps({
    title: {
        type: String,
        default: ''
    }
});
</script>

<style scoped>
.navbar {
    background: white;
    border-bottom: 1px solid #ffe0b3;
    padding: 1rem 2rem;
    position: sticky;
    top: 0;
    z-index: 100;
}

.navbar-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.navbar-left h1 {
    margin: 0;
    font-size: 1.5rem;
    color: #c2410c;
}

.navbar-right {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-name {
    font-weight: 600;
    color: #7c2d12;
}
</style>
