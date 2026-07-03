<template>
  <div class="evolution-container">
    <div class="evolution-header">
      <h1 class="evolution-title">Evolución de Pacientes</h1>
      <div class="evolution-header-actions">
        <button v-if="patient && sortedHistories.length" @click="downloadEvolution" class="download-pdf-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
          </svg>
          Descargar PDF
        </button>
        <button @click="goBack" class="back-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
          </svg>
          Volver
        </button>
      </div>
    </div>

    <div v-if="loading" class="loading">Cargando...</div>

    <div v-else-if="!patient" class="no-patient">
      <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      <p>Paciente no encontrado</p>
    </div>

    <div v-else class="evolution-content">
      <div class="patient-info-card">
        <div class="patient-avatar">
          {{ patient.nombres.charAt(0) }}{{ patient.apellidos.charAt(0) }}
        </div>
        <div class="patient-details">
          <h2>{{ patient.nombres }} {{ patient.apellidos }}</h2>
          <p>Cédula: {{ patient.cedula_identidad || 'N/A' }}</p>
          <p>Edad: {{ patient.edad || 'N/A' }}</p>
        </div>
      </div>

      <div v-if="!patient.medical_histories || patient.medical_histories.length === 0" class="no-consultations">
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" y1="13" x2="8" y2="13"/>
          <line x1="16" y1="17" x2="8" y2="17"/>
          <polyline points="10 9 9 9 8 9"/>
        </svg>
        <p>Este paciente no tiene consultas médicas registradas</p>
      </div>

      <div v-else class="timeline">
        <div v-for="(history, index) in sortedHistories" :key="history.id" class="timeline-item">
          <div class="timeline-marker">
            <div class="timeline-number">{{ index + 1 }}</div>
          </div>
          <div class="timeline-content">
            <div class="timeline-header">
              <h3>Consulta #{{ index + 1 }}</h3>
              <div class="timeline-header-right">
                <span class="timeline-date">{{ formatDate(history.created_at) }}</span>
                <button class="detail-toggle-btn" @click="toggleDetail(history.id)">
                  <svg v-if="!expandedIds.includes(history.id)" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    <line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/>
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    <line x1="8" y1="11" x2="14" y2="11"/>
                  </svg>
                  {{ expandedIds.includes(history.id) ? 'Ocultar detalle' : 'Ver detalle' }}
                </button>
              </div>
            </div>

            <div class="timeline-details">
              <div v-if="history.motivo_consulta" class="detail-section">
                <h4>Motivo de Consulta</h4>
                <p>{{ history.motivo_consulta }}</p>
              </div>

              <div v-if="history.diagnostico" class="detail-section">
                <h4>Diagnóstico</h4>
                <p>{{ history.diagnostico }}</p>
              </div>

              <div v-if="history.plan_tratamiento" class="detail-section">
                <h4>Plan de Tratamiento</h4>
                <p>{{ history.plan_tratamiento }}</p>
              </div>

              <!-- Detalle expandible -->
              <template v-if="expandedIds.includes(history.id)">
                <div v-if="history.enfermedad_actual" class="detail-section">
                  <h4>Enfermedad Actual</h4>
                  <p>{{ history.enfermedad_actual }}</p>
                </div>

                <div v-if="history.antecedentes_familiares" class="detail-section">
                  <h4>Antecedentes Familiares</h4>
                  <p>{{ history.antecedentes_familiares }}</p>
                </div>

                <div v-if="history.antecedentes_personales" class="detail-section">
                  <h4>Antecedentes Personales</h4>
                  <p>{{ history.antecedentes_personales }}</p>
                </div>

                <div v-if="history.examen_neurologico" class="detail-section">
                  <h4>Examen Neurológico</h4>
                  <p>{{ history.examen_neurologico }}</p>
                </div>

                <div v-if="history.examen_laboratorio" class="detail-section">
                  <h4>Examen de Laboratorio</h4>
                  <p>{{ history.examen_laboratorio }}</p>
                </div>

                <div v-if="history.examen_complementario" class="detail-section">
                  <h4>Examen Complementario</h4>
                  <p>{{ history.examen_complementario }}</p>
                </div>

                <div v-if="history.observacion" class="detail-section">
                  <h4>Observación</h4>
                  <p>{{ history.observacion }}</p>
                </div>

                <div v-if="history.evolucion" class="detail-section evolution-section">
                  <h4>Evolución</h4>
                  <p>{{ history.evolucion }}</p>
                </div>
              </template>
              <template v-else>
                <div v-if="history.evolucion" class="detail-section evolution-section">
                  <h4>Evolución</h4>
                  <p>{{ history.evolucion }}</p>
                </div>
              </template>
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
  name: 'Evolution',
  props: {
    patientId: {
      type: [String, Number],
      required: true
    }
  },
  data() {
    return {
      patient: null,
      loading: false,
      expandedIds: []
    };
  },
  computed: {
    sortedHistories() {
      if (!this.patient || !this.patient.medical_histories) return [];
      return [...this.patient.medical_histories].sort((a, b) => 
        new Date(a.created_at) - new Date(b.created_at)
      );
    }
  },
  mounted() {
    if (this.patientId) {
      this.loadPatientEvolution();
    }
  },
  methods: {
    async loadPatientEvolution() {
      this.loading = true;
      try {
        const response = await axios.get(`/api/patients/${this.patientId}`);
        this.patient = response.data;
      } catch (error) {
        console.error('Error loading patient evolution:', error);
        alert('Error al cargar la evolución del paciente');
      } finally {
        this.loading = false;
      }
    },
    formatDate(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    goBack() {
      this.$emit('back');
    },
    downloadEvolution() {
      const p = this.patient;
      const histories = this.sortedHistories;

      const fields = [
        { key: 'motivo_consulta',        label: 'Motivo de Consulta' },
        { key: 'enfermedad_actual',       label: 'Enfermedad Actual' },
        { key: 'antecedentes_familiares', label: 'Antecedentes Familiares' },
        { key: 'antecedentes_personales', label: 'Antecedentes Personales' },
        { key: 'examen_neurologico',      label: 'Examen Neurológico' },
        { key: 'examen_laboratorio',      label: 'Examen de Laboratorio' },
        { key: 'examen_complementario',   label: 'Examen Complementario' },
        { key: 'diagnostico',             label: 'Diagnóstico' },
        { key: 'plan_tratamiento',        label: 'Plan de Tratamiento' },
        { key: 'observacion',             label: 'Observación' },
        { key: 'evolucion',               label: 'Evolución', highlight: true },
      ];

      const consultasHtml = histories.map((h, i) => {
        const sectionsHtml = fields
          .filter(f => h[f.key])
          .map(f => `
            <div class="section${f.highlight ? ' evo-section' : ''}">
              <div class="section-label">${f.label}</div>
              <div class="section-value">${h[f.key].replace(/\n/g, '<br>')}</div>
            </div>`)
          .join('');

        return `
          <div class="consulta">
            <div class="consulta-header">
              <span class="consulta-num">Consulta #${i + 1}</span>
              <span class="consulta-date">${this.formatDate(h.created_at)}</span>
            </div>
            <div class="consulta-body">${sectionsHtml || '<p style="color:#999">Sin datos registrados</p>'}</div>
          </div>`;
      }).join('');

      const html = `<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Evolución — ${p.nombres} ${p.apellidos}</title>
  <style>
    @page { size: A4; margin: 18mm 20mm; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 13px; color: #212121; }
    .doc-header { border-bottom: 3px solid #1e3c72; padding-bottom: 14px; margin-bottom: 20px; }
    .doc-title { font-size: 22px; font-weight: bold; color: #1e3c72; margin-bottom: 6px; }
    .patient-row { display: flex; gap: 24px; flex-wrap: wrap; font-size: 13px; color: #444; }
    .patient-row span { margin-right: 6px; }
    .patient-row strong { color: #1e3c72; }
    .consulta { margin-bottom: 24px; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; page-break-inside: avoid; }
    .consulta-header { background: #1e3c72; color: white; display: flex; justify-content: space-between; align-items: center; padding: 10px 16px; }
    .consulta-num { font-weight: bold; font-size: 14px; }
    .consulta-date { font-size: 12px; opacity: 0.85; }
    .consulta-body { padding: 14px 16px; display: flex; flex-direction: column; gap: 12px; }
    .section { border-left: 3px solid #c5cae9; padding-left: 10px; }
    .section-label { font-size: 11px; font-weight: bold; color: #1e3c72; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 4px; }
    .section-value { font-size: 13px; color: #333; line-height: 1.6; }
    .evo-section { border-left-color: #7b1fa2; background: #faf5ff; padding: 8px 10px; border-radius: 4px; }
    .evo-section .section-label { color: #7b1fa2; }
    .evo-section .section-value { color: #4a148c; }
  </style>
</head>
<body>
  <div class="doc-header">
    <div class="doc-title">Evolución de Paciente</div>
    <div class="patient-row">
      <span><strong>Paciente:</strong> ${p.nombres} ${p.apellidos}</span>
      <span><strong>Cédula:</strong> ${p.cedula_identidad || 'N/A'}</span>
      <span><strong>Edad:</strong> ${p.edad || 'N/A'}</span>
      <span><strong>Total consultas:</strong> ${histories.length}</span>
    </div>
  </div>
  ${consultasHtml}
  <script>window.onload=function(){window.print();}<\/script>
</body>
</html>`;

      const blob = new Blob([html], { type: 'text/html;charset=utf-8' });
      const url = URL.createObjectURL(blob);
      const win = window.open(url, '_blank');
      if (!win) {
        const a = document.createElement('a');
        a.href = url;
        a.download = `evolucion_${p.apellidos}_${p.nombres}.html`;
        a.click();
      }
      setTimeout(() => URL.revokeObjectURL(url), 10000);
    },
    toggleDetail(id) {
      const idx = this.expandedIds.indexOf(id);
      if (idx === -1) {
        this.expandedIds.push(id);
      } else {
        this.expandedIds.splice(idx, 1);
      }
    }
  }
};
</script>

