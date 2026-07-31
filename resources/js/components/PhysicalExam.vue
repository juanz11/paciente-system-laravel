<template>
  <div class="pe-container">
    <div class="pe-toolbar">
      <button @click="$emit('back')" class="pe-back-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="19" y1="12" x2="5" y2="12"/>
          <polyline points="12 19 5 12 12 5"/>
        </svg>
        Volver
      </button>
      <h2 class="pe-toolbar-title">Examen Físico — {{ patient ? patient.nombres + ' ' + patient.apellidos : '' }}</h2>
      <div class="pe-toolbar-actions">
        <select v-if="exams.length > 0" v-model="selectedExamId" @change="loadSelectedExam" class="pe-history-select">
          <option value="">Cargar examen anterior...</option>
          <option v-for="e in exams" :key="e.id" :value="e.id">{{ formatDate(e.fecha) }}</option>
        </select>
        <button @click="saveExam" class="pe-save-btn" :disabled="saving">
          {{ saving ? 'Guardando...' : 'Guardar Examen' }}
        </button>
        <button @click="downloadExam" class="pe-download-btn">
          Descargar PDF
        </button>
      </div>
    </div>

    <div v-if="successMsg" class="pe-success">{{ successMsg }}</div>
    <div v-if="errorMsg" class="pe-error">{{ errorMsg }}</div>

    <div class="pe-form-card">
      <!-- Sección 1: Antecedentes de Importancia -->
      <div class="pe-section">
        <h3>Antecedentes de Importancia</h3>
        <div class="pe-checkbox-grid">
          <div v-for="item in antecedentesList" :key="item.key" class="pe-checkbox-item">
            <label>
              <input type="checkbox" v-model="form.antecedentes[item.key]" />
              {{ item.label }}
            </label>
          </div>
        </div>
      </div>

      <!-- Sección 2: Examen Físico y Signos Vitales -->
      <div class="pe-section">
        <h3>Examen Físico Actual y Signos Vitales</h3>
        <div class="pe-form-grid">
          <div class="pe-form-group">
            <label>Condiciones Generales</label>
            <select v-model="form.condiciones_generales">
              <option value="">Seleccione...</option>
              <option value="Buenas">Buenas</option>
              <option value="Regular">Regular</option>
              <option value="Malas">Malas</option>
            </select>
          </div>
          <div class="pe-form-group">
            <label>P.A. (mmHg)</label>
            <input type="text" v-model="form.pa" placeholder="Ej: 120/80" />
          </div>
          <div class="pe-form-group">
            <label>Pulso (ppm)</label>
            <input type="text" v-model="form.pulso" placeholder="Ej: 72" />
          </div>
          <div class="pe-form-group">
            <label>SO2 (%)</label>
            <input type="text" v-model="form.so2" placeholder="Ej: 98" />
          </div>
        </div>
      </div>

      <!-- Sección 3: Balance Corporal -->
      <div class="pe-section">
        <h3>Balance Corporal (Composición)</h3>
        <div class="pe-form-grid">
          <div class="pe-form-group"><label>Peso (kg)</label><input type="number" step="0.01" v-model="form.peso" @input="calcIMC" /></div>
          <div class="pe-form-group"><label>Talla (cm)</label><input type="number" step="0.01" v-model="form.talla" @input="calcIMC" /></div>
          <div class="pe-form-group"><label>IMC</label><input type="number" step="0.01" v-model="form.imc" readonly /></div>
          <div class="pe-form-group"><label>Circunferencia Abdominal (cm)</label><input type="number" step="0.01" v-model="form.circunferencia_abdominal" /></div>
          <div class="pe-form-group"><label>% Grasa</label><input type="number" step="0.01" v-model="form.grasa_pct" /></div>
          <div class="pe-form-group"><label>% Agua</label><input type="number" step="0.01" v-model="form.agua_pct" /></div>
          <div class="pe-form-group"><label>Masa Muscular (kg)</label><input type="number" step="0.01" v-model="form.masa_muscular" /></div>
          <div class="pe-form-group"><label>Tasa Física (1-9)</label><input type="number" v-model="form.tasa_fisica" /></div>
          <div class="pe-form-group"><label>Calorías (kcal)</label><input type="number" step="0.01" v-model="form.calorias" /></div>
          <div class="pe-form-group"><label>Edad Metabólica (años)</label><input type="number" v-model="form.edad_metabolica" /></div>
          <div class="pe-form-group"><label>Masa Ósea (kg)</label><input type="number" step="0.01" v-model="form.masa_osea" /></div>
          <div class="pe-form-group"><label>Grasa Visceral (1-59)</label><input type="number" v-model="form.grasa_visceral" /></div>
        </div>
        <div class="pe-form-group full-width">
          <label>Conclusión del Balance Corporal</label>
          <textarea v-model="form.conclusion_balance" rows="2"></textarea>
        </div>
      </div>

      <!-- Sección 4: Examen Físico por Sistemas -->
      <div class="pe-section">
        <h3>Examen Físico por Sistemas</h3>

        <h4 class="pe-subsection-title">Piel</h4>
        <div class="pe-checkbox-row">
          <label><input type="checkbox" v-model="form.examen_piel.sin_lesiones" /> Sin lesiones</label>
          <label><input type="checkbox" v-model="form.examen_piel.lesion_sospechosa" /> Lesión sospechosa</label>
          <label><input type="checkbox" v-model="form.examen_piel.tinte_icterico" /> Tinte ictérico</label>
          <label><input type="checkbox" v-model="form.examen_piel.palidez" /> Palidez</label>
        </div>

        <h4 class="pe-subsection-title">Pupilas</h4>
        <div class="pe-checkbox-row">
          <label><input type="checkbox" v-model="form.examen_pupilas.isocoricas" /> Isocóricas Normoreactivas</label>
          <label><input type="checkbox" v-model="form.examen_pupilas.fondo_normal" /> Fondo de ojo Normal</label>
        </div>
        <div class="pe-form-group">
          <label>Grado</label>
          <input type="text" v-model="form.examen_pupilas.grado" placeholder="Grado" />
        </div>

        <h4 class="pe-subsection-title">Cuello</h4>
        <div class="pe-form-grid">
          <div class="pe-form-group"><label>PVY</label><input type="text" v-model="form.examen_cuello.pvy" placeholder="Seno x d" /></div>
          <div class="pe-form-group">
            <label>Latidos Carotídeos</label>
            <select v-model="form.examen_cuello.latidos_carotideos">
              <option value="">Seleccione...</option>
              <option value="Presentes y simétricos">Presentes y simétricos</option>
              <option value="Disminuidos">Disminuidos</option>
              <option value="Soplos">Soplos</option>
            </select>
          </div>
          <div class="pe-form-group"><label>Movilidad</label><input type="text" v-model="form.examen_cuello.movilidad" placeholder="Normal" /></div>
          <div class="pe-form-group"><label>Adenopatías</label><input type="text" v-model="form.examen_cuello.adenopatias" placeholder="No" /></div>
        </div>

        <h4 class="pe-subsection-title">Tórax</h4>
        <div class="pe-form-grid">
          <div class="pe-form-group"><label>SPN</label><input type="text" v-model="form.examen_torax.spn" placeholder="SI/NO" /></div>
          <div class="pe-form-group"><label>Apex</label><input type="text" v-model="form.examen_torax.apex" placeholder="5° EIC/LMC" /></div>
          <div class="pe-form-group">
            <label>Ruidos Cardíacos</label>
            <select v-model="form.examen_torax.ruidos_cardiacos">
              <option value="">Seleccione...</option>
              <option value="Normales">Normales</option>
              <option value="R1">R1</option>
              <option value="R2">R2</option>
              <option value="R3">R3</option>
              <option value="R4">R4</option>
            </select>
          </div>
          <div class="pe-form-group">
            <label>Soplos</label>
            <select v-model="form.examen_torax.soplos">
              <option value="">Seleccione...</option>
              <option value="Ausentes">Ausentes</option>
              <option value="Presentes">Presentes</option>
              <option value="Sistólicos">Sistólicos</option>
              <option value="Diastólicos">Diastólicos</option>
            </select>
          </div>
          <div class="pe-form-group"><label>Área Soplo</label><input type="text" v-model="form.examen_torax.area_soplo" placeholder="Área" /></div>
        </div>

        <h4 class="pe-subsection-title">Pulmonar</h4>
        <div class="pe-form-grid">
          <div class="pe-form-group">
            <label>Murmullo Vesicular</label>
            <select v-model="form.examen_pulmonar.murmullo">
              <option value="">Seleccione...</option>
              <option value="Presente">Presente</option>
              <option value="Ausente">Ausente</option>
              <option value="Sibilantes">Sibilantes</option>
              <option value="Crepitantes">Crepitantes</option>
            </select>
          </div>
          <div class="pe-form-group"><label>Hemotórax Der</label><input type="text" v-model="form.examen_pulmonar.hemotorax_der" placeholder="1/3 Sup, Med, Inf" /></div>
          <div class="pe-form-group"><label>Hemotórax Izq</label><input type="text" v-model="form.examen_pulmonar.hemotorax_izq" placeholder="1/3 Sup, Med, Inf" /></div>
        </div>

        <h4 class="pe-subsection-title">Abdomen</h4>
        <div class="pe-checkbox-row">
          <label><input type="checkbox" v-model="form.examen_abdomen.normal" /> Normal</label>
          <label><input type="checkbox" v-model="form.examen_abdomen.gestante" /> Gestante</label>
          <label><input type="checkbox" v-model="form.examen_abdomen.hepatomegalia" /> Hepatomegalia</label>
          <label><input type="checkbox" v-model="form.examen_abdomen.esplenomegalia" /> Esplenomegalia</label>
          <label><input type="checkbox" v-model="form.examen_abdomen.masa_palpable" /> Masa palpable</label>
          <label><input type="checkbox" v-model="form.examen_abdomen.ascitis" /> Ascitis</label>
          <label><input type="checkbox" v-model="form.examen_abdomen.doloroso" /> Doloroso</label>
        </div>

        <h4 class="pe-subsection-title">Miembros</h4>
        <div class="pe-checkbox-row">
          <label><input type="checkbox" v-model="form.examen_miembros.normal" /> Normal</label>
          <label><input type="checkbox" v-model="form.examen_miembros.edema" /> Edema</label>
        </div>
        <div class="pe-form-grid">
          <div class="pe-form-group"><label>Várices (Grado)</label><input type="text" v-model="form.examen_miembros.varices_grado" placeholder="Grado" /></div>
          <div class="pe-form-group">
            <label>Pulsos Periféricos</label>
            <select v-model="form.examen_miembros.pulsos_perifericos">
              <option value="">Seleccione...</option>
              <option value="Presentes y simétricos">Presentes y simétricos</option>
              <option value="Alteraciones">Alteraciones</option>
            </select>
          </div>
        </div>

        <h4 class="pe-subsection-title">Neurológico</h4>
        <div class="pe-checkbox-row">
          <label><input type="checkbox" v-model="form.examen_neurologico.consciente_orientado" /> Consciente y orientado</label>
        </div>
      </div>

      <!-- Sección 5: Estudios Complementarios -->
      <div class="pe-section">
        <h3>Estudios Complementarios</h3>

        <h4 class="pe-subsection-title">Electrocardiograma (ECG)</h4>
        <div class="pe-form-grid">
          <div class="pe-form-group">
            <label>Ritmo</label>
            <select v-model="form.ecg.ritmo">
              <option value="">Seleccione...</option>
              <option value="Sinusal">Sinusal</option>
              <option value="No Sinusal">No Sinusal</option>
            </select>
          </div>
          <div class="pe-form-group"><label>Frecuencia (ipm)</label><input type="text" v-model="form.ecg.frecuencia" /></div>
          <div class="pe-form-group"><label>PR (ms)</label><input type="text" v-model="form.ecg.pr" /></div>
          <div class="pe-form-group"><label>QRS (ms)</label><input type="text" v-model="form.ecg.qrs" /></div>
          <div class="pe-form-group"><label>Eje (°)</label><input type="text" v-model="form.ecg.eje" /></div>
          <div class="pe-form-group"><label>QT (ms)</label><input type="text" v-model="form.ecg.qt" /></div>
          <div class="pe-form-group"><label>QTc (ms)</label><input type="text" v-model="form.ecg.qtc" /></div>
          <div class="pe-form-group">
            <label>Diagnóstico</label>
            <select v-model="form.ecg.diagnostico">
              <option value="">Seleccione...</option>
              <option value="Normal">Normal</option>
              <option value="Patológico">Patológico</option>
            </select>
          </div>
          <div class="pe-form-group full-width"><label>Alteraciones</label><input type="text" v-model="form.ecg.alteraciones" /></div>
        </div>

        <h4 class="pe-subsection-title">Rayos X de Tórax</h4>
        <div class="pe-form-group full-width">
          <textarea v-model="form.rayos_x_torax" rows="3" placeholder="Silueta cardíaca, Botón aórtico, Botón pulmonar, Hilios, Campos pulmonares"></textarea>
        </div>

        <h4 class="pe-subsection-title">Espirometría</h4>
        <div class="pe-form-grid">
          <div class="pe-form-group"><label>FEV1 (%)</label><input type="text" v-model="form.espirometria.fev1" /></div>
          <div class="pe-form-group"><label>FEV1/6 (%)</label><input type="text" v-model="form.espirometria.fev1_6" /></div>
          <div class="pe-form-group"><label>FEV6 (%)</label><input type="text" v-model="form.espirometria.fev6" /></div>
        </div>

        <h4 class="pe-subsection-title">Ecosonograma</h4>
        <div class="pe-form-group full-width">
          <textarea v-model="form.ecosonograma" rows="2" placeholder="Rx SPN CORN TABIQUE"></textarea>
        </div>
      </div>

      <!-- Sección 6: Laboratorio Clínico -->
      <div class="pe-section">
        <h3>Laboratorio Clínico</h3>

        <h4 class="pe-subsection-title">Hematología y Coagulación</h4>
        <div class="pe-form-grid">
          <div class="pe-form-group"><label>Hb</label><input type="text" v-model="form.hematologia.hb" /></div>
          <div class="pe-form-group"><label>Hto</label><input type="text" v-model="form.hematologia.hto" /></div>
          <div class="pe-form-group"><label>Glóbulos Blancos</label><input type="text" v-model="form.hematologia.globulos_blancos" /></div>
          <div class="pe-form-group"><label>Plaquetas</label><input type="text" v-model="form.hematologia.plaquetas" /></div>
          <div class="pe-form-group"><label>Segmentados (%)</label><input type="text" v-model="form.hematologia.segmentados" /></div>
          <div class="pe-form-group"><label>Linfocitos (%)</label><input type="text" v-model="form.hematologia.linfocitos" /></div>
          <div class="pe-form-group"><label>PT (s)</label><input type="text" v-model="form.hematologia.pt" /></div>
          <div class="pe-form-group"><label>PTT (s)</label><input type="text" v-model="form.hematologia.ptt" /></div>
        </div>

        <h4 class="pe-subsection-title">Serología y Química Básica</h4>
        <div class="pe-form-grid">
          <div class="pe-form-group"><label>VDRL</label><input type="text" v-model="form.serologia.vdrl" /></div>
          <div class="pe-form-group"><label>HIV</label><input type="text" v-model="form.serologia.hiv" /></div>
          <div class="pe-form-group"><label>Glicemia</label><input type="text" v-model="form.serologia.glicemia" /></div>
          <div class="pe-form-group"><label>Creatinina</label><input type="text" v-model="form.serologia.creatinina" /></div>
          <div class="pe-form-group"><label>Urea</label><input type="text" v-model="form.serologia.urea" /></div>
          <div class="pe-form-group"><label>Ácido Úrico</label><input type="text" v-model="form.serologia.acido_urico" /></div>
          <div class="pe-form-group"><label>TGO</label><input type="text" v-model="form.serologia.tgo" /></div>
          <div class="pe-form-group"><label>TGP</label><input type="text" v-model="form.serologia.tgp" /></div>
        </div>

        <h4 class="pe-subsection-title">Perfil Lipídico</h4>
        <div class="pe-form-grid">
          <div class="pe-form-group"><label>Colesterol Total</label><input type="text" v-model="form.perfil_lipidico.colesterol_total" /></div>
          <div class="pe-form-group"><label>Triglicéridos</label><input type="text" v-model="form.perfil_lipidico.trigliceridos" /></div>
          <div class="pe-form-group"><label>HDL</label><input type="text" v-model="form.perfil_lipidico.hdl" /></div>
          <div class="pe-form-group"><label>LDL</label><input type="text" v-model="form.perfil_lipidico.ldl" /></div>
        </div>

        <h4 class="pe-subsection-title">Electrólitos y Hormonas</h4>
        <div class="pe-form-grid">
          <div class="pe-form-group"><label>Potasio (K)</label><input type="text" v-model="form.electrolitos.potasio" /></div>
          <div class="pe-form-group"><label>Calcio (Ca)</label><input type="text" v-model="form.electrolitos.calcio" /></div>
          <div class="pe-form-group"><label>HbA1c</label><input type="text" v-model="form.electrolitos.hba1c" /></div>
          <div class="pe-form-group"><label>TSH</label><input type="text" v-model="form.electrolitos.tsh" /></div>
          <div class="pe-form-group"><label>T4L</label><input type="text" v-model="form.electrolitos.t4l" /></div>
        </div>

        <h4 class="pe-subsection-title">Examen de Orina</h4>
        <div class="pe-form-group full-width">
          <textarea v-model="form.orina.texto" rows="2" placeholder="Relación Albúmina/Creatinina"></textarea>
        </div>
      </div>

      <!-- Sección 7: Evaluación de Riesgo -->
      <div class="pe-section">
        <h3>Evaluación de Riesgo</h3>
        <div class="pe-form-grid">
          <div class="pe-form-group full-width">
            <label>Riesgo de Enfermedad Cardiovascular a 10 Años</label>
            <input type="text" v-model="form.riesgo_cardiovascular" placeholder="Ej: Bajo / Moderado / Alto" />
          </div>
          <div class="pe-form-group full-width">
            <label>Diagnóstico Clínico Global</label>
            <textarea v-model="form.diagnostico_global" rows="3"></textarea>
          </div>
        </div>
      </div>

      <!-- Sección 8: Indicaciones y Sugerencias -->
      <div class="pe-section">
        <h3>Indicaciones y Sugerencias Médicas</h3>
        <div class="pe-form-grid">
          <div class="pe-form-group full-width">
            <label>Dieta y Hábitos</label>
            <textarea v-model="form.dieta_habitos" rows="3"></textarea>
          </div>
          <div class="pe-form-group full-width">
            <label>Plan de Ejercicios y Estilo de Vida</label>
            <textarea v-model="form.plan_ejercicios" rows="3"></textarea>
          </div>
          <div class="pe-form-group full-width">
            <label>Tratamiento Farmacológico / Suplementación</label>
            <textarea v-model="form.tratamiento_farmacologico" rows="3"></textarea>
          </div>
          <div class="pe-form-group full-width">
            <label>Próximos Estudios o Citas de Control</label>
            <textarea v-model="form.proximos_estudios" rows="3"></textarea>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'PhysicalExam',
  props: {
    patient: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      form: this.getEmptyForm(),
      exams: [],
      selectedExamId: '',
      saving: false,
      successMsg: '',
      errorMsg: '',
      antecedentesList: [
        { key: 'alergia', label: 'Alergia' },
        { key: 'cardiopatia', label: 'Cardiopatía' },
        { key: 'valvulopatia', label: 'Valvulopatía' },
        { key: 'pericarditis', label: 'Pericarditis' },
        { key: 'chagas', label: 'Chagas' },
        { key: 'fiebre_reumatica', label: 'Fiebre Reumática' },
        { key: 'asma_epoc', label: 'Asma - EPOC' },
        { key: 'tiroides', label: 'Tiroides' },
        { key: 'hepatopatias', label: 'Hepatopatías' },
        { key: 'lues', label: 'LUES (Sífilis)' },
        { key: 'renal', label: 'Renal' },
        { key: 'reumatologicas', label: 'Reumatológicas' },
        { key: 'cns', label: 'Enfermedades del SNC' },
        { key: 'hemopatias', label: 'Hemopatías' },
        { key: 'colagenopatias', label: 'Colagenopatías' },
        { key: 'neumopatias', label: 'Neumopatías' },
        { key: 'quirurgico', label: 'Quirúrgico' },
        { key: 'tabaquicos', label: 'Tabaquicos' },
      ]
    };
  },
  mounted() {
    this.form.fecha = new Date().toISOString().split('T')[0];
    this.loadExams();
  },
  methods: {
    getEmptyForm() {
      return {
        fecha: '',
        antecedentes: {},
        condiciones_generales: '',
        pa: '',
        pulso: '',
        so2: '',
        peso: null,
        talla: null,
        imc: null,
        circunferencia_abdominal: null,
        grasa_pct: null,
        agua_pct: null,
        masa_muscular: null,
        tasa_fisica: null,
        calorias: null,
        edad_metabolica: null,
        masa_osea: null,
        grasa_visceral: null,
        conclusion_balance: '',
        examen_piel: {},
        examen_pupilas: {},
        examen_cuello: {},
        examen_torax: {},
        examen_pulmonar: {},
        examen_abdomen: {},
        examen_miembros: {},
        examen_neurologico: {},
        ecg: {},
        rayos_x_torax: '',
        espirometria: {},
        ecosonograma: '',
        hematologia: {},
        serologia: {},
        perfil_lipidico: {},
        electrolitos: {},
        orina: {},
        riesgo_cardiovascular: '',
        diagnostico_global: '',
        dieta_habitos: '',
        plan_ejercicios: '',
        tratamiento_farmacologico: '',
        proximos_estudios: '',
      };
    },
    calcIMC() {
      if (this.form.peso && this.form.talla) {
        const tallaM = this.form.talla / 100;
        if (tallaM > 0) {
          this.form.imc = (this.form.peso / (tallaM * tallaM)).toFixed(2);
        }
      }
    },
    async loadExams() {
      if (!this.patient) return;
      try {
        const res = await axios.get(`/api/patients/${this.patient.id}/physical-exams`);
        this.exams = res.data;
        if (this.exams.length > 0) {
          this.selectedExamId = this.exams[0].id;
          this.loadSelectedExam();
        }
      } catch (e) {
        console.error('Error loading exams:', e);
      }
    },
    async saveExam() {
      if (!this.patient) return;
      this.saving = true;
      this.successMsg = '';
      this.errorMsg = '';
      try {
        await axios.post('/api/physical-exams', {
          patient_id: this.patient.id,
          ...this.form,
        });
        this.successMsg = 'Examen guardado correctamente';
        await this.loadExams();
      } catch (e) {
        console.error('Error saving exam:', e);
        if (e.response && e.response.data && e.response.data.errors) {
          this.errorMsg = Object.values(e.response.data.errors).flat().join(', ');
        } else if (e.response && e.response.data && e.response.data.message) {
          this.errorMsg = e.response.data.message;
        } else {
          this.errorMsg = 'Error al guardar el examen';
        }
      } finally {
        this.saving = false;
      }
    },
    loadSelectedExam() {
      if (!this.selectedExamId) return;
      const exam = this.exams.find(e => e.id === this.selectedExamId);
      if (exam) {
        this.form = { ...this.getEmptyForm(), ...exam };
      }
    },
    formatDate(date) {
      if (!date) return '';
      return new Date(date).toLocaleDateString('es-ES');
    },
    downloadExam() {
      const win = window.open('', '_blank');
      const html = this.buildPrintHTML();
      win.document.write(html);
      win.document.close();
    },
    buildPrintHTML() {
      const p = this.patient || {};
      const f = this.form;
      const ant = f.antecedentes || {};
      const antChecked = this.antecedentesList
        .filter(a => ant[a.key])
        .map(a => a.label)
        .join(', ');

      return `<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Examen Físico - ${p.nombres || ''} ${p.apellidos || ''}</title>
<style>
  body { font-family: 'Times New Roman', serif; max-width: 800px; margin: 0 auto; padding: 20px; color: #333; }
  h1 { text-align: center; color: #1e3c72; font-size: 20px; margin-bottom: 5px; }
  h2 { color: #1e3c72; font-size: 16px; border-bottom: 2px solid #1e3c72; padding-bottom: 4px; margin-top: 20px; }
  h3 { color: #2a5298; font-size: 14px; margin-top: 15px; }
  .patient-info { margin-bottom: 15px; font-size: 13px; }
  .patient-info strong { color: #1e3c72; }
  .section { margin-bottom: 15px; }
  .grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; font-size: 12px; }
  .grid div { padding: 2px 0; }
  .full { grid-column: 1 / -1; }
  .label { color: #666; font-weight: bold; }
  .value { color: #333; }
  .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #ccc; font-size: 11px; text-align: center; color: #666; }
  @media print { body { padding: 10px; } }
</style>
</head>
<body>
  <h1>SVMI - Sociedad Venezolana de Medicina Interna</h1>
  <div class="patient-info">
    <strong>Paciente:</strong> ${p.nombres || ''} ${p.apellidos || ''} |
    <strong>Edad:</strong> ${p.edad || 'N/A'} |
    <strong>Sexo:</strong> ${p.sexo || 'N/A'} |
    <strong>C.I.:</strong> ${p.cedula_identidad || 'N/A'} |
    <strong>Fecha:</strong> ${this.formatDate(f.fecha)}
  </div>

  <h2>Antecedentes de Importancia</h2>
  <div class="section">${antChecked || 'Ninguno'}</div>

  <h2>Examen Físico y Signos Vitales</h2>
  <div class="grid">
    <div><span class="label">Cond. Generales:</span> <span class="value">${f.condiciones_generales || 'N/A'}</span></div>
    <div><span class="label">P.A.:</span> <span class="value">${f.pa || 'N/A'}</span></div>
    <div><span class="label">Pulso:</span> <span class="value">${f.pulso || 'N/A'}</span></div>
    <div><span class="label">SO2:</span> <span class="value">${f.so2 || 'N/A'}%</span></div>
  </div>

  <h2>Balance Corporal</h2>
  <div class="grid">
    <div><span class="label">Peso:</span> <span class="value">${f.peso || 'N/A'} kg</span></div>
    <div><span class="label">Talla:</span> <span class="value">${f.talla || 'N/A'} cm</span></div>
    <div><span class="label">IMC:</span> <span class="value">${f.imc || 'N/A'}</span></div>
    <div><span class="label">Circ. Abdominal:</span> <span class="value">${f.circunferencia_abdominal || 'N/A'} cm</span></div>
    <div><span class="label">% Grasa:</span> <span class="value">${f.grasa_pct || 'N/A'}%</span></div>
    <div><span class="label">% Agua:</span> <span class="value">${f.agua_pct || 'N/A'}%</span></div>
    <div><span class="label">Masa Muscular:</span> <span class="value">${f.masa_muscular || 'N/A'} kg</span></div>
    <div><span class="label">Grasa Visceral:</span> <span class="value">${f.grasa_visceral || 'N/A'}</span></div>
    <div><span class="label">Calorías:</span> <span class="value">${f.calorias || 'N/A'} kcal</span></div>
    <div><span class="label">Edad Metabólica:</span> <span class="value">${f.edad_metabolica || 'N/A'} años</span></div>
  </div>
  ${f.conclusion_balance ? `<div class="section"><span class="label">Conclusión:</span> ${f.conclusion_balance}</div>` : ''}

  <h2>Examen Físico por Sistemas</h2>
  <h3>Piel</h3>
  <div class="section">${this.formatCheckboxes(f.examen_piel, ['sin_lesiones:Sin lesiones', 'lesion_sospechosa:Lesión sospechosa', 'tinte_icterico:Tinte ictérico', 'palidez:Palidez'])}</div>
  <h3>Pupilas</h3>
  <div class="section">${this.formatCheckboxes(f.examen_pupilas, ['isocoricas:Isocóricas Normoreactivas', 'fondo_normal:Fondo de ojo Normal'])} ${f.examen_pupilas?.grado ? 'Grado: ' + f.examen_pupilas.grado : ''}</div>
  <h3>Cuello</h3>
  <div class="grid">
    <div><span class="label">PVY:</span> <span class="value">${f.examen_cuello?.pvy || 'N/A'}</span></div>
    <div><span class="label">Latidos Carotídeos:</span> <span class="value">${f.examen_cuello?.latidos_carotideos || 'N/A'}</span></div>
    <div><span class="label">Movilidad:</span> <span class="value">${f.examen_cuello?.movilidad || 'N/A'}</span></div>
    <div><span class="label">Adenopatías:</span> <span class="value">${f.examen_cuello?.adenopatias || 'N/A'}</span></div>
  </div>
  <h3>Tórax</h3>
  <div class="grid">
    <div><span class="label">SPN:</span> <span class="value">${f.examen_torax?.spn || 'N/A'}</span></div>
    <div><span class="label">Apex:</span> <span class="value">${f.examen_torax?.apex || 'N/A'}</span></div>
    <div><span class="label">Ruidos Cardíacos:</span> <span class="value">${f.examen_torax?.ruidos_cardiacos || 'N/A'}</span></div>
    <div><span class="label">Soplos:</span> <span class="value">${f.examen_torax?.soplos || 'N/A'}</span></div>
  </div>
  <h3>Pulmonar</h3>
  <div class="grid">
    <div><span class="label">Murmullo:</span> <span class="value">${f.examen_pulmonar?.murmullo || 'N/A'}</span></div>
    <div><span class="label">Hemotórax Der:</span> <span class="value">${f.examen_pulmonar?.hemotorax_der || 'N/A'}</span></div>
    <div><span class="label">Hemotórax Izq:</span> <span class="value">${f.examen_pulmonar?.hemotorax_izq || 'N/A'}</span></div>
  </div>
  <h3>Abdomen</h3>
  <div class="section">${this.formatCheckboxes(f.examen_abdomen, ['normal:Normal', 'gestante:Gestante', 'hepatomegalia:Hepatomegalia', 'esplenomegalia:Esplenomegalia', 'masa_palpable:Masa palpable', 'ascitis:Ascitis', 'doloroso:Doloroso'])}</div>
  <h3>Miembros</h3>
  <div class="grid">
    <div>${this.formatCheckboxes(f.examen_miembros, ['normal:Normal', 'edema:Edema'])}</div>
    <div><span class="label">Várices:</span> <span class="value">${f.examen_miembros?.varices_grado || 'N/A'}</span></div>
    <div><span class="label">Pulsos Periféricos:</span> <span class="value">${f.examen_miembros?.pulsos_perifericos || 'N/A'}</span></div>
  </div>
  <h3>Neurológico</h3>
  <div class="section">${this.formatCheckboxes(f.examen_neurologico, ['consciente_orientado:Consciente y orientado'])}</div>

  <h2>Estudios Complementarios</h2>
  <h3>ECG</h3>
  <div class="grid">
    <div><span class="label">Ritmo:</span> <span class="value">${f.ecg?.ritmo || 'N/A'}</span></div>
    <div><span class="label">Frecuencia:</span> <span class="value">${f.ecg?.frecuencia || 'N/A'}</span></div>
    <div><span class="label">PR:</span> <span class="value">${f.ecg?.pr || 'N/A'}</span></div>
    <div><span class="label">QRS:</span> <span class="value">${f.ecg?.qrs || 'N/A'}</span></div>
    <div><span class="label">Eje:</span> <span class="value">${f.ecg?.eje || 'N/A'}</span></div>
    <div><span class="label">QT:</span> <span class="value">${f.ecg?.qt || 'N/A'}</span></div>
    <div><span class="label">QTc:</span> <span class="value">${f.ecg?.qtc || 'N/A'}</span></div>
    <div><span class="label">Diagnóstico:</span> <span class="value">${f.ecg?.diagnostico || 'N/A'}</span></div>
  </div>
  ${f.rayos_x_torax ? `<h3>RX Tórax</h3><div class="section">${f.rayos_x_torax}</div>` : ''}
  ${f.ecosonograma ? `<h3>Ecosonograma</h3><div class="section">${f.ecosonograma}</div>` : ''}

  <h2>Laboratorio Clínico</h2>
  <h3>Hematología</h3>
  <div class="grid">
    <div><span class="label">Hb:</span> <span class="value">${f.hematologia?.hb || 'N/A'}</span></div>
    <div><span class="label">Hto:</span> <span class="value">${f.hematologia?.hto || 'N/A'}</span></div>
    <div><span class="label">GB:</span> <span class="value">${f.hematologia?.globulos_blancos || 'N/A'}</span></div>
    <div><span class="label">Plaquetas:</span> <span class="value">${f.hematologia?.plaquetas || 'N/A'}</span></div>
  </div>
  <h3>Serología y Química</h3>
  <div class="grid">
    <div><span class="label">Glicemia:</span> <span class="value">${f.serologia?.glicemia || 'N/A'}</span></div>
    <div><span class="label">Creatinina:</span> <span class="value">${f.serologia?.creatinina || 'N/A'}</span></div>
    <div><span class="label">Urea:</span> <span class="value">${f.serologia?.urea || 'N/A'}</span></div>
    <div><span class="label">TGO:</span> <span class="value">${f.serologia?.tgo || 'N/A'}</span></div>
    <div><span class="label">TGP:</span> <span class="value">${f.serologia?.tgp || 'N/A'}</span></div>
  </div>
  <h3>Perfil Lipídico</h3>
  <div class="grid">
    <div><span class="label">Col. Total:</span> <span class="value">${f.perfil_lipidico?.colesterol_total || 'N/A'}</span></div>
    <div><span class="label">Triglicéridos:</span> <span class="value">${f.perfil_lipidico?.trigliceridos || 'N/A'}</span></div>
    <div><span class="label">HDL:</span> <span class="value">${f.perfil_lipidico?.hdl || 'N/A'}</span></div>
    <div><span class="label">LDL:</span> <span class="value">${f.perfil_lipidico?.ldl || 'N/A'}</span></div>
  </div>

  <h2>Evaluación de Riesgo</h2>
  <div class="section"><span class="label">Riesgo Cardiovascular:</span> ${f.riesgo_cardiovascular || 'N/A'}</div>
  ${f.diagnostico_global ? `<div class="section"><span class="label">Diagnóstico Global:</span> ${f.diagnostico_global}</div>` : ''}

  <h2>Indicaciones y Sugerencias</h2>
  ${f.dieta_habitos ? `<div class="section"><span class="label">Dieta y Hábitos:</span> ${f.dieta_habitos}</div>` : ''}
  ${f.plan_ejercicios ? `<div class="section"><span class="label">Plan de Ejercicios:</span> ${f.plan_ejercicios}</div>` : ''}
  ${f.tratamiento_farmacologico ? `<div class="section"><span class="label">Tratamiento:</span> ${f.tratamiento_farmacologico}</div>` : ''}
  ${f.proximos_estudios ? `<div class="section"><span class="label">Próximos Estudios:</span> ${f.proximos_estudios}</div>` : ''}

  <div class="footer">
    <p>Documento generado por Sistema de Gestión Médica</p>
  </div>
</body>
</html>`;
    },
    formatCheckboxes(obj, items) {
      if (!obj) return 'N/A';
      const checked = items
        .filter(item => {
          const [key] = item.split(':');
          return obj[key];
        })
        .map(item => item.split(':')[1]);
      return checked.length > 0 ? checked.join(', ') : 'N/A';
    }
  }
};
</script>

