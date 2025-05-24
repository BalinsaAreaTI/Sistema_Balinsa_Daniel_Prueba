<template>
    <div class="w-full bg-[#F7BE15] fixed top-0 z-[100000]">
        <div class="flex pr md:pr-8 pr-4 justify-between items-center h-[50px]">
            <div class="flex justify-center items-center w-[50px] h-full cursor-pointer hover:bg-yellow-400/100" @click="clickHamburger">
                <svg class="text-base dark:text-white" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 20 20"><path fill="currentColor" fill-rule="evenodd" d="M3 5a1 1 0 0 1 1-1h12a1 1 0 1 1 0 2H4a1 1 0 0 1-1-1m0 5a1 1 0 0 1 1-1h12a1 1 0 1 1 0 2H4a1 1 0 0 1-1-1m0 5a1 1 0 0 1 1-1h12a1 1 0 1 1 0 2H4a1 1 0 0 1-1-1" clip-rule="evenodd"/></svg>
            </div>
            <div class="flex pr-1 gap-6 justify-between items-center h-[50px]">
                <Link :href="route('profile.show')" class="flex items-center gap-2 cursor-pointer">
                    <h1 class="font-bold shadow-white shadow-2xl sm:flex hidden md:text-xl text-sm uppercase text-white leading-tight text">
                        {{ greeting }},  {{ $page.props.auth.user.name }}</h1>
                </Link>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        dataOpenSideBar: Boolean,
        clickHamburger: Function,
    }
}
</script>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const greeting = ref('');

const getGreeting = () => {
    const currentHour = new Date().getHours(); // Obtener la hora actual
    if (currentHour < 12) {
        return 'Buenos Días'; // Saludo para la mañana
    } else if (currentHour < 18) {
        return 'Buenas Tardes'; // Saludo para la tarde
    } else {
        return 'Buenas Noches'; // Saludo para la noche
    }
};

// Asignar el saludo al ref
greeting.value = getGreeting();

</script>

<style>
.text {
    font-family: verdana;
    font-size: 22px;
    font-weight: 700;
    color: #f5f5f5;
    text-shadow: 1px 1px 1px #919191,
        1px 2px 1px #919191,
        1px 3px 1px #919191,
        1px 4px 1px #919191,
    1px 6px 6px rgba(16,16,16,0.4),
    1px 8px 10px rgba(16,16,16,0.2),
    1px 10px 35px rgba(16,16,16,0.2);
}
</style>