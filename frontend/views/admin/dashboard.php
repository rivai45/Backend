<?php /** Admin Dashboard */
function renderChange($change) {
    if (!$change || $change['dir'] === 'none') return '<div class="stat-card-change" style="color:var(--text-muted)">— Tidak ada data bulan lalu</div>';
    $dir   = $change['dir'];
    $val   = $change['val'];
    $cls   = $dir === 'up' ? 'positive' : 'negative';
    $arrow = $dir === 'up' ? '↑' : '↓';
    $sign  = $dir === 'up' ? '+' : '-';
    return "<div class=\"stat-card-change {$cls}\">{$arrow} {$sign}{$val}% vs bulan lalu</div>";
}
?>
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-card-label"><?= __('admin.total_users') ?></div>
    <div class="stat-card-value"><?= number_format($stats['total_users'] ?? 0) ?></div>
    <?= renderChange($stats['users_change'] ?? null) ?>
  </div>
  <div class="stat-card">
    <div class="stat-card-label"><?= __('admin.total_bookings') ?></div>
    <div class="stat-card-value"><?= number_format($stats['total_bookings'] ?? 0) ?></div>
    <?= renderChange($stats['bookings_change'] ?? null) ?>
  </div>
  <div class="stat-card">
    <div class="stat-card-label"><?= __('admin.total_revenue') ?></div>
    <div class="stat-card-value"><?= formatCurrency($stats['total_revenue'] ?? 0) ?></div>
    <?= renderChange($stats['revenue_change'] ?? null) ?>
  </div>
  <div class="stat-card">
    <div class="stat-card-label"><?= __('admin.cancellation_rate') ?></div>
    <div class="stat-card-value"><?= $stats['cancellation_rate'] ?? 0 ?>%</div>
    <?php
    // Untuk cancel rate, arah "baik" adalah turun
    $cc = $stats['cancel_change'] ?? null;
    if ($cc && $cc['dir'] !== 'none') {
        $cls   = $cc['dir'] === 'down' ? 'positive' : 'negative';
        $arrow = $cc['dir'] === 'down' ? '↓' : '↑';
        $sign  = $cc['dir'] === 'down' ? '-' : '+';
        echo "<div class=\"stat-card-change {$cls}\">{$arrow} {$sign}{$cc['val']}% vs bulan lalu</div>";
    } else {
        echo '<div class="stat-card-change" style="color:var(--text-muted)">— Tidak ada data bulan lalu</div>';
    }
    ?>
  </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: var(--space-4); margin-bottom: var(--space-6);">
  <!-- Chart: Monthly Revenue -->
  <div class="settings-card" style="margin-bottom: 0;">
    <h3 class="settings-card-title" style="margin-bottom: var(--space-4); font-size: 16px;">Tren Pendapatan & Pemesanan (6 Bulan)</h3>
    <div style="height: 300px; position: relative;">
      <canvas id="revenueChart"></canvas>
    </div>
  </div>

  <!-- Chart: Status Distribution -->
  <div class="settings-card" style="margin-bottom: 0;">
    <h3 class="settings-card-title" style="margin-bottom: var(--space-4); font-size: 16px;">Distribusi Status Pemesanan</h3>
    <div style="height: 300px; position: relative; display: flex; justify-content: center;">
      <canvas id="statusChart"></canvas>
    </div>
  </div>
</div>

