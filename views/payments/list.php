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
                        <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
                            Payment History
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            View and manage all payment transactions
                        </p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Today's Collections</dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-white"><?= formatCurrency($stats['today_payments'] ?? 0) ?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">This Month</dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-white"><?= formatCurrency($stats['month_payments'] ?? 0) ?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Collected</dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-white"><?= formatCurrency($stats['total_collected'] ?? 0) ?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Transactions</dt>
                                        <dd class="text-lg font-semibold text-gray-900 dark:text-white"><?= number_format($totalPayments) ?></dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-4 mb-6">
                    <form method="GET" action="<?= url('/payments') ?>" class="grid grid-cols-1 gap-4 sm:grid-cols-5">
                        <div>
                            <input type="text" name="search" value="<?= e($filters['search'] ?? '') ?>" placeholder="Search invoice #, client..."
                                   class="block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                        </div>
                        <div>
                            <input type="date" name="start_date" value="<?= e($filters['start_date'] ?? '') ?>" placeholder="Start Date"
                                   class="block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                        </div>
                        <div>
                            <input type="date" name="end_date" value="<?= e($filters['end_date'] ?? '') ?>" placeholder="End Date"
                                   class="block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                        </div>
                        <div>
                            <select name="payment_method" class="block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                                <option value="">All Payment Methods</option>
                                <?php foreach ($paymentMethods as $method): ?>
                                    <option value="<?= $method ?>" <?= ($filters['payment_method'] ?? '') == $method ? 'selected' : '' ?>>
                                        <?= e($method) ?>
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

                <!-- Payments Table -->
                <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Invoice</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Method</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reference</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php if (empty($payments)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No payments found</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your filters or check invoices to record payments.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($payments as $payment): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <?= formatDate($payment['payment_date']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="<?= url('/invoices/view') ?>?id=<?= $payment['invoice_id'] ?>" 
                                               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-medium">
                                                <?= e($payment['invoice_number']) ?>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <?= e($payment['client_name']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 dark:text-green-400">
                                            <?= formatCurrency($payment['amount'], $payment['currency_symbol'] ?? '$') ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                <?= e($payment['payment_method']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            <?= e($payment['reference_number'] ?? '-') ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            <?php if (!empty($payment['notes'])): ?>
                                                <span title="<?= e($payment['notes']) ?>">
                                                    <?= e(strlen($payment['notes']) > 30 ? substr($payment['notes'], 0, 30) . '...' : $payment['notes']) ?>
                                                </span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="flex items-center justify-center space-x-2 mt-6">
                        <!-- Previous button -->
                        <?php if ($page > 1): ?>
                            <a href="<?= url('/payments') ?>?page=<?= $page - 1 ?><?= !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?><?= !empty($filters['start_date']) ? '&start_date=' . $filters['start_date'] : '' ?><?= !empty($filters['end_date']) ? '&end_date=' . $filters['end_date'] : '' ?><?= !empty($filters['payment_method']) ? '&payment_method=' . urlencode($filters['payment_method']) : '' ?>" 
                               class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                                Previous
                            </a>
                        <?php endif; ?>
                        
                        <!-- Page numbers -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="px-3 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md">
                                    <?= $i ?>
                                </span>
                            <?php else: ?>
                                <a href="<?= url('/payments') ?>?page=<?= $i ?><?= !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?><?= !empty($filters['start_date']) ? '&start_date=' . $filters['start_date'] : '' ?><?= !empty($filters['end_date']) ? '&end_date=' . $filters['end_date'] : '' ?><?= !empty($filters['payment_method']) ? '&payment_method=' . urlencode($filters['payment_method']) : '' ?>" 
                                   class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                                    <?= $i ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <!-- Next button -->
                        <?php if ($page < $totalPages): ?>
                            <a href="<?= url('/payments') ?>?page=<?= $page + 1 ?><?= !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?><?= !empty($filters['start_date']) ? '&start_date=' . $filters['start_date'] : '' ?><?= !empty($filters['end_date']) ? '&end_date=' . $filters['end_date'] : '' ?><?= !empty($filters['payment_method']) ? '&payment_method=' . urlencode($filters['payment_method']) : '' ?>" 
                               class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/../../components/footer.php'; ?>
