<template>
  <div class="rf-container">
    <div class="rf-header">
      <h1 class="rf-title">Formato de Recipe</h1>
      <p class="rf-subtitle">Configure los datos que aparecerán en el encabezado y pie de sus recetas médicas</p>
    </div>

    <div class="rf-layout">
      <!-- Form -->
      <div class="rf-form-card">
        <form @submit.prevent="saveFormat" enctype="multipart/form-data">
          <div class="rf-section">
            <h3>Datos del Médico (Encabezado)</h3>
            <div class="rf-grid">
              <div class="rf-group full-width">
                <label>Nombre completo del médico</label>
                <input type="text" v-model="form.doctor_nombre" placeholder="Ej: Dra. María González" />
              </div>
              <div class="rf-group full-width">
                <label>Especialidad</label>
                <input type="text" v-model="form.doctor_especialidad" placeholder="Ej: Médico Psiquiatra" />
              </div>
              <div class="rf-group">
                <label>Código MMPS</label>
                <input type="text" v-model="form.doctor_codigo_mmps" placeholder="Ej: 12345" />
              </div>
              <div class="rf-group">
                <label>Código CM</label>
                <input type="text" v-model="form.doctor_codigo_cm" placeholder="Ej: CM-6789" />
              </div>
              <div class="rf-group">
                <label>Cédula de Identidad (CI)</label>
                <input type="text" v-model="form.doctor_ci" placeholder="Ej: V-12.345.678" />
              </div>
            </div>
          </div>

          <div class="rf-section">
            <h3>Imágenes</h3>
            <div class="rf-grid">
              <div class="rf-group">
                <label>Logo del consultorio</label>
                <div class="rf-upload-area" @click="$refs.logoInput.click()">
                  <img v-if="logoPreview" :src="logoPreview" class="rf-preview-img" alt="Logo" />
                  <div v-else class="rf-upload-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#2a5298" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                      <circle cx="8.5" cy="8.5" r="1.5"/>
                      <polyline points="21 15 16 10 5 21"/>
                    </svg>
                    <span>Subir logo</span>
                  </div>
                  <input ref="logoInput" type="file" accept="image/*" class="rf-file-hidden" @change="onLogoChange" />
                </div>
                <button v-if="logoPreview" type="button" class="rf-remove-btn" @click="removeLogo">Quitar logo</button>
              </div>
              <div class="rf-group">
                <label>Fondo de agua (marca de agua)</label>
                <div class="rf-upload-area" @click="$refs.fondoInput.click()">
                  <img v-if="fondoPreview" :src="fondoPreview" class="rf-preview-img rf-preview-watermark" alt="Fondo agua" />
                  <div v-else class="rf-upload-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#2a5298" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                      <circle cx="8.5" cy="8.5" r="1.5"/>
                      <polyline points="21 15 16 10 5 21"/>
                    </svg>
                    <span>Subir fondo de agua</span>
                  </div>
                  <input ref="fondoInput" type="file" accept="image/*" class="rf-file-hidden" @change="onFondoChange" />
                </div>
                <button v-if="fondoPreview" type="button" class="rf-remove-btn" @click="removeFondo">Quitar fondo</button>
              </div>
            </div>
          </div>

          <div class="rf-section">
            <h3>Datos de Contacto (Pie de página)</h3>
            <div class="rf-grid">
              <div class="rf-group full-width">
                <label>Dirección del consultorio</label>
                <input type="text" v-model="form.doctor_direccion" placeholder="Ej: Av. Principal, Edificio Médico, Piso 3, Consultorio 12" />
              </div>
              <div class="rf-group">
                <label>Teléfono / Número de contacto</label>
                <input type="text" v-model="form.doctor_telefono" placeholder="Ej: +58 412-123-4567" />
              </div>
            </div>
          </div>

          <div class="rf-footer">
            <div v-if="successMsg" class="rf-success">{{ successMsg }}</div>
            <div v-if="errorMsg" class="rf-error">{{ errorMsg }}</div>
            <button type="submit" class="rf-save-btn" :disabled="saving">
              {{ saving ? 'Guardando...' : 'Guardar Formato' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Preview -->
      <div class="rf-preview-card">
        <h3>Vista previa del encabezado</h3>
        <div class="rf-recipe-preview">
          <div class="rf-recipe-header">
            <div class="rf-recipe-logo-area">
              <img v-if="logoPreview" :src="logoPreview" class="rf-recipe-logo" alt="Logo" />
              <div v-else class="rf-recipe-logo-placeholder">LOGO</div>
            </div>
            <div class="rf-recipe-doctor-info">
              <div class="rf-doctor-name">{{ form.doctor_nombre || 'Dra. Nombre Apellido' }}</div>
              <div class="rf-doctor-specialty">{{ form.doctor_especialidad || 'Especialidad Médica' }}</div>
              <div v-if="form.doctor_codigo_mmps || form.doctor_codigo_cm || form.doctor_ci" class="rf-doctor-codes">
                <span v-if="form.doctor_codigo_mmps">MMPS: {{ form.doctor_codigo_mmps }}</span>
                <span v-if="form.doctor_codigo_cm">CM: {{ form.doctor_codigo_cm }}</span>
                <span v-if="form.doctor_ci">CI: {{ form.doctor_ci }}</span>
              </div>
            </div>
          </div>
          <div class="rf-recipe-divider"></div>
          <div class="rf-recipe-body-sample">
            <div class="rf-sample-row"><span class="rf-sample-label">Fecha:</span> <span>{{ todayFormatted }}</span></div>
            <div class="rf-sample-row"><span class="rf-sample-label">Paciente:</span> <span>Juan Ejemplo</span></div>
            <div class="rf-recipe-rx">
              <div class="rf-rx-symbol">℞</div>
              <div class="rf-rx-content">
                <div class="rf-rx-title">Indicaciones / Recipe</div>
                <div class="rf-rx-lines">
                  <div class="rf-rx-line"></div>
                  <div class="rf-rx-line"></div>
                  <div class="rf-rx-line"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="rf-recipe-footer-preview">
            <div>{{ form.doctor_direccion || 'Dirección del consultorio' }}</div>
            <div>{{ form.doctor_telefono || 'Teléfono de contacto' }}</div>
          </div>
          <div v-if="fondoPreview" class="rf-watermark-overlay">
            <img :src="fondoPreview" class="rf-watermark-img" alt="fondo" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'RecipeFormat',
  data() {
    return {
      form: {
        doctor_nombre: '',
        doctor_especialidad: '',
        doctor_codigo_mmps: '',
        doctor_codigo_cm: '',
        doctor_ci: '',
        doctor_direccion: '',
        doctor_telefono: '',
      },
      logoFile: null,
      fondoFile: null,
      logoPreview: null,
      fondoPreview: null,
      saving: false,
      successMsg: '',
      errorMsg: '',
    };
  },
  computed: {
    todayFormatted() {
      return new Date().toLocaleDateString('es-ES');
    }
  },
  mounted() {
    this.loadFormat();
    this.loadUser();
  },
  methods: {
    async loadFormat() {
      try {
        const res = await axios.get('/api/recipe-format');
        const d = res.data;
        this.form.doctor_nombre = d.doctor_nombre || '';
        this.form.doctor_especialidad = d.doctor_especialidad || '';
        this.form.doctor_codigo_mmps = d.doctor_codigo_mmps || '';
        this.form.doctor_codigo_cm = d.doctor_codigo_cm || '';
        this.form.doctor_ci = d.doctor_ci || '';
        this.form.doctor_direccion = d.doctor_direccion || '';
        this.form.doctor_telefono = d.doctor_telefono || '';
        if (d.doctor_logo) this.logoPreview = d.doctor_logo;
        if (d.doctor_fondo_agua) this.fondoPreview = d.doctor_fondo_agua;
      } catch (e) {
        console.error('Error loading recipe format:', e);
      }
    },
    async loadUser() {
      try {
        const res = await axios.get('/api/me');
        const user = res.data.user;
        if (!this.form.doctor_nombre && user.name) {
          this.form.doctor_nombre = user.name;
        }
      } catch (e) {
        console.error('Error loading user:', e);
      }
    },
    onLogoChange(e) {
      const file = e.target.files[0];
      if (file) {
        this.logoFile = file;
        this.logoPreview = URL.createObjectURL(file);
      }
    },
    onFondoChange(e) {
      const file = e.target.files[0];
      if (file) {
        this.fondoFile = file;
        this.fondoPreview = URL.createObjectURL(file);
      }
    },
    removeLogo() {
      this.logoFile = null;
      this.logoPreview = null;
      if (this.$refs.logoInput) this.$refs.logoInput.value = '';
    },
    removeFondo() {
      this.fondoFile = null;
      this.fondoPreview = null;
      if (this.$refs.fondoInput) this.$refs.fondoInput.value = '';
    },
    async saveFormat() {
      this.saving = true;
      this.successMsg = '';
      this.errorMsg = '';
      try {
        const fd = new FormData();
        fd.append('doctor_nombre', this.form.doctor_nombre);
        fd.append('doctor_especialidad', this.form.doctor_especialidad);
        fd.append('doctor_codigo_mmps', this.form.doctor_codigo_mmps);
        fd.append('doctor_codigo_cm', this.form.doctor_codigo_cm);
        fd.append('doctor_ci', this.form.doctor_ci);
        fd.append('doctor_direccion', this.form.doctor_direccion);
        fd.append('doctor_telefono', this.form.doctor_telefono);
        if (this.logoFile) fd.append('doctor_logo', this.logoFile);
        if (this.fondoFile) fd.append('doctor_fondo_agua', this.fondoFile);

        const res = await axios.post('/api/recipe-format', fd, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });

        if (res.data.doctor_logo) this.logoPreview = res.data.doctor_logo;
        if (res.data.doctor_fondo_agua) this.fondoPreview = res.data.doctor_fondo_agua;
        this.logoFile = null;
        this.fondoFile = null;
        this.successMsg = 'Formato guardado correctamente';
      } catch (e) {
        console.error('Error saving format:', e);
        if (e.response && e.response.data && e.response.data.errors) {
          const errors = e.response.data.errors;
          this.errorMsg = Object.values(errors).flat().join(', ');
        } else if (e.response && e.response.data && e.response.data.message) {
          this.errorMsg = e.response.data.message;
        } else {
          this.errorMsg = 'Error al guardar el formato: ' + (e.message || 'Error desconocido');
        }
      } finally {
        this.saving = false;
      }
    }
  }
};
</script>

