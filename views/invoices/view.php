<?php require_once __DIR__ . '/../../components/header.php'; ?>

<div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
    <?php require_once __DIR__ . '/../../components/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col overflow-hidden">
        <?php require_once __DIR__ . '/../../components/navbar.php'; ?>
        
        <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6">
            <div class="max-w-4xl mx-auto">
                <!-- Invoice Header -->
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 mb-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Invoice</h1>
                            <p class="text-lg text-gray-600 dark:text-gray-400 mt-1"><?= e($invoice['invoice_number']) ?></p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="<?= url('/invoices/pdf') ?>?id=<?= $invoice['id'] ?>" target="_blank" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download PDF
                            </a>
                            <a href="<?= url('/invoices/edit') ?>?id=<?= $invoice['id'] ?>" 
                               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Bill To:</h3>
                            <p class="text-gray-900 dark:text-white font-semibold"><?= e($invoice['client_name']) ?></p>
                            <?php if (!empty($invoice['client_email'])): ?>
                                <p class="text-sm text-gray-600 dark:text-gray-400"><?= e($invoice['client_email']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($invoice['client_phone'])): ?>
                                <p class="text-sm text-gray-600 dark:text-gray-400"><?= e($invoice['client_phone']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($invoice['client_address'])): ?>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2"><?= nl2br(e($invoice['client_address'])) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="text-right">
                            <div class="mb-3">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Status:</span>
                                <div class="mt-1"><?= getStatusBadge($invoice['status']) ?></div>
                            </div>
                            <p class="text-sm"><span class="text-gray-500 dark:text-gray-400">Invoice Date:</span> <strong><?= formatDate($invoice['invoice_date']) ?></strong></p>
                            <p class="text-sm"><span class="text-gray-500 dark:text-gray-400">Due Date:</span> <strong><?= formatDate($invoice['due_date']) ?></strong></p>
                            <p class="text-sm"><span class="text-gray-500 dark:text-gray-400">Currency:</span> <strong><?= e($invoice['currency_code']) ?></strong></p>
                        </div>
                    </div>
                </div>

                <!-- Line Items -->
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden mb-6">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Item</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tax</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white"><?= e($item['item_name']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400"><?= e($item['description']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 text-right"><?= number_format($item['quantity'], 2) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 text-right"><?= formatCurrency($item['unit_price'], $invoice['currency_symbol']) ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 text-right"><?= number_format($item['tax_percent'], 2) ?>%</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white text-right"><?= formatCurrency($item['line_total'], $invoice['currency_symbol']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 mb-6">
                    <div class="max-w-md ml-auto space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                            <span class="font-medium text-gray-900 dark:text-white"><?= formatCurrency($invoice['subtotal'], $invoice['currency_symbol']) ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Tax:</span>
                            <span class="font-medium text-gray-900 dark:text-white"><?= formatCurrency($invoice['tax_amount'], $invoice['currency_symbol']) ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Discount:</span>
                            <span class="font-medium text-gray-900 dark:text-white">-<?= formatCurrency($invoice['discount_amount'], $invoice['currency_symbol']) ?></span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                            <span class="text-gray-900 dark:text-white">Total:</span>
                            <span class="text-indigo-600 dark:text-indigo-400"><?= formatCurrency($invoice['total_amount'], $invoice['currency_symbol']) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <?php if (!empty($invoice['notes'])): ?>
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Notes:</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400"><?= nl2br(e($invoice['notes'])) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Back Button -->
                <div class="mt-6">
                    <a href="<?= url('/invoices') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Invoices
                    </a>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/../../components/footer.php'; ?>
