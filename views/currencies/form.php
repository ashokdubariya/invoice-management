<?php require_once __DIR__ . '/../../components/header.php'; ?>

<div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
    <?php require_once __DIR__ . '/../../components/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col overflow-hidden">
        <?php require_once __DIR__ . '/../../components/navbar.php'; ?>
        
        <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6">
            <div class="max-w-2xl mx-auto">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        <?= isset($currency) ? 'Edit Currency' : 'Add Currency' ?>
                    </h2>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <form method="POST" action="<?= isset($currency) ? url('/currencies/update') : url('/currencies/store') ?>" class="p-6 space-y-6">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <?php if (isset($currency)): ?>
                            <input type="hidden" name="id" value="<?= $currency['id'] ?>">
                        <?php endif; ?>

                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Currency Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="code" id="code" required maxlength="10"
                                   value="<?= isset($currency) ? e($currency['code']) : '' ?>"
                                   placeholder="USD, EUR, INR"
                                   class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white uppercase">
                        </div>

                        <div>
                            <label for="symbol" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Symbol <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="symbol" id="symbol" required maxlength="10"
                                   value="<?= isset($currency) ? e($currency['symbol']) : '' ?>"
                                   placeholder="$, €, ₹"
                                   class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <div>
                            <label for="exchange_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Exchange Rate
                            </label>
                            <input type="number" name="exchange_rate" id="exchange_rate" step="0.0001" min="0"
                                   value="<?= isset($currency) ? $currency['exchange_rate'] : '1.0000' ?>"
                                   class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Exchange rate relative to base currency</p>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="<?= url('/currencies') ?>" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                <?= isset($currency) ? 'Update' : 'Create' ?> Currency
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