<style scoped>
.rf-container {
  max-width: 1400px;
  margin: 0 auto;
}

.rf-header {
  margin-bottom: 28px;
}

.rf-title {
  color: #1e3c72;
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 6px;
}

.rf-subtitle {
  color: #6c757d;
  font-size: 15px;
}

.rf-layout {
  display: grid;
  grid-template-columns: 1fr 420px;
  gap: 28px;
  align-items: start;
}

@media (max-width: 1100px) {
  .rf-layout {
    grid-template-columns: 1fr;
  }
}

.rf-form-card {
  background: white;
  border-radius: 16px;
  padding: 28px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.rf-section {
  margin-bottom: 28px;
}

.rf-section h3 {
  color: #1e3c72;
  font-size: 17px;
  font-weight: 600;
  margin-bottom: 16px;
  padding-bottom: 8px;
  border-bottom: 2px solid #e8eaf6;
}

.rf-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.rf-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.rf-group.full-width {
  grid-column: 1 / -1;
}

.rf-group label {
  color: #1e3c72;
  font-weight: 600;
  font-size: 13px;
}

.rf-group input {
  padding: 10px 14px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.3s;
}

.rf-group input:focus {
  outline: none;
  border-color: #2a5298;
  box-shadow: 0 0 0 3px rgba(42,82,152,0.1);
}

.rf-upload-area {
  border: 2px dashed #c5cae9;
  border-radius: 10px;
  padding: 16px;
  cursor: pointer;
  min-height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: border-color 0.3s;
  position: relative;
  overflow: hidden;
}

.rf-upload-area:hover {
  border-color: #2a5298;
}

.rf-upload-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  color: #2a5298;
  font-size: 13px;
  font-weight: 500;
}

