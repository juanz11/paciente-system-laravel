<template>
  <div class="evolution-container">
    <div class="evolution-header">
      <h1 class="evolution-title">Evolución de Pacientes</h1>
      <button @click="goBack" class="back-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="19" y1="12" x2="5" y2="12"/>
          <polyline points="12 19 5 12 12 5"/>
        </svg>
        Volver
      </button>
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
              <span class="timeline-date">{{ formatDate(history.created_at) }}</span>
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

              <div v-if="history.evolucion" class="detail-section evolution-section">
                <h4>Evolución</h4>
                <p>{{ history.evolucion }}</p>
              </div>
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
      loading: false
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
}

.timeline-header h3 {
  color: #1e3c72;
  font-size: 18px;
  font-weight: 700;
}

.timeline-date {
  color: #6c757d;
  font-size: 14px;
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
