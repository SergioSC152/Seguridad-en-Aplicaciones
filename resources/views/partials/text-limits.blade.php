<style>
    textarea[maxlength]{overflow-y:auto;resize:vertical}
    .text-limit-meter{display:flex;align-items:center;gap:.5rem;margin-top:.3rem;font-size:.75rem;color:#6b7280;letter-spacing:normal;text-align:left}
    .text-limit-meter progress{width:70px;height:6px;accent-color:#0c820c}
    .text-limit-meter.at-limit{color:#925800;font-weight:600}
    .text-limit-meter.at-limit progress{accent-color:#b77900}
</style>
<script>
(() => {
    function attachTextLimits() {
        document.querySelectorAll('input[maxlength], textarea[maxlength]').forEach(field => {
            if (field.dataset.limitMeter || field.type === 'hidden' || field.maxLength <= 1) return;
            field.dataset.limitMeter = 'true';
            const meter = document.createElement('div');
            meter.className = 'text-limit-meter';
            meter.id = 'text-limit-' + document.querySelectorAll('.text-limit-meter').length;
            const bar = document.createElement('progress');
            bar.max = field.maxLength;
            bar.setAttribute('aria-hidden', 'true');
            const label = document.createElement('span');
            const notice = document.createElement('span');
            notice.setAttribute('role', 'status');
            meter.append(bar, label, notice);
            // Keep password toggle buttons next to their input.
            const group = field.parentElement;
            if (group.querySelector('[aria-controls="' + field.id + '"]')) group.after(meter);
            else field.after(meter);
            field.setAttribute('aria-describedby', [field.getAttribute('aria-describedby'), meter.id].filter(Boolean).join(' '));
            function update() {
                const count = field.value.length;
                const reached = count >= field.maxLength;
                bar.value = Math.min(count, field.maxLength);
                label.textContent = count + ' / ' + field.maxLength + ' caracteres';
                const message = reached ? 'Límite alcanzado' : '';
                if (notice.textContent !== message) notice.textContent = message;
                meter.classList.toggle('at-limit', reached);
            }
            field.addEventListener('input', update);
            field.addEventListener('change', update);
            field.form?.addEventListener('reset', () => setTimeout(update, 0));
            update();
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', attachTextLimits);
    else attachTextLimits();
})();
</script>
