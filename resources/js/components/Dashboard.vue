<template>
  <div class="dashboard">
    <nav class="navbar">
      <div class="navbar-brand">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
        </svg>
        <span>Sistema Médico</span>
      </div>
      <button @click="logout" class="logout-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
          <polyline points="16 17 21 12 16 7"/>
          <line x1="21" y1="12" x2="9" y2="12"/>
        </svg>
        Cerrar Sesión
      </button>
    </nav>

    <div class="dashboard-content">
      <div class="sidebar">
        <button 
          @click="currentView = 'patients'" 
          :class="['nav-item', { active: currentView === 'patients' }]"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
          Pacientes
        </button>
      </div>

      <main class="main-content">
        <Patients v-if="currentView === 'patients'" @view-evolution="showEvolution" />
        <Evolution v-if="currentView === 'evolution'" :patient-id="selectedPatientId" @back="currentView = 'patients'" />
      </main>
    </div>
  </div>
</template>

<script>
import Patients from './Patients.vue';
import Evolution from './Evolution.vue';

export default {
  name: 'Dashboard',
  components: {
    Patients,
    Evolution
  },
  data() {
    return {
      currentView: 'patients',
      selectedPatientId: null
    };
  },
  methods: {
    logout() {
      this.$emit('logout');
    },
    showEvolution(patientId) {
      this.selectedPatientId = patientId;
      this.currentView = 'evolution';
    }
  }
};
</script>

<style scoped>
.dashboard {
  min-height: 100vh;
  background: #f5f7fa;
}

.navbar {
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  padding: 16px 32px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.navbar-brand {
  display: flex;
  align-items: center;
  gap: 12px;
  color: white;
  font-size: 20px;
  font-weight: 700;
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
  transition: all 0.3s;
}

.logout-btn:hover {
  background: rgba(255, 255, 255, 0.3);
}

.dashboard-content {
  display: flex;
  min-height: calc(100vh - 70px);
}

.sidebar {
  width: 250px;
  background: white;
  padding: 20px;
  border-right: 1px solid #e0e0e0;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  width: 100%;
  padding: 12px 16px;
  border: none;
  background: transparent;
  color: #6c757d;
  font-size: 15px;
  font-weight: 500;
  cursor: pointer;
  border-radius: 8px;
  transition: all 0.3s;
  text-align: left;
}

.nav-item:hover {
  background: #f5f7fa;
  color: #1e3c72;
}

.nav-item.active {
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  color: white;
}

.main-content {
  flex: 1;
  padding: 32px;
  overflow-y: auto;
}
</style>
