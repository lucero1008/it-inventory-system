<script setup>
import Navbar from './Components/Navbar.vue';

</script>

<template>
    <Navbar/>

    <div class="main-bg">
        <div class="main-section">
            {{ $page.props.csrf_token }}
        </div>
    </div>
</template>
