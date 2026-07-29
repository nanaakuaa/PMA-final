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
    background: linear-gradient(180deg, rgba(6, 10, 20, 0.95), rgba(10, 14, 28, 0.98));
}

.main-content {
    margin-left: 250px;
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.content {
    flex: 1;
    padding: 2.5rem;
    background: rgba(15, 23, 42, 0.92);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 30px 90px rgba(0, 0, 0, 0.28);
    backdrop-filter: blur(18px);
    border-radius: 2rem;
}
</style>
