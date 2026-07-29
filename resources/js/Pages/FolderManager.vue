<template>
    <AuthenticatedLayout title="Folder Manager">
        <div class="folder-manager">
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

            <FolderList
                v-if="viewMode === 'list'"
                :folders="folderData.data || []"
                :loading="loading"
                @create="showCreateForm"
                @select="selectFolder"
                @edit="editFolder"
                @delete="confirmDelete"
            />

            <FolderCardView
                v-if="viewMode === 'card'"
                :folders="folderData.data || []"
                @create="showCreateForm"
                @select="selectFolder"
                @edit="editFolder"
                @delete="confirmDelete"
            />

            <!-- Pagination -->
            <Pagination
                v-if="folderData.total > 0"
                :pagination="folderData"
                :per-page="perPage"
                @page-change="handlePageChange"
                @per-page-change="handlePerPageChange"
            />

            <!-- Modal for create/edit form -->
            <div v-if="showForm" class="modal" @click.self="closeForm">
                <FolderForm
                    :folder="selectedFolder"
                    :errors="formErrors"
                    :submitting="submitting"
                    @submit="saveFolder"
                    @cancel="closeForm"
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
import FolderList from '../Components/Folders/FolderList.vue';
import FolderCardView from '../Components/Folders/FolderCardView.vue';
import FolderForm from '../Components/Folders/FolderForm.vue';
import Pagination from '../Components/Common/Pagination.vue';

const folderData = ref({ data: [], current_page: 1, last_page: 1, total: 0, from: 0, to: 0 });
const selectedFolder = ref(null);
const showForm = ref(false);
const loading = ref(false);
const submitting = ref(false);
const formErrors = ref({});
const viewMode = ref('list');
const currentPage = ref(1);
const perPage = ref(25);

const loadFolders = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/folders', {
            params: {
                page: currentPage.value,
                per_page: perPage.value
            }
        });
        folderData.value = response.data;
    } catch (error) {
        console.error('Failed to load folders:', error);
    } finally {
        loading.value = false;
    }
};

const handlePageChange = (page) => {
    currentPage.value = page;
    loadFolders();
};

const handlePerPageChange = (value) => {
    perPage.value = value;
    currentPage.value = 1;
    loadFolders();
};

const showCreateForm = () => {
    selectedFolder.value = null;
    formErrors.value = {};
    showForm.value = true;
};

const editFolder = (folder) => {
    selectedFolder.value = folder;
    formErrors.value = {};
    showForm.value = true;
};

const selectFolder = (folder) => {
    router.visit(`/passwords?folder_id=${folder.id}`);
};

const saveFolder = async (data) => {
    submitting.value = true;
    formErrors.value = {};

    try {
        if (selectedFolder.value) {
            await axios.put(`/api/folders/${selectedFolder.value.id}`, data);
        } else {
            await axios.post('/api/folders', data);
        }
        await loadFolders();
        closeForm();
    } catch (error) {
        console.error('Error saving folder:', error);
        if (error.response?.data?.errors) {
            formErrors.value = error.response.data.errors;
        } else if (error.response?.data?.message) {
            alert('Error: ' + error.response.data.message);
        } else {
            alert('Failed to save folder. Please try again.');
        }
    } finally {
        submitting.value = false;
    }
};

const confirmDelete = async (folder) => {
    if (folder.passwords_count > 0) {
        alert('Cannot delete folder with passwords. Please move or delete passwords first.');
        return;
    }

    if (confirm(`Are you sure you want to delete "${folder.name}"?`)) {
        try {
            await axios.delete(`/api/folders/${folder.id}`);
            await loadFolders();
        } catch (error) {
            console.error('Failed to delete folder:', error);
        }
    }
};

const closeForm = () => {
    showForm.value = false;
    selectedFolder.value = null;
    formErrors.value = {};
};

onMounted(() => {
    loadFolders();
});
</script>

<style scoped>
.folder-manager {
    max-width: 1200px;
}

.view-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.view-toggle {
    display: flex;
    gap: 0.5rem;
}

.toggle-btn {
    padding: 0.5rem 1rem;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.2s;
}

.toggle-btn:hover {
    background: #f5f5f5;
    border-color: #ff7a00;
}

.toggle-btn.active {
    background: #ff7a00;
    color: white;
    border-color: #ff7a00;
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
