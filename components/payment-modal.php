<?php
/**
 * Payment Modal Component
 * Reusable modal for recording payments
 */
?>

<!-- Payment Modal -->
<div x-data="paymentModal()" x-show="open" x-cloak class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="close()"></div>

        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form @submit.prevent="submitPayment()">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                Record Payment
                            </h3>
                            <div class="mt-4 space-y-4">
                                <!-- Payment Date -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Date</label>
                                    <input type="date" x-model="formData.payment_date" required
                                           class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <!-- Amount -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 dark:text-gray-400 sm:text-sm" x-text="currencySymbol"></span>
                                        </div>
                                        <input type="number" step="0.01" x-model="formData.amount" required min="0.01"
                                               :max="outstandingBalance"
                                               @input="calculateRemaining()"
                                               class="block w-full pl-8 pr-12 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Outstanding: <span x-text="formatCurrency(outstandingBalance)"></span>
                                    </p>
                                    <p x-show="remainingBalance < 0" class="mt-1 text-xs text-orange-600 dark:text-orange-400">
                                        ⚠️ Overpayment: <span x-text="formatCurrency(Math.abs(remainingBalance))"></span>
                                    </p>
                                </div>

                                <!-- Payment Method -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Method</label>
                                    <select x-model="formData.payment_method" required
                                            class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                        <template x-for="method in paymentMethods" :key="method">
                                            <option :value="method" x-text="method"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Reference Number -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reference Number (Optional)</label>
                                    <input type="text" x-model="formData.reference_number"
                                           placeholder="Transaction ID, Check #, etc."
                                           class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <!-- Notes -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes (Optional)</label>
                                    <textarea x-model="formData.notes" rows="2"
                                              placeholder="Additional payment details..."
                                              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                                </div>

                                <!-- Error Message -->
                                <div x-show="error" class="rounded-md bg-red-50 dark:bg-red-900/20 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-red-800 dark:text-red-200" x-text="error"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" :disabled="loading"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!loading">Record Payment</span>
                        <span x-show="loading">Processing...</span>
                    </button>
                    <button type="button" @click="close()" :disabled="loading"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function paymentModal() {
    return {
        open: false,
        loading: false,
        error: '',
        invoiceId: null,
        outstandingBalance: 0,
        currencySymbol: '$',
        remainingBalance: 0,
        paymentMethods: ['Cash', 'Check', 'Bank Transfer', 'Credit Card', 'Debit Card', 'PayPal', 'Stripe', 'Other'],
        formData: {
            invoice_id: null,
            payment_date: new Date().toISOString().split('T')[0],
            amount: '',
            payment_method: 'Cash',
            reference_number: '',
            notes: ''
        },

        openModal(invoiceId, outstandingBalance, currencySymbol = '$') {
            this.invoiceId = invoiceId;
            this.outstandingBalance = parseFloat(outstandingBalance);
            this.currencySymbol = currencySymbol;
            this.formData.invoice_id = invoiceId;
            this.formData.amount = outstandingBalance;
            this.remainingBalance = 0;
            this.error = '';
            this.open = true;
        },

        close() {
            this.open = false;
            this.resetForm();
        },

        resetForm() {
            this.formData = {
                invoice_id: null,
                payment_date: new Date().toISOString().split('T')[0],
                amount: '',
                payment_method: 'Cash',
                reference_number: '',
                notes: ''
            };
            this.error = '';
        },

        calculateRemaining() {
            const amount = parseFloat(this.formData.amount) || 0;
            this.remainingBalance = this.outstandingBalance - amount;
        },

        formatCurrency(amount) {
            return this.currencySymbol + parseFloat(amount).toFixed(2);
        },

        async submitPayment() {
            this.loading = true;
            this.error = '';

            try {
                const formData = new FormData();
                for (const key in this.formData) {
                    formData.append(key, this.formData[key]);
                }

                const response = await fetch('<?= url('/payments/store') ?>', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Reload the page to show updated payment info
                    window.location.reload();
                } else {
                    this.error = data.message || 'Failed to record payment';
                    if (data.errors && data.errors.length > 0) {
                        this.error = data.errors.join(', ');
                    }
                }
            } catch (error) {
                this.error = 'An error occurred while recording the payment';
                console.error('Payment error:', error);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
