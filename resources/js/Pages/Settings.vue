<template>
    <AuthenticatedLayout title="Settings">
        <div class="settings-page">
            <div class="settings-section card">
                <h2>Profile Settings</h2>
                <form @submit.prevent="updateProfile">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input
                            id="name"
                            v-model="profileForm.name"
                            type="text"
                            required
                        />
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            id="email"
                            v-model="profileForm.email"
                            type="email"
                            required
                        />
                    </div>

                    <button type="submit" class="btn" :disabled="updatingProfile">
                        {{ updatingProfile ? 'Updating...' : 'Update Profile' }}
                    </button>
                </form>
            </div>

            <div class="settings-section card">
                <h2>Change Password</h2>
                <form @submit.prevent="changePassword">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input
                            id="current_password"
                            v-model="passwordForm.current_password"
                            type="password"
                            required
                        />
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input
                            id="new_password"
                            v-model="passwordForm.new_password"
                            type="password"
                            required
                        />
                    </div>

                    <div class="form-group">
                        <label for="new_password_confirmation">Confirm New Password</label>
                        <input
                            id="new_password_confirmation"
                            v-model="passwordForm.new_password_confirmation"
                            type="password"
                            required
                        />
                    </div>

                    <button type="submit" class="btn" :disabled="updatingPassword">
                        {{ updatingPassword ? 'Changing...' : 'Change Password' }}
                    </button>
                </form>
            </div>

            <div class="settings-section card">
                <h2>Security</h2>
                <div class="security-options">
                    <div class="security-item">
                        <SessionManager />
                    </div>

                    <div class="security-item">
                        <div>
                            <h3>Export Data</h3>
                            <p>Download all your passwords and data</p>
                            <div class="export-formats">
                                <label>Format:</label>
                                <select v-model="exportFormat">
                                    <option value="json">JSON</option>
                                    <option value="csv">CSV (Excel)</option>
                                    <option value="xml">XML</option>
                                    <option value="pdf">PDF Report</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-secondary" @click="exportData" :disabled="exporting">
                            {{ exporting ? 'Exporting...' : 'Export' }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="settings-section card">
                <h2>Notifications</h2>
                <div class="security-item">
                    <div>
                        <h3>Web Push Notifications</h3>
                        <p>Enable browser push notifications for password changes</p>
                    </div>
                    <label class="toggle">
                        <input type="checkbox" v-model="webPushEnabled" @change="persistWebPushSetting" />
                        <span>Enable</span>
                    </label>
                </div>
                <NotificationPanel />
            </div>

            <div class="settings-section card danger-zone">
                <div class="danger-actions">
                    <div>
                        <h3>Delete Account</h3>
                        <p>Permanently delete your account and all data</p>
                    </div>
                    <button class="btn-danger" @click="confirmDeleteAccount">
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { subscribeUser, unsubscribeUser, setPushEnabled } from '../push-notifications';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';
import SessionManager from '../Components/Security/SessionManager.vue';
import NotificationPanel from '../Components/Notifications/NotificationPanel.vue';

const props = defineProps({
    user: Object
});

const profileForm = reactive({
    name: props.user?.name || '',
    email: props.user?.email || ''
});

const passwordForm = reactive({
    current_password: '',
    new_password: '',
    new_password_confirmation: ''
});

const updatingProfile = ref(false);
const updatingPassword = ref(false);
const exportFormat = ref('json');
const exporting = ref(false);
const webPushEnabled = ref(!!props.user?.web_push_enabled);

const updateProfile = async () => {
    updatingProfile.value = true;
    try {
        await axios.put('/api/profile', profileForm);
        alert('Profile updated successfully');
    } catch (error) {
        console.error('Failed to update profile:', error);
        alert('Failed to update profile');
    } finally {
        updatingProfile.value = false;
    }
};

const changePassword = async () => {
    if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
        alert('Passwords do not match');
        return;
    }

    updatingPassword.value = true;
    try {
        await axios.put('/api/password', passwordForm);
        alert('Password changed successfully');
        Object.keys(passwordForm).forEach(key => passwordForm[key] = '');
    } catch (error) {
        console.error('Failed to change password:', error);
        alert('Failed to change password');
    } finally {
        updatingPassword.value = false;
    }
};

const exportData = async () => {
    exporting.value = true;
    try {
        const response = await axios.get(`/api/export?format=${exportFormat.value}`, { responseType: 'blob' });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;

        const extensions = {
            json: 'json',
            csv: 'csv',
            xml: 'xml',
            pdf: 'pdf'
        };

        link.setAttribute('download', `mvault-export.${extensions[exportFormat.value]}`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        alert(`Exported successfully as ${exportFormat.value.toUpperCase()}`);
    } catch (error) {
        console.error('Failed to export data:', error);
        alert('Failed to export data');
    } finally {
        exporting.value = false;
    }
};

const persistWebPushSetting = async () => {
    try {
        if (webPushEnabled.value) {
            await subscribeUser();
        } else {
            await unsubscribeUser();
        }
        await setPushEnabled(webPushEnabled.value);
    } catch (e) {
        console.error('Failed to update push setting', e);
        alert('Failed to update push notification setting');
    }
};

onMounted(() => {
    // nothing extra for now; initial state from server props
});

const confirmDeleteAccount = () => {
    if (confirm('Are you absolutely sure? This action cannot be undone.')) {
        if (confirm('Type your password to confirm deletion')) {
            router.delete('/api/account');
        }
    }
};
</script>

<style scoped>
.settings-page {
    max-width: 900px;
}

.settings-section {
    margin-bottom: 2rem;
}

.settings-section.card {
    background: rgba(15, 23, 42, 0.9);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 1.75rem;
    padding: 1.75rem;
    box-shadow: 0 24px 70px rgba(0, 0, 0, 0.2);
}

.settings-section h2 {
    margin-bottom: 1.5rem;
    color: #f8fafc;
}

.form-group label {
    color: #cbd5e1;
}

.form-group input,
.form-group select {
    background: rgba(15, 23, 42, 0.72);
    color: #f8fafc;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.security-options {
    display: grid;
    gap: 1.5rem;
}

.security-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.2rem;
    background: rgba(15, 23, 42, 0.82);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 1.25rem;
}

.security-item h3,
.security-item p {
    color: #e2e8f0;
}

.export-formats {
    margin-top: 0.75rem;
}

.export-formats select {
    width: auto;
    min-width: 160px;
    background: rgba(15, 23, 42, 0.72);
    color: #f8fafc;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.toggle {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    color: #e2e8f0;
}

.toggle input {
    accent-color: #f59e0b;
}

.danger-zone {
    background: rgba(220, 38, 38, 0.12);
    border: 1px solid rgba(220, 38, 38, 0.25);
}

.danger-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.danger-actions h3 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
}

.danger-actions p {
    margin: 0;
    font-size: 0.875rem;
    color: #cbd5e1;
}

.btn-danger {
    background: #ef4444;
    color: #fff;
    border: none;
    padding: 0.95rem 1.5rem;
    border-radius: 999px;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn-danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 32px rgba(239, 68, 68, 0.18);
}
</style>
