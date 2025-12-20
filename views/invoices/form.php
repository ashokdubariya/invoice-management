<?php require_once __DIR__ . '/../../components/header.php'; ?>

<div x-data="invoiceForm()" x-init="init()" class="flex h-screen overflow-hidden">
    <?php require_once __DIR__ . '/../../components/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col overflow-hidden">
        <?php require_once __DIR__ . '/../../components/navbar.php'; ?>
        
        <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6">
            <div class="max-w-5xl mx-auto">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        <?= isset($invoice) ? 'Edit Invoice' : 'Create Invoice' ?>
                    </h2>
                </div>

                <form method="POST" action="<?= isset($invoice) ? url('/invoices/update') : url('/invoices/store') ?>" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <?php if (isset($invoice)): ?>
                        <input type="hidden" name="id" value="<?= $invoice['id'] ?>">
                    <?php endif; ?>

                    <!-- Invoice Header -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Invoice Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Invoice Number</label>
                                <input type="text" name="invoice_number" required
                                       value="<?= isset($invoice) ? e($invoice['invoice_number']) : e($invoiceNumber) ?>"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <!-- Client -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client *</label>
                                <select name="client_id" required class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">Select Client</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?= $client['id'] ?>" <?= (isset($invoice) && $invoice['client_id'] == $client['id']) ? 'selected' : '' ?>>
                                            <?= e($client['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Currency -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Currency *</label>
                                <select name="currency_id" required class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <?php foreach ($currencies as $currency): ?>
                                        <option value="<?= $currency['id'] ?>" 
                                                <?= (isset($invoice) && $invoice['currency_id'] == $currency['id']) ? 'selected' : (isset($defaultCurrency) && $defaultCurrency['id'] == $currency['id'] ? 'selected' : '') ?>>
                                            <?= e($currency['code']) ?> (<?= e($currency['symbol']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status *</label>
                                <select name="status" required class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="Draft" <?= (isset($invoice) && $invoice['status'] == 'Draft') ? 'selected' : '' ?>>Draft</option>
                                    <option value="Sent" <?= (isset($invoice) && $invoice['status'] == 'Sent') ? 'selected' : '' ?>>Sent</option>
                                    <option value="Paid" <?= (isset($invoice) && $invoice['status'] == 'Paid') ? 'selected' : '' ?>>Paid</option>
                                    <option value="Overdue" <?= (isset($invoice) && $invoice['status'] == 'Overdue') ? 'selected' : '' ?>>Overdue</option>
                                </select>
                            </div>

                            <!-- Invoice Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Invoice Date *</label>
                                <input type="date" name="invoice_date" required
                                       value="<?= isset($invoice) ? $invoice['invoice_date'] : date('Y-m-d') ?>"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>

                            <!-- Due Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Due Date *</label>
                                <input type="date" name="due_date" required
                                       value="<?= isset($invoice) ? $invoice['due_date'] : date('Y-m-d', strtotime('+30 days')) ?>"
                                       class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Line Items -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Line Items</h3>
                            <button type="button" @click="addItem()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Item
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Item</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Description</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-20">Qty</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-24">Price</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-20">Tax %</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-24">Total</th>
                                        <th class="px-3 py-2 w-10"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr>
                                            <td class="px-3 py-2">
                                                <input type="text" :name="'items[' + index + '][item_name]'" x-model="item.item_name" required
                                                       class="block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-1 px-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="text" :name="'items[' + index + '][description]'" x-model="item.description"
                                                       class="block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-1 px-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="number" :name="'items[' + index + '][quantity]'" x-model.number="item.quantity" @input="calculateTotals()" step="0.01" min="0" required
                                                       class="block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-1 px-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="number" :name="'items[' + index + '][unit_price]'" x-model.number="item.unit_price" @input="calculateTotals()" step="0.01" min="0" required
                                                       class="block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-1 px-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="number" :name="'items[' + index + '][tax_percent]'" x-model.number="item.tax_percent" @input="calculateTotals()" step="0.01" min="0" max="100"
                                                       class="block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-1 px-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                            </td>
                                            <td class="px-3 py-2 text-right text-sm font-medium text-gray-900 dark:text-white" x-text="formatCurrency(calculateLineTotal(item))"></td>
                                            <td class="px-3 py-2">
                                                <button type="button" @click="removeItem(index)" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Totals -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <div class="max-w-md ml-auto space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                                <span class="font-medium text-gray-900 dark:text-white" x-text="formatCurrency(subtotal)"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Tax:</span>
                                <span class="font-medium text-gray-900 dark:text-white" x-text="formatCurrency(taxAmount)"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <label class="text-gray-600 dark:text-gray-400">Discount:</label>
                                <input type="number" name="discount_amount" x-model.number="discountAmount" @input="calculateTotals()" step="0.01" min="0"
                                       class="w-32 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-1 px-2 text-sm text-right bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t border-gray-200 dark:border-gray-700 pt-3">
                                <span class="text-gray-900 dark:text-white">Total:</span>
                                <span class="text-gray-900 dark:text-white" x-text="formatCurrency(totalAmount)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                        <textarea name="notes" rows="4" class="block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"><?= isset($invoice) ? e($invoice['notes']) : '' ?></textarea>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-3">
                        <a href="<?= url('/invoices') ?>" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <?= isset($invoice) ? 'Update' : 'Create' ?> Invoice
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<script>
function invoiceForm() {
    return {
        items: [],
        subtotal: 0,
        taxAmount: 0,
        discountAmount: <?= isset($invoice) ? $invoice['discount_amount'] : 0 ?>,
        totalAmount: 0,
        
        init() {
            <?php if (isset($items) && !empty($items)): ?>
                this.items = <?= json_encode(array_map(function($item) {
                    return [
                        'item_name' => $item['item_name'],
                        'description' => $item['description'],
                        'quantity' => (float)$item['quantity'],
                        'unit_price' => (float)$item['unit_price'],
                        'tax_percent' => (float)$item['tax_percent']
                    ];
                }, $items)) ?>;
            <?php else: ?>
                this.addItem();
            <?php endif; ?>
            this.calculateTotals();
        },
        
        addItem() {
            this.items.push({
                item_name: '',
                description: '',
                quantity: 1,
                unit_price: 0,
                tax_percent: 0
            });
        },
        
        removeItem(index) {
            this.items.splice(index, 1);
            this.calculateTotals();
        },
        
        calculateLineTotal(item) {
            const subtotal = item.quantity * item.unit_price;
            const tax = (subtotal * item.tax_percent) / 100;
            return subtotal + tax;
        },
        
        calculateTotals() {
            this.subtotal = 0;
            this.taxAmount = 0;
            
            this.items.forEach(item => {
                const lineSubtotal = item.quantity * item.unit_price;
                const lineTax = (lineSubtotal * item.tax_percent) / 100;
                this.subtotal += lineSubtotal;
                this.taxAmount += lineTax;
            });
            
            this.totalAmount = this.subtotal + this.taxAmount - this.discountAmount;
        },
        
        formatCurrency(amount) {
            return '$' + amount.toFixed(2);
        }
    }
}
</script>

<?php require_once __DIR__ . '/../../components/notification.php'; ?>
<?php require_once __DIR__ . '/../../components/footer.php'; ?>
