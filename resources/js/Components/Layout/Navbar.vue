<template>
    <header class="navbar">
        <div class="navbar-content">
            <div class="navbar-left">
                <h1>{{ title }}</h1>
            </div>
            <div class="navbar-right">
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

defineProps({
    title: {
        type: String,
        default: ''
    }
});
</script>

<style scoped>
.navbar {
    background: rgba(15, 23, 42, 0.96);
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    padding: 1rem 2rem;
    position: sticky;
    top: 0;
    z-index: 100;
    backdrop-filter: blur(14px);
}

.navbar-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.navbar-left h1 {
    margin: 0;
    font-size: 1.5rem;
    color: #f59e0b;
}

.navbar-right {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-name {
    font-weight: 600;
    color: #e2e8f0;
}
</style>
