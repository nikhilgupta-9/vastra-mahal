<?php
$pageTitle = "Customer Inquiries & Leads";
require_once __DIR__ . '/includes/admin-header.php';

$success = '';
$error = '';

// Handle Actions
if (isset($_GET['action'])) {
    $inqId = (int)($_GET['id'] ?? 0);
    if ($_GET['action'] === 'mark_read' && $inqId > 0) {
        $pdo->prepare("UPDATE contact_inquiries SET is_read = 1 WHERE id = ?")->execute([$inqId]);
        $success = "Inquiry marked as read.";
    } elseif ($_GET['action'] === 'delete' && $inqId > 0) {
        $pdo->prepare("DELETE FROM contact_inquiries WHERE id = ?")->execute([$inqId]);
        $success = "Inquiry deleted successfully.";
    }
}

// Fetch all inquiries
$inquiries = $pdo->query("SELECT * FROM contact_inquiries ORDER BY is_read ASC, id DESC")->fetchAll();
?>

<!-- Alerts -->
<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> <?= e($success); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 rounded-3 shadow-sm bg-white p-3 mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-1" style="font-family: var(--vm-font-title);">Customer Contact Inquiries (<?= count($inquiries); ?>)</h5>
            <small class="text-muted">Messages and trial requests submitted by customers through the website contact form.</small>
        </div>
    </div>
</div>

<div class="card border-0 rounded-3 shadow-sm bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-secondary">
                <tr>
                    <th style="width: 50px;">Status</th>
                    <th>Customer Name</th>
                    <th>Phone / WhatsApp</th>
                    <th>Subject</th>
                    <th>Message Snippet</th>
                    <th>Date & Time</th>
                    <th class="text-end" style="width: 170px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inquiries)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-regular fa-envelope-open fs-1 mb-2 d-block text-secondary"></i>
                            No customer inquiries received yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($inquiries as $inq): 
                        $cleanPhone = preg_replace('/[^0-9]/', '', $inq['phone']);
                        if (strlen($cleanPhone) === 10) $cleanPhone = '91' . $cleanPhone;
                    ?>
                        <tr class="<?= $inq['is_read'] ? '' : 'table-light fw-semibold'; ?>">
                            <td>
                                <?php if ($inq['is_read']): ?>
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size:11px;">Read</span>
                                <?php else: ?>
                                    <span class="badge bg-danger" style="font-size:11px;">New</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="text-dark"><?= e($inq['name']); ?></div>
                                <small class="text-muted"><?= e($inq['email'] ?? 'No email'); ?></small>
                            </td>
                            <td>
                                <div><a href="tel:<?= e($inq['phone']); ?>" class="text-dark text-decoration-none"><?= e($inq['phone']); ?></a></div>
                                <a href="https://wa.me/<?= $cleanPhone; ?>?text=<?= urlencode("Hello " . $inq['name'] . "! We received your inquiry at The Vastra Mahal regarding " . $inq['subject'] . "."); ?>" target="_blank" class="small text-success text-decoration-none fw-bold">
                                    <i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light text-danger border border-danger-subtle"><?= e($inq['subject'] ?? 'General'); ?></span>
                            </td>
                            <td>
                                <span class="text-secondary small d-inline-block text-truncate" style="max-width: 250px;">
                                    <?= e($inq['message']); ?>
                                </span>
                            </td>
                            <td class="small text-muted text-nowrap">
                                <?= date('d M Y, h:i A', strtotime($inq['created_at'])); ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-light border text-primary" data-bs-toggle="modal" data-bs-target="#inqModal<?= $inq['id']; ?>" title="View Message">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <?php if (!$inq['is_read']): ?>
                                        <a href="inquiries.php?action=mark_read&id=<?= $inq['id']; ?>" class="btn btn-sm btn-light border text-success" title="Mark as Read">
                                            <i class="fa-solid fa-check"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="inquiries.php?action=delete&id=<?= $inq['id']; ?>" class="btn btn-sm btn-light border text-danger" title="Delete Inquiry" onclick="return confirm('Delete this inquiry?');">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>

                                <!-- Inquiry Modal -->
                                <div class="modal fade text-start" id="inqModal<?= $inq['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold" style="font-family: var(--vm-font-title);">Inquiry Details #<?= $inq['id']; ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <small class="text-muted text-uppercase fw-bold" style="font-size:11px;">Customer Information</small>
                                                    <h6 class="fw-bold text-dark mt-1"><?= e($inq['name']); ?></h6>
                                                    <div><i class="fa-solid fa-phone text-muted me-1"></i> <?= e($inq['phone']); ?></div>
                                                    <?php if (!empty($inq['email'])): ?>
                                                        <div><i class="fa-solid fa-envelope text-muted me-1"></i> <?= e($inq['email']); ?></div>
                                                    <?php endif; ?>
                                                    <div><i class="fa-regular fa-clock text-muted me-1"></i> <?= date('d F Y at h:i A', strtotime($inq['created_at'])); ?></div>
                                                </div>

                                                <div class="mb-3 p-3 bg-light rounded-3 border">
                                                    <small class="text-danger text-uppercase fw-bold" style="font-size:11px;">Subject: <?= e($inq['subject']); ?></small>
                                                    <div class="mt-2 text-dark" style="line-height:1.7;">
                                                        <?= nl2br(e($inq['message'])); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer d-flex justify-content-between">
                                                <a href="inquiries.php?action=mark_read&id=<?= $inq['id']; ?>" class="btn btn-sm btn-outline-secondary">Mark As Read</a>
                                                <a href="https://wa.me/<?= $cleanPhone; ?>?text=<?= urlencode("Hello " . $inq['name'] . "! The Vastra Mahal team is here regarding your inquiry: " . $inq['subject']); ?>" target="_blank" class="btn btn-sm btn-whatsapp">
                                                    <i class="fa-brands fa-whatsapp me-1"></i> Reply On WhatsApp
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
