<template>
    <div class="auth-container">
        <div class="auth-card">
            <h1>mVault <img src="https://thumbs.dreamstime.com/z/padlock-icon-isolated-orange-round-button-abstract-illustration-padlock-icon-orange-round-button-103972346.jpg" alt="" class="logo"></h1>
            <form @submit.prevent="submit">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        autofocus
                    />
                    <span v-if="form.errors.email" class="error">{{ form.errors.email }}</span>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                    />
                    <span v-if="form.errors.password" class="error">{{ form.errors.password }}</span>
                </div>

                <div class="form-group">
                    <label>
                        <input v-model="form.remember" type="checkbox" />
                        Remember me
                    </label>
                </div>

                <button type="submit" class="btn" :disabled="form.processing">
                    {{ form.processing ? 'Logging in...' : 'Login' }}
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login');
};
</script>

<style scoped>
.auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(circle at 20% 20%, #fff3e0, #fff7ed 40%, #ffffff 75%);
}

.auth-card {
    background: white;
    padding: 2.5rem;
    border-radius: 8px;
    box-shadow: 0 20px 60px rgba(249, 115, 22, 0.18);
    width: 100%;
    max-width: 500px;
    height: 600px;
    border: 1px solid #ffe0b3;
}

.auth-card h1 {
    text-align: center;
    margin-bottom: 2rem;
    color: #ff6200;
    font-size: 50px;
  font-weight: bold;
}
.btn {
    background: #ff7a00;
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    width: 100%;
    font-size: 1rem;
    transition: background 0.3s;
}
.error {
    color: #e74c3c;
    font-size: 0.875rem;
    display: block;
    margin-top: 0.25rem;
}

a {
    color: #f97316;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
.logo {
    width: 40px;
    height: 40px;
    vertical-align: middle;
    margin-left: 10px;
}

.form-group label input[type="checkbox"] {
    display: inline-block;
    margin-right: 0.5rem;
    vertical-align: middle;
    width: auto;
    padding: 0;
    margin-bottom: 0;
}

</style>
