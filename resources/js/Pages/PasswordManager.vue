<template>
    <AuthenticatedLayout title="Password Manager">
        <div class="password-manager">
            <div class="view-controls">
                <div class="view-toggle">
                    <button
                        @click="viewMode = 'list'"
                        :class="['toggle-btn', { active: viewMode === 'list' }]"
                        title="List View"
                    >
                        ☰ List
                    </button>
                    <button
                        @click="viewMode = 'card'"
                        :class="['toggle-btn', { active: viewMode === 'card' }]"
                        title="Card View"
                    >
                        ⊞ Cards
                    </button>
                </div>
            </div>

            <PasswordList
                v-if="viewMode === 'list'"
                :passwords="passwordData.data || []"
                :loading="loading"
                @create="showCreateForm"
                @select="selectPassword"
                @edit="editPassword"
                @delete="confirmDelete"
            />

            <PasswordCardView
                v-if="viewMode === 'card'"
                :passwords="passwordData.data || []"
                @create="showCreateForm"
                @select="selectPassword"
                @edit="editPassword"
                @delete="confirmDelete"
            />

            <!-- Pagination -->
            <Pagination
                v-if="passwordData.total > 0"
                :pagination="passwordData"
                :per-page="perPage"
                @page-change="handlePageChange"
                @per-page-change="handlePerPageChange"
            />

            <!-- Modal for create/edit form -->
            <div v-if="showForm" class="modal" @click.self="closeForm">
                <PasswordForm
                    ref="passwordFormRef"
                    :password="selectedPassword"
                    :folders="folders"
                    :errors="formErrors"
                    :submitting="submitting"
                    @submit="savePassword"
                    @cancel="closeForm"
                    @generate="showGenerator"
                />
            </div>

            <!-- Modal for password details -->
            <div v-if="showDetails" class="modal" @click.self="closeDetails">
                <PasswordDetails
                    :password="selectedPassword"
                    @edit="editPassword"
                    @delete="confirmDelete"
                    @close="closeDetails"
                />
            </div>

            <!-- Modal for password generator -->
            <div v-if="showGeneratorModal" class="modal" @click.self="closeGenerator">
                <PasswordGenerator
                    @use="useGeneratedPassword"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';
import PasswordList from '../Components/Passwords/PasswordList.vue';
import PasswordCardView from '../Components/Passwords/PasswordCardView.vue';
import PasswordForm from '../Components/Passwords/PasswordForm.vue';
import PasswordDetails from '../Components/Passwords/PasswordDetails.vue';
import PasswordGenerator from '../Components/Generator/PasswordGenerator.vue';
import Pagination from '../Components/Common/Pagination.vue';

const passwordData = ref({ data: [], current_page: 1, last_page: 1, total: 0, from: 0, to: 0 });
const folders = ref([]);
const departments = ref([]);
const selectedPassword = ref(null);
const showForm = ref(false);
const showDetails = ref(false);
const showGeneratorModal = ref(false);
const loading = ref(false);
const submitting = ref(false);
const formErrors = ref({});
const viewMode = ref('list');
const passwordFormRef = ref(null);
const currentPage = ref(1);
const perPage = ref(25);

const loadPasswords = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/passwords', {
            params: {
                page: currentPage.value,
                per_page: perPage.value
            }
        });
        passwordData.value = response.data;
    } catch (error) {
        console.error('Failed to load passwords:', error);
    } finally {
        loading.value = false;
    }
};

const loadFolders = async () => {
    try {
        const response = await axios.get('/api/folders');
        folders.value = response.data.data || response.data;
    } catch (error) {
        console.error('Failed to load folders:', error);
    }
};

const loadDepartments = async () => {
    try {
        const response = await axios.get('/api/departments');
        departments.value = response.data;
    } catch (error) {
        console.error('Failed to load departments:', error);
    }
};

const handlePageChange = (page) => {
    currentPage.value = page;
    loadPasswords();
};

const handlePerPageChange = (value) => {
    perPage.value = value;
    currentPage.value = 1;
    loadPasswords();
};

const showCreateForm = () => {
    selectedPassword.value = null;
    formErrors.value = {};
    showForm.value = true;
};

const editPassword = (password) => {
    selectedPassword.value = password;
    formErrors.value = {};
    showDetails.value = false;

    // Fetch full password details including decrypted password
    axios.get(`/api/passwords/${password.id}`)
        .then(response => {
            selectedPassword.value = response.data;
            showForm.value = true;
            // Set the password in the form after it opens
            setTimeout(() => {
                if (passwordFormRef.value?.setPassword) {
                    passwordFormRef.value.setPassword(response.data.password);
                }
            }, 0);
        })
        .catch(error => {
            console.error('Failed to fetch password:', error);
            alert('Failed to load password details');
        });
};

const selectPassword = (password) => {
    selectedPassword.value = password;
    showDetails.value = true;
};

const savePassword = async (data) => {
    submitting.value = true;
    formErrors.value = {};

    try {
        if (selectedPassword.value) {
            await axios.put(`/api/passwords/${selectedPassword.value.id}`, data);
        } else {
            await axios.post('/api/passwords', data);
        }
        await loadPasswords();
        closeForm();
    } catch (error) {
        console.error('Error saving password:', error);
        if (error.response?.data?.errors) {
            formErrors.value = error.response.data.errors;
        } else if (error.response?.data?.message) {
            alert('Error: ' + error.response.data.message);
        } else {
            alert('Failed to save password. Please try again.');
        }
    } finally {
        submitting.value = false;
    }
};

const confirmDelete = async (password) => {
    if (confirm(`Are you sure you want to delete "${password.title}"?`)) {
        try {
            await axios.delete(`/api/passwords/${password.id}`);
            closeDetails();
            await loadPasswords();
        } catch (error) {
            console.error('Failed to delete password:', error);
        }
    }
};

const closeForm = () => {
    showForm.value = false;
    selectedPassword.value = null;
    formErrors.value = {};
};

const closeDetails = () => {
    showDetails.value = false;
    selectedPassword.value = null;
};

const showGenerator = () => {
    showGeneratorModal.value = true;
};

const closeGenerator = () => {
    showGeneratorModal.value = false;
};

const useGeneratedPassword = (password) => {
    if (passwordFormRef.value) {
        passwordFormRef.value.setPassword(password);
    }
    closeGenerator();
};

onMounted(() => {
    loadPasswords();
    loadFolders();
    loadDepartments();
});
</script>

<style scoped>
.password-manager {
    max-width: 1400px;
}

.view-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding: 1.25rem 1.5rem;
    background: rgba(15, 23, 42, 0.88);
    border-radius: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.view-toggle {
    display: flex;
    gap: 0.75rem;
}

.toggle-btn {
    padding: 0.75rem 1.15rem;
    border: 1px solid rgba(255, 255, 255, 0.12);
    background: rgba(255, 255, 255, 0.05);
    color: #e2e8f0;
    border-radius: 999px;
    cursor: pointer;
    font-size: 0.95rem;
    transition: all 0.2s ease;
}

.toggle-btn:hover {
    background: rgba(255, 255, 255, 0.12);
}

.toggle-btn.active {
    background: rgba(249, 115, 22, 0.18);
    color: #fbbf24;
    border-color: rgba(249, 115, 22, 0.4);
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 2rem;
}
</style>