<div class="admin-table-container">
  <div class="admin-table-header">
    <h3><?= __('admin.recent_bookings') ?></h3>
    <a href="<?= BASE_URL ?>?page=admin/booking" class="btn btn-outline btn-sm"><?= __('home.view_all') ?></a>
  </div>
  <table class="data-table">
    <thead><tr>
      <th><?= __('admin.booking_id') ?></th><th><?= __('admin.guest_name') ?></th><th><?= __('admin.hotel_name') ?></th>
      <th><?= __('admin.check_in') ?></th><th><?= __('admin.check_out') ?></th><th><?= __('admin.amount') ?></th><th><?= __('admin.status') ?></th>
    </tr></thead>
    <tbody>
    <?php foreach(($recentBookings ?? []) as $b):
      $statusClass = match($b['status']){'confirmed'=>'success','pending'=>'warning','cancelled'=>'danger','expired'=>'danger','completed'=>'info',default=>'warning'};
    ?>
    <tr>
      <td style="font-weight:600;color:var(--color-gold)"><?= $b['booking_code'] ?></td>
      <td><?= htmlspecialchars($b['guest_name']) ?></td>
      <td><?= htmlspecialchars($b['hotel_name']) ?></td>
      <td><?= formatDate($b['check_in']) ?></td>
      <td><?= formatDate($b['check_out']) ?></td>
      <td style="font-weight:600"><?= formatCurrency($b['total_price']) ?></td>
      <td><span class="badge badge-<?= $statusClass ?>"><?= __('status.'.$b['status']) ?></span></td>
    </tr>
    <?php endforeach; ?>
    <?php if(empty($recentBookings)): ?><tr><td colspan="7" class="text-center text-muted" style="padding:var(--space-8)"><?= __('admin.no_data') ?></td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Theme colors
    const colorGold = '#D4A373';
    const colorGoldLight = 'rgba(212, 163, 115, 0.2)';
    const colorText = '#f0f0f0';
    const colorGrid = 'rgba(255, 255, 255, 0.05)';
    const colorTooltipBg = 'rgba(10, 10, 10, 0.9)';

    // Common Chart Options
    Chart.defaults.color = '#9ca3af';
    Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { labels: { color: colorText } },
            tooltip: {
                backgroundColor: colorTooltipBg,
                titleColor: colorGold,
                bodyColor: colorText,
                borderColor: 'rgba(212, 163, 115, 0.3)',
                borderWidth: 1,
                padding: 10,
                cornerRadius: 8
            }
        }
    };

    // 1. Revenue & Bookings Chart
    const monthlyData = <?= json_encode($stats['monthly_revenue'] ?? []) ?>;
    const labels = monthlyData.map(d => {
        const [y, m] = d.month.split('-');
        const date = new Date(y, m - 1);
        return date.toLocaleString('id-ID', { month: 'short', year: 'numeric' });
    });
    const revenues = monthlyData.map(d => d.revenue);
    const bookingsCount = monthlyData.map(d => d.bookings);

    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pendapatan (Rp)',
                    data: revenues,
                    backgroundColor: colorGoldLight,
                    borderColor: colorGold,
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'y'
                },
                {
                    label: 'Jml Pemesanan',
                    data: bookingsCount,
                    type: 'line',
                    borderColor: '#3b82f6',
                    backgroundColor: '#3b82f6',
                    borderWidth: 2,
                    tension: 0.3,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            ...commonOptions,
            scales: {
                x: { grid: { color: colorGrid } },
                y: { 
                    type: 'linear', display: true, position: 'left',
                    grid: { color: colorGrid },
                    ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'M' }
                },
                y1: { 
                    type: 'linear', display: true, position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: { precision: 0 }
                }
            }
        }
    });

    // 2. Status Distribution Chart
    const statusData = <?= json_encode($stats['status_distribution'] ?? []) ?>;
    const statusMap = {
        'pending': { label: 'Tertunda', color: '#f59e0b' },
        'confirmed': { label: 'Terkonfirmasi', color: '#10b981' },
        'checked_in': { label: 'Check In', color: '#3b82f6' },
        'completed': { label: 'Selesai', color: '#8b5cf6' },
        'cancelled': { label: 'Dibatalkan', color: '#ef4444' },
        'expired': { label: 'Kedaluwarsa', color: '#6b7280' }
    };
    
    const pieLabels = [];
    const pieValues = [];
    const pieColors = [];

    statusData.forEach(d => {
        const conf = statusMap[d.status] || { label: d.status, color: '#9ca3af' };
        pieLabels.push(conf.label);
        pieValues.push(d.count);
        pieColors.push(conf.color);
    });

    if (pieValues.length === 0) {
        pieLabels.push('Belum ada data');
        pieValues.push(1);
        pieColors.push('#2a2a2a');
    }

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: pieLabels,
            datasets: [{
                data: pieValues,
                backgroundColor: pieColors,
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            ...commonOptions,
            cutout: '65%',
            plugins: {
                ...commonOptions.plugins,
                legend: { position: 'right', labels: { color: colorText, usePointStyle: true, padding: 20 } }
            }
        }
    });
});
</script>
