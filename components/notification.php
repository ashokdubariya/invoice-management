<!-- Toast Notification Component -->
<div x-data="{ 
    show: <?= isset($flash['message']) && $flash['message'] ? 'true' : 'false' ?>,
    message: '<?= addslashes($flash['message'] ?? '') ?>',
    type: '<?= $flash['type'] ?? 'success' ?>'
}" 
     x-show="show" 
     x-init="if(show) { setTimeout(() => show = false, 5000) }"
     x-cloak
     class="fixed top-4 right-4 z-50 max-w-sm w-full">
    
    <div :class="{
        'bg-green-50 border-green-500 dark:bg-green-900 dark:border-green-700': type === 'success',
        'bg-red-50 border-red-500 dark:bg-red-900 dark:border-red-700': type === 'error',
        'bg-blue-50 border-blue-500 dark:bg-blue-900 dark:border-blue-700': type === 'info',
        'bg-yellow-50 border-yellow-500 dark:bg-yellow-900 dark:border-yellow-700': type === 'warning'
    }" 
         class="border-l-4 p-4 rounded-lg shadow-lg">
        
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <!-- Success Icon -->
                <svg x-show="type === 'success'" class="h-5 w-5 text-green-600 dark:text-green-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                
                <!-- Error Icon -->
                <svg x-show="type === 'error'" class="h-5 w-5 text-red-600 dark:text-red-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
            </div>
            
            <div class="ml-3 flex-1">
                <p :class="{
                    'text-green-800 dark:text-green-200': type === 'success',
                    'text-red-800 dark:text-red-200': type === 'error',
                    'text-blue-800 dark:text-blue-200': type === 'info',
                    'text-yellow-800 dark:text-yellow-200': type === 'warning'
                }" 
                   class="text-sm font-medium" x-text="message"></p>
            </div>
            
            <button @click="show = false" class="ml-3 flex-shrink-0">
                <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>
</div>
