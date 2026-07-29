<template>
    <AuthenticatedLayout title="User Management">
        <div class="user-management">
            <div class="header">
                <h1>Manage Users</h1>
                <button @click="showCreateModal = true" class="btn">+ Add New User</button>
            </div>

            <div class="users-table">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in userData.data" :key="user.id">
                            <td>{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td>
                                <span v-if="user.department" class="badge badge-department">
                                    {{ user.department.name }}
                                </span>
                                <span v-else class="text-gray">—</span>
                            </td>
                            <td>
                                <span class="badge" :class="user.is_admin ? 'badge-admin' : 'badge-user'">
                                    {{ user.is_admin ? 'Admin' : 'User' }}
                                </span>
                            </td>
                            <td>{{ formatDate(user.created_at) }}</td>
                            <td>
                                <button @click="editUser(user)" class="btn-secondary btn-sm">Edit</button>
                                <button @click="deleteUser(user)" class="btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <Pagination
                v-if="userData.total > 0"
                :pagination="userData"
                :per-page="perPage"
                @page-change="handlePageChange"
                @per-page-change="handlePerPageChange"
            />

            <!-- Create/Edit Modal -->
            <div v-if="showCreateModal || editingUser" class="modal" @click.self="closeModal">
                <div class="modal-content">
                    <h2>{{ editingUser ? 'Edit User' : 'Create New User' }}</h2>
                    <form @submit.prevent="submitForm">
                        <div class="form-group">
                            <label>Name *</label>
                            <input v-model="form.name" type="text" required />
                        </div>

                        <div class="form-group">
                            <label>Email *</label>
                            <input v-model="form.email" type="email" required />
                        </div>

                        <div class="form-group">
                            <label>Password {{ editingUser ? '' : '*' }}</label>
                            <input v-model="form.password" type="password" :required="!editingUser" />
                        </div>

                        <div class="form-group">
                            <label>Confirm Password {{ editingUser ? '' : '*' }}</label>
                            <input v-model="form.password_confirmation" type="password" :required="!editingUser" />
                        </div>

                        <div class="form-group">
                            <label>Department</label>
                            <select v-model="form.department_id">
                                <option :value="null">No Department</option>
                                <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                                    {{ dept.name }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>
                                <input v-model="form.is_admin" type="checkbox" />
                                Admin User
                            </label>
                        </div>

                        <div class="form-actions">
                            <button type="button" @click="closeModal" class="btn-secondary">Cancel</button>
                            <button type="submit" class="btn" :disabled="submitting">
                                {{ submitting ? 'Saving...' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';
import Pagination from '../../Components/Common/Pagination.vue';

const props = defineProps({
    users: {
        type: Object,
        required: true
    },
    departments: {
        type: Array,
        default: () => []
    }
});

const userData = computed(() => props.users);
const departments = computed(() => props.departments);
const perPage = ref(25);

const showCreateModal = ref(false);
const editingUser = ref(null);
const submitting = ref(false);

const form = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    is_admin: false,
    department_id: null
});

const handlePageChange = (page) => {
    router.get('/admin/users', { page, per_page: perPage.value }, {
        preserveState: true,
        preserveScroll: true
    });
};

const handlePerPageChange = (value) => {
    perPage.value = value;
    router.get('/admin/users', { page: 1, per_page: value }, {
        preserveState: true,
        preserveScroll: true
    });
};

const resetForm = () => {
    form.name = '';
    form.email = '';
    form.password = '';
    form.password_confirmation = '';
    form.is_admin = false;
    form.department_id = null;
};

const editUser = (user) => {
    editingUser.value = user;
    form.name = user.name;
    form.email = user.email;
    form.password = '';
    form.password_confirmation = '';
    form.is_admin = user.is_admin;
    form.department_id = user.department_id;
};

const closeModal = () => {
    showCreateModal.value = false;
    editingUser.value = null;
    resetForm();
};

const submitForm = () => {
    submitting.value = true;

    if (editingUser.value) {
        router.put(`/admin/users/${editingUser.value.id}`, form, {
            onFinish: () => {
                submitting.value = false;
                closeModal();
            }
        });
    } else {
        router.post('/admin/users', form, {
            onFinish: () => {
                submitting.value = false;
                closeModal();
            }
        });
    }
};

const deleteUser = (user) => {
    if (confirm(`Are you sure you want to delete ${user.name}?`)) {
        router.delete(`/admin/users/${user.id}`);
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};
</script>

<style scoped>
.user-management {
    max-width: 1400px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.header h1 {
    margin: 0;
    color: #c2410c;
}

.users-table {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #fff7ed;
    padding: 0.75rem;
    text-align: left;
    font-weight: 600;
    color: #c2410c;
    border-bottom: 2px solid #fdba74;
}

td {
    padding: 0.75rem;
    border-bottom: 1px solid #ffe0b3;
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-admin {
    background: #fff7ed;
    color: #c2410c;
    border: 1px solid #fdba74;
}

.badge-user {
    background: #f0f0f0;
    color: #666;
}

.badge-department {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #7dd3fc;
}

.text-gray {
    color: #999;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    margin-right: 0.5rem;
}

.btn-danger {
    background-color: #dc2626;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.form-group label input[type="checkbox"] {
    display: inline-block;
    margin-right: 0.5rem;
    vertical-align: middle;
    width: auto;
    padding: 0;
    margin-bottom: 0;
}

.btn-danger:hover {
    background-color: #b91c1c;
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
}

.modal-content {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    max-width: 500px;
    width: 100%;
}

.modal-content h2 {
    margin-top: 0;
    color: #c2410c;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
    justify-content: flex-end;
}
</style>
