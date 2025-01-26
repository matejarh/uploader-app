<script setup>

</script>

<template>
    <nav class="bg-gray-900 text-white p-4 flex justify-between items-center">
  <button class="text-gray-400 hover:text-white focus:outline-none">
    <i class="fas fa-bars"></i>
  </button>
  <div class="flex items-center space-x-4">
    <span>Welcome, {{ $page.props.auth.user.name }}</span>
    <img :src="$page.props.auth.user.profile_photo_url" class="w-10 h-10 rounded-full" alt="User Avatar">
  </div>
</nav>
</template>
