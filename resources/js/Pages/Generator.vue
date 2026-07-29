<template>
    <AuthenticatedLayout title="Password Generator">
        <div class="generator-page">
            <PasswordGenerator @use="usePassword" />
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';
import PasswordGenerator from '../Components/Generator/PasswordGenerator.vue';

const usePassword = (password) => {
    // Copy to clipboard
    navigator.clipboard.writeText(password).then(() => {
        alert('Password copied to clipboard!');
    });
};
</script>

<style scoped>
.generator-page {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}
</style>
