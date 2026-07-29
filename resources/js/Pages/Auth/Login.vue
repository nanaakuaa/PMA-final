<template>
    <div class="auth-shell">
        <div class="hero-panel">
            <div class="hero-icon">🔒</div>
            <div class="hero-text">
                <h1>Personal Passwords</h1>
                <p>Securely store and access your accounts from one private vault. Fast, polished, and built for you.</p>
            </div>
            <div class="badge-row">
                <div class="badge">Private</div>
                <div class="badge">Autofill</div>
                <div class="badge">Encrypted</div>
            </div>
        </div>

        <div class="auth-panel glass-card">
            <div class="panel-head">
                <div class="brand-mark"></div>
                <div>
                    <h2>My Vault</h2>
                    <p>Personal password manager</p>
                </div>
            </div>

            <p class="subtitle">Sign in to view your secure password vault</p>

            <form @submit.prevent="submit" class="auth-form">
                <div class="form-group">
                    <label>Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="Enter your email"
                    />
                    <span class="error" v-if="form.errors.email">
                        {{ form.errors.email }}
                    </span>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input
                        v-model="form.password"
                        type="password"
                        placeholder="Enter your password"
                    />
                    <span class="error" v-if="form.errors.password">
                        {{ form.errors.password }}
                    </span>
                </div>

                <div class="remember">
                    <label>
                        <input
                            type="checkbox"
                            v-model="form.remember"
                        />
                        Remember me
                    </label>
                </div>

                <button class="btn" :disabled="form.processing">
                    {{ form.processing ? 'Logging In...' : 'Login' }}
                </button>

                <p class="switch-text">
                    Don't have an account?
                    <Link href="/register">Create one</Link>
                </p>
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
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', Arial, Helvetica, sans-serif;
}

.auth-shell {
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1.2fr 0.9fr;
    gap: 2rem;
    padding: 3rem;
    background: radial-gradient(circle at top left, rgba(249, 115, 22, 0.15), transparent 25%),
        radial-gradient(circle at bottom right, rgba(255, 255, 255, 0.08), transparent 20%),
        linear-gradient(135deg, #04050f, #0f172a 90%);
}

.hero-panel,
.auth-panel {
    position: relative;
    overflow: hidden;
}

.hero-panel {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 3rem;
    border-radius: 2rem;
    background: rgba(15, 23, 42, 0.78);
    border: 1px solid rgba(255, 255, 255, 0.06);
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.42);
}

.hero-icon {
    width: 4.5rem;
    height: 4.5rem;
    border-radius: 1.5rem;
    display: grid;
    place-items: center;
    background: rgba(251, 191, 36, 0.18);
    color: #fbbf24;
    font-size: 2rem;
    margin-bottom: 2rem;
}

.hero-text h1 {
    font-size: clamp(2.75rem, 4vw, 4rem);
    line-height: 1.05;
    color: #fff;
    margin-bottom: 1rem;
}

.hero-text p {
    max-width: 38rem;
    color: #cbd5e1;
    font-size: 1rem;
    line-height: 1.85;
}

.badge-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 2.5rem;
}

.badge {
    padding: 0.85rem 1.25rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.08);
    color: #fff;
    font-size: 0.95rem;
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.auth-panel {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 2.5rem;
    border-radius: 2rem;
    background: rgba(7, 10, 23, 0.88);
    border: 1px solid rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(22px);
    box-shadow: 0 35px 90px rgba(0, 0, 0, 0.38);
}

.panel-head {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.75rem;
}

.brand-mark {
    width: 3.4rem;
    height: 3.4rem;
    border-radius: 1rem;
    background: linear-gradient(135deg, #fbbf24, #f97316);
    box-shadow: 0 14px 36px rgba(251, 191, 36, 0.22);
}

.panel-head h2 {
    margin: 0;
    font-size: 2rem;
    color: #fff;
}

.panel-head p {
    margin: 0.25rem 0 0;
    color: #94a3b8;
    font-size: 0.95rem;
}

.subtitle {
    color: #94a3b8;
    margin-bottom: 2rem;
    font-size: 0.96rem;
}

.auth-form {
    display: grid;
    gap: 1.25rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.75rem;
    color: #cbd5e1;
    font-size: 0.95rem;
}

.form-group input {
    width: 100%;
    padding: 1rem 1.1rem;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 1.2rem;
    background: rgba(15, 23, 42, 0.9);
    color: #fff;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-group input:focus {
    border-color: rgba(251, 191, 36, 0.9);
    box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.12);
}

.remember label {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    color: #cbd5e1;
    font-size: 0.95rem;
}

.remember input {
    width: auto;
    accent-color: #fbbf24;
}

.btn {
    width: 100%;
    padding: 1.1rem 1.25rem;
    border: none;
    border-radius: 999px;
    background: linear-gradient(135deg, #fbbf24, #f97316);
    color: #070b17;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 18px 35px rgba(251, 191, 36, 0.22);
}

.switch-text {
    color: #94a3b8;
    font-size: 0.95rem;
    text-align: center;
    margin-top: 0.5rem;
}

.switch-text a {
    color: #fbbf24;
    font-weight: 700;
    text-decoration: none;
}

.switch-text a:hover {
    text-decoration: underline;
}

@media (max-width: 1024px) {
    .auth-shell {
        grid-template-columns: 1fr;
        padding: 2rem;
    }

    .hero-panel,
    .auth-panel {
        padding: 2rem;
    }
}

@media (max-width: 720px) {
    .auth-shell {
        padding: 1.25rem;
    }

    .hero-icon {
        width: 3.2rem;
        height: 3.2rem;
    }

    .panel-head {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
