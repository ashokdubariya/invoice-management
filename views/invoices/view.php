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

                <!-- Payment Summary Card -->
                <div class="bg-gradient-to-r from-green-50 to-blue-50 dark:from-green-900/20 dark:to-blue-900/20 shadow sm:rounded-lg p-6 mb-6 border border-green-200 dark:border-green-800">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Summary</h3>
                        <button @click="Alpine.$data(document.querySelector('[x-data*=paymentModal]')).openModal(<?= $invoice['id'] ?>, <?= $invoice['outstanding_balance'] ?? $invoice['total_amount'] ?>, '<?= $invoice['currency_symbol'] ?? '$' ?>')"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Record Payment
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Amount</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                <?= formatCurrency($invoice['total_amount'], $invoice['currency_symbol']) ?>
                            </p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Paid</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
                                <?= formatCurrency($invoice['amount_paid'] ?? 0, $invoice['currency_symbol']) ?>
                            </p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Outstanding</p>
                            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400 mt-1">
                                <?= formatCurrency($invoice['outstanding_balance'] ?? $invoice['total_amount'], $invoice['currency_symbol']) ?>
                            </p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Payment Status</p>
                            <div class="mt-1">
                                <?= getStatusBadge($invoice['status']) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <?php if (!empty($payments)): ?>
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment History</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= count($payments) ?> payment(s) recorded</p>
                        </div>
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Method</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reference</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Notes</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <?php foreach ($payments as $payment): ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            <?= formatDate($payment['payment_date']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 dark:text-green-400">
                                            <?= formatCurrency($payment['amount'], $invoice['currency_symbol']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                <?= e($payment['payment_method']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            <?= e($payment['reference_number'] ?? '-') ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            <?= e($payment['notes'] ? (strlen($payment['notes']) > 30 ? substr($payment['notes'], 0, 30) . '...' : $payment['notes']) : '-') ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <form method="POST" action="<?= url('/payments/delete') ?>" class="inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this payment? This will update the invoice status.');">
                                                <input type="hidden" name="payment_id" value="<?= $payment['id'] ?>">
                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 mb-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No payments recorded</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by recording the first payment for this invoice.</p>
                        <div class="mt-6">
                            <button @click="Alpine.$data(document.querySelector('[x-data*=paymentModal]')).openModal(<?= $invoice['id'] ?>, <?= $invoice['outstanding_balance'] ?? $invoice['total_amount'] ?>, '<?= $invoice['currency_symbol'] ?? '$' ?>')"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Record First Payment
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

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

<?php require_once __DIR__ . '/../../components/payment-modal.php'; ?>
<?php require_once __DIR__ . '/../../components/footer.php'; ?>
