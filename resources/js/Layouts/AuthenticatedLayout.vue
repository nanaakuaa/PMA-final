<template>
    <div class="app-layout">
        <Sidebar />
        <div class="main-content">
            <Navbar :title="title" />
            <main class="content">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import Sidebar from '../Components/Layout/Sidebar.vue';
import Navbar from '../Components/Layout/Navbar.vue';

defineProps({
    title: {
        type: String,
        default: ''
    }
});
</script>

<style scoped>
.app-layout {
    display: flex;
    min-height: 100vh;
}

.main-content {
    margin-left: 250px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.content {
    flex: 1;
    padding: 2rem;
    background: #fff7ed;
}
</style>
