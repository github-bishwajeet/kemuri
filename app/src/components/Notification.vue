<template>
    <div>
        <div v-if="success" class="w-full flex text-gray-900 font-light mb-4 p-4 rounded-lg border bg-green-50 border-green-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-4" viewBox="0 0 20 20" fill="green">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <label class="w-full" v-html="message"></label>
            <svg @click="close('success')" style="cursor:pointer" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </div>
        <div v-if="error" class="w-full flex text-gray-900 font-light mb-4 p-4 rounded-lg border bg-red-50 border-red-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-4" viewBox="0 0 20 20" fill="red">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>            
            <label class="w-full" v-html="message"></label>
            <svg @click="close('error')" style="cursor:pointer" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </div>
    </div>
</template>
 <script>
export default{
    props : ['message', 'success', 'error'],
    methods : {
        close(type){
            this.$emit('close', type);
        }
    }
}
 </script>
