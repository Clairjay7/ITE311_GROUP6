<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>
<style>
    .dashboard-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
    .mini-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px; box-shadow: 0 2px 6px rgba(15,23,42,.08); position: relative; overflow: hidden; transition: all .25s ease; }
    .mini-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #2e7d32, #43a047); }
    .mini-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16,24,40,.12); }
    .mini-title { margin: 0; font-size: 14px; font-weight: 700; color: #2e7d32; }
    .mini-value { margin-top: 8px; font-size: 28px; font-weight: 800; color: #1f2937; }
    .mini-subtext { margin-top: 4px; font-size: 12px; color: #64748b; }
    @media (max-width: 600px) { .mini-value { font-size: 24px; } }
</style>
<div class="dashboard-summary">
    <div class="mini-card">
        <div class="mini-title">Today's Appointments</div>
        <div id="appointments_today" class="mini-value">--</div>
        <div class="mini-subtext">+3 from yesterday</div>
    </div>
    
    <div class="mini-card">
        <div class="mini-title">Waiting Patients</div>
        <div id="waiting_patients" class="mini-value">--</div>
        <div class="mini-subtext">In queue</div>
    </div>

    <div class="mini-card">
        <div class="mini-title">New Registrations</div>
        <div id="new_registrations" class="mini-value">--</div>
        <div class="mini-subtext">Today</div>
    </div>

    <div class="mini-card">
        <div class="mini-title">Total In-Patients</div>
        <div id="total_inpatients" class="mini-value">--</div>
        <div class="mini-subtext">Overall registered</div>
    </div>

    <div class="mini-card">
        <div class="mini-title">Total Out-Patients</div>
        <div id="total_outpatients" class="mini-value">--</div>
        <div class="mini-subtext">Overall registered</div>
    </div>
</div>

<script>
const endpoint = '<?= site_url('receptionist/dashboard/stats') ?>';
async function refreshDashboard(){
  try{
    const res = await fetch(endpoint, { headers: { 'Accept': 'application/json' } });
    if(!res.ok) throw new Error('Network');
    const data = await res.json();
    const setText = (id, val) => { const el = document.getElementById(id); if(el) el.textContent = val; };
    setText('appointments_today', data.appointments_today ?? '--');
    setText('waiting_patients', data.waiting_patients ?? '--');
    setText('new_registrations', data.new_registrations ?? '--');
    setText('total_inpatients', data.total_inpatients ?? '--');
    setText('total_outpatients', data.total_outpatients ?? '--');

    const amt = typeof data.pending_payments_amount === 'number' ? new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(data.pending_payments_amount) : '₱--';
    setText('pending_payments_amount', amt);
    setText('pending_invoices', (data.pending_invoices ?? '--') + ' invoices');
  }catch(e){ /* silent fail */ }
}
window.addEventListener('DOMContentLoaded', () => {
  refreshDashboard();
  setInterval(refreshDashboard, 15000);
});
</script>
<?= $this->endSection() ?>