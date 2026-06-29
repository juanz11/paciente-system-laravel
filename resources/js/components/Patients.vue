<template>
  <div class="patients-container">
    <div class="patients-header">
      <h1 class="patients-title">Gestión de Pacientes</h1>
      <button @click="showCreateModal = true" class="create-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Nuevo Paciente
      </button>
    </div>

    <div class="search-bar">
      <input
        type="text"
        v-model="searchQuery"
        @input="searchPatients"
        placeholder="Buscar por nombre..."
        class="search-input"
      />
    </div>

    <div v-if="loading" class="loading">Cargando...</div>

    <div v-else class="patients-grid">
      <div v-for="patient in patients" :key="patient.id" class="patient-card">
        <div class="patient-header">
          <div class="patient-avatar">
            {{ patient.nombres.charAt(0) }}{{ patient.apellidos.charAt(0) }}
          </div>
          <div class="patient-info">
            <h3>{{ patient.nombres }} {{ patient.apellidos }}</h3>
            <p>{{ patient.cedula_identidad || 'Sin cédula' }}</p>
          </div>
        </div>
        <div class="patient-details">
          <div class="detail-item">
            <span class="detail-label">Edad:</span>
            <span class="detail-value">{{ patient.edad || 'N/A' }}</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Teléfono:</span>
            <span class="detail-value">{{ patient.telefono || 'N/A' }}</span>
          </div>
          <div class="detail-item">
            <span class="detail-label">Ciudad:</span>
            <span class="detail-value">{{ patient.ciudad || 'N/A' }}</span>
          </div>
        </div>
        <div class="patient-actions">
          <button @click="viewPatient(patient)" class="action-btn view-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
            Ver
          </button>
          <button @click="editPatient(patient)" class="action-btn edit-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Editar
          </button>
          <button @click="openConsultationModalFromCard(patient)" class="action-btn consult-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
            </svg>
            Consulta
          </button>
          <button @click="viewEvolution(patient)" class="action-btn evolution-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
              <polyline points="17 6 23 6 23 12"/>
            </svg>
            Evolución
          </button>
          <button @click="openRecipe(patient)" class="action-btn recipe-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
              <line x1="16" y1="13" x2="8" y2="13"/>
              <line x1="16" y1="17" x2="8" y2="17"/>
              <polyline points="10 9 9 9 8 9"/>
            </svg>
            Recipe
          </button>
          <button @click="deletePatient(patient.id)" class="action-btn delete-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"/>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
            </svg>
            Eliminar
          </button>
        </div>
      </div>

      <div v-if="patients.length === 0" class="no-patients">
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        <p>No hay pacientes registrados</p>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || showEditModal" class="modal-overlay">
      <div class="modal-content">
        <div class="modal-header">
          <h2>{{ showEditModal ? 'Editar Paciente' : 'Nuevo Paciente' }}</h2>
          <button @click="closeModal" class="close-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>
        <form @submit.prevent="savePatient" class="modal-form">
          <div class="form-section">
            <h3>Datos Personales</h3>
            <div class="form-grid">
              <div class="form-group">
                <label>Nombres *</label>
                <input type="text" v-model="form.nombres" required />
              </div>
              <div class="form-group">
                <label>Apellidos *</label>
                <input type="text" v-model="form.apellidos" required />
              </div>
              <div class="form-group">
                <label>Fecha de Nacimiento *</label>
                <input type="date" v-model="form.fecha_nacimiento" required />
              </div>
              <div class="form-group">
                <label>Sexo *</label>
                <select v-model="form.sexo" required>
                  <option value="">Seleccionar</option>
                  <option value="Masculino">Masculino</option>
                  <option value="Femenino">Femenino</option>
                </select>
              </div>
              <div class="form-group">
                <label>Lugar de Nacimiento</label>
                <input type="text" v-model="form.lugar_nacimiento" />
              </div>
              <div class="form-group">
                <label>Nacionalidad</label>
                <input type="text" v-model="form.nacionalidad" />
              </div>
              <div class="form-group">
                <label>Cédula de Identidad</label>
                <input type="text" v-model="form.cedula_identidad" />
              </div>
              <div class="form-group">
                <label>Edad</label>
                <input type="number" v-model="form.edad" />
              </div>
              <div class="form-group">
                <label>Teléfono</label>
                <input type="text" v-model="form.telefono" />
              </div>
              <div class="form-group">
                <label>Ciudad</label>
                <input type="text" v-model="form.ciudad" />
              </div>
              <div class="form-group">
                <label>Estado</label>
                <input type="text" v-model="form.estado" />
              </div>
              <div class="form-group full-width">
                <label>Dirección</label>
                <textarea v-model="form.direccion" rows="2"></textarea>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h3>Datos de Emergencia</h3>
            <div class="form-grid">
              <div class="form-group">
                <label>Nombre del Contacto</label>
                <input type="text" v-model="form.emergencia_nombre" />
              </div>
              <div class="form-group">
                <label>Parentesco</label>
                <input type="text" v-model="form.emergencia_parentesco" />
              </div>
              <div class="form-group">
                <label>Teléfono de Emergencia</label>
                <input type="text" v-model="form.emergencia_telefono" />
              </div>
              <div class="form-group full-width">
                <label>Dirección de Emergencia</label>
                <textarea v-model="form.emergencia_direccion" rows="2"></textarea>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" @click="closeModal" class="cancel-btn">Cancelar</button>
            <button type="submit" class="save-btn" :disabled="saving">
              {{ saving ? 'Guardando...' : 'Guardar' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- View Modal -->
    <div v-if="showViewModal" class="modal-overlay">
      <div class="modal-content large-modal">
        <div class="modal-header">
          <h2>Detalle del Paciente</h2>
          <button @click="showViewModal = false" class="close-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>
        <div class="patient-detail">
          <div class="detail-section">
            <h3>Datos Personales</h3>
            <div class="detail-grid">
              <div class="detail-row"><span>Nombre:</span> <strong>{{ selectedPatient.nombres }} {{ selectedPatient.apellidos }}</strong></div>
              <div class="detail-row"><span>Fecha de Nacimiento:</span> <strong>{{ formatDate(selectedPatient.fecha_nacimiento) }}</strong></div>
              <div class="detail-row"><span>Sexo:</span> <strong>{{ selectedPatient.sexo }}</strong></div>
              <div class="detail-row"><span>Cédula:</span> <strong>{{ selectedPatient.cedula_identidad || 'N/A' }}</strong></div>
              <div class="detail-row"><span>Edad:</span> <strong>{{ selectedPatient.edad || 'N/A' }}</strong></div>
              <div class="detail-row"><span>Teléfono:</span> <strong>{{ selectedPatient.telefono || 'N/A' }}</strong></div>
              <div class="detail-row"><span>Ciudad:</span> <strong>{{ selectedPatient.ciudad || 'N/A' }}</strong></div>
              <div class="detail-row"><span>Estado:</span> <strong>{{ selectedPatient.estado || 'N/A' }}</strong></div>
              <div class="detail-row full-width"><span>Dirección:</span> <strong>{{ selectedPatient.direccion || 'N/A' }}</strong></div>
            </div>
          </div>

          <div class="detail-section">
            <h3>Contacto de Emergencia</h3>
            <div class="detail-grid">
              <div class="detail-row"><span>Nombre:</span> <strong>{{ selectedPatient.emergencia_nombre || 'N/A' }}</strong></div>
              <div class="detail-row"><span>Parentesco:</span> <strong>{{ selectedPatient.emergencia_parentesco || 'N/A' }}</strong></div>
              <div class="detail-row"><span>Teléfono:</span> <strong>{{ selectedPatient.emergencia_telefono || 'N/A' }}</strong></div>
              <div class="detail-row full-width"><span>Dirección:</span> <strong>{{ selectedPatient.emergencia_direccion || 'N/A' }}</strong></div>
            </div>
          </div>

          <div class="detail-section">
            <h3>Historial Médico</h3>
            <button @click="openConsultationModal" class="add-history-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
              </svg>
              Agregar Consulta
            </button>
            <div v-if="selectedPatient.medical_histories && selectedPatient.medical_histories.length > 0" class="history-list">
              <div v-for="history in selectedPatient.medical_histories" :key="history.id" class="history-item">
                <div class="history-content">
                  <div class="history-date">{{ formatDate(history.created_at) }}</div>
                  <div class="history-diagnosis">{{ history.diagnostico || 'Sin diagnóstico' }}</div>
                </div>
                <button @click="deleteMedicalHistory(history.id)" class="delete-history-btn">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                  </svg>
                </button>
              </div>
            </div>
            <div v-else class="no-history">
              No hay consultas registradas
            </div>
          </div>

          <div class="detail-section">
            <h3>Historial de Recipes</h3>
            <button @click="openRecipeFromView" class="add-history-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
              </svg>
              Crear Recipe
            </button>
            <div v-if="recipes.length > 0" class="history-list">
              <div v-for="recipe in recipes" :key="recipe.id" class="history-item">
                <div class="history-content">
                  <div class="history-date">{{ formatDate(recipe.fecha) }}</div>
                  <div class="history-diagnosis">{{ recipe.indicaciones.substring(0, 60) }}{{ recipe.indicaciones.length > 60 ? '...' : '' }}</div>
                </div>
                <button @click="deleteRecipe(recipe.id)" class="delete-history-btn">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                  </svg>
                </button>
              </div>
            </div>
            <div v-else class="no-history">
              No hay recipes registrados
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Consultation Modal -->
    <div v-if="showConsultationModal" class="modal-overlay">
      <div class="modal-content large-modal">
        <div class="modal-header">
          <h2>Nueva Consulta Médica</h2>
          <button @click="closeConsultationModal" class="close-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>
        <form @submit.prevent="saveConsultation" class="modal-form">
          <div class="form-section">
            <h3>Información de la Consulta</h3>
            <div class="form-grid">
              <div class="form-group full-width">
                <label>Motivo de Consulta</label>
                <textarea v-model="consultationForm.motivo_consulta" rows="3" placeholder="Describa el motivo de la consulta"></textarea>
              </div>
              <div class="form-group full-width">
                <label>Enfermedad Actual</label>
                <textarea v-model="consultationForm.enfermedad_actual" rows="3" placeholder="Describa la enfermedad actual"></textarea>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h3>Antecedentes</h3>
            <div class="form-grid">
              <div class="form-group full-width">
                <label>Antecedentes Familiares</label>
                <textarea v-model="consultationForm.antecedentes_familiares" rows="3" placeholder="Antecedentes médicos familiares"></textarea>
              </div>
              <div class="form-group full-width">
                <label>Antecedentes Personales</label>
                <textarea v-model="consultationForm.antecedentes_personales" rows="3" placeholder="Antecedentes médicos personales"></textarea>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h3>Exámenes</h3>
            <div class="form-grid">
              <div class="form-group full-width">
                <label>Examen Neurológico</label>
                <textarea v-model="consultationForm.examen_neurologico" rows="3" placeholder="Resultados del examen neurológico"></textarea>
              </div>
              <div class="form-group full-width">
                <label>Examen de Laboratorio</label>
                <textarea v-model="consultationForm.examen_laboratorio" rows="3" placeholder="Resultados de laboratorio"></textarea>
              </div>
              <div class="form-group full-width">
                <label>Examen Complementario</label>
                <textarea v-model="consultationForm.examen_complementario" rows="3" placeholder="Resultados de exámenes complementarios"></textarea>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h3>Diagnóstico y Tratamiento</h3>
            <div class="form-grid">
              <div class="form-group full-width">
                <label>Diagnóstico</label>
                <textarea v-model="consultationForm.diagnostico" rows="3" placeholder="Diagnóstico del paciente"></textarea>
              </div>
              <div class="form-group full-width">
                <label>Plan de Tratamiento</label>
                <textarea v-model="consultationForm.plan_tratamiento" rows="3" placeholder="Plan de tratamiento a seguir"></textarea>
              </div>
              <div class="form-group full-width">
                <label>Observaciones</label>
                <textarea v-model="consultationForm.observacion" rows="3" placeholder="Observaciones adicionales"></textarea>
              </div>
              <div class="form-group full-width">
                <label>Evolución (Opcional)</label>
                <textarea v-model="consultationForm.evolucion" rows="3" placeholder="Evolución del paciente entre consultas"></textarea>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" @click="closeConsultationModal" class="cancel-btn">Cancelar</button>
            <button type="submit" class="save-btn" :disabled="savingConsultation">
              {{ savingConsultation ? 'Guardando...' : 'Guardar Consulta' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Patients',
  data() {
    return {
      patients: [],
      loading: false,
      searchQuery: '',
      showCreateModal: false,
      showEditModal: false,
      showViewModal: false,
      showConsultationModal: false,
      selectedPatient: null,
      saving: false,
      savingConsultation: false,
      recipes: [],
      form: {
        nombres: '',
        apellidos: '',
        fecha_nacimiento: '',
        sexo: '',
        lugar_nacimiento: '',
        nacionalidad: '',
        cedula_identidad: '',
        edad: '',
        telefono: '',
        ciudad: '',
        estado: '',
        direccion: '',
        emergencia_nombre: '',
        emergencia_parentesco: '',
        emergencia_telefono: '',
        emergencia_direccion: ''
      },
      consultationForm: {
        patient_id: null,
        motivo_consulta: '',
        enfermedad_actual: '',
        antecedentes_familiares: '',
        antecedentes_personales: '',
        examen_neurologico: '',
        examen_laboratorio: '',
        examen_complementario: '',
        diagnostico: '',
        plan_tratamiento: '',
        observacion: '',
        evolucion: ''
      }
    };
  },
  mounted() {
    this.loadPatients();
  },
  methods: {
    async loadPatients() {
      this.loading = true;
      try {
        const response = await axios.get('/api/patients');
        this.patients = response.data;
      } catch (error) {
        console.error('Error loading patients:', error);
      } finally {
        this.loading = false;
      }
    },
    async searchPatients() {
      if (this.searchQuery.trim()) {
        try {
          const response = await axios.get(`/api/patients?search=${this.searchQuery}`);
          this.patients = response.data;
        } catch (error) {
          console.error('Error searching patients:', error);
        }
      } else {
        this.loadPatients();
      }
    },
    closeModal() {
      this.showCreateModal = false;
      this.showEditModal = false;
      this.resetForm();
    },
    resetForm() {
      this.form = {
        nombres: '',
        apellidos: '',
        fecha_nacimiento: '',
        sexo: '',
        lugar_nacimiento: '',
        nacionalidad: '',
        cedula_identidad: '',
        edad: '',
        telefono: '',
        ciudad: '',
        estado: '',
        direccion: '',
        emergencia_nombre: '',
        emergencia_parentesco: '',
        emergencia_telefono: '',
        emergencia_direccion: ''
      };
      this.selectedPatient = null;
    },
    async savePatient() {
      this.saving = true;
      try {
        if (this.showEditModal && this.selectedPatient) {
          await axios.put(`/api/patients/${this.selectedPatient.id}`, this.form);
        } else {
          await axios.post('/api/patients', this.form);
        }
        this.closeModal();
        this.loadPatients();
      } catch (error) {
        console.error('Error saving patient:', error);
        alert('Error al guardar el paciente');
      } finally {
        this.saving = false;
      }
    },
    async viewPatient(patient) {
      this.selectedPatient = patient;
      await this.loadMedicalHistories(patient.id);
      await this.loadRecipes(patient.id);
      this.showViewModal = true;
    },
    editPatient(patient) {
      this.selectedPatient = patient;
      this.form = { ...patient };
      this.showEditModal = true;
    },
    async deletePatient(id) {
      if (confirm('¿Está seguro de eliminar este paciente?')) {
        try {
          await axios.delete(`/api/patients/${id}`);
          this.loadPatients();
        } catch (error) {
          console.error('Error deleting patient:', error);
          alert('Error al eliminar el paciente');
        }
      }
    },
    async deleteMedicalHistory(historyId) {
      if (confirm('¿Está seguro de eliminar esta consulta médica?')) {
        try {
          await axios.delete(`/api/medical-histories/${historyId}`);
          await this.loadMedicalHistories(this.selectedPatient.id);
        } catch (error) {
          console.error('Error deleting medical history:', error);
          alert('Error al eliminar la consulta médica');
        }
      }
    },
    async loadRecipes(patientId) {
      try {
        const response = await axios.get(`/api/patients/${patientId}/recipes`);
        this.recipes = response.data;
      } catch (error) {
        console.error('Error loading recipes:', error);
      }
    },
    async deleteRecipe(recipeId) {
      if (confirm('¿Está seguro de eliminar este recipe?')) {
        try {
          await axios.delete(`/api/recipes/${recipeId}`);
          await this.loadRecipes(this.selectedPatient.id);
        } catch (error) {
          console.error('Error deleting recipe:', error);
          alert('Error al eliminar el recipe');
        }
      }
    },
    openRecipeFromView() {
      this.$emit('view-recipe', this.selectedPatient);
    },
    formatDate(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString('es-ES');
    },
    async loadMedicalHistories(patientId) {
      try {
        const response = await axios.get(`/api/patients/${patientId}/medical-histories`);
        this.selectedPatient.medical_histories = response.data;
      } catch (error) {
        console.error('Error loading medical histories:', error);
      }
    },
    openConsultationModal() {
      this.consultationForm.patient_id = this.selectedPatient.id;
      this.consultationForm = this.getConsultationFormWithPreviousData();
      this.showConsultationModal = true;
    },
    async openConsultationModalFromCard(patient) {
      this.selectedPatient = patient;
      await this.loadMedicalHistories(patient.id);
      this.consultationForm.patient_id = patient.id;
      this.consultationForm = this.getConsultationFormWithPreviousData();
      this.showConsultationModal = true;
    },
    getConsultationFormWithPreviousData() {
      const baseForm = {
        patient_id: this.selectedPatient.id,
        motivo_consulta: '',
        enfermedad_actual: '',
        antecedentes_familiares: '',
        antecedentes_personales: '',
        examen_neurologico: '',
        examen_laboratorio: '',
        examen_complementario: '',
        diagnostico: '',
        plan_tratamiento: '',
        observacion: '',
        evolucion: ''
      };

      // Si hay consultas anteriores, copiar todos los datos de la última consulta
      if (this.selectedPatient.medical_histories && this.selectedPatient.medical_histories.length > 0) {
        const lastConsultation = this.selectedPatient.medical_histories[0];
        baseForm.motivo_consulta = lastConsultation.motivo_consulta || '';
        baseForm.enfermedad_actual = lastConsultation.enfermedad_actual || '';
        baseForm.antecedentes_familiares = lastConsultation.antecedentes_familiares || '';
        baseForm.antecedentes_personales = lastConsultation.antecedentes_personales || '';
        baseForm.examen_neurologico = lastConsultation.examen_neurologico || '';
        baseForm.examen_laboratorio = lastConsultation.examen_laboratorio || '';
        baseForm.examen_complementario = lastConsultation.examen_complementario || '';
        baseForm.diagnostico = lastConsultation.diagnostico || '';
        baseForm.plan_tratamiento = lastConsultation.plan_tratamiento || '';
        baseForm.observacion = lastConsultation.observacion || '';
        baseForm.evolucion = lastConsultation.evolucion || '';
      }

      return baseForm;
    },
    closeConsultationModal() {
      this.showConsultationModal = false;
      this.resetConsultationForm();
    },
    resetConsultationForm() {
      this.consultationForm = {
        patient_id: null,
        motivo_consulta: '',
        enfermedad_actual: '',
        antecedentes_familiares: '',
        antecedentes_personales: '',
        examen_neurologico: '',
        examen_laboratorio: '',
        examen_complementario: '',
        diagnostico: '',
        plan_tratamiento: '',
        observacion: '',
        evolucion: ''
      };
    },
    async saveConsultation() {
      this.savingConsultation = true;
      try {
        await axios.post('/api/medical-histories', this.consultationForm);
        this.closeConsultationModal();
        await this.loadMedicalHistories(this.selectedPatient.id);
      } catch (error) {
        console.error('Error saving consultation:', error);
        alert('Error al guardar la consulta');
      } finally {
        this.savingConsultation = false;
      }
    },
    viewEvolution(patient) {
      this.$emit('view-evolution', patient.id);
    },
    openRecipe(patient) {
      this.$emit('view-recipe', patient);
    }
  }
};
</script>

<style scoped>
.patients-container {
  max-width: 1400px;
  margin: 0 auto;
}

.patients-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.patients-title {
  color: #1e3c72;
  font-size: 28px;
  font-weight: 700;
}

.create-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.create-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(30, 60, 114, 0.3);
}

.search-bar {
  margin-bottom: 24px;
}

.search-input {
  width: 100%;
  padding: 14px 20px;
  border: 2px solid #e0e0e0;
  border-radius: 10px;
  font-size: 15px;
  transition: all 0.3s;
}

.search-input:focus {
  outline: none;
  border-color: #2a5298;
  box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
}

.loading {
  text-align: center;
  padding: 40px;
  color: #6c757d;
  font-size: 16px;
}

.patients-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 20px;
}

.patient-card {
  background: white;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s;
}

.patient-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.patient-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 20px;
}

