<template>
    <div class="bg-gray-100">
        <header class="p-4 bg-black">
          <div class="flex text-white font-bold text-lg"> 
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 mt-1" viewBox="0 0 20 20" fill="currentColor">
                <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
                <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" />
            </svg>
            Stock Analysis
          </div>
        </header>
        <div id="app">
          <CSV></CSV>
        </div>
    </div>
</template>

<script>
import CSV from './components/CSV.vue'

export default {
  name: 'App',
  components: {
    CSV
  }
}
</script>