<style scoped>
.pe-container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 20px;
}

.pe-toolbar {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 24px;
  flex-wrap: wrap;
}

.pe-toolbar-title {
  flex: 1;
  color: #1e3c72;
  font-size: 22px;
  font-weight: 700;
  min-width: 0;
}

.pe-toolbar-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.pe-back-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #e3f2fd;
  color: #1976d2;
  border: none;
  padding: 10px 16px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.pe-back-btn:hover {
  background: #bbdefb;
}

.pe-history-select {
  padding: 10px 14px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 14px;
  color: #37474f;
  background: white;
  cursor: pointer;
  max-width: 250px;
}

.pe-history-select:focus {
  outline: none;
  border-color: #2a5298;
}

.pe-save-btn {
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

.pe-save-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0,105,92,0.3);
}

.pe-save-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.pe-download-btn {
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

.pe-download-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(30,60,114,0.3);
}

.pe-success {
  background: #e8f5e9;
  color: #2e7d32;
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 16px;
  font-weight: 600;
  font-size: 14px;
}

.pe-error {
  background: #ffebee;
  color: #c62828;
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 16px;
  font-weight: 600;
  font-size: 14px;
}

.pe-form-card {
  background: white;
  border-radius: 16px;
  padding: 28px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.pe-section {
  margin-bottom: 28px;
  padding-bottom: 20px;
  border-bottom: 1px solid #f0f0f0;
}

.pe-section:last-child {
  border-bottom: none;
}

.pe-section h3 {
  color: #1e3c72;
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 16px;
}

.pe-subsection-title {
  color: #2a5298;
  font-size: 14px;
  font-weight: 600;
  margin: 16px 0 8px;
}

.pe-form-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

.pe-form-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.pe-form-group.full-width {
  grid-column: 1 / -1;
}

.pe-form-group label {
  font-size: 13px;
  font-weight: 600;
  color: #546e7a;
}

.pe-form-group input,
.pe-form-group select,
.pe-form-group textarea {
  padding: 10px 12px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 14px;
  color: #37474f;
  transition: border-color 0.3s;
  font-family: inherit;
}

.pe-form-group input:focus,
.pe-form-group select:focus,
.pe-form-group textarea:focus {
  outline: none;
  border-color: #2a5298;
}

.pe-checkbox-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
}

.pe-checkbox-item label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #37474f;
  cursor: pointer;
}

.pe-checkbox-item input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #2a5298;
}

.pe-checkbox-row {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  margin-bottom: 12px;
}

.pe-checkbox-row label {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
  color: #37474f;
  cursor: pointer;
}

.pe-checkbox-row input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #2a5298;
}

@media (max-width: 768px) {
  .pe-toolbar {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
  }

  .pe-toolbar-title {
    font-size: 18px;
    text-align: center;
  }

  .pe-toolbar-actions {
    flex-direction: column;
    width: 100%;
  }

  .pe-history-select,
  .pe-save-btn,
  .pe-download-btn,
  .pe-back-btn {
    width: 100%;
    justify-content: center;
  }

  .pe-form-grid,
  .pe-checkbox-grid {
    grid-template-columns: 1fr;
  }
}
</style>