.rf-file-hidden {
  display: none;
}

.rf-preview-img {
  max-height: 100px;
  max-width: 100%;
  border-radius: 6px;
  object-fit: contain;
}

.rf-preview-watermark {
  opacity: 0.5;
}

.rf-remove-btn {
  background: #ffebee;
  color: #c62828;
  border: none;
  border-radius: 6px;
  padding: 6px 12px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
  align-self: flex-start;
}

.rf-remove-btn:hover {
  background: #ffcdd2;
}

.rf-footer {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 16px;
  padding-top: 24px;
  border-top: 1px solid #e0e0e0;
  flex-wrap: wrap;
}

.rf-success {
  color: #2e7d32;
  font-size: 14px;
  font-weight: 600;
}

.rf-error {
  color: #c62828;
  font-size: 14px;
  font-weight: 600;
}

.rf-save-btn {
  padding: 12px 28px;
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.rf-save-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(30,60,114,0.3);
}

.rf-save-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 1100px) {
  .rf-layout {
    grid-template-columns: 1fr;
  }

  .rf-preview-card {
    position: static;
  }
}

@media (max-width: 768px) {
  .rf-header {
    text-align: center;
  }

  .rf-title {
    font-size: 22px;
  }

  .rf-grid {
    grid-template-columns: 1fr;
  }

  .rf-footer {
    flex-direction: column;
    align-items: stretch;
  }

  .rf-save-btn {
    width: 100%;
  }

  .rf-upload-actions {
    flex-direction: column;
    width: 100%;
  }

  .rf-upload-btn,
  .rf-remove-btn {
    width: 100%;
  }
}

