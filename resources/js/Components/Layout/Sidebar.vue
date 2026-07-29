<template>
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>mVault <img src="https://thumbs.dreamstime.com/z/padlock-icon-isolated-orange-round-button-abstract-illustration-padlock-icon-orange-round-button-103972346.jpg" alt="" class="logo"></h2>
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
    background: white;
    color: #1f2933;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    overflow-y: auto;
    border-right: 1px solid #ffe0b3;
}
.sidebar-nav
{
    background: #f5f4f4;
}
.sidebar-header {
    padding: 1.5rem;
    border-bottom: 1px solid #ffe0b3;
    background: white;
    color: white;
}

.sidebar-header h2 {
    margin: 0;
    font-size: 2.5rem;
    color: #f97316;
}

.sidebar-nav {
    padding: 1rem 0;
}

.nav-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    color: #ff7300;
    text-decoration: none;
    transition: all 0.3s;
}

.nav-item:hover {
    background: #ffe0b3;
    color: #fd6100;
}

.nav-item.active {
    background: #ff6a00;
    color: #ff6f00;
    border-left: 3px solid #f9f8f7;
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
}
.links {
    font-size: 1.1rem;
    font-weight: 500;
    color: #000000;
}
</style>
