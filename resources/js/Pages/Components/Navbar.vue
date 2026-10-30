<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { UserIcon, Bars3Icon, SunIcon, MoonIcon } from '@heroicons/vue/24/solid'
import Sidebar from './Sidebar.vue';

const page = usePage();
const isDarkMode = ref(false);

onMounted(() => {
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        isDarkMode.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDarkMode.value = false;
        document.documentElement.classList.remove('dark');
    }
});

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;

    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

const displayTitle = computed(() => {
    return page.props.pageTitle || 'IT Inventory Management';
});

const isSidebarOpen = ref(false);
const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

</script>
<template>
    <nav class="navbar">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
            <div class="flex items-center justify-start rtl:justify-end">
                <button type="button" @click="toggleSidebar" class="lg:hidden text-heading bg-transparent box-border border border-transparent hover:bg-neutral-secondary-medium focus:ring-4 focus:ring-neutral-tertiary font-medium leading-5 rounded-base text-sm p-2 focus:outline-none">
                    <span class="sr-only">Open sidebar</span>
                    <Bars3Icon class="w-6 h-6" aria-hidden="true"/>
                </button>
                <div class="flex ms-2 md:me-24">
                  <span class="navbar-title hidden sm:block">IT Inventory Management</span>
                  <span class="navbar-title sm:hidden">{{ displayTitle }}</span>
                </div>
            </div>
                <div class="flex items-center">
                    <div>
                        <button @click="toggleDarkMode" class="navbar-toggle-dark-light-mode">
                            <span class="sr-only">{{ isDarkMode ? 'Dark' : 'Light' }}</span>
                            <SunIcon v-if="isDarkMode" class="navbar-light-mode-btn"/>
                            <MoonIcon v-else class="navbar-dark-mode-btn"/>
                        </button>
                    </div>
                    <div class="flex flex-row-reverse items-center ms-3">
                        <button type="button" class="navbar-user-menu" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                            <span class="sr-only">Open user menu</span>
                            <UserIcon class="w-8 h-8 py-2 text-gray-500 bg-gray-300 rounded-full"/>
                        </button>
                        <div class="z-50 hidden navbar-user-menu-dropdown" id="dropdown-user">
                            <div class="px-4 py-3 border-b border-default-medium" role="none">
                                <p class="navbar-current-user" role="none">
                                    {{ $page.props.auth.user.name }}
                                </p>
                            </div>
                            <ul class="p-2 text-[12px] text-body font-medium" role="none">
                                <li>
                                    <Link href="#" class="navbar-user-menu-dropdown-list" role="menuitem">Settings</Link>
                                </li>
                                <li>
                                    <Link as="button" href="/logout" method="post" class="navbar-user-menu-dropdown-list" role="menuitem">Sign Out</Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <Sidebar :isOpen="isSidebarOpen" @close="isSidebarOpen = false" />
</template>