/* Preview Card */
.rf-preview-card {
  background: white;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  position: sticky;
  top: 20px;
}

.rf-preview-card h3 {
  color: #1e3c72;
  font-size: 15px;
  font-weight: 600;
  margin-bottom: 16px;
}

.rf-recipe-preview {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 16px;
  position: relative;
  overflow: hidden;
  background: white;
  font-family: 'Times New Roman', Times, serif;
}

.rf-recipe-header {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 12px;
}

.rf-recipe-logo-area {
  flex-shrink: 0;
}

.rf-recipe-logo {
  width: 70px;
  height: 70px;
  object-fit: contain;
  border-radius: 4px;
}

.rf-recipe-logo-placeholder {
  width: 70px;
  height: 70px;
  background: #e8eaf6;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  font-weight: 700;
  color: #5c6bc0;
}

.rf-recipe-doctor-info {
  flex: 1;
}

.rf-doctor-name {
  font-size: 16px;
  font-weight: 700;
  color: #1e3c72;
  margin-bottom: 2px;
}

.rf-doctor-specialty {
  font-size: 13px;
  color: #3949ab;
  font-style: italic;
  margin-bottom: 4px;
}

.rf-doctor-codes {
  font-size: 11px;
  color: #546e7a;
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.rf-recipe-divider {
  height: 2px;
  background: linear-gradient(90deg, #1e3c72 0%, #2a5298 100%);
  margin-bottom: 10px;
  border-radius: 1px;
}

.rf-recipe-body-sample {
  margin-bottom: 12px;
}

.rf-sample-row {
  font-size: 12px;
  color: #37474f;
  margin-bottom: 4px;
}

.rf-sample-label {
  font-weight: 700;
  color: #1e3c72;
  margin-right: 4px;
}

.rf-recipe-rx {
  display: flex;
  gap: 8px;
  margin-top: 10px;
}

.rf-rx-symbol {
  font-size: 24px;
  font-weight: 700;
  color: #1e3c72;
  line-height: 1;
}

.rf-rx-content {
  flex: 1;
}

.rf-rx-title {
  font-size: 11px;
  font-weight: 700;
  color: #1e3c72;
  margin-bottom: 6px;
}

.rf-rx-lines {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.rf-rx-line {
  height: 1px;
  background: #cfd8dc;
  border-radius: 1px;
}

.rf-recipe-footer-preview {
  margin-top: 14px;
  padding-top: 8px;
  border-top: 1px solid #e0e0e0;
  font-size: 10px;
  color: #546e7a;
  text-align: center;
  line-height: 1.6;
}

.rf-watermark-overlay {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 60%;
  pointer-events: none;
  display: flex;
  align-items: center;
  justify-content: center;
}

.rf-watermark-img {
  width: 100%;
  opacity: 0.12;
  object-fit: contain;
}
</style>
