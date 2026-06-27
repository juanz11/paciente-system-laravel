<template>
  <div class="recipe-outer">
    <!-- Toolbar -->
    <div class="recipe-toolbar">
      <button @click="$emit('back')" class="recipe-back-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6"/>
        </svg>
        Volver
      </button>
      <h2 class="recipe-toolbar-title">Recipe Médico — {{ patient ? patient.nombres + ' ' + patient.apellidos : '' }}</h2>
      <button @click="downloadRecipe" class="recipe-download-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
          <polyline points="7 10 12 15 17 10"/>
          <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Descargar Recipe
      </button>
    </div>

    <div class="recipe-workspace">
      <!-- Recipe Form -->
      <div class="recipe-form-panel">
        <div class="recipe-form-card">
          <h3>Datos del Recipe</h3>
          <div class="recipe-form-group">
            <label>Fecha</label>
            <input type="date" v-model="recipeData.fecha" />
          </div>
          <div class="recipe-form-group">
            <label>Paciente</label>
            <input type="text" :value="patientFullName" readonly class="readonly-input" />
          </div>
          <div class="recipe-form-group">
            <label>Recipe / Indicaciones</label>
            <textarea v-model="recipeData.indicaciones" rows="10" placeholder="Escriba las indicaciones del recipe aquí..."></textarea>
          </div>
          <div v-if="successMsg" class="recipe-success">{{ successMsg }}</div>
        </div>
      </div>

      <!-- Recipe Preview (printable) -->
      <div class="recipe-preview-panel">
        <h3 class="preview-label">Vista previa del recipe</h3>
        <div id="recipe-print-area" class="recipe-paper">
          <!-- Watermark -->
          <div v-if="format.doctor_fondo_agua" class="recipe-watermark">
            <img :src="format.doctor_fondo_agua" alt="fondo" />
          </div>

          <!-- Header -->
          <div class="rp-header">
            <div class="rp-logo-col">
              <img v-if="format.doctor_logo" :src="format.doctor_logo" class="rp-logo" alt="Logo" />
              <div v-else class="rp-logo-empty"></div>
            </div>
            <div class="rp-doctor-col">
              <div class="rp-doctor-name">{{ format.doctor_nombre || 'Nombre del Médico' }}</div>
              <div class="rp-doctor-specialty">{{ format.doctor_especialidad || 'Especialidad' }}</div>
              <div class="rp-doctor-codes">
                <span v-if="format.doctor_codigo_mmps">MMPS: {{ format.doctor_codigo_mmps }}</span>
                <span v-if="format.doctor_codigo_cm">&nbsp;&nbsp;CM: {{ format.doctor_codigo_cm }}</span>
                <span v-if="format.doctor_ci">&nbsp;&nbsp;CI: {{ format.doctor_ci }}</span>
              </div>
            </div>
          </div>

          <div class="rp-divider"></div>

          <!-- Patient & Date -->
          <div class="rp-meta">
            <div class="rp-meta-row">
              <span class="rp-meta-label">Fecha:</span>
              <span class="rp-meta-value">{{ recipeData.fecha ? formatDate(recipeData.fecha) : '___/___/______' }}</span>
            </div>
            <div class="rp-meta-row">
              <span class="rp-meta-label">Paciente:</span>
              <span class="rp-meta-value">{{ patientFullName }}</span>
            </div>
            <div v-if="patient && patient.cedula_identidad" class="rp-meta-row">
              <span class="rp-meta-label">C.I.:</span>
              <span class="rp-meta-value">{{ patient.cedula_identidad }}</span>
            </div>
          </div>

          <!-- Rx Body -->
          <div class="rp-rx-section">
            <div class="rp-rx-symbol">℞</div>
            <div class="rp-rx-body">
              <div class="rp-rx-heading">Recipe / Indicaciones</div>
              <div class="rp-rx-content">
                <template v-if="recipeData.indicaciones">
                  <p v-for="(line, i) in indicacionesLines" :key="i" class="rp-rx-line-text">{{ line }}</p>
                </template>
                <template v-else>
                  <div class="rp-rx-blank-line" v-for="n in 12" :key="n"></div>
                </template>
              </div>
            </div>
          </div>

          <!-- Signature area -->
          <div class="rp-signature-area">
            <div class="rp-signature-line"></div>
            <div class="rp-signature-name">{{ format.doctor_nombre || 'Nombre del Médico' }}</div>
            <div class="rp-signature-specialty">{{ format.doctor_especialidad || '' }}</div>
          </div>

          <!-- Footer -->
          <div class="rp-divider rp-divider-bottom"></div>
          <div class="rp-footer">
            <div v-if="format.doctor_direccion">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                <circle cx="12" cy="10" r="3"/>
              </svg>
              {{ format.doctor_direccion }}
            </div>
            <div v-if="format.doctor_telefono">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.63 3.47 2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.9a16 16 0 0 0 6 6l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
              </svg>
              {{ format.doctor_telefono }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Recipe',
  props: {
    patient: {
      type: Object,
      default: null
    }
  },
  emits: ['back'],
  data() {
    return {
      format: {
        doctor_nombre: '',
        doctor_especialidad: '',
        doctor_codigo_mmps: '',
        doctor_codigo_cm: '',
        doctor_ci: '',
        doctor_logo: null,
        doctor_fondo_agua: null,
        doctor_direccion: '',
        doctor_telefono: '',
      },
      recipeData: {
        fecha: new Date().toISOString().substring(0, 10),
        indicaciones: '',
      },
      successMsg: '',
    };
  },
  computed: {
    patientFullName() {
      if (!this.patient) return '';
      return (this.patient.nombres || '') + ' ' + (this.patient.apellidos || '');
    },
    indicacionesLines() {
      if (!this.recipeData.indicaciones) return [];
      return this.recipeData.indicaciones.split('\n');
    }
  },
  mounted() {
    this.loadFormat();
  },
  methods: {
    async loadFormat() {
      try {
        const res = await axios.get('/api/recipe-format');
        const d = res.data;
        this.format.doctor_nombre = d.doctor_nombre || '';
        this.format.doctor_especialidad = d.doctor_especialidad || '';
        this.format.doctor_codigo_mmps = d.doctor_codigo_mmps || '';
        this.format.doctor_codigo_cm = d.doctor_codigo_cm || '';
        this.format.doctor_ci = d.doctor_ci || '';
        this.format.doctor_logo = d.doctor_logo || null;
        this.format.doctor_fondo_agua = d.doctor_fondo_agua || null;
        this.format.doctor_direccion = d.doctor_direccion || '';
        this.format.doctor_telefono = d.doctor_telefono || '';
      } catch (e) {
        console.error('Error loading format:', e);
      }
    },
    formatDate(dateStr) {
      if (!dateStr) return '';
      const d = new Date(dateStr + 'T00:00:00');
      return d.toLocaleDateString('es-ES');
    },
    downloadRecipe() {
      const style = `
        <style>
          @page { size: A4; margin: 20mm 18mm; }
          body { margin:0; padding:0; font-family: 'Times New Roman', Times, serif; }
          .recipe-paper { position:relative; width:100%; min-height:250mm; padding:0; background:white; overflow:hidden; }
          .recipe-watermark { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:55%; z-index:0; pointer-events:none; }
          .recipe-watermark img { width:100%; opacity:0.1; object-fit:contain; }
          .rp-header { display:flex; align-items:flex-start; gap:16px; margin-bottom:12px; position:relative; z-index:1; }
          .rp-logo { width:80px; height:80px; object-fit:contain; }
          .rp-logo-empty { width:80px; height:80px; }
          .rp-doctor-col { flex:1; }
          .rp-doctor-name { font-size:20px; font-weight:bold; color:#1a3a6e; margin-bottom:2px; }
          .rp-doctor-specialty { font-size:14px; color:#3949ab; font-style:italic; margin-bottom:4px; }
          .rp-doctor-codes { font-size:12px; color:#546e7a; }
          .rp-divider { height:2px; background:linear-gradient(90deg,#1e3c72,#2a5298); margin:10px 0; border-radius:1px; }
          .rp-divider-bottom { margin-top:auto; margin-bottom:8px; }
          .rp-meta { margin-bottom:14px; position:relative; z-index:1; }
          .rp-meta-row { font-size:13px; margin-bottom:4px; }
          .rp-meta-label { font-weight:bold; color:#1e3c72; margin-right:6px; }
          .rp-meta-value { color:#37474f; }
          .rp-rx-section { display:flex; gap:10px; margin-bottom:20px; position:relative; z-index:1; }
          .rp-rx-symbol { font-size:40px; font-weight:bold; color:#1e3c72; line-height:1; margin-top:4px; }
          .rp-rx-body { flex:1; }
          .rp-rx-heading { font-size:13px; font-weight:bold; color:#1e3c72; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px; }
          .rp-rx-line-text { font-size:13px; color:#212121; margin-bottom:6px; line-height:1.5; }
          .rp-rx-blank-line { border-bottom:1px solid #cfd8dc; margin-bottom:12px; height:1px; }
          .rp-signature-area { text-align:right; margin-bottom:16px; position:relative; z-index:1; }
          .rp-signature-line { border-top:1px solid #37474f; width:180px; margin-left:auto; margin-bottom:4px; }
          .rp-signature-name { font-size:13px; font-weight:bold; color:#1e3c72; }
          .rp-signature-specialty { font-size:12px; color:#546e7a; font-style:italic; }
          .rp-footer { text-align:center; font-size:11px; color:#546e7a; line-height:1.8; position:relative; z-index:1; }
        </style>
      `;

      let logoHtml = '';
      if (this.format.doctor_logo) {
        logoHtml = `<img src="${this.format.doctor_logo}" class="rp-logo" alt="logo" crossorigin="anonymous" />`;
      } else {
        logoHtml = `<div class="rp-logo-empty"></div>`;
      }

      let watermarkHtml = '';
      if (this.format.doctor_fondo_agua) {
        watermarkHtml = `<div class="recipe-watermark"><img src="${this.format.doctor_fondo_agua}" alt="fondo" crossorigin="anonymous" /></div>`;
      }

      let codesHtml = '';
      const parts = [];
      if (this.format.doctor_codigo_mmps) parts.push(`MMPS: ${this.format.doctor_codigo_mmps}`);
      if (this.format.doctor_codigo_cm) parts.push(`CM: ${this.format.doctor_codigo_cm}`);
      if (this.format.doctor_ci) parts.push(`CI: ${this.format.doctor_ci}`);
      if (parts.length) codesHtml = `<div class="rp-doctor-codes">${parts.join('&nbsp;&nbsp;')}</div>`;

      let metaHtml = `
        <div class="rp-meta-row"><span class="rp-meta-label">Fecha:</span> <span class="rp-meta-value">${this.recipeData.fecha ? this.formatDate(this.recipeData.fecha) : '___/___/______'}</span></div>
        <div class="rp-meta-row"><span class="rp-meta-label">Paciente:</span> <span class="rp-meta-value">${this.patientFullName}</span></div>
      `;
      if (this.patient && this.patient.cedula_identidad) {
        metaHtml += `<div class="rp-meta-row"><span class="rp-meta-label">C.I.:</span> <span class="rp-meta-value">${this.patient.cedula_identidad}</span></div>`;
      }

      let rxBodyHtml = '';
      if (this.recipeData.indicaciones) {
        rxBodyHtml = this.indicacionesLines.map(l => `<p class="rp-rx-line-text">${l || '&nbsp;'}</p>`).join('');
      } else {
        rxBodyHtml = Array(12).fill('<div class="rp-rx-blank-line"></div>').join('');
      }

      let footerHtml = '';
      if (this.format.doctor_direccion) footerHtml += `<div>${this.format.doctor_direccion}</div>`;
      if (this.format.doctor_telefono) footerHtml += `<div>${this.format.doctor_telefono}</div>`;

      const html = `<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Recipe — ${this.patientFullName}</title>
  ${style}
</head>
<body>
  <div class="recipe-paper">
    ${watermarkHtml}
    <div class="rp-header">
      <div class="rp-logo-col">${logoHtml}</div>
      <div class="rp-doctor-col">
        <div class="rp-doctor-name">${this.format.doctor_nombre || 'Nombre del Médico'}</div>
        <div class="rp-doctor-specialty">${this.format.doctor_especialidad || ''}</div>
        ${codesHtml}
      </div>
    </div>
    <div class="rp-divider"></div>
    <div class="rp-meta">${metaHtml}</div>
    <div class="rp-rx-section">
      <div class="rp-rx-symbol">&#x211E;</div>
      <div class="rp-rx-body">
        <div class="rp-rx-heading">Recipe / Indicaciones</div>
        <div>${rxBodyHtml}</div>
      </div>
    </div>
    <div class="rp-signature-area">
      <div class="rp-signature-line"></div>
      <div class="rp-signature-name">${this.format.doctor_nombre || ''}</div>
      <div class="rp-signature-specialty">${this.format.doctor_especialidad || ''}</div>
    </div>
    <div class="rp-divider rp-divider-bottom"></div>
    <div class="rp-footer">${footerHtml}</div>
  </div>
  <script>window.onload=function(){window.print();}<\/script>
</body>
</html>`;

      const blob = new Blob([html], { type: 'text/html;charset=utf-8' });
      const url = URL.createObjectURL(blob);
      const win = window.open(url, '_blank');
      if (!win) {
        const a = document.createElement('a');
        a.href = url;
        a.download = `recipe_${this.patient ? this.patient.apellidos : 'paciente'}_${this.recipeData.fecha || 'fecha'}.html`;
        a.click();
      }
      setTimeout(() => URL.revokeObjectURL(url), 10000);
    }
  }
};
</script>

