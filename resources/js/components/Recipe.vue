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
      <div class="recipe-toolbar-actions">
        <select v-if="recipes.length > 0" v-model="selectedRecipeId" @change="loadSelectedRecipe" class="recipe-history-select">
          <option value="">Cargar recipe anterior...</option>
          <option v-for="r in recipes" :key="r.id" :value="r.id">{{ formatDate(r.fecha) }} — {{ r.indicaciones.substring(0, 30) }}...</option>
        </select>
        <button v-if="selectedRecipeId" @click="deleteRecipe" class="recipe-delete-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6"/>
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
          </svg>
          Eliminar Recipe
        </button>
        <button @click="saveRecipe" class="recipe-save-btn" :disabled="saving">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
            <polyline points="17 21 17 13 7 13 7 21"/>
            <polyline points="7 3 7 8 15 8"/>
          </svg>
          {{ saving ? 'Guardando...' : 'Guardar Recipe' }}
        </button>
        <button @click="downloadRecipe" class="recipe-download-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
          </svg>
          Descargar Recipe
        </button>
      </div>
    </div>

    <div class="recipe-workspace">
      <!-- Recipe Form -->
      <div class="recipe-form-panel">
        <div class="recipe-form-card">
          <h3>Datos del Recipe</h3>
          <div class="recipe-form-group">
            <div class="recipe-label-row">
              <label>Fecha</label>
              <label class="recipe-check-label">
                <input type="checkbox" v-model="recipeData.mostrarFecha" />
                Mostrar fecha
              </label>
            </div>
            <input v-if="recipeData.mostrarFecha" type="date" v-model="recipeData.fecha" />
            <div v-else class="recipe-no-fecha">Sin fecha (no aparecerá en el recipe)</div>
          </div>
          <div class="recipe-form-group">
            <label>Paciente</label>
            <input type="text" :value="patientFullName" readonly class="readonly-input" />
          </div>
          <div class="recipe-form-group">
            <label>Recipe 1 / Indicaciones</label>
            <textarea v-model="recipeData.indicaciones" rows="8" placeholder="Escriba las indicaciones del recipe aquí..."></textarea>
          </div>
          <div class="recipe-form-group">
            <label>Recipe 2 / Indicaciones (opcional — segunda columna)</label>
            <textarea v-model="recipeData.indicaciones2" rows="8" placeholder="Indicaciones para la segunda copia del recipe..."></textarea>
          </div>
          <div v-if="successMsg" class="recipe-success">{{ successMsg }}</div>
        </div>
      </div>

      <!-- Recipe Preview (printable) — dos columnas -->
      <div class="recipe-preview-panel">
        <h3 class="preview-label">Vista previa del recipe</h3>
        <div id="recipe-print-area" class="recipe-paper-dual">

          <!-- COLUMNA 1 -->
          <div class="recipe-col">
            <div v-if="format.doctor_fondo_agua" class="recipe-watermark">
              <img :src="format.doctor_fondo_agua" alt="fondo" />
            </div>
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
            <div class="rp-meta">
              <div v-if="recipeData.mostrarFecha" class="rp-meta-row"><span class="rp-meta-label">Fecha:</span><span class="rp-meta-value">{{ recipeData.fecha ? formatDate(recipeData.fecha) : '___/___/______' }}</span></div>
              <div class="rp-meta-row"><span class="rp-meta-label">Paciente:</span><span class="rp-meta-value">{{ patientFullName }}</span></div>
              <div v-if="patient && patient.cedula_identidad" class="rp-meta-row"><span class="rp-meta-label">C.I.:</span><span class="rp-meta-value">{{ patient.cedula_identidad }}</span></div>
            </div>
            <div class="rp-rx-section">
              <div class="rp-rx-symbol">℞</div>
              <div class="rp-rx-body">
                <div class="rp-rx-heading">Recipe / Indicaciones</div>
                <template v-if="recipeData.indicaciones">
                  <p v-for="(line, i) in indicacionesLines" :key="i" class="rp-rx-line-text">{{ line }}</p>
                </template>
                <template v-else>
                  <div class="rp-rx-blank-line" v-for="n in 10" :key="n"></div>
                </template>
              </div>
            </div>
            <div class="rp-signature-area">
              <div class="rp-signature-line"></div>
              <div class="rp-signature-name">{{ format.doctor_nombre || 'Nombre del Médico' }}</div>
              <div class="rp-signature-specialty">{{ format.doctor_especialidad || '' }}</div>
            </div>
            <div class="rp-divider rp-divider-bottom"></div>
            <div class="rp-footer">
              <div v-if="format.doctor_direccion">{{ format.doctor_direccion }}</div>
              <div v-if="format.doctor_telefono">{{ format.doctor_telefono }}</div>
            </div>
          </div>

          <!-- Separador vertical -->
          <div class="recipe-col-separator"></div>

          <!-- COLUMNA 2 -->
          <div class="recipe-col">
            <div v-if="format.doctor_fondo_agua" class="recipe-watermark">
              <img :src="format.doctor_fondo_agua" alt="fondo" />
            </div>
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
            <div class="rp-meta">
              <div v-if="recipeData.mostrarFecha" class="rp-meta-row"><span class="rp-meta-label">Fecha:</span><span class="rp-meta-value">{{ recipeData.fecha ? formatDate(recipeData.fecha) : '___/___/______' }}</span></div>
              <div class="rp-meta-row"><span class="rp-meta-label">Paciente:</span><span class="rp-meta-value">{{ patientFullName }}</span></div>
              <div v-if="patient && patient.cedula_identidad" class="rp-meta-row"><span class="rp-meta-label">C.I.:</span><span class="rp-meta-value">{{ patient.cedula_identidad }}</span></div>
            </div>
            <div class="rp-rx-section">
              <div class="rp-rx-symbol">℞</div>
              <div class="rp-rx-body">
                <div class="rp-rx-heading">Recipe / Indicaciones</div>
                <template v-if="recipeData.indicaciones2">
                  <p v-for="(line, i) in indicacionesLines2" :key="i" class="rp-rx-line-text">{{ line }}</p>
                </template>
                <template v-else>
                  <div class="rp-rx-blank-line" v-for="n in 10" :key="n"></div>
                </template>
              </div>
            </div>
            <div class="rp-signature-area">
              <div class="rp-signature-line"></div>
              <div class="rp-signature-name">{{ format.doctor_nombre || 'Nombre del Médico' }}</div>
              <div class="rp-signature-specialty">{{ format.doctor_especialidad || '' }}</div>
            </div>
            <div class="rp-divider rp-divider-bottom"></div>
            <div class="rp-footer">
              <div v-if="format.doctor_direccion">{{ format.doctor_direccion }}</div>
              <div v-if="format.doctor_telefono">{{ format.doctor_telefono }}</div>
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
        indicaciones2: '',
        mostrarFecha: true,
      },
      successMsg: '',
      recipes: [],
      selectedRecipeId: '',
      saving: false,
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
    },
    indicacionesLines2() {
      if (!this.recipeData.indicaciones2) return [];
      return this.recipeData.indicaciones2.split('\n');
    }
  },
  mounted() {
    this.loadFormat();
    this.loadRecipes();
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
    async loadRecipes() {
      if (!this.patient) return;
      try {
        const res = await axios.get(`/api/patients/${this.patient.id}/recipes`);
        this.recipes = res.data;
      } catch (e) {
        console.error('Error loading recipes:', e);
      }
    },
    async saveRecipe() {
      if (!this.patient) return;
      this.saving = true;
      this.successMsg = '';
      try {
        const indicacionesCompletas = this.recipeData.indicaciones2
          ? this.recipeData.indicaciones + '\n---RECIPE2---\n' + this.recipeData.indicaciones2
          : this.recipeData.indicaciones;
        await axios.post('/api/recipes', {
          patient_id: this.patient.id,
          fecha: this.recipeData.fecha,
          indicaciones: indicacionesCompletas,
        });
        this.successMsg = 'Recipe guardado correctamente';
        await this.loadRecipes();
      } catch (e) {
        console.error('Error saving recipe:', e);
        this.successMsg = 'Error al guardar el recipe';
      } finally {
        this.saving = false;
      }
    },
    async loadSelectedRecipe() {
      if (!this.selectedRecipeId) return;
      const recipe = this.recipes.find(r => r.id === this.selectedRecipeId);
      if (recipe) {
        this.recipeData.fecha = recipe.fecha;
        const parts = recipe.indicaciones.split('\n---RECIPE2---\n');
        this.recipeData.indicaciones = parts[0] || '';
        this.recipeData.indicaciones2 = parts[1] || '';
      }
    },
    async deleteRecipe() {
      if (!this.selectedRecipeId) return;
      if (confirm('¿Está seguro de eliminar este recipe?')) {
        try {
          await axios.delete(`/api/recipes/${this.selectedRecipeId}`);
          this.successMsg = 'Recipe eliminado correctamente';
          this.selectedRecipeId = '';
          this.recipeData.fecha = new Date().toISOString().substring(0, 10);
          this.recipeData.indicaciones = '';
          this.recipeData.indicaciones2 = '';
          await this.loadRecipes();
        } catch (e) {
          console.error('Error deleting recipe:', e);
          this.successMsg = 'Error al eliminar el recipe';
        }
      }
    },
    formatDate(dateStr) {
      if (!dateStr) return '';
      const d = new Date(dateStr + 'T00:00:00');
      return d.toLocaleDateString('es-ES');
    },
    buildRecipeColHtml(logoHtml, watermarkHtml, codesHtml, metaHtml, rxBodyHtml, footerHtml) {
      return `
        <div class="recipe-col">
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
        </div>`;
    },
    downloadRecipe() {
      const style = `
        <style>
          @page { size: A4 landscape; margin: 12mm 14mm; }
          body { margin:0; padding:0; font-family: 'Times New Roman', Times, serif; }
          .recipe-page { display:flex; gap:0; align-items:stretch; width:100%; }
          .recipe-col { flex:1; min-width:0; padding:10px 14px; position:relative; overflow:hidden; box-sizing:border-box; }
          .recipe-col-sep { width:2px; background:repeating-linear-gradient(to bottom,#90a4ae 0,#90a4ae 6px,transparent 6px,transparent 12px); flex-shrink:0; margin:6px 0; }
          .recipe-watermark { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:55%; z-index:0; pointer-events:none; }
          .recipe-watermark img { width:100%; opacity:0.1; object-fit:contain; }
          .rp-header { display:flex; align-items:flex-start; gap:12px; margin-bottom:10px; position:relative; z-index:1; }
          .rp-logo { width:64px; height:64px; object-fit:contain; }
          .rp-logo-empty { width:64px; height:64px; }
          .rp-doctor-col { flex:1; }
          .rp-doctor-name { font-size:16px; font-weight:bold; color:#1a3a6e; margin-bottom:2px; }
          .rp-doctor-specialty { font-size:12px; color:#3949ab; font-style:italic; margin-bottom:4px; }
          .rp-doctor-codes { font-size:11px; color:#546e7a; }
          .rp-divider { height:2px; background:linear-gradient(90deg,#1e3c72,#2a5298); margin:8px 0; border-radius:1px; position:relative; z-index:1; }
          .rp-divider-bottom { margin-top:auto; margin-bottom:6px; }
          .rp-meta { margin-bottom:10px; position:relative; z-index:1; }
          .rp-meta-row { font-size:12px; margin-bottom:3px; }
          .rp-meta-label { font-weight:bold; color:#1e3c72; margin-right:5px; }
          .rp-meta-value { color:#37474f; }
          .rp-rx-section { display:flex; gap:8px; margin-bottom:16px; position:relative; z-index:1; }
          .rp-rx-symbol { font-size:34px; font-weight:bold; color:#1e3c72; line-height:1; margin-top:3px; }
          .rp-rx-body { flex:1; }
          .rp-rx-heading { font-size:11px; font-weight:bold; color:#1e3c72; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px; }
          .rp-rx-line-text { font-size:12px; color:#212121; margin-bottom:5px; line-height:1.5; }
          .rp-rx-blank-line { border-bottom:1px solid #cfd8dc; margin-bottom:10px; height:1px; }
          .rp-signature-area { text-align:right; margin-bottom:12px; position:relative; z-index:1; }
          .rp-signature-line { border-top:1px solid #37474f; width:150px; margin-left:auto; margin-bottom:3px; }
          .rp-signature-name { font-size:12px; font-weight:bold; color:#1e3c72; }
          .rp-signature-specialty { font-size:11px; color:#546e7a; font-style:italic; }
          .rp-footer { text-align:center; font-size:10px; color:#546e7a; line-height:1.8; position:relative; z-index:1; }
        </style>
      `;

      const logoHtml = this.format.doctor_logo
        ? `<img src="${this.format.doctor_logo}" class="rp-logo" alt="logo" crossorigin="anonymous" />`
        : `<div class="rp-logo-empty"></div>`;

      const watermarkHtml = this.format.doctor_fondo_agua
        ? `<div class="recipe-watermark"><img src="${this.format.doctor_fondo_agua}" alt="fondo" crossorigin="anonymous" /></div>`
        : '';

      const codeParts = [];
      if (this.format.doctor_codigo_mmps) codeParts.push(`MMPS: ${this.format.doctor_codigo_mmps}`);
      if (this.format.doctor_codigo_cm) codeParts.push(`CM: ${this.format.doctor_codigo_cm}`);
      if (this.format.doctor_ci) codeParts.push(`CI: ${this.format.doctor_ci}`);
      const codesHtml = codeParts.length ? `<div class="rp-doctor-codes">${codeParts.join('&nbsp;&nbsp;')}</div>` : '';

      let metaHtml = '';
      if (this.recipeData.mostrarFecha) {
        metaHtml += `<div class="rp-meta-row"><span class="rp-meta-label">Fecha:</span> <span class="rp-meta-value">${this.recipeData.fecha ? this.formatDate(this.recipeData.fecha) : '___/___/______'}</span></div>`;
      }
      metaHtml += `<div class="rp-meta-row"><span class="rp-meta-label">Paciente:</span> <span class="rp-meta-value">${this.patientFullName}</span></div>`;
      if (this.patient && this.patient.cedula_identidad) {
        metaHtml += `<div class="rp-meta-row"><span class="rp-meta-label">C.I.:</span> <span class="rp-meta-value">${this.patient.cedula_identidad}</span></div>`;
      }

      const rxBody1 = this.recipeData.indicaciones
        ? this.indicacionesLines.map(l => `<p class="rp-rx-line-text">${l || '&nbsp;'}</p>`).join('')
        : Array(10).fill('<div class="rp-rx-blank-line"></div>').join('');

      const rxBody2 = this.recipeData.indicaciones2
        ? this.indicacionesLines2.map(l => `<p class="rp-rx-line-text">${l || '&nbsp;'}</p>`).join('')
        : Array(10).fill('<div class="rp-rx-blank-line"></div>').join('');

      let footerHtml = '';
      if (this.format.doctor_direccion) footerHtml += `<div>${this.format.doctor_direccion}</div>`;
      if (this.format.doctor_telefono) footerHtml += `<div>${this.format.doctor_telefono}</div>`;

      const col1 = this.buildRecipeColHtml(logoHtml, watermarkHtml, codesHtml, metaHtml, rxBody1, footerHtml);
      const col2 = this.buildRecipeColHtml(logoHtml, watermarkHtml, codesHtml, metaHtml, rxBody2, footerHtml);

      const html = `<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Recipe — ${this.patientFullName}</title>
  ${style}
</head>
<body>
  <div class="recipe-page">
    ${col1}
    <div class="recipe-col-sep"></div>
    ${col2}
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
  min-width: 0;
}

.recipe-toolbar-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.recipe-history-select {
  padding: 10px 14px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 14px;
  color: #37474f;
  background: white;
  cursor: pointer;
  transition: all 0.3s;
  max-width: 250px;
}

@media (max-width: 768px) {
  .recipe-toolbar {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
  }

  .recipe-toolbar-title {
    font-size: 18px;
    text-align: center;
  }

  .recipe-toolbar-actions {
    flex-direction: column;
    width: 100%;
  }

  .recipe-history-select {
    width: 100%;
    max-width: none;
  }

  .recipe-save-btn,
  .recipe-download-btn,
  .recipe-delete-btn {
    width: 100%;
    justify-content: center;
  }

  .recipe-back-btn {
    width: 100%;
    justify-content: center;
  }
}

.recipe-history-select:focus {
  outline: none;
  border-color: #2a5298;
}

.recipe-save-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #00695c 0%, #00796b 100%);
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.recipe-save-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0,105,92,0.3);
}

.recipe-save-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
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

.recipe-delete-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #ffebee;
  color: #c62828;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.recipe-delete-btn:hover {
  background: #ffcdd2;
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

.recipe-label-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2px;
}

.recipe-check-label {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: #546e7a;
  font-weight: 500;
  cursor: pointer;
}

.recipe-check-label input[type="checkbox"] {
  width: 15px;
  height: 15px;
  cursor: pointer;
  accent-color: #2a5298;
}

.recipe-no-fecha {
  padding: 8px 12px;
  background: #f5f7fa;
  border: 2px dashed #c5cae9;
  border-radius: 8px;
  font-size: 13px;
  color: #90a4ae;
  font-style: italic;
}

/* Preview panel */
.preview-label {
  color: #1e3c72;
  font-size: 15px;
  font-weight: 600;
  margin-bottom: 12px;
}

.recipe-paper-dual {
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.12);
  padding: 20px 16px;
  font-family: 'Times New Roman', Times, serif;
  display: flex;
  gap: 0;
  align-items: stretch;
}

.recipe-col {
  flex: 1;
  min-width: 0;
  padding: 12px 16px;
  position: relative;
  overflow: hidden;
}

.recipe-col-separator {
  width: 2px;
  background: repeating-linear-gradient(
    to bottom,
    #90a4ae 0px,
    #90a4ae 6px,
    transparent 6px,
    transparent 12px
  );
  flex-shrink: 0;
  margin: 8px 0;
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
