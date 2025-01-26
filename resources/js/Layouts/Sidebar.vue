<script setup>
import { Link } from '@inertiajs/vue3';

</script>

<template>
    <aside class="w-64 h-screen bg-gray-800 text-white">
        <div class="p-4 text-center font-bold text-xl border-b border-gray-700">
            Admin Panel
        </div>
        <nav>
            <ul>
                <li class="p-4 border-b border-gray-700 hover:bg-gray-700">
                    <Link :href="route('dashboard')" class="flex items-center">
                        <span class="material-icons">dashboard</span>
                        <span class="ml-2">Dashboard</span>
                    </Link>
                </li>
            </ul>
        </nav>
    </aside>
</template>