<style scoped>
.recipe-outer {
  max-width: 1400px;
  margin: 0 auto;
}

.recipe-toolbar {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 24px;
  flex-wrap: wrap;
}

.recipe-toolbar-title {
  flex: 1;
  color: #1e3c72;
  font-size: 22px;
  font-weight: 700;
}

.recipe-back-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  background: white;
  color: #1e3c72;
  border: 2px solid #c5cae9;
  padding: 10px 18px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.recipe-back-btn:hover {
  background: #e8eaf6;
}

.recipe-download-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  color: white;
  border: none;
  padding: 10px 22px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.recipe-download-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(30,60,114,0.3);
}

.recipe-workspace {
  display: grid;
  grid-template-columns: 340px 1fr;
  gap: 28px;
  align-items: start;
}

@media (max-width: 900px) {
  .recipe-workspace {
    grid-template-columns: 1fr;
  }
}

.recipe-form-card {
  background: white;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.recipe-form-card h3 {
  color: #1e3c72;
  font-size: 17px;
  font-weight: 600;
  margin-bottom: 18px;
}

.recipe-form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 16px;
}

.recipe-form-group label {
  color: #1e3c72;
  font-weight: 600;
  font-size: 13px;
}

.recipe-form-group input,
.recipe-form-group textarea {
  padding: 10px 14px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.3s;
  font-family: inherit;
  resize: vertical;
}

