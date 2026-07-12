<?php /** Admin Revenue Dashboard with Chart.js */ ?>
<div class="stats-grid">
  <div class="stat-card" style="border-color:var(--color-gold)"><div class="stat-card-label">Total Pendapatan Bersih</div><div class="stat-card-value" style="color:var(--color-gold)"><?= formatCurrency($totalRevenue) ?></div></div>
  <div class="stat-card"><div class="stat-card-label">Total Booking Berhasil</div><div class="stat-card-value"><?= number_format($totalBookings) ?></div></div>
  <div class="stat-card"><div class="stat-card-label">Pendapatan Rata-rata/Booking</div><div class="stat-card-value"><?= formatCurrency($totalBookings>0 ? $totalRevenue/$totalBookings : 0) ?></div></div>
</div>

<div class="chart-container">
  <div class="chart-header"><h3>Grafik Pendapatan 6 Bulan Terakhir</h3></div>
  <canvas id="revenueChart" height="100"></canvas>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const rawData = <?= json_encode($monthlyData) ?>;
    
    const labels = rawData.map(d => {
        const [y, m] = d.month.split('-');
        const date = new Date(y, m-1);
        return date.toLocaleString('<?= getLang()==='id'?'id-ID':'en-US' ?>', {month:'short', year:'numeric'});
    });
    const data = rawData.map(d => parseFloat(d.revenue));
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.length ? labels : ['Belum ada data'],
            datasets: [{
                label: 'Pendapatan (IDR)',
                data: data.length ? data : [0],
                borderColor: '#D4A373',
                backgroundColor: 'rgba(212, 163, 115, 0.1)',
                borderWidth: 2,
                pointBackgroundColor: '#121212',
                pointBorderColor: '#D4A373',
                pointBorderWidth: 2,
                pointRadius: 4,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,0.05)' },
                    ticks: { color: '#9CA3AF', callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'M' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#9CA3AF' }
                }
            }
        }
    });
});
</script>
