// resources/js/app.js
import './bootstrap';
import '../css/app.css';

import Alpine from 'alpinejs';
window.Alpine = Alpine;

window.reservaSheet = function () {
  return {
    reservaOpen: false,
    loading: false,
    buscado: false,
    habitaciones: [],
    noches: 0,
    form: { check_in: '', check_out: '', huespedes: 1 },

    init() {
      this.$watch('reservaOpen', (open) => {
        if (open) {
          this.$nextTick(() => this.reinitCalendars());
        } else {
          this.destroyCalendars();
        }
      });
    },

    waitForFp(cb) {
      if (window.flatpickr) return cb();
      let tries = 0;
      const id = setInterval(() => {
        tries++;
        if (window.flatpickr || tries > 50) {
          clearInterval(id);
          if (window.flatpickr) cb();
        }
      }, 100);
    },

    reinitCalendars() {
      this.waitForFp(() => {
        if (!this._fpIn  && this.$refs.checkIn)  this.fpInit(this.$refs.checkIn,  'in');
        if (!this._fpOut && this.$refs.checkOut) this.fpInit(this.$refs.checkOut, 'out');
      });
    },

    fpInit(el, which) {
      const common = {
        locale: 'es',
        altInput: true,
        altFormat: 'd M Y',
        dateFormat: 'Y-m-d',
        disableMobile: true,
        appendTo: document.body,
      };

      if (which === 'in') {
        if (this._fpIn) { this._fpIn.destroy(); this._fpIn = null; }
        this._fpIn = flatpickr(el, {
          ...common,
          minDate: 'today',
          defaultDate: this.form.check_in || null,
          onChange: (selectedDates, dateStr) => {
            this.form.check_in = dateStr;
            if (this._fpOut) {
              const next = dateStr
                ? new Date(new Date(dateStr).getTime() + 86400000)
                : 'today';
              this._fpOut.set('minDate', next);
              if (this.form.check_out && selectedDates[0]) {
                const out = new Date(this.form.check_out);
                if (out <= selectedDates[0]) {
                  this.form.check_out = '';
                  this._fpOut.clear();
                }
              }
            }
          }
        });
        el.addEventListener('focus', () => this._fpIn && this._fpIn.open());
      } else {
        if (this._fpOut) { this._fpOut.destroy(); this._fpOut = null; }
        this._fpOut = flatpickr(el, {
          ...common,
          minDate: this.form.check_in
            ? new Date(new Date(this.form.check_in).getTime() + 86400000)
            : 'today',
          defaultDate: this.form.check_out || null,
          onChange: (selectedDates, dateStr) => { this.form.check_out = dateStr; }
        });
        el.addEventListener('focus', () => this._fpOut && this._fpOut.open());
      }
    },

    destroyCalendars() {
      if (this._fpIn)  { this._fpIn.destroy();  this._fpIn  = null; }
      if (this._fpOut) { this._fpOut.destroy(); this._fpOut = null; }
    },

    closeSheet() {
      this.reservaOpen = false;
      this.buscado = false;
      this.habitaciones = [];
      this.noches = 0;
      this.form = { check_in: '', check_out: '', huespedes: 1 };
      this.destroyCalendars();
    },

    money(n) {
      try {
        return new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', maximumFractionDigits: 0 }).format(n ?? 0);
      } catch { return '$' + (n ?? 0); }
    },

    calcNoches() {
      if (!this.form.check_in || !this.form.check_out) return 0;
      const d1 = new Date(this.form.check_in), d2 = new Date(this.form.check_out);
      return Math.max(0, Math.floor((d2 - d1) / (1000*60*60*24)));
    },

    total(p) { return (this.noches || 0) * (p ?? 0); },

    async buscarDisponibilidad() {
      this.loading = true; this.buscado = false; this.habitaciones = [];
      this.noches = this.calcNoches();

      if (!this.form.check_in || !this.form.check_out || this.noches <= 0) {
        this.loading = false;
        alert('Revisa las fechas (check-out debe ser posterior a check-in).');
        return;
      }

      const qs = new URLSearchParams({
        check_in: this.form.check_in,
        check_out: this.form.check_out,
        huespedes: this.form.huespedes || 1,
      });

      try {
        const res = await fetch(`${window.routeReservasBuscar}?` + qs.toString(), {
          headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error();
        const data = await res.json();
        this.habitaciones = data.habitaciones ?? [];
      } catch (e) {
        console.error(e);
        alert('No se pudo cargar la disponibilidad. Intenta nuevamente.');
      } finally {
        this.loading = false;
        this.buscado = true;
      }
    }
  };
};

Alpine.start();
