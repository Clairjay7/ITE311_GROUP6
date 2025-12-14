<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Surgery List<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid" style="padding: 20px;">
    <div class="modern-card">
        <div class="card-header-modern" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h4 style="color: white; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-procedures"></i>
                Surgery List
            </h4>
        </div>
        <div class="card-body-modern" style="padding: 20px;">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success" style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #10b981;">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #ef4444;">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (empty($surgeries)): ?>
                <div style="text-align: center; padding: 40px; color: #6b7280;">
                    <i class="fas fa-procedures" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                    <p style="font-size: 16px; margin: 0;">No surgeries found.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table-modern" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Patient</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Surgery Type</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Date & Time</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600;">OR Room</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Status</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Countdown</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($surgeries as $surgery): ?>
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="padding: 12px;">
                                        <strong><?= esc($surgery['patient_name'] ?? 'Unknown') ?></strong>
                                        <br>
                                        <small style="color: #6b7280;">ID: <?= $surgery['patient_id'] ?></small>
                                    </td>
                                    <td style="padding: 12px;">
                                        <?= esc($surgery['surgery_type'] ?? 'N/A') ?>
                                    </td>
                                    <td style="padding: 12px;">
                                        <strong><?= !empty($surgery['surgery_date']) ? date('M d, Y', strtotime($surgery['surgery_date'])) : 'N/A' ?></strong>
                                        <br>
                                        <small style="color: #6b7280;">
                                            <?= !empty($surgery['surgery_time']) ? date('g:i A', strtotime($surgery['surgery_time'])) : 'N/A' ?>
                                        </small>
                                    </td>
                                    <td style="padding: 12px;">
                                        <?php if (!empty($surgery['or_room_number'])): ?>
                                            <span class="badge-modern" style="background: #dbeafe; color: #1e40af; padding: 6px 12px;">
                                                <?= esc($surgery['or_room_number']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: #6b7280;">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px;">
                                        <?php
                                        $status = strtolower($surgery['status'] ?? 'unknown');
                                        $statusColors = [
                                            'scheduled' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                                            'in_progress' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                                            'completed' => ['bg' => '#d1fae5', 'text' => '#065f46'],
                                            'cancelled' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                                        ];
                                        $colors = $statusColors[$status] ?? ['bg' => '#f3f4f6', 'text' => '#374151'];
                                        ?>
                                        <span class="badge-modern" style="background: <?= $colors['bg'] ?>; color: <?= $colors['text'] ?>; padding: 6px 12px; text-transform: capitalize;">
                                            <?= esc(ucfirst($status)) ?>
                                        </span>
                                    </td>
                                    <td style="padding: 12px;">
                                        <?php if ($surgery['status'] === 'scheduled' && !empty($surgery['countdown_ends'])): ?>
                                            <?php
                                            $endTime = strtotime($surgery['countdown_ends']);
                                            $now = time();
                                            $remaining = $endTime - $now;
                                            ?>
                                            <?php if ($remaining > 0): ?>
                                                <span id="countdown-<?= $surgery['id'] ?>" style="background: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-family: monospace;">
                                                    --:--:--
                                                </span>
                                                <script>
                                                    (function() {
                                                        const endTime = new Date('<?= $surgery['countdown_ends'] ?>').getTime();
                                                        const countdownEl = document.getElementById('countdown-<?= $surgery['id'] ?>');
                                                        
                                                        function updateCountdown() {
                                                            const now = new Date().getTime();
                                                            const distance = endTime - now;
                                                            
                                                            if (distance < 0) {
                                                                countdownEl.textContent = '00:00:00';
                                                                countdownEl.style.background = '#d1fae5';
                                                                countdownEl.style.color = '#065f46';
                                                                return;
                                                            }
                                                            
                                                            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                                            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                                            
                                                            countdownEl.textContent = 
                                                                String(hours).padStart(2, '0') + ':' + 
                                                                String(minutes).padStart(2, '0') + ':' + 
                                                                String(seconds).padStart(2, '0');
                                                        }
                                                        
                                                        updateCountdown();
                                                        setInterval(updateCountdown, 1000);
                                                    })();
                                                </script>
                                            <?php else: ?>
                                                <span style="background: #d1fae5; color: #065f46; padding: 6px 12px; border-radius: 6px; font-weight: 600;">
                                                    Completed
                                                </span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span style="color: #6b7280;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px;">
                                        <a href="<?= site_url('doctor/patients/view/' . $surgery['patient_id']) ?>" 
                                           class="btn-modern btn-sm-modern" 
                                           style="background: #3b82f6; color: white; padding: 6px 12px; text-decoration: none; border-radius: 6px; display: inline-block; margin-right: 4px;"
                                           title="View Patient">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