.recipe-form-group input:focus,
.recipe-form-group textarea:focus {
  outline: none;
  border-color: #2a5298;
  box-shadow: 0 0 0 3px rgba(42,82,152,0.1);
}

.readonly-input {
  background: #f5f7fa;
  color: #546e7a;
}

.recipe-success {
  color: #2e7d32;
  font-size: 13px;
  font-weight: 600;
  margin-top: 8px;
}

/* Preview panel */
.preview-label {
  color: #1e3c72;
  font-size: 15px;
  font-weight: 600;
  margin-bottom: 12px;
}

.recipe-paper {
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.12);
  padding: 28px 32px;
  min-height: 600px;
  position: relative;
  overflow: hidden;
  font-family: 'Times New Roman', Times, serif;
}

.recipe-watermark {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 55%;
  z-index: 0;
  pointer-events: none;
}

.recipe-watermark img {
  width: 100%;
  opacity: 0.1;
  object-fit: contain;
}

.rp-header {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 12px;
  position: relative;
  z-index: 1;
}

.rp-logo-col {
  flex-shrink: 0;
}

.rp-logo {
  width: 80px;
  height: 80px;
  object-fit: contain;
  border-radius: 4px;
}

.rp-logo-empty {
  width: 80px;
  height: 80px;
}

