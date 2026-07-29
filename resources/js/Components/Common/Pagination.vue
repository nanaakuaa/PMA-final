<template>
    <div v-if="pagination.total > 0" class="pagination-container">
        <div class="pagination-info">
            Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
        </div>

        <div class="pagination-controls">
            <button
                @click="goToPage(currentPage - 1)"
                :disabled="currentPage === 1"
                class="pagination-btn"
            >
                ← Previous
            </button>

            <div class="pagination-pages">
                <button
                    v-for="page in pages"
                    :key="page"
                    @click="goToPage(page)"
                    :class="['page-btn', { active: page === currentPage }]"
                >
                    {{ page }}
                </button>
            </div>

            <button
                @click="goToPage(currentPage + 1)"
                :disabled="currentPage === pagination.last_page"
                class="pagination-btn"
            >
                Next →
            </button>
        </div>

        <div class="pagination-per-page">
            <label for="per-page">Per page:</label>
            <select id="per-page" :value="perPage" @change="changePerPage">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    pagination: {
        type: Object,
        required: true
    },
    perPage: {
        type: Number,
        default: 25
    }
});

const emit = defineEmits(['page-change', 'per-page-change']);

const currentPage = computed(() => props.pagination.current_page);

const pages = computed(() => {
    const pages = [];
    const totalPages = props.pagination.last_page;
    const current = currentPage.value;
    const maxVisible = 5;

    if (totalPages <= maxVisible) {
        for (let i = 1; i <= totalPages; i++) {
            pages.push(i);
        }
    } else {
        pages.push(1);
        if (current > 3) pages.push('...');

        const start = Math.max(2, current - 1);
        const end = Math.min(totalPages - 1, current + 1);

        for (let i = start; i <= end; i++) {
            if (!pages.includes(i)) pages.push(i);
        }

        if (current < totalPages - 2) pages.push('...');
        pages.push(totalPages);
    }

    return pages;
});

const goToPage = (page) => {
    if (typeof page === 'number' && page >= 1 && page <= props.pagination.last_page) {
        emit('page-change', page);
    }
};

const changePerPage = (e) => {
    emit('per-page-change', parseInt(e.target.value));
};
</script>

<style scoped>
.pagination-container {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    padding: 1.5rem;
    background: white;
    border-radius: 8px;
    border-top: 1px solid #e0e0e0;
    margin-top: 1.5rem;
}

.pagination-info {
    font-size: 0.9rem;
    color: #666;
    text-align: center;
}

.pagination-controls {
    display: flex;
    gap: 1rem;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
}

.pagination-btn {
    padding: 0.5rem 1rem;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.2s;
}

.pagination-btn:hover:not(:disabled) {
    background: #ff7a00;
    border-color: #ff7a00;
    color: white;
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-pages {
    display: flex;
    gap: 0.5rem;
}

.page-btn {
    padding: 0.5rem 0.75rem;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.2s;
    min-width: 40px;
}

.page-btn:hover:not(.active) {
    background: #f5f5f5;
    border-color: #ff7a00;
}

.page-btn.active {
    background: #ff7a00;
    color: white;
    border-color: #ff7a00;
    font-weight: 600;
}

.page-btn:disabled {
    cursor: default;
}

.pagination-per-page {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.pagination-per-page select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9rem;
}

.pagination-per-page select:focus {
    outline: none;
    border-color: #ff7a00;
    box-shadow: 0 0 0 3px rgba(255, 122, 0, 0.1);
}
</style>