<style scoped>
.evolution-container {
  max-width: 1200px;
  margin: 0 auto;
}

.evolution-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 32px;
  flex-wrap: wrap;
  gap: 12px;
}

.evolution-header-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.download-pdf-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.download-pdf-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(30,60,114,0.3);
}

.evolution-title {
  color: #1e3c72;
  font-size: 28px;
  font-weight: 700;
}

.back-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: white;
  color: #1e3c72;
  border: 2px solid #1e3c72;
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.back-btn:hover {
  background: #1e3c72;
  color: white;
}

.loading {
  text-align: center;
  padding: 60px;
  color: #6c757d;
  font-size: 16px;
}

.no-patient,
.no-consultations {
  text-align: center;
  padding: 60px;
  color: #ccc;
}

.no-patient p,
.no-consultations p {
  margin-top: 16px;
  font-size: 16px;
}

.patient-info-card {
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  color: white;
  border-radius: 16px;
  padding: 24px;
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 32px;
  box-shadow: 0 4px 12px rgba(30, 60, 114, 0.3);
}

.patient-avatar {
  width: 80px;
  height: 80px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  font-weight: 700;
}

.patient-details h2 {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 8px;
}

.patient-details p {
  font-size: 14px;
  opacity: 0.9;
  margin-bottom: 4px;
}

