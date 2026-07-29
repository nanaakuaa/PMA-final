<template>
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>My Vault <img src="https://thumbs.dreamstime.com/z/padlock-icon-isolated-orange-round-button-abstract-illustration-padlock-icon-orange-round-button-103972346.jpg" alt="" class="logo"></h2>
        </div>
        <nav class="sidebar-nav">
            <Link href="/dashboard" class="nav-item" :class="{ active: $page.url === '/dashboard' }">
                <span class="links">Dashboard</span>
            </Link>
            <Link href="/passwords" class="nav-item" :class="{ active: $page.url.startsWith('/passwords') }">

                <span class="links">Passwords</span>
            </Link>
            <Link href="/folders" class="nav-item" :class="{ active: $page.url.startsWith('/folders') }">

                <span class="links">Folders</span>
            </Link>
            <Link href="/generator" class="nav-item" :class="{ active: $page.url === '/generator' }">

                <span class="links">Generator</span>
            </Link>
            <Link v-if="$page.props.auth?.user?.is_admin" href="/audit" class="nav-item" :class="{ active: $page.url === '/audit' }">

                <span class="links">Audit Log</span>
            </Link>
            <Link v-if="$page.props.auth?.user?.is_admin" href="/admin/users" class="nav-item" :class="{ active: $page.url.startsWith('/admin') }">

                <span class="links">Manage Users</span>
            </Link>
            <Link href="/settings" class="nav-item" :class="{ active: $page.url === '/settings' }">

                <span class="links">Settings</span>
            </Link>
        </nav>
    </aside>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
.sidebar {
    width: 250px;
    background: rgba(8, 15, 35, 0.98);
    color: #cbd5e1;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    overflow-y: auto;
    border-right: 1px solid rgba(255, 255, 255, 0.08);
}
.sidebar-nav {
    padding: 1rem 0;
}
.sidebar-header {
    padding: 1.75rem 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    background: rgba(15, 23, 42, 0.96);
}

.sidebar-header h2 {
    margin: 0;
    font-size: 1.9rem;
    color: #fbbf24;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.nav-item {
    display: flex;
    align-items: center;
    padding: 0.9rem 1.5rem;
    color: #cbd5e1;
    text-decoration: none;
    transition: all 0.2s ease;
}

.nav-item:hover {
    background: rgba(255, 255, 255, 0.06);
    color: #f8fafc;
}

.nav-item.active {
    background: rgba(249, 115, 22, 0.18);
    color: #fbbf24;
    border-left: 3px solid #f59e0b;
    font-weight: 600;
}

.nav-item .icon {
    margin-right: 0.75rem;
    font-size: 1.2rem;
}
.logo {
    width: 30px;
    height: 30px;
    vertical-align: middle;
    margin-left: 10px;
    border-radius: 999px;
}
.links {
    font-size: 1.05rem;
    font-weight: 500;
    color: inherit;
}
</style>
