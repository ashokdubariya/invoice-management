<?php require_once __DIR__ . '/../../components/header.php'; ?>

<div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
    <?php require_once __DIR__ . '/../../components/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col overflow-hidden">
        <?php require_once __DIR__ . '/../../components/navbar.php'; ?>
        
        <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6">
            <div class="max-w-xl mx-auto">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Company Settings</h2>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <form method="POST" action="<?= url('/settings/update') ?>" enctype="multipart/form-data" class="p-6 space-y-6">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                        <!-- Logo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Logo</label>
                            <?php if (!empty($settings['company_logo'])): ?>
                                <div class="mb-3">
                                    <img src="/assets/uploads/logos/<?= e($settings['company_logo']) ?>" alt="Company Logo" class="h-20 object-contain">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="company_logo" accept="image/*"
                                   class="block w-full text-sm text-gray-500 dark:text-gray-400
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-indigo-50 file:text-indigo-700
                                          hover:file:bg-indigo-100
                                          dark:file:bg-indigo-900 dark:file:text-indigo-300">
                        </div>

                        <!-- Company Name -->
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company Name</label>
                            <input type="text" name="company_name" id="company_name"
                                   value="<?= e($settings['company_name'] ?? '') ?>"
                                   class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <!-- Address -->
                        <div>
                            <label for="company_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                            <textarea name="company_address" id="company_address" rows="3"
                                      class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"><?= e($settings['company_address'] ?? '') ?></textarea>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="company_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                            <input type="text" name="company_phone" id="company_phone"
                                   value="<?= e($settings['company_phone'] ?? '') ?>"
                                   class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="company_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input type="email" name="company_email" id="company_email"
                                   value="<?= e($settings['company_email'] ?? '') ?>"
                                   class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <!-- Website -->
                        <div>
                            <label for="company_website" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Website</label>
                            <input type="text" name="company_website" id="company_website"
                                   value="<?= e($settings['company_website'] ?? '') ?>"
                                   class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <!-- Tax Number -->
                        <div>
                            <label for="company_tax_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tax/GST Number</label>
                            <input type="text" name="company_tax_number" id="company_tax_number"
                                   value="<?= e($settings['company_tax_number'] ?? '') ?>"
                                   class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Save Settings
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
