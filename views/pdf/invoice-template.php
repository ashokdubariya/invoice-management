<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice <?= e($invoice['invoice_number']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 20px;
        }
        .company-info {
            float: left;
            width: 50%;
        }
        .invoice-info {
            float: right;
            width: 45%;
            text-align: right;
        }
        .logo {
            max-height: 80px;
            margin-bottom: 10px;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        .client-info {
            margin: 30px 0;
            padding: 15px;
            background: #f9fafb;
            border-left: 3px solid #4F46E5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #4F46E5;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        .totals table {
            margin: 0;
        }
        .totals td {
            border: none;
            padding: 5px 10px;
        }
        .total-row {
            font-weight: bold;
            font-size: 14px;
            background: #f3f4f6;
        }
        .notes {
            margin-top: 40px;
            padding: 15px;
            background: #f9fafb;
            border-left: 3px solid #10B981;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .payment-info {
            margin-top: 30px;
            padding: 15px;
            background: #f0fdf4;
            border-left: 3px solid #10B981;
        }
        .payment-history {
            margin-top: 20px;
        }
        .payment-history table {
            font-size: 11px;
        }
        .paid-watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            font-weight: bold;
            color: rgba(16, 185, 129, 0.1);
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header clearfix">
        <div class="company-info">
            <?php if (!empty($settings['company_logo'])): ?>
                <img src="<?= LOGO_PATH . $settings['company_logo'] ?>" alt="Logo" class="logo">
            <?php endif; ?>
            <h2 style="margin: 0; color: #4F46E5;"><?= e($settings['company_name'] ?? 'Your Company') ?></h2>
            <p style="margin: 5px 0;">
                <?= nl2br(e($settings['company_address'] ?? '')) ?><br>
                <?php if (!empty($settings['company_phone'])): ?>
                    Phone: <?= e($settings['company_phone']) ?><br>
                <?php endif; ?>
                <?php if (!empty($settings['company_email'])): ?>
                    Email: <?= e($settings['company_email']) ?><br>
                <?php endif; ?>
                <?php if (!empty($settings['company_tax_number'])): ?>
                    Tax No: <?= e($settings['company_tax_number']) ?>
                <?php endif; ?>
            </p>
        </div>
        <div class="invoice-info">
            <h1 style="margin: 0; color: #4F46E5;">INVOICE</h1>
            <p style="margin: 10px 0;">
                <strong>Invoice #:</strong> <?= e($invoice['invoice_number']) ?><br>
                <strong>Date:</strong> <?= formatDate($invoice['invoice_date']) ?><br>
                <strong>Due Date:</strong> <?= formatDate($invoice['due_date']) ?><br>
                <strong>Status:</strong> <?= e($invoice['status']) ?>
            </p>
        </div>
    </div>

    <!-- Client Info -->
    <div class="client-info">
        <h3 style="margin: 0 0 10px 0;">Bill To:</h3>
        <strong><?= e($invoice['client_name']) ?></strong><br>
        <?php if (!empty($invoice['client_email'])): ?>
            <?= e($invoice['client_email']) ?><br>
        <?php endif; ?>
        <?php if (!empty($invoice['client_phone'])): ?>
            <?= e($invoice['client_phone']) ?><br>
        <?php endif; ?>
        <?php if (!empty($invoice['client_address'])): ?>
            <?= nl2br(e($invoice['client_address'])) ?><br>
        <?php endif; ?>
        <?php if (!empty($invoice['client_gst_vat'])): ?>
            GST/VAT: <?= e($invoice['client_gst_vat']) ?>
        <?php endif; ?>
    </div>

    <!-- Line Items -->
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Description</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Tax %</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= e($item['item_name']) ?></td>
                    <td><?= e($item['description']) ?></td>
                    <td class="text-right"><?= number_format($item['quantity'], 2) ?></td>
                    <td class="text-right"><?= formatCurrency($item['unit_price'], $invoice['currency_symbol']) ?></td>
                    <td class="text-right"><?= number_format($item['tax_percent'], 2) ?>%</td>
                    <td class="text-right"><?= formatCurrency($item['line_total'], $invoice['currency_symbol']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td class="text-right"><?= formatCurrency($invoice['subtotal'], $invoice['currency_symbol']) ?></td>
            </tr>
            <tr>
                <td>Tax:</td>
                <td class="text-right"><?= formatCurrency($invoice['tax_amount'], $invoice['currency_symbol']) ?></td>
            </tr>
            <tr>
                <td>Discount:</td>
                <td class="text-right">-<?= formatCurrency($invoice['discount_amount'], $invoice['currency_symbol']) ?></td>
            </tr>
            <tr class="total-row">
                <td>Total:</td>
                <td class="text-right"><?= formatCurrency($invoice['total_amount'], $invoice['currency_symbol']) ?></td>
            </tr>
        </table>
    </div>

    <div class="clearfix"></div>

    <?php
    // Add PAID watermark if invoice is fully paid
    if ($invoice['status'] === 'Paid' || ($invoice['outstanding_balance'] ?? $invoice['total_amount']) <= 0): ?>
        <div class="paid-watermark">PAID</div>
    <?php endif; ?>

    <!-- Payment Information -->
    <?php
    $amountPaid = $invoice['amount_paid'] ?? 0;
    $outstandingBalance = $invoice['outstanding_balance'] ?? ($invoice['total_amount'] - $amountPaid);
    
    if ($amountPaid > 0):
    ?>
        <div class="payment-info">
            <h4 style="margin: 0 0 10px 0; color: #10B981;">Payment Information</h4>
            <table style="margin: 0;">
                <tr>
                    <td style="border: none; padding: 3px 0;"><strong>Total Amount:</strong></td>
                    <td style="border: none; padding: 3px 0; text-align: right;"><?= formatCurrency($invoice['total_amount'], $invoice['currency_symbol']) ?></td>
                </tr>
                <tr>
                    <td style="border: none; padding: 3px 0;"><strong>Amount Paid:</strong></td>
                    <td style="border: none; padding: 3px 0; text-align: right; color: #10B981; font-weight: bold;"><?= formatCurrency($amountPaid, $invoice['currency_symbol']) ?></td>
                </tr>
                <tr style="border-top: 2px solid #10B981;">
                    <td style="border: none; padding: 8px 0 3px 0;"><strong>Outstanding Balance:</strong></td>
                    <td style="border: none; padding: 8px 0 3px 0; text-align: right; font-weight: bold; font-size: 14px; color: <?= $outstandingBalance > 0 ? '#F59E0B' : '#10B981' ?>;">
                        <?= formatCurrency($outstandingBalance, $invoice['currency_symbol']) ?>
                    </td>
                </tr>
            </table>
        </div>

        <?php
        // Load payment history if available
        if (isset($payments) && !empty($payments)):
        ?>
            <div class="payment-history">
                <h4 style="margin: 0 0 10px 0;">Payment History</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?= formatDate($payment['payment_date']) ?></td>
                                <td><?= formatCurrency($payment['amount'], $invoice['currency_symbol']) ?></td>
                                <td><?= e($payment['payment_method']) ?></td>
                                <td><?= e($payment['reference_number'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Notes -->
    <?php if (!empty($invoice['notes'])): ?>
        <div class="notes">
            <h4 style="margin: 0 0 10px 0;">Notes:</h4>
            <?= nl2br(e($invoice['notes'])) ?>
        </div>
    <?php endif; ?>

    <!-- Banking Details -->
    <?php
    $currencyCode = $invoice['currency_code'];
    $showBanking = false;
    $bankingDetails = [];
    
    // Check which currency and prepare banking details
    if ($currencyCode === 'USD') {
        $showBanking = !empty($settings['bank_usd_account_holder']) || !empty($settings['bank_usd_account_number']);
        $bankingDetails = [
            'Account Holder' => $settings['bank_usd_account_holder'] ?? '',
            'Account Number' => $settings['bank_usd_account_number'] ?? '',
            'Routing Number (ACH or ABA)' => $settings['bank_usd_routing_ach'] ?? '',
            'Wire Routing Number' => $settings['bank_usd_wire_routing'] ?? '',
            'Swift/BIC' => $settings['bank_usd_swift_bic'] ?? '',
            'Bank Name' => $settings['bank_usd_bank_name'] ?? '',
            'Bank Address' => $settings['bank_usd_bank_address'] ?? ''
        ];
    } elseif ($currencyCode === 'GBP') {
        $showBanking = !empty($settings['bank_gbp_account_holder']) || !empty($settings['bank_gbp_account_number']);
        $bankingDetails = [
            'Account Holder' => $settings['bank_gbp_account_holder'] ?? '',
            'Account Number' => $settings['bank_gbp_account_number'] ?? '',
            'IBAN' => $settings['bank_gbp_iban'] ?? '',
            'UK Sort Code' => $settings['bank_gbp_sort_code'] ?? '',
            'Swift/BIC' => $settings['bank_gbp_swift_bic'] ?? '',
            'Bank Name' => $settings['bank_gbp_bank_name'] ?? '',
            'Bank Address' => $settings['bank_gbp_bank_address'] ?? ''
        ];
    } elseif ($currencyCode === 'INR') {
        $showBanking = !empty($settings['bank_inr_bank_name']) || !empty($settings['bank_inr_account_number']);
        $bankingDetails = [
            'Bank Name' => $settings['bank_inr_bank_name'] ?? '',
            'Account Name' => $settings['bank_inr_account_name'] ?? '',
            'Account Number' => $settings['bank_inr_account_number'] ?? '',
            'IFSC Code' => $settings['bank_inr_ifsc_code'] ?? ''
        ];
    }
    ?>
    
    <?php if ($showBanking): ?>
        <div class="notes" style="margin-top: 30px;">
            <h4 style="margin: 0 0 10px 0;">Banking Details:</h4>
            <?php foreach ($bankingDetails as $label => $value): ?>
                <?php if (!empty($value)): ?>
                    <p style="margin: 3px 0;"><strong><?= e($label) ?>:</strong> <?= nl2br(e($value)) ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Footer -->
    <?php /* <div class="footer">
        <p>Thank you for your business!</p>
        <?php if (!empty($settings['company_website'])): ?>
            <p><?= e($settings['company_website']) ?></p>
        <?php endif; ?>
    </div> */ ?>
</body>
</html>