.patient-avatar {
  width: 56px;
  height: 56px;
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  font-weight: 700;
}

.patient-info h3 {
  color: #1e3c72;
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 4px;
}

.patient-info p {
  color: #6c757d;
  font-size: 14px;
}

.patient-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 20px;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
}

.detail-label {
  color: #6c757d;
}

.detail-value {
  color: #1e3c72;
  font-weight: 600;
}

.patient-actions {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

.action-btn {
  flex: 1;
  min-width: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 10px 8px;
  border: none;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  white-space: nowrap;
}

.view-btn {
  background: #e3f2fd;
  color: #1976d2;
}

.view-btn:hover {
  background: #bbdefb;
}

.edit-btn {
  background: #fff3e0;
  color: #f57c00;
}

.edit-btn:hover {
  background: #ffe0b2;
}

.consult-btn {
  background: #e8f5e9;
  color: #2e7d32;
}

.consult-btn:hover {
  background: #c8e6c9;
}

.delete-btn {
  background: #ffebee;
  color: #c62828;
}

.delete-btn:hover {
  background: #ffcdd2;
}

.evolution-btn {
  background: #f3e5f5;
  color: #7b1fa2;
}

.evolution-btn:hover {
  background: #e1bee7;
}

.recipe-btn {
  background: #e0f7fa;
  color: #00695c;
}

.recipe-btn:hover {
  background: #b2ebf2;
}

.no-patients {
  grid-column: 1 / -1;
  text-align: center;
  padding: 60px;
  color: #ccc;
}

.no-patients p {
  margin-top: 16px;
  font-size: 16px;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-content {
  background: white;
  border-radius: 20px;
  max-width: 800px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
}

.large-modal {
  max-width: 1000px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px;
  border-bottom: 1px solid #e0e0e0;
}

.modal-header h2 {
  color: #1e3c72;
  font-size: 24px;
  font-weight: 700;
}

.close-btn {
  background: transparent;
  border: none;
  cursor: pointer;
  color: #6c757d;
  transition: color 0.3s;
}

.close-btn:hover {
  color: #1e3c72;
}

.modal-form {
  padding: 24px;
}

.form-section {
  margin-bottom: 24px;
}

.form-section h3 {
  color: #1e3c72;
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 16px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  color: #1e3c72;
  font-weight: 600;
  font-size: 14px;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 10px 14px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.3s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #2a5298;
  box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.1);
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 24px;
  border-top: 1px solid #e0e0e0;
}

.cancel-btn {
  padding: 12px 24px;
  border: 2px solid #e0e0e0;
  background: white;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.cancel-btn:hover {
  background: #f5f7fa;
}

.save-btn {
  padding: 12px 24px;
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.save-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(30, 60, 114, 0.3);
}

.save-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.patient-detail {
  padding: 24px;
}

.detail-section {
  margin-bottom: 32px;
}

.detail-section h3 {
  color: #1e3c72;
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 16px;
}

.detail-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

.detail-row {
  display: flex;
  gap: 8px;
  font-size: 14px;
}

.detail-row.full-width {
  grid-column: 1 / -1;
}

.detail-row span {
  color: #6c757d;
}

.detail-row strong {
  color: #1e3c72;
}

.add-history-btn {
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
  margin-bottom: 16px;
}

.add-history-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(30, 60, 114, 0.3);
}

.history-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.history-item {
  background: #f5f7fa;
  padding: 16px;
  border-radius: 8px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
}

.history-content {
  flex: 1;
}

.history-date {
  color: #6c757d;
  font-size: 12px;
  margin-bottom: 4px;
}

.history-diagnosis {
  color: #1e3c72;
  font-weight: 600;
  font-size: 14px;
}

.delete-history-btn {
  background: #ffebee;
  color: #c62828;
  border: none;
  padding: 8px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.delete-history-btn:hover {
  background: #ffcdd2;
}

.no-history {
  color: #6c757d;
  font-size: 14px;
  padding: 20px;
  text-align: center;
  background: #f5f7fa;
  border-radius: 8px;
}
</style>