.rp-doctor-col {
  flex: 1;
}

.rp-doctor-name {
  font-size: 20px;
  font-weight: bold;
  color: #1a3a6e;
  margin-bottom: 2px;
}

.rp-doctor-specialty {
  font-size: 14px;
  color: #3949ab;
  font-style: italic;
  margin-bottom: 4px;
}

.rp-doctor-codes {
  font-size: 12px;
  color: #546e7a;
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.rp-divider {
  height: 2px;
  background: linear-gradient(90deg, #1e3c72 0%, #2a5298 100%);
  margin: 10px 0;
  border-radius: 1px;
  position: relative;
  z-index: 1;
}

.rp-divider-bottom {
  margin-top: 16px;
  margin-bottom: 8px;
}

.rp-meta {
  margin-bottom: 14px;
  position: relative;
  z-index: 1;
}

.rp-meta-row {
  font-size: 13px;
  margin-bottom: 4px;
}

.rp-meta-label {
  font-weight: bold;
  color: #1e3c72;
  margin-right: 6px;
}

.rp-meta-value {
  color: #37474f;
}

.rp-rx-section {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  position: relative;
  z-index: 1;
}

.rp-rx-symbol {
  font-size: 40px;
  font-weight: bold;
  color: #1e3c72;
  line-height: 1;
  margin-top: 4px;
}

.rp-rx-body {
  flex: 1;
}

.rp-rx-heading {
  font-size: 13px;
  font-weight: bold;
  color: #1e3c72;
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.rp-rx-line-text {
  font-size: 13px;
  color: #212121;
  margin-bottom: 6px;
  line-height: 1.5;
  min-height: 18px;
}

.rp-rx-blank-line {
  border-bottom: 1px solid #cfd8dc;
  margin-bottom: 12px;
  height: 1px;
}

.rp-signature-area {
  text-align: right;
  margin-bottom: 16px;
  position: relative;
  z-index: 1;
}

.rp-signature-line {
  border-top: 1px solid #37474f;
  width: 180px;
  margin-left: auto;
  margin-bottom: 4px;
}

.rp-signature-name {
  font-size: 13px;
  font-weight: bold;
  color: #1e3c72;
}

.rp-signature-specialty {
  font-size: 12px;
  color: #546e7a;
  font-style: italic;
}

.rp-footer {
  text-align: center;
  font-size: 11px;
  color: #546e7a;
  line-height: 1.8;
  position: relative;
  z-index: 1;
}
</style>