.timeline {
  position: relative;
  padding-left: 40px;
}

.timeline::before {
  content: '';
  position: absolute;
  left: 15px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: linear-gradient(to bottom, #1e3c72, #2a5298);
}

.timeline-item {
  position: relative;
  margin-bottom: 32px;
}

.timeline-marker {
  position: absolute;
  left: -40px;
  top: 0;
  width: 32px;
  height: 32px;
}

.timeline-number {
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
  box-shadow: 0 2px 8px rgba(30, 60, 114, 0.3);
}

.timeline-content {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  border-left: 4px solid #1e3c72;
}

.timeline-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 16px;
  border-bottom: 1px solid #e0e0e0;
  flex-wrap: wrap;
  gap: 8px;
}

.timeline-header h3 {
  color: #1e3c72;
  font-size: 18px;
  font-weight: 700;
}

.timeline-header-right {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.timeline-date {
  color: #6c757d;
  font-size: 14px;
}

.detail-toggle-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  background: #e8eaf6;
  color: #1e3c72;
  border: none;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.detail-toggle-btn:hover {
  background: #c5cae9;
}

.timeline-details {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.detail-section h4 {
  color: #1e3c72;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 8px;
}

.detail-section p {
  color: #333;
  font-size: 14px;
  line-height: 1.6;
  white-space: pre-wrap;
}

.evolution-section {
  background: #f3e5f5;
  padding: 16px;
  border-radius: 8px;
  border-left: 4px solid #7b1fa2;
}

.evolution-section h4 {
  color: #7b1fa2;
}

.evolution-section p {
  color: #4a148c;
}
</style>
