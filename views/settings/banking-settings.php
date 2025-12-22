<?php require_once __DIR__ . '/../../components/header.php'; ?>

<div x-data="{ sidebarOpen: false, activeTab: 'usd' }" class="flex h-screen overflow-hidden">
    <?php require_once __DIR__ . '/../../components/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col overflow-hidden">
        <?php require_once __DIR__ . '/../../components/navbar.php'; ?>
        
        <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6">
            <div class="max-w-4xl mx-auto">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Banking Details</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configure banking information for different currencies to display on invoices</p>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <!-- Tabs -->
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="flex -mb-px">
                            <button @click="activeTab = 'usd'" 
                                    :class="activeTab === 'usd' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                                    class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm">
                                USD ($)
                            </button>
                            <button @click="activeTab = 'gbp'" 
                                    :class="activeTab === 'gbp' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                                    class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm">
                                GBP (£)
                            </button>
                            <button @click="activeTab = 'inr'" 
                                    :class="activeTab === 'inr' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                                    class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm">
                                INR (₹)
                            </button>
                        </nav>
                    </div>

                    <form method="POST" action="<?= url('/settings/banking/update') ?>" class="p-6">
                        <!-- USD Tab -->
                        <div x-show="activeTab === 'usd'" class="space-y-6">
                            <div>
                                <label for="bank_usd_account_holder" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Account Holder</label>
                                <input type="text" name="bank_usd_account_holder" id="bank_usd_account_holder"
                                       value="<?= e($settings['bank_usd_account_holder'] ?? '') ?>"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <div>
                                <label for="bank_usd_account_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Account Number</label>
                                <input type="text" name="bank_usd_account_number" id="bank_usd_account_number"
                                       value="<?= e($settings['bank_usd_account_number'] ?? '') ?>"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <div>
                                <label for="bank_usd_routing_ach" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Routing Number (ACH or ABA)</label>
                                <input type="text" name="bank_usd_routing_ach" id="bank_usd_routing_ach"
                                       value="<?= e($settings['bank_usd_routing_ach'] ?? '') ?>"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <div>
                                <label for="bank_usd_wire_routing" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Wire Routing Number</label>
                                <input type="text" name="bank_usd_wire_routing" id="bank_usd_wire_routing"
                                       value="<?= e($settings['bank_usd_wire_routing'] ?? '') ?>"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <div>
                                <label for="bank_usd_swift_bic" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Swift/BIC</label>
                                <input type="text" name="bank_usd_swift_bic" id="bank_usd_swift_bic"
                                       value="<?= e($settings['bank_usd_swift_bic'] ?? '') ?>"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <div>
                                <label for="bank_usd_bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bank Name</label>
                                <input type="text" name="bank_usd_bank_name" id="bank_usd_bank_name"
                                       value="<?= e($settings['bank_usd_bank_name'] ?? '') ?>"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <div>
                                <label for="bank_usd_bank_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bank Address</label>
                                <textarea name="bank_usd_bank_address" id="bank_usd_bank_address" rows="3"
                                          class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"><?= e($settings['bank_usd_bank_address'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- GBP Tab -->
                        <div x-show="activeTab === 'gbp'" class="space-y-6">
                            <div>
                                <label for="bank_gbp_account_holder" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Account Holder</label>
                                <input type="text" name="bank_gbp_account_holder" id="bank_gbp_account_holder"
                                       value="<?= e($settings['bank_gbp_account_holder'] ?? '') ?>"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <div>
                                <label for="bank_gbp_account_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Account Number</label>
                                <input type="text" name="bank_gbp_account_number" id="bank_gbp_account_number"
                                       value="<?= e($settings['bank_gbp_account_number'] ?? '') ?>"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <div>
                                <label for="bank_gbp_iban" class="block text-sm font-medium text-gray-700 dark:text-gray-300">IBAN</label>
                                <input type="text" name="bank_gbp_iban" id="bank_gbp_iban"
                                       value="<?= e($settings['bank_gbp_iban'] ?? '') ?>"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <div>
                                <label for="bank_gbp_sort_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">UK Sort Code</label>
                                <input type="text" name="bank_gbp_sort_code" id="bank_gbp_sort_code"
                                       value="<?= e($settings['bank_gbp_sort_code'] ?? '') ?>"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <div>
                                <label for="bank_gbp_swift_bic" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Swift/BIC</label>
                                <input type="text" name="bank_gbp_swift_bic" id="bank_gbp_swift_bic"
                                       value="<?= e($settings['bank_gbp_swift_bic'] ?? '') ?>"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <div>
                                <label for="bank_gbp_bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bank Name</label>
                                <input type="text" name="bank_gbp_bank_name" id="bank_gbp_bank_name"
                                       value="<?= e($settings['bank_gbp_bank_name'] ?? '') ?>"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <div>
                                <label for="bank_gbp_bank_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bank Address</label>
                                <textarea name="bank_gbp_bank_address" id="bank_gbp_bank_address" rows="3"
                                          class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"><?= e($settings['bank_gbp_bank_address'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- INR Tab -->
                        <div x-show="activeTab === 'inr'" class="space-y-6">
                            <div>
                                <label for="bank_inr_bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bank Name</label>
                                <input type="text" name="bank_inr_bank_name" id="bank_inr_bank_name"
                                       value="<?= e($settings['bank_inr_bank_name'] ?? '') ?>"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <div>
                                <label for="bank_inr_account_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Account Name</label>
                                <input type="text" name="bank_inr_account_name" id="bank_inr_account_name"
                                       value="<?= e($settings['bank_inr_account_name'] ?? '') ?>"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <div>
                                <label for="bank_inr_account_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Account Number</label>
                                <input type="text" name="bank_inr_account_number" id="bank_inr_account_number"
                                       value="<?= e($settings['bank_inr_account_number'] ?? '') ?>"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <div>
                                <label for="bank_inr_ifsc_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">IFSC Code</label>
                                <input type="text" name="bank_inr_ifsc_code" id="bank_inr_ifsc_code"
                                       value="<?= e($settings['bank_inr_ifsc_code'] ?? '') ?>"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                        </div>

                        <div class="flex justify-end mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Save Banking Details
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/../../components/notification.php'; ?>
<?php require_once __DIR__ . '/../../components/footer.php'; ?>
