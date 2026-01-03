<?php require_once __DIR__ . '/../../components/header.php'; ?>

<div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
    <?php require_once __DIR__ . '/../../components/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col overflow-hidden">
        <?php require_once __DIR__ . '/../../components/navbar.php'; ?>
        
        <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Page Header -->
                <div class="md:flex md:items-center md:justify-between mb-6">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">Invoices</h2>
                    </div>
                    <div class="mt-4 flex md:mt-0 md:ml-4">
                        <a href="<?= url('/invoices/create') ?>" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create Invoice
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4 mb-6">
                    <form method="GET" action="<?= url('/invoices') ?>" class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                        <div>
                            <input type="text" name="search" value="<?= e($filters['search']) ?>" placeholder="Search invoices..."
                                   class="block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                        </div>
                        <div>
                            <select name="status" class="block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                                <option value="">All Statuses</option>
                                <option value="Draft" <?= $filters['status'] == 'Draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="Sent" <?= $filters['status'] == 'Sent' ? 'selected' : '' ?>>Sent</option>
                                <option value="Partially Paid" <?= $filters['status'] == 'Partially Paid' ? 'selected' : '' ?>>Partially Paid</option>
                                <option value="Paid" <?= $filters['status'] == 'Paid' ? 'selected' : '' ?>>Paid</option>
                                <option value="Overdue" <?= $filters['status'] == 'Overdue' ? 'selected' : '' ?>>Overdue</option>
                            </select>
                        </div>
                        <div>
                            <select name="client_id" class="block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                                <option value="">All Clients</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?= $client['id'] ?>" <?= $filters['client_id'] == $client['id'] ? 'selected' : '' ?>>
                                        <?= e($client['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Invoices Table -->
                <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Invoice #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Paid</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Outstanding</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Due Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php if (empty($invoices)): ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No invoices found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($invoices as $invoice): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            <a href="<?= url('/invoices/view') ?>?id=<?= $invoice['id'] ?>" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                <?= e($invoice['invoice_number']) ?>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?= e($invoice['client_name']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium"><?= formatCurrency($invoice['total_amount'], $invoice['currency_symbol']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 dark:text-green-400"><?= formatCurrency($invoice['amount_paid'] ?? 0, $invoice['currency_symbol']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-orange-600 dark:text-orange-400 font-medium"><?= formatCurrency($invoice['outstanding_balance'] ?? $invoice['total_amount'], $invoice['currency_symbol']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm"><?= getStatusBadge($invoice['status']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"><?= formatDate($invoice['due_date']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <a href="<?= url('/invoices/pdf') ?>?id=<?= $invoice['id'] ?>" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">PDF</a>
                                            <a href="<?= url('/invoices/edit') ?>?id=<?= $invoice['id'] ?>" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">Edit</a>
                                            <form method="POST" action="<?= url('/invoices/delete') ?>" class="inline" onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                                                <input type="hidden" name="id" value="<?= $invoice['id'] ?>">
                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?= getPagination($page, $totalPages, '/invoices') ?>
            </div>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/../../components/notification.php'; ?>
<?php require_once __DIR__ . '/../../components/footer.php'; ?>
